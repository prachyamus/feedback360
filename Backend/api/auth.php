<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

// ตรวจสอบ method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed. Only POST is supported.', 405);
}

// ดึงข้อมูลจาก POST (รองรับทั้ง JSON และ form data)
$data = getPostData();

$username = isset($data['emp_id']) ? $data['emp_id'] : (isset($data['username']) ? $data['username'] : '');
$password = isset($data['password']) ? $data['password'] : '';

// ตรวจสอบข้อมูล
if (empty($username) || empty($password)) {
    sendErrorResponse('Username and password are required');
}

// เชื่อมต่อ ERP และตรวจสอบ login
$result = checkLogin($username, $password);

if ($result['success']) {
    // สร้าง token
    $token = createToken($username);
    $result['data']['token'] = $token;
    
    // ไม่ตั้งค่า cookies ที่ backend ให้ frontend จัดการเอง
    sendJsonResponse($result['data']);
} else {
    sendErrorResponse($result['message'], 401);
}

// ฟังก์ชันตรวจสอบ login
function checkLogin($username, $password) {
    // เรียก ERP API
    $erpResponse = callERP('CHECK_LOGIN_GET_PERSONAL_INFORMATION', array(
        'EMP_USER' => $username,
        'EMP_PASSWORD' => $password
    ));
    
    if (!$erpResponse) {
        return array(
            'success' => false,
            'message' => 'Cannot connect to ERP system'
        );
    }
    
    $messageReturn = isset($erpResponse['MessageReturn']) ? $erpResponse['MessageReturn'] : '';
    
    if ($messageReturn === 'Login Success') {
        // ดึงข้อมูลพนักงาน
        $empData = callERP('GET_PERSONAL_INFORMATION', array(
            'EMP_CODE' => $erpResponse['EmployeeCode']
        ));
        
        if ($empData) {
            // ตรวจสอบสิทธิ์ admin
            $empData['admin_permiss'] = checkAdminPermission($erpResponse['EmployeeCode']);
            $empData['MessageReturn'] = $messageReturn;
            
            return array(
                'success' => true,
                'data' => $empData
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Cannot get employee data'
            );
        }
    } else {
        return array(
            'success' => false,
            'message' => $messageReturn ? $messageReturn : 'Invalid username or password'
        );
    }
}

// ฟังก์ชันเรียก ERP API
function callERP($function, $params) {
    $curl = curl_init();
    
    // สร้าง parameters
    $paramString = '';
    foreach ($params as $key => $value) {
        $paramString .= '"' . $key . '":"' . $value . '",';
    }
    $paramString = rtrim($paramString, ',');
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://192.168.1.21:8000/Services/Information/EdenERP_Service.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array(
            'callServer' => '[{"Function":"' . $function . '",' . $paramString . ',"SOURCE_PATH":"WebService"}]'
        ),
        CURLOPT_TIMEOUT => 30
    ));
    
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    
    if ($error) {
        error_log('cURL Error: ' . $error);
        return null;
    }
    
    if (!$response) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    if (!$data || !isset($data["Result"][0]["Data"])) {
        return null;
    }
    
    return $data["Result"][0]["Data"];
}

// ฟังก์ชันตรวจสอบสิทธิ์ admin
function checkAdminPermission($empId) {
    global $conn;
    
    if (!$conn) {
        return 1; // Default level
    }
    
    $sql = "SELECT emp_permission FROM admin_user WHERE emp_id = ? AND emp_permission > 0";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $empId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['emp_permission'];
        }
    }
    
    return 1; // Default level
}

// ฟังก์ชันสร้าง token
function createToken($userId) {
    $random = md5(uniqid(mt_rand(), true));
    return base64_encode($userId . ':' . time() . ':' . $random);
}
?>
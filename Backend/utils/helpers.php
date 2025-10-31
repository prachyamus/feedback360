<?php

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function validateRequiredFields($data, $requiredFields) {
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(array(
                'success' => false,
                'message' => "Field '{$field}' is required"
            ));
            exit;
        }
    }
}

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(array(
        'success' => true,
        'data' => $data
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

function sendErrorResponse($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode(array(
        'success' => false,
        'message' => $message
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

function getPostData() {
    $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
    
    if (stripos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents("php://input"), true);
        return $data ? $data : array();
    }
    
    return $_POST;
}

function setAuthCookie($name, $value, $days = 1) {
    $expireTime = time() + ($days * 86400);
    setcookie($name, $value, $expireTime, "/");
}

function getCookie($name) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
}

function deleteAuthCookie($name) {
    setcookie($name, "", time() - 3600, "/");
}

function setUserCookies($userData, $token, $days = 1) {
    setAuthCookie('auth_token', $token, $days);
    setAuthCookie('E_Code', isset($userData['EmployeeCode']) ? $userData['EmployeeCode'] : '', $days);
    setAuthCookie('E_LocalFirstName', isset($userData['LocalFirstName']) ? $userData['LocalFirstName'] : '', $days);
    setAuthCookie('E_LocalLastName', isset($userData['LocalLastName']) ? $userData['LocalLastName'] : '', $days);
    setAuthCookie('E_ImagePath', isset($userData['ImagePath']) ? $userData['ImagePath'] : '', $days);
    setAuthCookie('E_Level', isset($userData['Level']) ? $userData['Level'] : '', $days);
    setAuthCookie('E_Division', isset($userData['DivisionName']) ? $userData['DivisionName'] : '', $days);
    setAuthCookie('admin_permiss', isset($userData['admin_permiss']) ? $userData['admin_permiss'] : '1', $days);
    setAuthCookie('adminlevel', isset($userData['admin_permiss']) ? $userData['admin_permiss'] : '1', $days);
}

function deleteAllAuthCookies() {
    $cookies = array(
        'auth_token',
        'E_Code',
        'E_LocalFirstName',
        'E_LocalLastName',
        'E_ImagePath',
        'E_Level',
        'E_Division',
        'admin_permiss',
        'adminlevel'
    );
    
    foreach ($cookies as $cookie) {
        deleteAuthCookie($cookie);
    }
}
//ดึงข้อมูลจาก ERP
function getERPUser($emp_code)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://192.168.1.21:8000/Services/Information/EdenERP_Service.php",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => array('callServer' => '[{"Function":"GET_PERSONAL_INFORMATION","EMP_CODE":"' . $emp_code . '","SOURCE_PATH":"WebService"}]'),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $someArray = json_decode($response, true);
    $aDataFromService = $someArray["Result"][0]["Data"];
    return $aDataFromService;
}
<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    getAllUsers($conn);
} elseif ($method === 'POST') {
    handlePost($conn);
} else {
    sendErrorResponse('Method not allowed. Only GET and POST are supported.', 405);
}

function getAllUsers($conn) {
    $rounded = isset($_GET['rounded']) ? $_GET['rounded'] : '5';
    
    $sql = "SELECT * FROM user_list WHERE rounded = ? ORDER BY emp_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rounded);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    sendJsonResponse($users);
}

function handlePost($conn) {
    $data = getPostData();
    $action = isset($data['action']) ? $data['action'] : 'create';
    
    switch ($action) {
        case 'create':
            createUser($data, $conn);
            break;
        
        case 'update':
            updateUser($data, $conn);
            break;
        
        case 'delete':
            deleteUser($data, $conn);
            break;
        
        default:
            sendErrorResponse('Invalid action. Supported actions: create, update, delete');
    }
}

function createUser($data, $conn) {
    validateRequiredFields($data, array('emp_id', 'rounded'));
    
    $emp_id = sanitizeInput($data['emp_id']);
    $rounded = sanitizeInput($data['rounded']);
    $emp_fname = sanitizeInput(isset($data['emp_fname']) ? $data['emp_fname'] : '');
    $emp_lname = sanitizeInput(isset($data['emp_lname']) ? $data['emp_lname'] : '');
    $emp_nickname = sanitizeInput(isset($data['emp_nickname']) ? $data['emp_nickname'] : '');
    $emp_div = sanitizeInput(isset($data['emp_div']) ? $data['emp_div'] : '');
    $emp_dep = sanitizeInput(isset($data['emp_dep']) ? $data['emp_dep'] : '');
    $emp_position = sanitizeInput(isset($data['emp_position']) ? $data['emp_position'] : '');
    $emp_level = sanitizeInput(isset($data['emp_level']) ? $data['emp_level'] : '');
    
    $sql = "INSERT INTO user_list (emp_id, rounded, emp_fname, emp_lname, emp_nickname, emp_div, emp_dep, emp_position, emp_level) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $emp_id, $rounded, $emp_fname, $emp_lname, $emp_nickname, $emp_div, $emp_dep, $emp_position, $emp_level);
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'สร้างผู้ใช้สำเร็จ', 'id' => $conn->insert_id), 201);
    } else {
        sendErrorResponse('Failed to create user: ' . $conn->error, 500);
    }
}

function updateUser($data, $conn) {
    validateRequiredFields($data, array('emp_id', 'rounded'));
    
    $emp_id = sanitizeInput($data['emp_id']);
    $rounded = sanitizeInput($data['rounded']);
    
    $fields = array();
    $types = '';
    $values = array();
    
    $updateFields = array('emp_fname', 'emp_lname', 'emp_nickname', 'emp_div', 'emp_dep', 'emp_position', 'emp_level');
    
    foreach ($updateFields as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = ?";
            $types .= 's';
            $values[] = sanitizeInput($data[$field]);
        }
    }
    
    if (empty($fields)) {
        sendErrorResponse('No fields to update');
    }
    
    $types .= 'ss';
    $values[] = $emp_id;
    $values[] = $rounded;
    
    $sql = "UPDATE user_list SET " . implode(', ', $fields) . " WHERE emp_id = ? AND rounded = ?";
    $stmt = $conn->prepare($sql);
    call_user_func_array(array($stmt, 'bind_param'), array_merge(array($types), $values));
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'อัปเดตผู้ใช้สำเร็จ'));
    } else {
        sendErrorResponse('Failed to update user: ' . $conn->error, 500);
    }
}

function deleteUser($data, $conn) {
    validateRequiredFields($data, array('emp_id', 'rounded'));
    
    $emp_id = sanitizeInput($data['emp_id']);
    $rounded = sanitizeInput($data['rounded']);
    
    $sql = "DELETE FROM user_list WHERE emp_id = ? AND rounded = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $emp_id, $rounded);
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'ลบผู้ใช้สำเร็จ'));
    } else {
        sendErrorResponse('Failed to delete user: ' . $conn->error, 500);
    }
}

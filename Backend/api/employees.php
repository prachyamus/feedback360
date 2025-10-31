<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    getAllEmployees($conn);
} elseif ($method === 'POST') {
    handlePost($conn);
} else {
    sendErrorResponse('Method not allowed. Only GET and POST are supported.', 405);
}

function getAllEmployees($conn) {
    if (!$conn) {
        sendErrorResponse('Database connection failed', 500);
    }
    
    $sql = "SELECT * FROM user_list ORDER BY emp_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $employees = array();
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    sendJsonResponse($employees);
}

function handlePost($conn) {
    if (!$conn) {
        sendErrorResponse('Database connection failed', 500);
    }
    
    $data = getPostData();
    $action = isset($data['action']) ? $data['action'] : 'create';
    
    switch ($action) {
        case 'create':
            createEmployee($data, $conn);
            break;
        
        case 'update':
            updateEmployee($data, $conn);
            break;
        
        case 'delete':
            deleteEmployee($data, $conn);
            break;
        
        case 'fetch':
            fetchEmployee($data, $conn);
            break;
        
        default:
            sendErrorResponse('Invalid action. Supported actions: create, update, delete, fetch');
    }
}

function createEmployee($data, $conn) {
    validateRequiredFields($data, array('emp_id', 'emp_fname', 'emp_lname'));
    
    $emp_id = sanitizeInput($data['emp_id']);
    $emp_fname = sanitizeInput($data['emp_fname']);
    $emp_lname = sanitizeInput($data['emp_lname']);
    $emp_nickname = sanitizeInput(isset($data['emp_nickname']) ? $data['emp_nickname'] : '');
    $emp_div = sanitizeInput(isset($data['emp_div']) ? $data['emp_div'] : '');
    $emp_dep = sanitizeInput(isset($data['emp_dep']) ? $data['emp_dep'] : '');
    $emp_position = sanitizeInput(isset($data['emp_position']) ? $data['emp_position'] : '');
    $emp_level = sanitizeInput(isset($data['emp_level']) ? $data['emp_level'] : '');
    $rounded = sanitizeInput(isset($data['rounded']) ? $data['rounded'] : '5');
    
    $sql = "INSERT INTO user_list (emp_id, emp_fname, emp_lname, emp_nickname, emp_div, emp_dep, emp_position, emp_level, rounded) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $emp_id, $emp_fname, $emp_lname, $emp_nickname, $emp_div, $emp_dep, $emp_position, $emp_level, $rounded);
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'เพิ่มพนักงานสำเร็จ', 'id' => $conn->insert_id), 201);
    } else {
        sendErrorResponse('Failed to create employee: ' . $conn->error, 500);
    }
}

function updateEmployee($data, $conn) {
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
    
    $sql = "UPDATE user_list SET 
            emp_fname = ?, emp_lname = ?, emp_nickname = ?, emp_div = ?, 
            emp_dep = ?, emp_position = ?, emp_level = ?
            WHERE emp_id = ? AND rounded = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $emp_fname, $emp_lname, $emp_nickname, $emp_div, $emp_dep, $emp_position, $emp_level, $emp_id, $rounded);
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'อัปเดตพนักงานสำเร็จ'));
    } else {
        sendErrorResponse('Failed to update employee: ' . $conn->error, 500);
    }
}

function deleteEmployee($data, $conn) {
    validateRequiredFields($data, array('emp_id', 'rounded'));
    
    $emp_id = sanitizeInput($data['emp_id']);
    $rounded = sanitizeInput($data['rounded']);
    
    $sql = "DELETE FROM user_list WHERE emp_id = ? AND rounded = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $emp_id, $rounded);
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'ลบพนักงานสำเร็จ'));
    } else {
        sendErrorResponse('Failed to delete employee: ' . $conn->error, 500);
    }
}

function fetchEmployee($data, $conn) {
    validateRequiredFields($data, array('emp_id', 'rounded'));
    
    $emp_id = sanitizeInput($data['emp_id']);
    $rounded = sanitizeInput($data['rounded']);
    
    $sql = "SELECT * FROM user_list WHERE emp_id = ? AND rounded = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $emp_id, $rounded);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        sendJsonResponse($row);
    } else {
        sendErrorResponse('Employee not found', 404);
    }
}
?>
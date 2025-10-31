<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    getAllRounds($conn);
} elseif ($method === 'POST') {
    handlePost($conn);
} else {
    sendErrorResponse('Method not allowed. Only GET and POST are supported.', 405);
}

function getAllRounds($conn) {
    if (!$conn) {
        sendErrorResponse('Database connection failed', 500);
    }
    
    $sql = "SELECT * FROM rounds ORDER BY r_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rounds = array();
    while ($row = $result->fetch_assoc()) {
        $rounds[] = $row;
    }
    
    sendJsonResponse($rounds);
}

function handlePost($conn) {
    if (!$conn) {
        sendErrorResponse('Database connection failed', 500);
    }
    
    $data = getPostData();
    $action = isset($data['action']) ? $data['action'] : '';
    
    switch ($action) {
        case 'list':
            getAllRounds($conn);
            break;
        
        case 'fetch':
            fetchRound($data, $conn);
            break;
        
        case 'save':
            saveRound($data, $conn);
            break;
        
        case 'delete':
            deleteRound($data, $conn);
            break;
        
        default:
            sendErrorResponse('Invalid action. Supported actions: list, fetch, save, delete');
    }
}

function fetchRound($data, $conn) {
    validateRequiredFields($data, array('id'));
    
    $id = sanitizeInput($data['id']);
    
    $sql = "SELECT * FROM rounds WHERE r_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        sendJsonResponse($row);
    } else {
        sendErrorResponse('Round not found', 404);
    }
}

function saveRound($data, $conn) {
    $r_id = $data['r_id'];
    $r_title = $data['r_title'];
    $r_start = $data['r_start'];
    $r_end = $data['r_end'];
    $r_status = $data['r_status'];

    if (empty($r_id)) {
        $sql = "INSERT INTO rounds (r_title, r_start, r_end, r_status) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $r_title, $r_start, $r_end, $r_status);
        
        if ($stmt->execute()) {
            sendJsonResponse(array('message' => 'เพิ่มรอบการประเมินสำเร็จ', 'id' => $conn->insert_id), 201);
        } else {
            sendErrorResponse('Failed to create round: ' . $conn->error, 500);
        }
    } else {
        $sql = "UPDATE rounds SET 
                r_title = ?,
                r_start = ?,
                r_end = ?,
                r_status = ?,
                update_date = NOW()
                WHERE r_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $r_title, $r_start, $r_end, $r_status, $r_id);
        
        if ($stmt->execute()) {
            sendJsonResponse(array('message' => 'อัปเดตรอบการประเมินสำเร็จ'));
        } else {
            sendErrorResponse('Failed to update round: ' . $conn->error, 500);
        }
    }
}

function deleteRound($data, $conn) {
    validateRequiredFields($data, array('r_id'));
    
    $r_id = sanitizeInput($data['r_id']);
    
    $sql = "DELETE FROM rounds WHERE r_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $r_id);
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'ลบรอบการประเมินสำเร็จ'));
    } else {
        sendErrorResponse('Failed to delete round: ' . $conn->error, 500);
    }
}
?>


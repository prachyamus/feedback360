<?php

include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

function handleFetchAll($group = '') {
    global $conn;
    
    if ($group !== '') {
        $sql = "SELECT * FROM question_group WHERE que_group = ? ORDER BY que_group ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $group);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT * FROM question_group ORDER BY que_group ASC";
        $result = $conn->query($sql);
    }
    
    $groups = [];
    while ($row = $result->fetch_assoc()) {
        $groups[] = $row;
    }
    
    return ['success' => true, 'data' => $groups];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $group = isset($_GET['group']) ? $_GET['group'] : '';
    $res = handleFetchAll($group);
} else {
    $res = ['success' => false, 'message' => 'Only GET method is supported'];
}

echo json_encode($res);
?>

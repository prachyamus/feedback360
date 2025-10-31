<?php

include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

function handleFetchAll() {
    global $conn;
    
    $sql = "SELECT * FROM s_band ORDER BY ban_id ASC";
    $result = $conn->query($sql);
    
    $bands = [];
    while ($row = $result->fetch_assoc()) {
        $bands[] = $row;
    }
    
    return ['success' => true, 'data' => $bands];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = handleFetchAll();
} else {
    $res = ['success' => false, 'message' => 'Only GET method is supported'];
}

echo json_encode($res);
?>

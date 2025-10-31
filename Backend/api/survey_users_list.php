<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendErrorResponse('Method not allowed. Only GET is supported.', 405);
}

if (!$conn) {
    sendErrorResponse('Database connection failed', 500);
}

$rounded = isset($_GET['rounded']) ? $_GET['rounded'] : '5';
$users = getAllUsers($conn, $rounded);
sendJsonResponse($users);

function getAllUsers($conn, $rounded) {
    $sql = "SELECT u_id,emp_id,emp_fname,emp_lname,emp_nickname,emp_div,emp_dep,emp_position,emp_level FROM user_list WHERE rounded = ? ORDER BY emp_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rounded);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}


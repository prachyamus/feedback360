<?php
include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

$res = getAllUsers($conn);
sendJsonResponse($res);

function getAllUsers($conn){
    $sql = "SELECT u_id,emp_id,emp_fname,emp_lname,emp_nickname,emp_div,emp_dep,emp_position,emp_level FROM user_list WHERE 1 Group By emp_id ORDER BY emp_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);   
}

<?php

include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

if(isset($_GET['emp_id'])) {
    $emp_id = $_GET['emp_id'];
    $res = get_data($emp_id);
    sendJsonResponse($res);
}

function get_data($person_id){
    global $conn;
    $sql = "SELECT *, s_answer.sub_id FROM s_answer JOIN s_subject ON s_answer.sub_id = s_subject.sub_id WHERE s_answer.person_id = ? GROUP BY s_answer.sub_id ORDER BY s_subject.sub_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s",$person_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows>0){
        $rows = $result->fetch_all(MYSQLI_ASSOC);
    }
    return $rows;
}
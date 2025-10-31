<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

// Expect POST: em_id, rounded
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed. Only POST is supported.', 405);
}

$data = getPostData();
validateRequiredFields($data, array('em_id'));

$em_id = sanitizeInput($data['em_id']);
$rounded = sanitizeInput(isset($data['rounded']) ? $data['rounded'] : '5');

if (!$conn) {
    sendErrorResponse('Database connection failed', 500);
}

// Answers map for pass flag
function get_answers($conn, $em_id, $rounded) {
    $sql = "SELECT person_id FROM s_answer WHERE who_id = ? AND rounded = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $em_id, $rounded);
    $stmt->execute();
    $result = $stmt->get_result();
    $answers = array();
    while ($row = $result->fetch_assoc()) {
        $answers[$row['person_id']] = 1;
    }
    return $answers;
}

function important($conn, $em_id, $rounded, $answers) {
    $sql = "SELECT *, (CASE WHEN B.person_id IS NOT NULL THEN 1 ELSE 0 END) AS pass 
            FROM (SELECT * FROM user_list WHERE rounded = ? HAVING ? IN (emp_id, part1, part2, part3, part4, part5, part6, part7, part8, part9, part10)) A 
            LEFT JOIN (SELECT person_id, who_id FROM s_answer WHERE who_id = ? AND rounded = ?) B 
            ON A.emp_id = B.person_id 
            GROUP BY A.emp_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $rounded, $em_id, $em_id, $rounded);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = array();
    while ($row = $result->fetch_assoc()) {
        $row['pass'] = isset($answers[$row['emp_id']]) ? 1 : 0;
        $res[] = $row;
    }
    return $res;
}

function otherlist($conn, $em_id, $rounded, $answers) {
    $sql = "SELECT *, (CASE WHEN B.person_id IS NOT NULL THEN 1 ELSE 0 END) AS pass 
            FROM (SELECT * FROM user_list WHERE rounded = ? HAVING ? NOT IN (emp_id, part1, part2, part3, part4, part5, part6, part7, part8, part9, part10)) A 
            LEFT JOIN (SELECT person_id, who_id FROM s_answer WHERE who_id = ? AND rounded = ?) B 
            ON A.emp_id = B.person_id 
            GROUP BY A.emp_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $rounded, $em_id, $em_id, $rounded);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = array();
    while ($row = $result->fetch_assoc()) {
        $row['pass'] = isset($answers[$row['emp_id']]) ? 1 : 0;
        $res[] = $row;
    }
    return $res;
}

$answers = get_answers($conn, $em_id, $rounded);

$res = array();
$res['important'] = important($conn, $em_id, $rounded, $answers);
$res['otherlist'] = otherlist($conn, $em_id, $rounded, $answers);

sendJsonResponse($res);
?>



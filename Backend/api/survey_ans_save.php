<?php

include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$sub_id = isset($_POST['sub_id']) ? $_POST['sub_id'] : '';
$person_id = isset($_POST['person_id']) ? $_POST['person_id'] : '';
$who_id = isset($_POST['who_id']) ? $_POST['who_id'] : '';
$whotype_id = isset($_POST['whotype_id']) ? $_POST['whotype_id'] : '';
$rounded = isset($_POST['rounded']) ? $_POST['rounded'] : '';

if (!$sub_id || !$person_id || !$who_id || !$rounded) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields: sub_id, person_id, who_id, rounded']);
    exit;
}

try {
    $res = saveSurveyAnswers($sub_id, $person_id, $who_id, $whotype_id, $rounded);
    echo json_encode(['success' => true, 'message' => 'บันทึกคำตอบสำเร็จ', 'data' => $res]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to save survey answers: ' . $e->getMessage()]);
}

function saveSurveyAnswers($sub_id, $person_id, $who_id, $whotype_id, $rounded) {
    global $conn;
    $save_time = date('d/m/Y H:i:s');
    
    $sql = "SELECT que_id FROM s_question WHERE sub_id = ? ORDER BY que_num ASC, que_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sub_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $saved_count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $que_id = $row['que_id'];
        
        $answer_save = '';
        
        if (isset($_POST['answers']) && is_array($_POST['answers']) && isset($_POST['answers'][$que_id])) {
            $answer_save = $_POST['answers'][$que_id];
        }
        
        if ($answer_save !== '') {
            save_answer($sub_id, $que_id, $person_id, $who_id, $whotype_id, $answer_save, $save_time, $rounded);
            $saved_count++;
        }
    }
    
    $stmt->close();
    return ['saved_count' => $saved_count];
}

function save_answer($sub_id, $que_id, $person_id, $who_id, $whotype_id, $answer_save, $save_time, $rounded) {
    global $conn;
    $sql = "INSERT INTO s_answer(sub_id, que_id, person_id, who_id, whotype_id, answer_save, save_time, rounded) 
            VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $sub_id, $que_id, $person_id, $who_id, $whotype_id, $answer_save, $save_time, $rounded);
    $stmt->execute();
    $stmt->close();
}


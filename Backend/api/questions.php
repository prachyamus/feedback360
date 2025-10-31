<?php

include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

function listQuestions($id, $group) {
    global $conn;
    $sql = "SELECT * FROM `s_question` LEFT JOIN question_group 
                                ON s_question.que_group =question_group.que_group
                                WHERE s_question.sub_id= ? AND question_group.group_report= ?
                                GROUP BY s_question.que_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id, $group);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    
    if ($rows) {
        return ['success' => true, 'data' => $rows];
    } else {
        return ['success' => false, 'message' => 'Question not found'];
    }
}

// function handleFetchAll($subject_id = '') {
//     global $conn;
    
//     if ($subject_id !== '') {
//         $sql = "SELECT q.*, g.group_name 
//                 FROM s_question q 
//                 LEFT JOIN question_group g ON q.que_group = g.que_group 
//                 WHERE q.sub_id = ? 
//                 ORDER BY q.que_num ASC, q.que_id ASC";
//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param('i', $subject_id);
//         $stmt->execute();
//         $result = $stmt->get_result();
//     } else {
//         $sql = "SELECT q.*, 
//                        g.group_name,
//                        s.sub_title 
//                 FROM s_question q 
//                 LEFT JOIN question_group g ON q.que_group = g.que_group 
//                 LEFT JOIN s_subject s ON q.sub_id = s.sub_id
//                 ORDER BY q.que_num ASC, q.que_id ASC";
//         $result = $conn->query($sql);
//     }
    
//     $questions = [];
//     while ($row = $result->fetch_assoc()) {
//         $questions[] = $row;
//     }
    
//     return ['success' => true, 'data' => $questions];
// }

// function handleSave($data) {
//     global $conn;
    
//     $que_id = isset($data['que_id']) ? $data['que_id'] : '';
//     $sub_id = isset($data['sub_id']) ? $data['sub_id'] : '';
//     $que_type = isset($data['que_type']) ? $data['que_type'] : '';
//     $que_group = isset($data['que_group']) ? $data['que_group'] : '';
//     $que_num = isset($data['que_num']) ? $data['que_num'] : '';
//     $que_title = isset($data['que_title']) ? $data['que_title'] : '';
//     $que_title_m = isset($data['que_title_m']) ? $data['que_title_m'] : '';
//     $div_type = isset($data['div_type']) ? $data['div_type'] : '';
//     $que_dna = isset($data['que_dna']) ? $data['que_dna'] : '';
    
//     if ($que_id === '') {
//         $sql = "INSERT INTO s_question (sub_id, que_type, que_group, que_num, que_title, que_title_m, div_type, que_dna) 
//                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param('iiiissss', $sub_id, $que_type, $que_group, $que_num, $que_title, $que_title_m, $div_type, $que_dna);
        
//         if ($stmt->execute()) {
//             return ['success' => true, 'message' => 'สร้างคำถามสำเร็จ', 'id' => $conn->insert_id];
//         } else {
//             return ['success' => false, 'message' => $stmt->error];
//         }
//     } else {
//         $sql = "UPDATE s_question SET 
//                 sub_id = ?, 
//                 que_type = ?, 
//                 que_group = ?, 
//                 que_num = ?, 
//                 que_title = ?, 
//                 que_title_m = ?, 
//                 div_type = ?, 
//                 que_dna = ? 
//                 WHERE que_id = ?";
        
//         $stmt = $conn->prepare($sql);
//         $stmt->bind_param('iiiissssi', $sub_id, $que_type, $que_group, $que_num, $que_title, $que_title_m, $div_type, $que_dna, $que_id);
        
//         if ($stmt->execute()) {
//             return ['success' => true, 'message' => 'อัปเดตคำถามสำเร็จ'];
//         } else {
//             return ['success' => false, 'message' => $stmt->error];
//         }
//     }
// }

// function handleDelete($question_id) {
//     global $conn;
    
//     if ($question_id === '') {
//         return ['success' => false, 'message' => 'missing question_id'];
//     }
    
//     $sql = "DELETE FROM s_question WHERE que_id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param('i', $question_id);
    
//     if ($stmt->execute()) {
//         return ['success' => true, 'message' => 'ลบคำถามสำเร็จ'];
//     } else {
//         return ['success' => false, 'message' => $stmt->error];
//     }
// }

// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//     $subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : '';
//     $res = handleFetchAll($subject_id);
// } else {
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} else {
    echo json_encode(['success' => false, 'message' => 'Action is required']);
    exit;
}

    switch ($action) {
        case 'fetch':
            $res = listQuestions($_POST['id'], $_POST['group']);
            break;
        case 'create':
            $res = handleSave($_POST);
            break;
        case 'update':
            $res = handleSave($_POST);
            break;
        case 'delete':
            $res = handleDelete($_POST['question_id']);
            break;
        default:
            $res = ['success' => false, 'message' => 'unknown action'];
    }
echo json_encode($res);


<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    getSurveyAnswers($conn);
} elseif ($method === 'POST') {
    handlePost($conn);
} else {
    sendErrorResponse('Method not allowed. Only GET and POST are supported.', 405);
}

function getSurveyAnswers($conn) {
    $emp_id = isset($_GET['emp_id']) ? $_GET['emp_id'] : '';
    $rounded = isset($_GET['rounded']) ? $_GET['rounded'] : '5';
    $sub_id = isset($_GET['sub_id']) ? $_GET['sub_id'] : '';
    
    if ($emp_id && $sub_id) {
        $sql = "SELECT 
                    sa.*,
                    sq.q_title,
                    sq.q_type,
                    ss.sub_title
                FROM survey_answer sa
                JOIN s_question sq ON sa.q_id = sq.q_id
                JOIN s_subject ss ON sq.sub_id = ss.sub_id
                WHERE sa.emp_id = ? AND sa.rounded = ? AND sq.sub_id = ?
                ORDER BY sa.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $emp_id, $rounded, $sub_id);
    } elseif ($emp_id) {
        $sql = "SELECT 
                    sa.*,
                    sq.q_title,
                    sq.q_type,
                    ss.sub_title
                FROM survey_answer sa
                JOIN s_question sq ON sa.q_id = sq.q_id
                JOIN s_subject ss ON sq.sub_id = ss.sub_id
                WHERE sa.emp_id = ? AND sa.rounded = ?
                ORDER BY sa.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $emp_id, $rounded);
    } else {
        $sql = "SELECT 
                    sa.*,
                    sq.q_title,
                    sq.q_type,
                    ss.sub_title,
                    ul.emp_fname,
                    ul.emp_lname
                FROM survey_answer sa
                JOIN s_question sq ON sa.q_id = sq.q_id
                JOIN s_subject ss ON sq.sub_id = ss.sub_id
                JOIN user_list ul ON sa.emp_id = ul.emp_id AND sa.rounded = ul.rounded
                WHERE sa.rounded = ?
                ORDER BY sa.created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $rounded);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $answers = array();
    while ($row = $result->fetch_assoc()) {
        $answers[] = $row;
    }
    
    sendJsonResponse($answers);
}

function handlePost($conn) {
    $data = getPostData();
    $action = isset($data['action']) ? $data['action'] : '';
    
    switch ($action) {
        case 'save':
            saveSurveyAnswer($data, $conn);
            break;
        
        case 'bulk_save':
            saveBulkSurveyAnswers($data, $conn);
            break;
        
        case 'delete':
            deleteSurveyAnswer($data, $conn);
            break;
        
        default:
            sendErrorResponse('Invalid action. Supported actions: save, bulk_save, delete');
    }
}

function saveSurveyAnswer($data, $conn) {
    validateRequiredFields($data, array('emp_id', 'q_id', 'answer_score', 'rounded'));
    
    $emp_id = sanitizeInput($data['emp_id']);
    $q_id = sanitizeInput($data['q_id']);
    $answer_score = sanitizeInput($data['answer_score']);
    $answer_comment = sanitizeInput(isset($data['answer_comment']) ? $data['answer_comment'] : '');
    $rounded = sanitizeInput($data['rounded']);
    
    $sql = "INSERT INTO survey_answer (emp_id, q_id, answer_score, answer_comment, rounded, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
            answer_score = VALUES(answer_score),
            answer_comment = VALUES(answer_comment),
            updated_at = NOW()";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $emp_id, $q_id, $answer_score, $answer_comment, $rounded);
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'บันทึกคำตอบสำเร็จ'), 201);
    } else {
        sendErrorResponse('Failed to save answer: ' . $conn->error, 500);
    }
}

function saveBulkSurveyAnswers($data, $conn) {
    validateRequiredFields($data, array('emp_id', 'answers', 'rounded'));
    
    $emp_id = sanitizeInput($data['emp_id']);
    $rounded = sanitizeInput($data['rounded']);
    $answers = $data['answers'];
    
    if (!is_array($answers)) {
        sendErrorResponse('Answers must be an array');
    }
    
    $conn->begin_transaction();
    
    try {
        foreach ($answers as $answer) {
            $q_id = sanitizeInput($answer['q_id']);
            $answer_score = sanitizeInput($answer['answer_score']);
            $answer_comment = sanitizeInput($answer['answer_comment'] ?? '');
            
            $sql = "INSERT INTO survey_answer (emp_id, q_id, answer_score, answer_comment, rounded, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE 
                    answer_score = VALUES(answer_score),
                    answer_comment = VALUES(answer_comment),
                    updated_at = NOW()";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $emp_id, $q_id, $answer_score, $answer_comment, $rounded);
            $stmt->execute();
        }
        
        $conn->commit();
        sendJsonResponse(array('message' => 'บันทึกคำตอบทั้งหมดสำเร็จ'), 201);
    } catch (Exception $e) {
        $conn->rollback();
        sendErrorResponse('Failed to save answers: ' . $e->getMessage(), 500);
    }
}

function deleteSurveyAnswer($data, $conn) {
    validateRequiredFields($data, array('answer_id'));
    
    $answer_id = sanitizeInput($data['answer_id']);
    
    $sql = "DELETE FROM survey_answer WHERE answer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $answer_id);
    
    if ($stmt->execute()) {
        sendJsonResponse(array('message' => 'ลบคำตอบสำเร็จ'));
    } else {
        sendErrorResponse('Failed to delete answer: ' . $conn->error, 500);
    }
}

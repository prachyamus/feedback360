<?php

include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

function handleFetchOnce($id) {
    global $conn;
    $sql = "SELECT s.*, b.ban_title FROM s_subject s LEFT JOIN s_band b ON s.sub_band = b.ban_id WHERE s.sub_id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if ($row) {
        return ['success' => true, 'data' => $row];
    } else {
        return ['success' => false, 'message' => 'Survey not found'];
    }
}

function handleFetchAll($rounded = '') {
    global $conn;
    
    if ($rounded !== '') {
        $sql = "SELECT s.*, b.ban_title FROM s_subject s LEFT JOIN s_band b ON s.sub_band = b.ban_id WHERE s.rounded = ? ORDER BY s.sub_id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $rounded);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT s.*, b.ban_title FROM s_subject s LEFT JOIN s_band b ON s.sub_band = b.ban_id ORDER BY s.sub_id DESC";
        $result = $conn->query($sql);
    }
    
    $surveys = [];
    while ($row = $result->fetch_assoc()) {
        $surveys[] = $row;
    }
    
    return ['success' => true, 'data' => $surveys];
}

function handleSave($data) {
    global $conn;
    
    $sub_id = $data['sub_id'];
    $sub_title = $data['sub_title'];
    $sub_title_m = $data['sub_title_m'];
    $sub_discrip = $data['sub_discrip'];
    $sub_discrip_m = $data['sub_discrip_m'];
    $sub_band = $data['sub_band'];
    $type_group = $data['type_group'];
    $sub_status = $data['sub_status'];
    $rounded = $data['rounded'];
    $sumary_chart = $data['sumary_chart'];
    $sumary_group = $data['sumary_group'];
    $sumary_question = $data['sumary_question'];
    
    if ($sub_id === '') {
        $sql = "INSERT INTO s_subject (sub_title, sub_title_m, sub_discrip, sub_discrip_m, sub_band, type_group, sub_status, rounded, sumary_chart, sumary_group, sumary_question) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssssssss', $sub_title, $sub_title_m, $sub_discrip, $sub_discrip_m, $sub_band, $type_group, $sub_status, $rounded, $sumary_chart, $sumary_group, $sumary_question);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'เพิ่มแบบสำรวจสำเร็จ', 'id' => $conn->insert_id];
        } else {
            return ['success' => false, 'message' => $stmt->error];
        }
    } else {
        $sql = "UPDATE s_subject SET 
                sub_title = ?,
                sub_title_m = ?,
                sub_discrip = ?,
                sub_discrip_m = ?,
                sub_band = ?,
                type_group = ?,
                sub_status = ?,
                sumary_chart = ?,
                sumary_group = ?,
                sumary_question = ?
                WHERE sub_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssssssi', $sub_title, $sub_title_m, $sub_discrip, $sub_discrip_m, $sub_band, $type_group, $sub_status, $sumary_chart, $sumary_group, $sumary_question, $sub_id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'อัปเดตแบบสำรวจสำเร็จ'];
        } else {
            return ['success' => false, 'message' => $stmt->error];
        }
    }
}

function handleDelete($sub_id) {
    global $conn;
    
    if ($sub_id === '') {
        return ['success' => false, 'message' => 'missing sub_id'];
    }
    
    $sql = "DELETE FROM s_subject WHERE sub_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $sub_id);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'ลบแบบสำรวจสำเร็จ'];
    } else {
        return ['success' => false, 'message' => $stmt->error];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = handleFetchAll('');
} else {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Action is required']);
        exit;
    }

    switch ($action) {
        case 'fetch':
            $res = handleFetchOnce($_POST['id']);
            break;
        case 'list':
            $rounded = isset($_POST['rounded']) ? $_POST['rounded'] : '';
            $res = handleFetchAll($rounded);
            break;
        case 'save':
            $res = handleSave($_POST);
            break;
        case 'delete':
            $res = handleDelete($_POST['sub_id']);
            break;
        default:
            $res = ['success' => false, 'message' => 'unknown action'];
    }
}

echo json_encode($res);
?>

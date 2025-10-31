<?php

include '../config/database.php';
include '../config/cors.php';
include '../utils/helpers.php';

function handleFetchOnce($id) {
    global $conn;
    $sql = "SELECT * FROM admin_user WHERE id = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $erp_data = getERPUser($row['emp_id']);
    $row['fullname'] = $erp_data['LocalFirstName'] . ' ' . $erp_data['LocalLastName'];
    $stmt->close();
    return ['success' => true, 'data' => $row];
}

function handleFetchAll() {
    global $conn;
    $sql = "SELECT id, emp_id, emp_permission FROM admin_user ORDER BY id DESC";
    $query = $conn->query($sql);
    $res = [];
    while ($row = $query->fetch_assoc()) {
        $erp_data = getERPUser($row['emp_id']);
        $row['fullname'] = $erp_data['LocalFirstName'] . ' ' . $erp_data['LocalLastName'];
        $res[] = $row;
    }
    return ['success' => true, 'data' => $res];
}

function handleSave($id, $emp_id, $emp_permission) {
    global $conn;
    if ($id === '') {
        $sql = "INSERT INTO admin_user (emp_id, emp_permission) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $emp_id, $emp_permission);
    } else {
        $sql = "UPDATE admin_user SET emp_permission = ? WHERE emp_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $emp_permission, $emp_id);
    }
    return ['success' => $stmt->execute(), 'message' => $stmt->error];
}

function handleDelete($id) {
    global $conn;
    if ($id === '') { return ['success' => false, 'message' => 'missing id']; }
    $sql = "DELETE FROM admin_user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    return ['success' => $stmt->execute(), 'message' => $stmt->error];
}



if(isset($_POST['action'])) { 
    $action = $_POST['action']; 
}else{
    sendErrorResponse('Action is required');
}
switch ($action) {
    case 'fetch':
        $res = handleFetchOnce($_POST['id']);
        break;
    case 'list':
        $res = handleFetchAll();
        break;
    case 'save':
        $res = handleSave($_POST['id'], $_POST['emp_id'], $_POST['emp_permission']);
        break;
    case 'delete':
        $res = handleDelete($_POST['id']);
        break;
    default:
        $res = ['success' => false, 'message' => 'unknown action'];
}

echo json_encode($res);

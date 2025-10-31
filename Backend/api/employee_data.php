<?php
include '../config/cors.php';
include '../config/database.php';
include '../utils/helpers.php';

if(isset($_GET['emp_id'])) {
    $emp_id = $_GET['emp_id'];
    $erp_data = getERPUser($emp_id);
    echo json_encode($erp_data);
}
function getERPData($emp_id) {
    $erp_data = getERPUser($emp_id);
    return $erp_data;
}
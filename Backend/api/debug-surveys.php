<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendErrorResponse('Method not allowed. Only GET is supported.', 405);
}

// Check database connection
if (!$conn) {
    sendErrorResponse('Database connection failed', 500);
}

// Check if tables exist
$tables = ['s_subject', 's_band'];
$tableStatus = [];

foreach ($tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($sql);
    $tableStatus[$table] = $result && $result->num_rows > 0;
}

// Try to get data from s_subject table
$subjectData = [];
if ($tableStatus['s_subject']) {
    $sql = "SELECT COUNT(*) as count FROM s_subject";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $subjectData['count'] = $row['count'];
    }
}

// Try to get data from s_band table
$bandData = [];
if ($tableStatus['s_band']) {
    $sql = "SELECT COUNT(*) as count FROM s_band";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $bandData['count'] = $row['count'];
    }
}

// Try the actual query from surveys.php
$queryResult = null;
$queryError = null;
if ($tableStatus['s_subject'] && $tableStatus['s_band']) {
    try {
        $sql = "SELECT s.*, b.ban_title FROM s_subject s LEFT JOIN s_band b ON s.sub_band = b.ban_id ORDER BY s.sub_id DESC LIMIT 5";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $queryResult = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $queryError = $conn->error;
        }
    } catch (Exception $e) {
        $queryError = $e->getMessage();
    }
}

// If we have query results, return them in the expected format
if ($queryResult !== null) {
    sendJsonResponse($queryResult);
} else {
    // If no data or error, return debug info
    sendJsonResponse([
        'success' => false,
        'debug' => [
            'database_connected' => true,
            'tables_exist' => $tableStatus,
            'subject_data' => $subjectData,
            'band_data' => $bandData,
            'query_error' => $queryError,
            'php_version' => PHP_VERSION,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
}
?>

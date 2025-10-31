<?php
include_once '../config/cors.php';
include_once '../config/database.php';
include_once '../utils/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    getApiInfo();
} else {
    sendErrorResponse('Method not allowed. Only GET is supported.', 405);
}

function getApiInfo() {
    $apiInfo = [
        'name' => '360 Feedback API',
        'version' => '1.0.0',
        'description' => 'REST API for 360 Feedback System',
        'endpoints' => [
            'auth' => [
                'GET /api/auth' => 'Check authentication status',
                'POST /api/auth' => 'Login, logout, or register (action: login/logout/register)'
            ],
            'users' => [
                'GET /api/users' => 'Get all users (rounded parameter)',
                'POST /api/users' => 'Create, update, or delete users (action: create/update/delete)'
            ],
            'surveys' => [
                'GET /api/surveys' => 'Get all surveys (rounded parameter)',
                'POST /api/surveys' => 'Create, update, or delete surveys (action: save/delete)'
            ],
            'questions' => [
                'GET /api/questions' => 'Get all questions (sub_id parameter)',
                'POST /api/questions' => 'Create, update, or delete questions (action: save/delete)'
            ],
            'employees' => [
                'GET /api/employees' => 'Get all employees (rounded, search parameters)',
                'POST /api/employees' => 'Create, update, or delete employees (action: save/delete)'
            ],
            'reports' => [
                'GET /api/reports' => 'Get reports (type: summary/user/subject/group)',
                'POST /api/reports' => 'Export reports (action: export)'
            ],
            'survey-answers' => [
                'GET /api/survey-answers' => 'Get survey answers (emp_id, rounded, sub_id parameters)',
                'POST /api/survey-answers' => 'Save survey answers (action: save/bulk_save/delete)'
            ],
            'admin' => [
                'GET /api/admin' => 'Get admin data (action: dashboard/users/statistics)',
                'POST /api/admin' => 'Admin operations (action: update_user_status/bulk_operations)'
            ]
        ],
        'authentication' => [
            'type' => 'Cookie-based or Authorization header',
            'token_format' => 'Base64 encoded user token'
        ],
        'cors' => [
            'enabled' => true,
            'allowed_origins' => ['http://localhost:3000', 'http://localhost:3001'],
            'allowed_methods' => ['GET', 'POST', 'OPTIONS']
        ]
    ];
    
    sendJsonResponse($apiInfo);
}

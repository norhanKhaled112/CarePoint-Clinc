<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // غيّري اسم المستخدم
define('DB_PASS', '');            // غيّري كلمة المرور
define('DB_NAME', 'carepointclinc');

// MySQL Connection 
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// connection check 
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

//  encoding Arabic, characters 
$conn->set_charset('utf8mb4');
?>
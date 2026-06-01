<?php
    define('DB_HOST', 'sql211.infinityfree.com');
    define('DB_USER', 'if0_42065544');       
    define('DB_PASS', 'BaCoDevDerker20'); 
    define('DB_NAME', 'if0_42065544_bakeco_db');

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }
?>
<?php
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    require __DIR__ . '/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        exit;
    }

    // Get and sanitize inputs
$name     = trim($_POST['name']     ?? '');
$phone    = trim($_POST['number']   ?? '');
$address  = trim($_POST['address']  ?? '');
$landmark = trim($_POST['landmark'] ?? '');
$order    = trim($_POST['message']  ?? '');

// Basic validation
$errors = [];
if ($name === '')    $errors[] = 'Name is required.';
if ($phone === '')   $errors[] = 'Phone number is required.';
if ($address === '') $errors[] = 'Address is required.';
if ($order === '')   $errors[] = 'Order is required.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Insert into database
$stmt = $conn->prepare(
    'INSERT INTO orders (name, phone, address, landmark, `order`) VALUES (?, ?, ?, ?, ?)'
);
$stmt->bind_param('sssss', $name, $phone, $address, $landmark, $order);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order submitted successfully!']);
} else {
     http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save order. Please try again.']);
}

    $stmt->close();
    $conn->close();
?>
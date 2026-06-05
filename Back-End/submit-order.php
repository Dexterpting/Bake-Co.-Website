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
    // Send email notification
    try {
        require __DIR__ . '/PHPMailer/src/Exception.php';
        require __DIR__ . '/PHPMailer/src/PHPMailer.php';
        require __DIR__ . '/PHPMailer/src/SMTP.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bakeco26@gmail.com';    // your Gmail
        $mail->Password   = 'your_app_password_here'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Email content
        $mail->setFrom('bakeco26@gmail.com', 'Bake & Co. Website');
        $mail->addAddress('bakeco26@gmail.com');     // where to receive notifications
        $mail->Subject = 'New Order Received — Bake & Co.';
        $mail->isHTML(true);
        $mail->Body = "
            <h2 style='color:#c9933d;'>New Order Received!</h2>
            <table style='border-collapse:collapse;width:100%;'>
                <tr><td style='padding:8px;font-weight:bold;'>Name</td><td style='padding:8px;'>{$name}</td></tr>
                <tr style='background:#f9f9f9;'><td style='padding:8px;font-weight:bold;'>Phone</td><td style='padding:8px;'>{$phone}</td></tr>
                <tr><td style='padding:8px;font-weight:bold;'>Address</td><td style='padding:8px;'>{$address}</td></tr>
                <tr style='background:#f9f9f9;'><td style='padding:8px;font-weight:bold;'>Landmark</td><td style='padding:8px;'>{$landmark}</td></tr>
                <tr><td style='padding:8px;font-weight:bold;'>Order</td><td style='padding:8px;'>{$order}</td></tr>
            </table>
            <p style='margin-top:16px;color:#7b6553;font-size:.85rem;'>View all orders at your <a href='https://bake-co.infinityfree.me/Back-End/admin.php'>admin panel</a>.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        // Email failed but order still saved — don't block the response
    }

    echo json_encode(['success' => true, 'message' => 'Order submitted successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save order. Please try again.']);
}

    $stmt->close();
    $conn->close();
?>
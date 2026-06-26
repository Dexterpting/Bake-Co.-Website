<?php

    require __DIR__ . '/config.php';

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');

    require __DIR__ . '/db.php';
    require __DIR__ . '/rate-limiter.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
        exit;
    }

    // Rate limit: max 3 order submissions per 10 minutes per IP
    $rateCheck = checkRateLimit($conn, 'submit_order', 3, 600);

    if (!$rateCheck['allowed']) {
        http_response_code(429);
        $minutes = ceil($rateCheck['retry_after'] / 60);
        echo json_encode([
            'success' => false,
            'message' => "Too many orders submitted. Please try again in {$minutes} minute(s)."
        ]);
        exit;
    }

    // Get and sanitize inputs
$name     = trim($_POST['name']     ?? '');
$phone    = trim($_POST['number']   ?? '');
$address  = trim($_POST['address']  ?? '');
$landmark = trim($_POST['landmark'] ?? '');
$delivery_date = trim($_POST['delivery_date'] ?? '');

// Build order summary from product and quantity arrays
$products  = $_POST['product']  ?? [];
$quantities = $_POST['quantity'] ?? [];
$orderLines = [];
$total_amount = 0;
$total_qty    = 0;
$unitLines = [];

foreach ($products as $i => $product) {
    $product  = trim($product);
    $qty      = (int) ($quantities[$i] ?? 1);
    if ($product !== '') {
        $orderLines[]  = "x{$qty} {$product}";
        $total_qty    += $qty;

        // Look up price AND unit from products table
        $safe_product = $conn->real_escape_string($product);
        $price_result = $conn->query("SELECT price, unit FROM products WHERE name = '$safe_product' LIMIT 1");
        if ($price_result && $price_result->num_rows > 0) {
            $prod_data      = $price_result->fetch_assoc();
            $price          = (float) $prod_data['price'];
            $total_amount  += $price * $qty;
            $unitLines[]    = $prod_data['unit'];
        } else {
            $unitLines[] = 'per box';
        }
    }
}

$order = implode(', ', $orderLines);
$order_units = implode(', ', $unitLines);

$errors = [];
if ($name === '')         $errors[] = 'Name is required.';
if ($phone === '')        $errors[] = 'Phone number is required.';
if ($address === '')      $errors[] = 'Address is required.';
if (empty($orderLines))   $errors[] = 'At least one product is required.';
if ($delivery_date === '') $errors[] = 'Delivery date is required.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Insert into database
$stmt = $conn->prepare(
    'INSERT INTO orders (name, phone, address, landmark, delivery_date, order_items, order_units, total_amount, total_qty) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->bind_param('sssssssdi', $name, $phone, $address, $landmark, $delivery_date, $order, $order_units, $total_amount, $total_qty);

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
        $mail->Username   = MAIL_USER;   // your Gmail
        $mail->Password   = MAIL_PASS;   // Gmail App Password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email content
        $mail->setFrom(MAIL_USER, 'Bake & Co. Website'); //change to actual bake&co email address
        $mail->addAddress(MAIL_TO);                    // where to receive notifications
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
                <tr style='background:#f9f9f9;'><td style='padding:8px;font-weight:bold;'>Delivery Date</td><td style='padding:8px;'>{$delivery_date}</td></tr>
            </table>
            <p style='margin-top:16px;color:#7b6553;font-size:.85rem;'>View all orders at your <a href='https://bake-co.infinityfree.me/Back-End/admin.php'>admin panel</a>.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        // Log the actual error so we can see it
        error_log('PHPMailer Error: ' . $e->getMessage());
        // Also store it in the response temporarily for debugging
        file_put_contents(__DIR__ . '/mail_error.log', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . "\n", FILE_APPEND);
    }

    echo json_encode(['success' => true, 'message' => 'Order submitted successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save order. Please try again.']);
}

    $stmt->close();
    $conn->close();
?>
<?php
header("Access-Control-Allow-Origin: https://bake-co.vercel.app");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require __DIR__ . '/db.php';

$result = $conn->query('SELECT name, description, category, price, unit, image FROM products ORDER BY category, id ASC');
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);
$conn->close();
?>
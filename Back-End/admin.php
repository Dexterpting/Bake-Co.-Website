<?php
session_start();
$admin_password = 'bakeco2024';

define('BASE_URL', 'https://bake-co.infinityfree.me/Back-End/');
define('ASSET_URL', 'https://bake-co.infinityfree.me/Front-End/');

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . BASE_URL . 'admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin'] = true;
        header('Location: ' . BASE_URL . 'admin.php');
        exit;
    } else {
        $error = 'Wrong password.';
    }
}

if (!isset($_SESSION['admin'])) {
    $error = $error ?? null;
    include __DIR__ . '/../Front-End/pages/admin/login.html.php';
    exit;
}

require_once __DIR__ . '/db.php';

if (isset($_GET['api'])) {
    $result = $conn->query('SELECT * FROM orders ORDER BY submitted_at DESC');
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($orders);
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM orders WHERE id = $id");
    header('Location: ' . BASE_URL . 'admin.php?page=' . ($_GET['from'] ?? 'dashboard'));
exit;
}

$page = $_GET['page'] ?? 'dashboard';

if ($page === 'orders') {
    $result = $conn->query('SELECT * FROM orders ORDER BY submitted_at DESC');
    include __DIR__ . '/../Front-End/pages/admin/orders.html.php';
} else {
    $total  = $conn->query('SELECT COUNT(*) as count FROM orders')->fetch_assoc()['count'];
    $recent = $conn->query('SELECT * FROM orders ORDER BY submitted_at DESC LIMIT 5');
    include __DIR__ . '/../Front-End/pages/admin/dashboard.html.php';
}
?>
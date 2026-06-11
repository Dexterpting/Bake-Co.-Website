<?php
session_start();

require_once __DIR__ . '/config.php';
$admin_password = ADMIN_PASS;

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

// API endpoint
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

// Delete order
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM orders WHERE id = $id");
    header('Location: ' . BASE_URL . 'admin.php?page=' . ($_GET['from'] ?? 'dashboard'));
    exit;
}

    // Delete product
if (isset($_GET['delete_product'])) {
    $id = (int) $_GET['delete_product'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: ' . BASE_URL . 'admin.php?page=products');
    exit;
}

// Add products
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name        = trim($_POST['prod_name']        ?? '');
    $description = trim($_POST['prod_description'] ?? '');
    $category    = trim($_POST['prod_category']    ?? '');
    $price       = (float) ($_POST['prod_price']   ?? 0);
    $unit        = trim($_POST['prod_unit']        ?? 'per box');
    $imageFilename = '';

    // Handle file upload
    if (isset($_FILES['prod_image']) && $_FILES['prod_image']['error'] === 0) {
        $allowed     = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize     = 5 * 1024 * 1024; // 5MB
        $fileType    = $_FILES['prod_image']['type'];
        $fileSize    = $_FILES['prod_image']['size'];
        $tmpName     = $_FILES['prod_image']['tmp_name'];
        $origName    = basename($_FILES['prod_image']['name']);
        $ext         = pathinfo($origName, PATHINFO_EXTENSION);
        $newName     = uniqid('product_') . '.' . $ext;
        $uploadPath  = '/home/vol9_4/infinityfree.com/if0_42065544/htdocs/Front-End/src/img/' . $newName;

        if (in_array($fileType, $allowed) && $fileSize <= $maxSize) {
            if (move_uploaded_file($tmpName, $uploadPath)) {
                $imageFilename = $newName;
            }
        }
    }

    if ($name && $description && $category && $imageFilename) {
    $stmt = $conn->prepare('INSERT INTO products (name, description, category, price, unit, image) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssdss', $name, $description, $category, $price, $unit, $imageFilename);
    $stmt->execute();
    $stmt->close();
}

    header('Location: ' . BASE_URL . 'admin.php?page=products');
    exit;
}

// Page routing
$page = $_GET['page'] ?? 'dashboard';

if ($page === 'orders') {
    $result = $conn->query('SELECT * FROM orders ORDER BY submitted_at DESC');
    include '/home/vol9_4/infinityfree.com/if0_42065544/htdocs/Front-End/pages/admin/orders.html.php';
} elseif ($page === 'products') {
    $products = $conn->query('SELECT * FROM products ORDER BY category, created_at DESC');
    include '/home/vol9_4/infinityfree.com/if0_42065544/htdocs/Front-End/pages/admin/products.html.php';
} else {
    $total_orders = $conn->query('SELECT COUNT(*) as count FROM orders')->fetch_assoc()['count'];

    // Date filter
    $date_from = $_GET['date_from'] ?? '';
    $date_to   = $_GET['date_to']   ?? '';
    $filter_by = $_GET['filter_by'] ?? '';

    $where = '';
    if ($filter_by === 'today') {
        $where = "WHERE DATE(submitted_at) = CURDATE()";
    } elseif ($filter_by === 'week') {
        $where = "WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } elseif ($filter_by === 'month') {
        $where = "WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    } elseif ($date_from && $date_to) {
        $date_from = $conn->real_escape_string($date_from);
        $date_to   = $conn->real_escape_string($date_to);
        $where = "WHERE DATE(submitted_at) BETWEEN '$date_from' AND '$date_to'";
    }

    $recent_limit = isset($_GET['show_all']) ? 999 : 10;
    $recent = $conn->query("SELECT * FROM orders ORDER BY submitted_at DESC LIMIT $recent_limit");
    $total_recent = $conn->query('SELECT COUNT(*) as count FROM orders')->fetch_assoc()['count'];

    include '/home/vol9_4/infinityfree.com/if0_42065544/htdocs/Front-End/pages/admin/dashboard.html.php';
}
?>
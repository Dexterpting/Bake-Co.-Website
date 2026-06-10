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
    $name        = trim($_POST['prod_name'] ?? '');
    $description = trim($_POST['prod_description'] ?? '');
    $category    = trim($_POST['prod_category'] ?? '');

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
        $stmt = $conn->prepare('INSERT INTO products (name, description, category, image) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $description, $category, $imageFilename);
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
    include __DIR__ . '/../Front-End/pages/admin/orders.html.php';
} elseif ($page === 'products') {
    $products = $conn->query('SELECT * FROM products ORDER BY category, created_at DESC');
    include __DIR__ . '/../Front-End/pages/admin/products.html.php';
} else {
    $total  = $conn->query('SELECT COUNT(*) as count FROM orders')->fetch_assoc()['count'];
    $recent = $conn->query('SELECT * FROM orders ORDER BY submitted_at DESC LIMIT 5');
    include __DIR__ . '/../Front-End/pages/admin/dashboard.html.php';
}
?>
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

// db.php moved up here so $conn is available for rate limiting
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/rate-limiter.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $loginCheck = checkRateLimit($conn, 'admin_login', 5, 900);

    if (!$loginCheck['allowed']) {
        $minutes = ceil($loginCheck['retry_after'] / 60);
        $error = "Too many login attempts. Please try again in {$minutes} minute(s).";
    } elseif ($_POST['password'] === $admin_password) {
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

// Latest order check endpoint
if (isset($_GET['latest_order'])) {
    $result = $conn->query('SELECT MAX(id) as latest FROM orders');
    $row = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode(['latest_id' => (int) $row['latest']]);
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

// Edit product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id          = (int) $_POST['edit_id'];
    $name        = trim($_POST['prod_name']        ?? '');
    $description = trim($_POST['prod_description'] ?? '');
    $category    = trim($_POST['prod_category']    ?? '');
    $price       = (float) ($_POST['prod_price']   ?? 0);
    $unit        = trim($_POST['prod_unit']        ?? 'per box');

    // Handle new image upload if provided
    if (isset($_FILES['prod_image']) && $_FILES['prod_image']['error'] === 0) {
        $allowed    = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize    = 5 * 1024 * 1024;
        $fileType   = $_FILES['prod_image']['type'];
        $fileSize   = $_FILES['prod_image']['size'];
        $tmpName    = $_FILES['prod_image']['tmp_name'];
        $origName   = basename($_FILES['prod_image']['name']);
        $ext        = pathinfo($origName, PATHINFO_EXTENSION);
        $newName    = uniqid('product_') . '.' . $ext;
        $uploadPath = '/home/vol9_4/infinityfree.com/if0_42065544/htdocs/Front-End/src/img/' . $newName;

        if (in_array($fileType, $allowed) && $fileSize <= $maxSize) {
            if (move_uploaded_file($tmpName, $uploadPath)) {
                $stmt = $conn->prepare('UPDATE products SET name=?, description=?, category=?, price=?, unit=?, image=? WHERE id=?');
                $stmt->bind_param('sssdss i', $name, $description, $category, $price, $unit, $newName, $id);
                $stmt->execute();
                $stmt->close();
            }
        }
    } else {
        // No new image — update without image
        $stmt = $conn->prepare('UPDATE products SET name=?, description=?, category=?, price=?, unit=? WHERE id=?');
        $stmt->bind_param('sssdsi', $name, $description, $category, $price, $unit, $id);
        $stmt->execute();
        $stmt->close();
    }

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

    // 1. Define filters first
    $date_from = isset($_GET['date_from']) ? $conn->real_escape_string($_GET['date_from']) : '';
    $date_to   = isset($_GET['date_to'])   ? $conn->real_escape_string($_GET['date_to'])   : '';
    $filter_by = $_GET['filter_by'] ?? '';

    // 2. Build $stats_where
    if ($date_from && $date_to) {
        $stats_where = "WHERE DATE(submitted_at) BETWEEN '$date_from' AND '$date_to'";
    } elseif ($filter_by === 'all') {
        $stats_where = '';
    } elseif ($filter_by === 'tomorrow') {
        $stats_where = "WHERE DATE(submitted_at) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    } elseif ($filter_by === 'week') {
        $stats_where = "WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } elseif ($filter_by === 'month') {
        $stats_where = "WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    } else {
        $stats_where = "WHERE DATE(submitted_at) = CURDATE()";
    }

    // 3. ONLY THEN query total_sales and unit breakdown
    $total_sales = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders $stats_where")->fetch_assoc()['total'];

    // Get breakdown per unit type with correct quantities
    $unit_breakdown = [];
    $unit_result = $conn->query("SELECT order_items, order_units FROM orders $stats_where");
    while ($urow = $unit_result->fetch_assoc()) {
        $items = explode(', ', $urow['order_items']);
        $units = explode(', ', $urow['order_units']);

        foreach ($items as $i => $item) {
            $unit = trim($units[$i] ?? '');
            if ($unit === '') continue;

            // Extract quantity from "x5 Classic Cheese Ensaymada" → 5
            preg_match('/^x(\d+)/', trim($item), $matches);
            $qty = isset($matches[1]) ? (int) $matches[1] : 1;

            if (!isset($unit_breakdown[$unit])) {
                $unit_breakdown[$unit] = 0;
            }
            $unit_breakdown[$unit] += $qty;
        }
    }

    // Recent orders filter
    $orders_date_from = isset($_GET['orders_date_from']) ? $conn->real_escape_string($_GET['orders_date_from']) : '';
    $orders_date_to   = isset($_GET['orders_date_to'])   ? $conn->real_escape_string($_GET['orders_date_to'])   : '';
    $orders_filter    = $_GET['orders_filter'] ?? '';

    if ($orders_date_from && $orders_date_to) {
    $orders_where = "WHERE DATE(submitted_at) BETWEEN '$orders_date_from' AND '$orders_date_to'";
    } elseif ($orders_filter === 'all') {
        $orders_where = '';
    } elseif ($orders_filter === 'tomorrow') {
        $orders_where = "WHERE DATE(submitted_at) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    } elseif ($orders_filter === 'week') {
        $orders_where = "WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } elseif ($orders_filter === 'month') {
        $orders_where = "WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    } else {
        $orders_where = "WHERE DATE(submitted_at) = CURDATE()";
    }

    $recent_limit = isset($_GET['show_all']) ? 999 : 10;
    $total_recent = $conn->query("SELECT COUNT(*) as count FROM orders $orders_where")->fetch_assoc()['count'];
    $recent       = $conn->query("SELECT * FROM orders $orders_where ORDER BY submitted_at DESC LIMIT $recent_limit");

    include '/home/vol9_4/infinityfree.com/if0_42065544/htdocs/Front-End/pages/admin/dashboard.html.php';
}
?>
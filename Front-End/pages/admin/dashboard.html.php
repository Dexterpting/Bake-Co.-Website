<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard — Bake & Co.</title>
  <link rel="stylesheet" href="/Front-End/src/styles/admin-style.css">
</head>
<body class="dashboard-page">
  <nav class="admin-nav">
    <h1>Bake & Co. Admin</h1>
    <a href="/Back-End/admin.php?logout=1" class="logout">Logout</a>
  </nav>
  <div class="dashboard">

    <div class="stats">
      <div class="stat-card">
        <p class="stat-label">Total Orders</p>
        <p class="stat-number"><?= $total ?></p>
      </div>
    </div>

    <div class="quick-links">
      <h2>Quick Links</h2>
      <div class="links-row">
        <a href="/Back-End/admin.php?page=orders" class="link-btn">View All Orders</a>
        <a href="/Back-End/admin.php?page=products" class="link-btn">Manage Products</a>
      </div>
    </div>

    <div class="recent-orders">
      <h2>Recent Orders</h2>
      <div class="table-warpper">
        <table>
          <thead>
            <tr>
              <th>#</th><th>Name</th><th>Phone</th>
              <th>Address</th><th>Landmark</th>
              <th>Order</th><th>Date</th><th></th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $recent->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><?= htmlspecialchars($row['address']) ?></td>
              <td><?= htmlspecialchars($row['landmark']) ?></td>
              <td><?= nl2br(htmlspecialchars($row['order_items'])) ?></td>
              <td><?= $row['submitted_at'] ?></td>
              <td><a class="delete" href="?delete=<?= $row['id'] ?>&from=dashboard" onclick="return confirm('Delete this order?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</body>
</html>
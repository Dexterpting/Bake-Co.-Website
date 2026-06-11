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

    <!-- Stats Filter -->
    <div class="stats-filter">
      <form method="GET" action="/Back-End/admin.php" class="filter-form">
        <input type="hidden" name="page" value="dashboard">
        <div class="filter-row">
          <div class="filter-group">
            <label>Date From</label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
          </div>
          <div class="filter-group">
            <label>Date To</label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
          </div>
          <div class="filter-group">
            <label>Filter By</label>
            <select name="filter_by">
              <option value="">All Time</option>
              <option value="today" <?= ($_GET['filter_by'] ?? '') === 'today' ? 'selected' : '' ?>>Today</option>
              <option value="week" <?= ($_GET['filter_by'] ?? '') === 'week' ? 'selected' : '' ?>>Last 7 Days</option>
              <option value="month" <?= ($_GET['filter_by'] ?? '') === 'month' ? 'selected' : '' ?>>Last 30 Days</option>
            </select>
          </div>
          <button type="submit" class="filter-btn">Apply</button>
          <a href="/Back-End/admin.php" class="filter-reset">Reset</a>
        </div>
      </form>
    </div>

    <!-- Stats -->
    <div class="stats">
      <div class="stat-card">
        <p class="stat-label">Total Orders</p>
        <p class="stat-number"><?= $total_orders ?></p>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="quick-links">
      <h2>Quick Links</h2>
      <div class="links-row">
        <a href="/Back-End/admin.php?page=orders" class="link-btn">View All Orders</a>
        <a href="/Back-End/admin.php?page=products" class="link-btn">Manage Products</a>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="recent-orders">
      <h2>Recent Orders</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Landmark</th>
              <th>Order</th>
              <th>Date Order Placed</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if ($recent->num_rows === 0): ?>
            <tr><td colspan="8" style="text-align:center;color:#7b6553;">No orders yet.</td></tr>
            <?php else: ?>
            <?php while ($row = $recent->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['phone']) ?></td>
              <td><?= htmlspecialchars($row['address']) ?></td>
              <td><?= htmlspecialchars($row['landmark']) ?></td>
              <td><?= nl2br(htmlspecialchars($row['order_items'])) ?></td>
              <td><?= $row['submitted_at'] ?></td>
              <td><a class="delete" href="/Back-End/admin.php?delete=<?= $row['id'] ?>&from=dashboard" onclick="return confirm('Delete this order?')">Delete</a></td>
            </tr>
            <?php endwhile; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Show More -->
      <?php if (!isset($_GET['show_all']) && $total_recent > 10): ?>
      <div style="text-align:center;margin-top:16px;">
        <a href="/Back-End/admin.php?show_all=1" class="link-btn">Show More (<?= $total_recent - 10 ?> remaining)</a>
      </div>
      <?php elseif (isset($_GET['show_all'])): ?>
      <div style="text-align:center;margin-top:16px;">
        <a href="/Back-End/admin.php" class="link-btn">Show Less</a>
      </div>
      <?php endif; ?>
    </div>

  </div>
</body>
</html>
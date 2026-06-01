<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orders — Bake & Co.</title>
  <link rel="stylesheet" href="/Front-End/src/styles/admin-style.css">
</head>
<body class="orders-page">
  <nav class="admin-nav">
    <h1>Bake & Co. Admin</h1>
    <a href="/Back-End/admin.php?logout=1" class="logout">Logout</a>
  </nav>
  <div class="orders-wrapper">
    <a class="back-btn" href="/Back-End/admin.php">← Back to Dashboard</a>
    <h1>Orders</h1>
    <p class="count">Total: <?= $result->num_rows ?> order(s)</p>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>#</th><th>Name</th><th>Phone</th>
            <th>Address</th><th>Landmark</th>
            <th>Order / Message</th><th>Date Order Placed</th><th></th>
          </tr>
        </thead>
        <tbody id="ordersBody">
          <tr><td colspan="8">Loading orders...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
  <script src="/Front-End/src/js/admin-page/admin-script.js"></script>
</body>
</html>
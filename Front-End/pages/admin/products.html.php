<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Products — Bake & Co.</title>
  <link rel="stylesheet" href="/Front-End/src/styles/admin-style.css">
</head>
<body class="orders-page">
  <nav class="admin-nav">
    <h1>Bake & Co. Admin</h1>
    <a href="/Back-End/admin.php?logout=1" class="logout">Logout</a>
  </nav>
  <div class="orders-wrapper">
    <a class="back-btn" href="/Back-End/admin.php">← Back to Dashboard</a>
    <h1>Products</h1>

    <!-- Add Product Form -->
    <div class="add-product-form">
      <h2>Add New Product</h2>
      <form method="POST" action="/Back-End/admin.php?page=products" enctype="multipart/form-data">
        <div class="form-grid">
          <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="prod_name" placeholder="e.g. Classic Cheese Ensaymada" required>
          </div>
          <div class="form-group">
            <label>Category</label>
            <select name="prod_category" required>
              <option value="">Select category</option>
              <option value="pastries">Sweet Breads / Pastries</option>
              <option value="cheese">Cheese Cakes</option>
              <option value="moist">Moist Cakes</option>
              <option value="custom-made">Custom Made Order</option>
            </select>
          </div>
          <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="prod_image" accept="image/*" required>
            <small style="color:#7b6553;font-size:.78rem;">JPG, PNG, WEBP — max 5MB</small>
          </div>
          <div class="form-group full-width">
            <label>Description</label>
            <textarea name="prod_description" rows="3" placeholder="Describe the product..." required></textarea>
          </div>
        </div>
        <button type="submit" name="add_product">Add Product</button>
      </form>
    </div>

    <!-- Products Table -->
    <h2 style="margin: 32px 0 14px;">All Products</h2>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Category</th>
            <th>Description</th>
            <th>Image</th>
            <th>Date Added</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if ($products->num_rows === 0): ?>
          <tr><td colspan="7" style="text-align:center;color:#7b6553;">No products yet.</td></tr>
          <?php else: ?>
          <?php while ($row = $products->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><img src="/Front-End/src/img/<?= htmlspecialchars($row['image']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:6px;"></td>
            <td><?= $row['created_at'] ?></td>
            <td><a class="delete" href="/Back-End/admin.php?delete_product=<?= $row['id'] ?>&from=products" onclick="return confirm('Delete this product?')">Delete</a></td>
          </tr>
          <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
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
            <label>Price (₱)</label>
            <input type="number" name="prod_price" placeholder="e.g. 150" min="0" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Unit / Sold As</label>
            <select name="prod_unit">
              <option value="per box">Per Box</option>
              <option value="per piece">Per Piece</option>
              <option value="per slice">Per Slice</option>
              <option value="per dozen">Per Dozen</option>
              <option value="per order">Per Order</option>
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
            <th>Price</th>
            <th>Unit</th>
            <th>Description</th>
            <th>Image</th>
            <th>Date Added</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if ($products->num_rows === 0): ?>
          <tr><td colspan="9" style="text-align:center;color:#7b6553;">No products yet.</td></tr>
          <?php else: ?>
          <?php while ($row = $products->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td>₱<?= number_format($row['price'], 2) ?></td>
            <td><?= htmlspecialchars($row['unit']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><img src="/Front-End/src/img/<?= htmlspecialchars($row['image']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:6px;"></td>
            <td><?= $row['created_at'] ?></td>
            <td style="display:flex;gap:8px;align-items:center;">
              <a class="edit-btn"
                 href="#"
                 onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['name'])) ?>', '<?= htmlspecialchars(addslashes($row['description'])) ?>', '<?= $row['category'] ?>', <?= $row['price'] ?>, '<?= $row['unit'] ?>')">
                Edit
              </a>
              <a class="delete" href="/Back-End/admin.php?delete_product=<?= $row['id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal-overlay" id="editModal">
    <div class="modal-box">
      <button class="modal-close" onclick="closeEditModal()">✕</button>
      <h2>Edit Product</h2>
      <form method="POST" action="/Back-End/admin.php?page=products" enctype="multipart/form-data">
        <input type="hidden" name="edit_id" id="edit_id">
        <div class="form-grid">
          <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="prod_name" id="edit_name" required>
          </div>
          <div class="form-group">
            <label>Category</label>
            <select name="prod_category" id="edit_category" required>
              <option value="pastries">Sweet Breads / Pastries</option>
              <option value="cheese">Cheese Cakes</option>
              <option value="moist">Moist Cakes</option>
              <option value="custom-made">Custom Made Order</option>
            </select>
          </div>
          <div class="form-group">
            <label>Price (₱)</label>
            <input type="number" name="prod_price" id="edit_price" min="0" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Unit / Sold As</label>
            <select name="prod_unit" id="edit_unit">
              <option value="per box">Per Box</option>
              <option value="per piece">Per Piece</option>
              <option value="per slice">Per Slice</option>
              <option value="per dozen">Per Dozen</option>
              <option value="per order">Per Order</option>
            </select>
          </div>
          <div class="form-group">
            <label>New Image (optional)</label>
            <input type="file" name="prod_image" accept="image/*">
            <small style="color:#7b6553;font-size:.78rem;">Leave blank to keep current image</small>
          </div>
          <div class="form-group full-width">
            <label>Description</label>
            <textarea name="prod_description" id="edit_description" rows="3" required></textarea>
          </div>
        </div>
        <button type="submit" name="edit_product">Save Changes</button>
      </form>
    </div>
  </div>

  <script src="/Front-End/src/js/admin-page/admin-prod-script.js"></script>
</body>
</html>
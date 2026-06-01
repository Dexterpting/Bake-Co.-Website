<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login — Bake & Co.</title>
  <link rel="stylesheet" href="/VA-Development/Lorena-Landing-Page/Landing-Page/Front-End/src/styles/admin-style.css">
</head>
<body class="login-page">
  <form method="POST" action="/VA-Development/Lorena-Landing-Page/Landing-Page/Back-End/admin.php">
    <h2>Bake & Co. Admin</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
</body>
</html>
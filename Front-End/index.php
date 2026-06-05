<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/styles/main-style.css">
    <title>Bake & Co.</title>
</head>
<body>
    <nav>
        <h1><a href="">Bake & Co.</a></h1>
        <ul id="navMenu">
            <li><a href="#products-section">Menu</a></li>
            <li><a href="#review-section">Reviews</a></li>
            <li><a href="#contact-section">Order Now !</a></li>
        </ul>
        <button class="nav-hamburger" id="navToggle" aria-label="Open menu">&#9776;</button>
    </nav>
    <div class="nav-overlay" id="navOverlay"></div>

    <section id="main-section" class="Hero">
        <div class="left-side">
            <img src="./src/img/picture1.jpg" alt="Ensaimada-Bread">
        </div>
        <div class="right-side">
            <h1>Bake & Co.</h1>
            <p>Freshly baked premium ensaymada with rich cream cheese filling and generous toppings - made to satisfy every bite.</p>
            <button><a href="#contact-section">Order Now</a></button>
        </div>
    </section>

<section id="products-section" class="products">
<div class="tab"><h1>PRODUCTS</h1></div>

<div class="category-filters">
  <select id="categorySelect">
    <option value="all">All</option>
    <option value="pastries">Sweet Breads / Pastries</option>
    <option value="cheese">Cheese Cakes</option>
    <option value="moist">Moist Cakes</option>
    <option value="custom-made">Custom Made Order</option>
  </select>
</div>

<div class="products-inner">
  <?php
    require_once __DIR__ . '/../Back-End/db.php';
    $result = $conn->query('SELECT * FROM products ORDER BY category, id ASC');
    while ($row = $result->fetch_assoc()):
      $imgPath = './src/img/' . htmlspecialchars($row['image']);
  ?>
  <div class="card" data-category="<?= htmlspecialchars($row['category']) ?>">
    <h2><?= htmlspecialchars($row['name']) ?></h2>
    <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($row['name']) ?>">
    <p><?= htmlspecialchars($row['description']) ?></p>
    <button><a href="#contact-section">Order Now</a></button>
  </div>
  <?php endwhile; ?>
</div>
</section>

    <section id="review-section" class="reviews">
        <h1>Reviews</h1>
        <div class="reviews-inner">
            <div class="box">
                <p>The ensaymada is super soft and the cream cheese filling makes it even better. My family finished one box in one sitting.</p>
                <p>-John — Cebu</p>
            </div>
            <div class="box">
                <p>“Ordered the Alcapone flavor and it really lives up to the hype. Fresh, cheesy, and worth it.”</p>
                <p>-Paul — Mactan</p>
            </div>
            <div class="box">
                <p>“Cheese topping is generous and the bread stays soft even after a few hours. Highly recommended.”</p>
                <p>-Kyle — Lapu-Lapu</p>
            </div>
        </div>
        <button><a href="https://www.facebook.com/profile.php?id=61567567501151&sk=reviews_given">See More</a></button>
    </section>

    <section id="contact-section" class="contact">
        <div class="contact-info">
            <h2>Contact</h2>
            <p>📞 +639672737622</p>
            <p>✉️ bakeco26@gmail.com</p>
            <h3>Follow Socials</h3>
            <a href="https://www.facebook.com/profile.php?id=61567567501151" class="fb-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="white">
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                </svg>
            </a>
        </div>

        <div class="contact-form-wrapper" >
            <form class="contact-form" method="POST">
                <input type="text"  name="name" placeholder="Name"/>
                <input type="tel" name="number" placeholder="Phone Number"/>
                <input type="text" name="address" placeholder="Location for delivery"/>
                <input type="text" name="landmark" placeholder="Landmark for the delivery location">
                <textarea name="message" placeholder="Order" rows="4"></textarea>
                <button type="submit">Submit</button>
            </form>
        </div>
    </section>

    <script src="./src/js/landing-page/phone-nav-script.js"></script>
    <script src="./src/js/landing-page/multi-prod-script.js"></script>
    <script src="./src/js/landing-page/contact-script.js"></script>

</body>
</html>
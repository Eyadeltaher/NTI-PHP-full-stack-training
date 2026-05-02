<?php

session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopZone – Home</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

  <link rel="stylesheet" href="style.css">
</head>

<body>

  <?php include 'navbar.php'; ?>

  <header class="hero-header">
    <div class="hero-overlay">
      <div class="hero-content text-center text-white">
        <h1 class="display-3 font-weight-bold">Welcome to Our Store</h1>
        <p class="lead mt-3">Discover amazing products at unbeatable prices.</p>
        <a href="products.php" class="btn btn-warning btn-lg mt-3">
          Shop Now →
        </a>
      </div>
    </div>
  </header>

  <section class="py-5 bg-light">
    <div class="container">
      <div class="row text-center">

        <div class="col-md-4 mb-4">
          <div class="card shadow-sm border-0 h-100 p-4">
            <h2>🚚</h2>
            <h5 class="mt-2">Free Shipping</h5>
            <p class="text-muted">On all orders over $50</p>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="card shadow-sm border-0 h-100 p-4">
            <h2>🔒</h2>
            <h5 class="mt-2">Secure Payment</h5>
            <p class="text-muted">100% secure transactions</p>
          </div>
        </div>

        <div class="col-md-4 mb-4">
          <div class="card shadow-sm border-0 h-100 p-4">
            <h2>↩️</h2>
            <h5 class="mt-2">Easy Returns</h5>
            <p class="text-muted">30-day return policy</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; 2024 ShopZone. All rights reserved.</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
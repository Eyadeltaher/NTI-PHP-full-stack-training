<?php
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">

  <a class="navbar-brand fw-bold" href="index.php">🛒 ShopZone</a>

  <button class="navbar-toggler" type="button" data-toggle="collapse"
          data-target="#navLinks">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navLinks">
    <ul class="navbar-nav ml-auto">

      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="products.php">All Products</a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="account.php">Account</a>
      </li>

      <?php if (isset($_SESSION['email'])): ?>
        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">Logout</a>
        </li>
      <?php endif; ?>

    </ul>
  </div>
</nav>

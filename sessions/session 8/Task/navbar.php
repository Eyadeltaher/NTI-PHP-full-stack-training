<?php
// navbar.php — shared navbar included in every page
// We check if a session user exists to show logout link
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">

  <!-- LEFT SIDE: Website Name / Brand -->
  <a class="navbar-brand fw-bold" href="index.php">🛒 ShopZone</a>

  <!-- Hamburger button for mobile -->
  <button class="navbar-toggler" type="button" data-toggle="collapse"
          data-target="#navLinks">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- RIGHT SIDE: Navigation Links -->
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

      <!-- Show Logout only if user is logged in (session has email) -->
      <?php if (isset($_SESSION['email'])): ?>
        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">Logout</a>
        </li>
      <?php endif; ?>

    </ul>
  </div>
</nav>

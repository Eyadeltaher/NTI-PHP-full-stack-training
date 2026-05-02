<?php
// ============================================================
// products.php — ALL PRODUCTS PAGE
// Uses an associative array of products + foreach loop
// Displays each product as a Bootstrap Card
// ============================================================
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopZone – All Products</title>
  <link rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- ======= NAVBAR ======= -->
  <?php include 'navbar.php'; ?>

  <!-- ======= PAGE TITLE ======= -->
  <div class="page-title bg-dark text-white text-center py-4">
    <h2 class="mb-0">🛍️ All Products</h2>
    <p class="text-muted mb-0">Browse our latest collection</p>
  </div>

  <!-- ======= PRODUCTS SECTION ======= -->
  <section class="py-5">
    <div class="container">

      <?php
      // -------------------------------------------------------
      // STEP 1: Define our products as an ASSOCIATIVE ARRAY
      //
      // Structure:
      //   key   → product name (string)
      //   value → array containing: price, img, desc
      //
      // The image files (1.png ... 6.png) should be inside
      // an "images/" folder in your project directory.
      // Replace them with your own image filenames.
      // -------------------------------------------------------

      $products = [

        'Wireless Headphones' => [
          'price' => '620',
          'img'   => 'images/1.png',
          'desc'  => 'High-quality sound with noise cancellation and 20hr battery.'
        ],

        'Smart Watch' => [
          'price' => '6500',
          'img'   => 'images/2.png',
          'desc'  => 'Track fitness, notifications and more on your wrist.'
        ],

        'Running Shoes' => [
          'price' => '1200',
          'img'   => 'images/3.png',
          'desc'  => 'Lightweight and comfortable shoes for daily running.'
        ],

        'Laptop Backpack' => [
          'price' => '850',
          'img'   => 'images/4.png',
          'desc'  => 'Water-resistant backpack fits up to 15.6" laptops.'
        ],

        'Bluetooth Speaker' => [
          'price' => '450',
          'img'   => 'images/5.png',
          'desc'  => 'Portable 360° sound with 12 hours playtime.'
        ],

        'Mechanical Keyboard' => [
          'price' => '1800',
          'img'   => 'images/6.png',
          'desc'  => 'RGB backlit mechanical keys with tactile feedback.'
        ],

      ]; // end $products array

      // -------------------------------------------------------
      // STEP 2: Open the Bootstrap row that holds our cards
      // -------------------------------------------------------
      ?>

      <div class="row">

        <?php
        // -------------------------------------------------------
        // STEP 3: FOREACH LOOP
        //
        //   $product → holds the KEY (product name)
        //   $values  → holds the VALUE array (price, img, desc)
        //
        // On each iteration we output one Bootstrap Card
        // -------------------------------------------------------

        foreach ($products as $product => $values):
        ?>

          <!-- Each card takes 4 columns (3 cards per row on md screens) -->
          <div class="col-md-4 mb-4">

            <!--
              Bootstrap Card structure:
              https://getbootstrap.com/docs/4.4/components/card/
            -->
            <div class="card h-100 shadow-sm product-card">

              <!-- Product Image -->
              <img
                src="<?php echo $values['img']; ?>"
                class="card-img-top product-img"
                alt="<?php echo $product; ?>"
                onerror="this.src='https://via.placeholder.com/300x200?text=Product+Image'"
              >
              <!--
                onerror above = fallback placeholder if the image file
                is missing, so the layout doesn't break during dev
              -->

              <div class="card-body d-flex flex-column">

                <!-- Product Name (the KEY from our array) -->
                <h5 class="card-title text-dark font-weight-bold">
                  <?php echo $product; ?>
                </h5>

                <!-- Product Description -->
                <p class="card-text text-muted flex-grow-1">
                  <?php echo $values['desc']; ?>
                </p>

                <!-- Price + Button row pinned to bottom -->
                <div class="mt-auto">
                  <span class="badge badge-warning text-dark p-2 mb-2 font-weight-bold">
                    💰 <?php echo $values['price']; ?> EGP
                  </span>
                  <br>
                  <button class="btn btn-dark btn-block mt-1">
                    Add to Cart
                  </button>
                </div>

              </div><!-- /card-body -->
            </div><!-- /card -->

          </div><!-- /col -->

        <?php endforeach; // end of foreach loop ?>

      </div><!-- /row -->

    </div><!-- /container -->
  </section>

  <!-- ======= FOOTER ======= -->
  <footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; 2024 ShopZone. All rights reserved.</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

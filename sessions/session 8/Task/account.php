<?php
// ============================================================
// account.php — ACCOUNT PAGE
//
// TWO STATES:
//   STATE 1 → User NOT logged in → Show Login Form (email + password)
//   STATE 2 → User IS logged in  → Show Profile Form (7 fields)
//
// Each form has full validation before storing in session
// ============================================================
session_start();

// We'll collect errors in this array
$errors = [];
$success = '';

// ============================================================
// HANDLE FORM SUBMISSIONS (when user clicks Submit)
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // ----------------------------------------------------------
  // CASE A: Login form was submitted (has 'login_submit' key)
  // ----------------------------------------------------------
  if (isset($_POST['login_submit'])) {

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    // --- VALIDATION ---

    // 1. Email must not be empty
    if (empty($email)) {
      $errors[] = 'Email is required.';
    }
    // 2. Email must be a valid format (e.g. user@example.com)
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Please enter a valid email address.';
    }

    // 3. Password must not be empty
    if (empty($password)) {
      $errors[] = 'Password is required.';
    }
    // 4. Password must be at least 6 characters
    elseif (strlen($password) < 6) {
      $errors[] = 'Password must be at least 6 characters long.';
    }

    // --- IF NO ERRORS → store in session and redirect ---
    if (empty($errors)) {
      $_SESSION['email']    = $email;
      $_SESSION['password'] = $password;

      // Redirect to All Products page
      header('Location: products.php');
      exit();
    }
  }

  // ----------------------------------------------------------
  // CASE B: Profile form was submitted (has 'profile_submit')
  // ----------------------------------------------------------
  elseif (isset($_POST['profile_submit'])) {

    $username   = trim($_POST['username']   ?? '');
    $password   = trim($_POST['password']   ?? '');
    $email      = trim($_POST['email']      ?? '');
    $phone      = trim($_POST['phone']      ?? '');
    $facebook   = trim($_POST['facebook']   ?? '');
    $twitter    = trim($_POST['twitter']    ?? '');
    $instagram  = trim($_POST['instagram']  ?? '');

    // --- VALIDATION FOR ALL 7 FIELDS ---

    // 1. Username
    if (empty($username)) {
      $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3) {
      $errors[] = 'Username must be at least 3 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
      $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }

    // 2. Password
    if (empty($password)) {
      $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
      $errors[] = 'Password must be at least 6 characters.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
      $errors[] = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
      $errors[] = 'Password must contain at least one number.';
    }

    // 3. Email
    if (empty($email)) {
      $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Please enter a valid email address.';
    }

    // 4. Phone Number
    if (empty($phone)) {
      $errors[] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
      $errors[] = 'Phone number must be 10–15 digits (numbers only).';
    }

    // 5. Facebook URL
    if (empty($facebook)) {
      $errors[] = 'Facebook profile URL is required.';
    } elseif (!filter_var($facebook, FILTER_VALIDATE_URL)) {
      $errors[] = 'Facebook URL must be a valid URL (e.g. https://facebook.com/yourname).';
    } elseif (strpos($facebook, 'facebook.com') === false) {
      $errors[] = 'Please enter a valid Facebook URL.';
    }

    // 6. Twitter URL
    if (empty($twitter)) {
      $errors[] = 'Twitter profile URL is required.';
    } elseif (!filter_var($twitter, FILTER_VALIDATE_URL)) {
      $errors[] = 'Twitter URL must be a valid URL (e.g. https://twitter.com/yourname).';
    } elseif (strpos($twitter, 'twitter.com') === false && strpos($twitter, 'x.com') === false) {
      $errors[] = 'Please enter a valid Twitter/X URL.';
    }

    // 7. Instagram URL
    if (empty($instagram)) {
      $errors[] = 'Instagram profile URL is required.';
    } elseif (!filter_var($instagram, FILTER_VALIDATE_URL)) {
      $errors[] = 'Instagram URL must be a valid URL (e.g. https://instagram.com/yourname).';
    } elseif (strpos($instagram, 'instagram.com') === false) {
      $errors[] = 'Please enter a valid Instagram URL.';
    }

    // --- IF NO ERRORS → store in session and redirect ---
    if (empty($errors)) {
      $_SESSION['username']  = $username;
      $_SESSION['password']  = $password;
      $_SESSION['email']     = $email;
      $_SESSION['phone']     = $phone;
      $_SESSION['facebook']  = $facebook;
      $_SESSION['twitter']   = $twitter;
      $_SESSION['instagram'] = $instagram;

      // Redirect to Home page
      header('Location: index.php');
      exit();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ShopZone – Account</title>
  <link rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- ======= NAVBAR ======= -->
  <?php include 'navbar.php'; ?>

  <div class="container py-5">

    <?php
    // ============================================================
    // DECIDE WHICH STATE TO SHOW
    //
    // If $_SESSION['email'] is set  → user is logged in → STATE 2
    // Otherwise                      → user not logged in → STATE 1
    // ============================================================

    if (!isset($_SESSION['email'])):
    ?>

      <!-- =========================================
           STATE 1: User NOT logged in — Login Form
           ========================================= -->

      <div class="row justify-content-center">
        <div class="col-md-5">

          <div class="card shadow form-card">
            <div class="card-body p-4">

              <h3 class="text-center mb-1 font-weight-bold">Welcome Back 👋</h3>
              <p class="text-center text-muted mb-4">Sign in to your account</p>

              <!-- Show Errors if any -->
              <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                  <ul class="mb-0 pl-3">
                    <?php foreach ($errors as $error): ?>
                      <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <!-- LOGIN FORM -->
              <!--
                action=""     = submit to the same page (account.php)
                method="post" = send data via POST (not visible in URL)
              -->
              <form action="" method="post">

                <!-- Email Field -->
                <div class="form-group">
                  <label for="email">Email Address</label>
                  <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                  >
                  <!-- htmlspecialchars() prevents XSS attacks when repopulating field -->
                </div>

                <!-- Password Field -->
                <div class="form-group">
                  <label for="password">Password</label>
                  <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Min. 6 characters"
                  >
                </div>

                <!-- Hidden field to tell PHP which form was submitted -->
                <input type="hidden" name="login_submit" value="1">

                <button type="submit" class="btn btn-dark btn-block mt-3">
                  Sign In
                </button>

              </form>

            </div>
          </div><!-- /card -->

        </div>
      </div>

    <?php else: ?>

      <!-- =========================================
           STATE 2: User IS logged in — Profile Form
           ========================================= -->

      <div class="row justify-content-center">
        <div class="col-md-7">

          <div class="card shadow form-card">
            <div class="card-body p-4">

              <h3 class="text-center mb-1 font-weight-bold">Complete Your Profile ✏️</h3>
              <p class="text-center text-muted mb-4">
                Logged in as: <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong>
              </p>

              <!-- Show Errors if any -->
              <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                  <ul class="mb-0 pl-3">
                    <?php foreach ($errors as $error): ?>
                      <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <!-- PROFILE FORM -->
              <form action="" method="post">

                <!-- 1. Username -->
                <div class="form-group">
                  <label>Username</label>
                  <input type="text" class="form-control" name="username"
                    placeholder="e.g. john_doe"
                    value="<?php echo htmlspecialchars($_SESSION['username'] ?? $_POST['username'] ?? ''); ?>">
                </div>

                <!-- 2. Password -->
                <div class="form-group">
                  <label>New Password</label>
                  <input type="password" class="form-control" name="password"
                    placeholder="Min. 6 chars, 1 uppercase, 1 number">
                </div>

                <!-- 3. Email -->
                <div class="form-group">
                  <label>Email Address</label>
                  <input type="email" class="form-control" name="email"
                    placeholder="you@example.com"
                    value="<?php echo htmlspecialchars($_SESSION['email'] ?? $_POST['email'] ?? ''); ?>">
                </div>

                <!-- 4. Phone Number -->
                <div class="form-group">
                  <label>Phone Number</label>
                  <input type="text" class="form-control" name="phone"
                    placeholder="e.g. 01012345678"
                    value="<?php echo htmlspecialchars($_SESSION['phone'] ?? $_POST['phone'] ?? ''); ?>">
                </div>

                <hr>
                <p class="text-muted small">Social Media Profiles</p>

                <!-- 5. Facebook -->
                <div class="form-group">
                  <label>Facebook URL</label>
                  <input type="url" class="form-control" name="facebook"
                    placeholder="https://facebook.com/yourname"
                    value="<?php echo htmlspecialchars($_SESSION['facebook'] ?? $_POST['facebook'] ?? ''); ?>">
                </div>

                <!-- 6. Twitter -->
                <div class="form-group">
                  <label>Twitter / X URL</label>
                  <input type="url" class="form-control" name="twitter"
                    placeholder="https://twitter.com/yourname"
                    value="<?php echo htmlspecialchars($_SESSION['twitter'] ?? $_POST['twitter'] ?? ''); ?>">
                </div>

                <!-- 7. Instagram -->
                <div class="form-group">
                  <label>Instagram URL</label>
                  <input type="url" class="form-control" name="instagram"
                    placeholder="https://instagram.com/yourname"
                    value="<?php echo htmlspecialchars($_SESSION['instagram'] ?? $_POST['instagram'] ?? ''); ?>">
                </div>

                <!-- Hidden field to tell PHP which form was submitted -->
                <input type="hidden" name="profile_submit" value="1">

                <button type="submit" class="btn btn-dark btn-block mt-2">
                  Save Profile
                </button>

              </form>

            </div>
          </div><!-- /card -->

        </div>
      </div>

    <?php endif; ?>

  </div><!-- /container -->

  <!-- ======= FOOTER ======= -->
  <footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; 2024 ShopZone. All rights reserved.</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php

session_start();

$errors = [];
$success = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {


  if (isset($_POST['login_submit'])) {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');


    if (empty($email)) {
      $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
      $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
      $errors[] = 'Password must be at least 6 characters long.';
    }

    if (empty($errors)) {
      $_SESSION['email'] = $email;
      $_SESSION['password'] = $password;

      header('Location: products.php');
      exit();
    }
  } elseif (isset($_POST['profile_submit'])) {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $facebook = trim($_POST['facebook'] ?? '');
    $twitter = trim($_POST['twitter'] ?? '');
    $instagram = trim($_POST['instagram'] ?? '');


    if (empty($username)) {
      $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3) {
      $errors[] = 'Username must be at least 3 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
      $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }

    if (empty($password)) {
      $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
      $errors[] = 'Password must be at least 6 characters.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
      $errors[] = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
      $errors[] = 'Password must contain at least one number.';
    }

    if (empty($email)) {
      $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Please enter a valid email address.';
    }

    if (empty($phone)) {
      $errors[] = 'Phone number is required.';
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) {
      $errors[] = 'Phone number must be 10–15 digits (numbers only).';
    }

    if (empty($facebook)) {
      $errors[] = 'Facebook profile URL is required.';
    } elseif (!filter_var($facebook, FILTER_VALIDATE_URL)) {
      $errors[] = 'Facebook URL must be a valid URL (e.g. https://facebook.com/yourname).';
    } elseif (strpos($facebook, 'facebook.com') === false) {
      $errors[] = 'Please enter a valid Facebook URL.';
    }

    if (empty($twitter)) {
      $errors[] = 'Twitter profile URL is required.';
    } elseif (!filter_var($twitter, FILTER_VALIDATE_URL)) {
      $errors[] = 'Twitter URL must be a valid URL (e.g. https://twitter.com/yourname).';
    } elseif (strpos($twitter, 'twitter.com') === false && strpos($twitter, 'x.com') === false) {
      $errors[] = 'Please enter a valid Twitter/X URL.';
    }

    if (empty($instagram)) {
      $errors[] = 'Instagram profile URL is required.';
    } elseif (!filter_var($instagram, FILTER_VALIDATE_URL)) {
      $errors[] = 'Instagram URL must be a valid URL (e.g. https://instagram.com/yourname).';
    } elseif (strpos($instagram, 'instagram.com') === false) {
      $errors[] = 'Please enter a valid Instagram URL.';
    }

    if (empty($errors)) {
      $_SESSION['username'] = $username;
      $_SESSION['password'] = $password;
      $_SESSION['email'] = $email;
      $_SESSION['phone'] = $phone;
      $_SESSION['facebook'] = $facebook;
      $_SESSION['twitter'] = $twitter;
      $_SESSION['instagram'] = $instagram;

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
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
</head>

<body>


  <?php include 'navbar.php'; ?>

  <div class="container py-5">

    <?php


    if (!isset($_SESSION['email'])):
      ?>



      <div class="row justify-content-center">
        <div class="col-md-5">

          <div class="card shadow form-card">
            <div class="card-body p-4">

              <h3 class="text-center mb-1 font-weight-bold">Welcome Back 👋</h3>
              <p class="text-center text-muted mb-4">Sign in to your account</p>

              <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                  <ul class="mb-0 pl-3">
                    <?php foreach ($errors as $error): ?>
                      <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>



              <form action="" method="post">

                <div class="form-group">
                  <label for="email">Email Address</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" name="password"
                    placeholder="Min. 6 characters">
                </div>

                <input type="hidden" name="login_submit" value="1">

                <button type="submit" class="btn btn-dark btn-block mt-3">
                  Sign In
                </button>

              </form>

            </div>
          </div>

        </div>
      </div>

    <?php else: ?>


      <div class="row justify-content-center">
        <div class="col-md-7">

          <div class="card shadow form-card">
            <div class="card-body p-4">

              <h3 class="text-center mb-1 font-weight-bold">Complete Your Profile ✏️</h3>
              <p class="text-center text-muted mb-4">
                Logged in as: <strong><?php echo htmlspecialchars($_SESSION['email']); ?></strong>
              </p>

              <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                  <ul class="mb-0 pl-3">
                    <?php foreach ($errors as $error): ?>
                      <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>

              <form action="" method="post">

                <div class="form-group">
                  <label>Username</label>
                  <input type="text" class="form-control" name="username" placeholder="e.g. john_doe"
                    value="<?php echo htmlspecialchars($_SESSION['username'] ?? $_POST['username'] ?? ''); ?>">
                </div>

                <div class="form-group">
                  <label>New Password</label>
                  <input type="password" class="form-control" name="password"
                    placeholder="Min. 6 chars, 1 uppercase, 1 number">
                </div>

                <div class="form-group">
                  <label>Email Address</label>
                  <input type="email" class="form-control" name="email" placeholder="you@example.com"
                    value="<?php echo htmlspecialchars($_SESSION['email'] ?? $_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                  <label>Phone Number</label>
                  <input type="text" class="form-control" name="phone" placeholder="e.g. 01012345678"
                    value="<?php echo htmlspecialchars($_SESSION['phone'] ?? $_POST['phone'] ?? ''); ?>">
                </div>

                <hr>
                <p class="text-muted small">Social Media Profiles</p>

                <div class="form-group">
                  <label>Facebook URL</label>
                  <input type="url" class="form-control" name="facebook" placeholder="https://facebook.com/yourname"
                    value="<?php echo htmlspecialchars($_SESSION['facebook'] ?? $_POST['facebook'] ?? ''); ?>">
                </div>

                <div class="form-group">
                  <label>Twitter / X URL</label>
                  <input type="url" class="form-control" name="twitter" placeholder="https://twitter.com/yourname"
                    value="<?php echo htmlspecialchars($_SESSION['twitter'] ?? $_POST['twitter'] ?? ''); ?>">
                </div>

                <div class="form-group">
                  <label>Instagram URL</label>
                  <input type="url" class="form-control" name="instagram" placeholder="https://instagram.com/yourname"
                    value="<?php echo htmlspecialchars($_SESSION['instagram'] ?? $_POST['instagram'] ?? ''); ?>">
                </div>

                <input type="hidden" name="profile_submit" value="1">

                <button type="submit" class="btn btn-dark btn-block mt-2">
                  Save Profile
                </button>

              </form>

            </div>
          </div>

        </div>
      </div>

    <?php endif; ?>

  </div>

  <footer class="bg-dark text-white text-center py-3">
    <p class="mb-0">&copy; 2026 ShopZone. All rights reserved.</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
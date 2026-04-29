
# PHP — Forms, Superglobals, Sessions, Cookies & Files

---

## Superglobals — What Are They?

Superglobals are built-in PHP arrays that are always available everywhere in your script — inside functions, outside functions, in any file. You never need to declare them with `global`.

The main ones:

| Superglobal | Contains |
|---|---|
| `$_GET` | Data sent via URL query string |
| `$_POST` | Data sent via form POST method |
| `$_REQUEST` | Combined: GET + POST + COOKIE |
| `$_COOKIE` | Cookie data sent by the browser |
| `$_SESSION` | Session data stored on the server |
| `$_SERVER` | Server and request information |
| `$_FILES` | Uploaded file information |

---

## $_GET — Receiving URL Data

`$_GET` reads data passed in the URL after a `?` — called a query string.

```
http://localhost/index.php?name=Ahmed&age=25
```

```php
<?php
  echo $_GET['name'];   // Ahmed
  echo $_GET['age'];    // 25
?>
```

### When a Form Uses `method="get"`

Data is appended to the URL **visibly:**

```html
<form method="get" action="index.php">
  <input type="text" name="name">
  <input type="password" name="password">
  <button type="submit" name="submit">Submit</button>
</form>
```

Submitting sends the browser to:
```
index.php?name=Ahmed&password=1234&submit=
```

```php
<?php
  if (isset($_GET['submit'])) {
    $name     = $_GET['name'];
    $password = $_GET['password'];
    echo "Welcome, " . $name;
  }
?>
```

### When to Use GET

- Search forms — search terms in the URL are shareable and bookmarkable
- Filters and sorting on a page
- Pagination (`?page=2`)
- Any data that is safe to be visible in the URL

### When NOT to Use GET

- Passwords, tokens, sensitive data — they appear in the URL, browser history, and server logs
- Any form that creates or modifies data

---

## $_POST — Receiving Form Data

`$_POST` reads data sent in the HTTP request body — invisible in the URL.

```html
<form method="post" action="index.php">
  <input type="text" name="name">
  <input type="password" name="password">
  <button type="submit" name="submit">Submit</button>
</form>
```

```php
<?php
  if (isset($_POST['submit'])) {
    $name     = $_POST['name'];
    $password = $_POST['password'];
    echo "Welcome, " . $name;
  } else {
    echo "Form not submitted";
  }
?>
```

### `isset()` — Always Check Before Reading

Always wrap `$_POST` access in `isset()`. If the form was not submitted and you try to read `$_POST['name']`, PHP throws an undefined index warning.

```php
<?php
  // Safe pattern — always use isset
  if (isset($_POST['submit'])) {
    $name = $_POST['name'];
  }

  // Even safer — check the request method instead
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
  }
?>
```

> Checking `$_SERVER['REQUEST_METHOD'] == 'POST'` is more reliable than `isset($_POST['submit'])` because it does not depend on a submit button being present — it checks the actual HTTP method.

### GET vs POST Comparison

| | GET | POST |
|---|---|---|
| Data location | URL query string | Request body |
| Visible | Yes — in URL, browser history, server logs | No |
| Bookmarkable | Yes | No |
| Data limit | ~2000 characters (URL length limit) | No practical limit |
| Use for | Reading/searching | Creating, updating, deleting |
| Safe for passwords | No | Yes |

---

## $_REQUEST — GET + POST + COOKIE Combined

`$_REQUEST` merges the data from `$_GET`, `$_POST`, and `$_COOKIE` into one array.

```php
<?php
  // Works whether the form used GET or POST
  $name = $_REQUEST['name'];
?>
```

> Avoid `$_REQUEST` in real projects. It is ambiguous — you cannot tell where the data came from. Use `$_GET` or `$_POST` explicitly so your code is clear and secure.

---

## Cookies — Storing Data in the Browser

A cookie is a small piece of data the server sends to the browser. The browser stores it and sends it back with every future request to that domain.

### Setting a Cookie

```php
<?php
  setcookie(name, value, expiry, path, domain, secure, httponly);

  // Basic usage
  setcookie('username', 'Ahmed', time() + 3600);
  // Name: username | Value: Ahmed | Expires: 1 hour from now

  // Never expires (until browser is closed)
  setcookie('theme', 'dark');

  // Expires in 30 days
  setcookie('username', 'Ahmed', time() + (30 * 24 * 60 * 60));

  // Delete a cookie — set expiry in the past
  setcookie('username', '', time() - 3600);
?>
```

### Reading a Cookie

```php
<?php
  if (isset($_COOKIE['username'])) {
    echo $_COOKIE['username'];   // Ahmed
  }
?>
```

### Important Cookie Rules

`setcookie()` must be called **before any HTML output** — before `<!DOCTYPE html>`, before any `echo`. Cookies are sent as HTTP headers, and headers must be sent before any body content.

```php
<?php
  setcookie('username', 'Ahmed', time() + 3600);  // Must come first
?>
<!DOCTYPE html>                                   // HTML comes after
```

### Pre-filling a Form from a Cookie

This is the pattern from the training code — remembering the user's last input:

```html
<input type="text" name="name" 
  value="<?php if (isset($_COOKIE['username'])) echo $_COOKIE['username']; ?>">

<input type="password" name="password"
  value="<?php if (isset($_COOKIE['password'])) echo $_COOKIE['password']; ?>">
```

### Saving Form Input to a Cookie

```php
<?php
  if (isset($_POST['submit'])) {
    $name     = $_POST['name'];
    $password = $_POST['password'];

    if (isset($_POST['remember'])) {
      setcookie('username', $name,     time() + (7 * 24 * 60 * 60));
      setcookie('password', $password, time() + (7 * 24 * 60 * 60));
    }
  }
?>
```

> Storing passwords in cookies is shown here for learning purposes only. In real applications, never store plain-text passwords anywhere — not in cookies, not in sessions. Store only a session ID or token.

### Cookie vs Session

| | Cookie | Session |
|---|---|---|
| Stored where | Browser (client-side) | Server (server-side) |
| Visible to user | Yes — can be read in browser | No |
| Secure | Less secure | More secure |
| Size limit | ~4KB per cookie | No practical limit |
| Expiry | You set it explicitly | Ends when browser closes (by default) |
| Use for | Remember me, preferences, themes | Login state, cart, user data |

---

## Sessions — Storing Data on the Server

A session stores data on the server and identifies the user with a unique session ID sent as a cookie. Unlike regular cookies, session data never travels to the browser.

### Starting a Session

```php
<?php
  session_start();  // Must be the FIRST thing in the file — before any HTML
?>
```

`session_start()` must be called on every page that uses sessions. If the user already has a session, it resumes it. If not, it creates a new one.

### Storing Data in a Session

```php
<?php
  session_start();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $password = $_POST['password'];

    $_SESSION['name']     = $name;
    $_SESSION['password'] = $password;

    header('location: welcome.php');  // Redirect after storing
    exit();                           // Always exit after header redirect
  }
?>
```

### Reading Session Data (on another page)

```php
<?php
  session_start();
  echo "Welcome, " . $_SESSION['name'];
?>
```

### `header('location: ...')` and `exit()`

`header('location: welcome.php')` sends an HTTP redirect — it tells the browser to go to a new URL. But PHP continues executing after it unless you call `exit()`.

```php
<?php
  header('location: welcome.php');
  exit();  // Stop execution — without this, code below still runs
?>
```

> Always call `exit()` immediately after `header('location:...')`. Without it, the rest of your PHP code runs even though the browser is already redirecting — this can cause bugs and security issues.

### Destroying a Session — Logout

```php
<?php
  session_start();         // Resume the session
  session_destroy();       // Destroy all session data
  header('location: home.php');
  exit();
?>
```

From the training code — a logout page is just these four lines:

```php
<?php
  session_start();
  session_destroy();
  header('location: home.php');
  exit();
?>
```

### Storing Errors in a Session (Flash Messages)

A common pattern — store errors in the session, redirect, then show and clear them:

```php
<?php
  // On the form processing page (index.php)
  session_start();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'];
    $password = $_POST['password'];
    $errors   = [];

    if (strlen($name) < 5 || strlen($name) > 12) {
      $errors[] = "Name must be between 5 and 12 characters";
    }
    if (strlen($password) < 3 || strlen($password) > 9) {
      $errors[] = "Password must be between 3 and 9 characters";
    }

    if ($errors) {
      $_SESSION['errors'] = $errors;   // Store errors in session
      header('location: home.php');    // Go back to the form
      exit();
    }

    // No errors — save user and proceed
    $_SESSION['name'] = $name;
    header('location: welcome.php');
    exit();
  }
?>
```

```php
<?php
  // On the form page (home.php) — show errors if any
  session_start();

  if (isset($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
      echo $error . "<br>";
    }
    unset($_SESSION['errors']);   // Clear after showing — "flash message"
  }
?>
```

> This is called a **flash message** pattern — data stored in the session just to survive one redirect, then deleted after being displayed.

---

## $_SERVER — Server and Request Information

`$_SERVER` is an array PHP fills automatically with information about the server, the current request, headers, and the script being run.

```php
<?php
  print_r($_SERVER);   // Print everything to see what's available
?>
```

### Most Useful $_SERVER Values

```php
<?php
  echo $_SERVER['REQUEST_METHOD'];   // GET or POST — how the request was made
  echo $_SERVER['PHP_SELF'];         // Path of current script: /index.php
  echo $_SERVER['HTTP_HOST'];        // Domain: localhost or example.com
  echo $_SERVER['SERVER_NAME'];      // Server name
  echo $_SERVER['DOCUMENT_ROOT'];    // Absolute path to htdocs folder
  echo $_SERVER['REMOTE_ADDR'];      // User's IP address
  echo $_SERVER['HTTP_USER_AGENT'];  // Browser information
  echo $_SERVER['QUERY_STRING'];     // Everything after ? in the URL
  echo $_SERVER['REQUEST_URI'];      // Full path + query string: /index.php?name=Ahmed
?>
```

### The Most Important Use — Checking Request Method

```php
<?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the form
    $name = $_POST['name'];
  }
  // If it's GET (page load), do nothing — just show the form
?>
```

> This is the standard way to handle a page that both shows a form (GET) and processes it (POST) in the same file. One file, two behaviors depending on the request method.

---

## Validation — Checking User Input

Never trust data from the user. Always validate before using it.

### Why Validate

- Users make mistakes — wrong format, too long, empty fields
- Malicious users try to inject harmful data
- The database may reject invalid data — you want to handle this gracefully

### Common Validation Techniques

```php
<?php
  $name     = $_POST['name'];
  $email    = $_POST['email'];
  $age      = $_POST['age'];
  $errors   = [];

  // Check if empty
  if (empty($name)) {
    $errors[] = "Name is required";
  }

  // Check length
  if (strlen($name) < 3 || strlen($name) > 20) {
    $errors[] = "Name must be between 3 and 20 characters";
  }

  // Check if numeric
  if (!is_numeric($age)) {
    $errors[] = "Age must be a number";
  }

  // Check range
  if ($age < 1 || $age > 120) {
    $errors[] = "Age must be between 1 and 120";
  }

  // Check email format using built-in filter
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address";
  }

  // Only proceed if no errors
  if (empty($errors)) {
    // Safe to use the data
  } else {
    foreach ($errors as $error) {
      echo $error . "<br>";
    }
  }
?>
```

### Sanitizing Input — Cleaning Data

Validation checks if data is acceptable. Sanitization cleans the data before using it.

```php
<?php
  // Remove HTML tags — prevents XSS attacks
  $name = strip_tags($_POST['name']);

  // Remove extra whitespace
  $name = trim($_POST['name']);

  // Convert special HTML characters to safe entities
  // < becomes &lt;  > becomes &gt;  " becomes &quot;
  $name = htmlspecialchars($_POST['name']);

  // Combined — the safe way to output any user input in HTML
  $name = htmlspecialchars(trim(strip_tags($_POST['name'])));
?>
```

> `htmlspecialchars()` is the most important sanitization function. Always use it when displaying user input in HTML. Without it, a user can type `<script>alert('hacked')</script>` into a form and have it execute in other users' browsers — this is called an XSS (Cross-Site Scripting) attack.

### PHP Built-in Filter Functions

```php
<?php
  // Validate
  filter_var($email, FILTER_VALIDATE_EMAIL);   // true or false
  filter_var($url,   FILTER_VALIDATE_URL);     // true or false
  filter_var($age,   FILTER_VALIDATE_INT);     // int or false
  filter_var($ip,    FILTER_VALIDATE_IP);      // true or false

  // Sanitize — clean the value
  filter_var($name,  FILTER_SANITIZE_STRING);     // Remove tags
  filter_var($email, FILTER_SANITIZE_EMAIL);       // Remove illegal characters
  filter_var($url,   FILTER_SANITIZE_URL);         // Remove illegal characters
  filter_var($num,   FILTER_SANITIZE_NUMBER_INT);  // Remove non-numeric characters

  // Example usage
  $email = $_POST['email'];

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email";
  } else {
    $clean_email = filter_var($email, FILTER_SANITIZE_EMAIL);
    // Use $clean_email
  }
?>
```

---

## $_FILES — Handling File Uploads

### The HTML Form

A file upload form must have `enctype="multipart/form-data"` — without this, the file data is not sent.

```html
<form method="post" action="index.php" enctype="multipart/form-data">
  <input type="file" name="image">
  <button type="submit" name="submit">Upload</button>
</form>
```

> `enctype="multipart/form-data"` is mandatory for file uploads. Without it, `$_FILES` will be empty.

### What $_FILES Contains

When a file is uploaded, PHP fills `$_FILES` with an array of information about it:

```php
<?php
  print_r($_FILES['image']);
  /*
  Array (
    [name]     => photo.jpg          ← original filename from the user's computer
    [type]     => image/jpeg         ← MIME type (do not trust this for security)
    [tmp_name] => /tmp/phpXXXXXX     ← where PHP temporarily stored the file
    [error]    => 0                  ← 0 means no error
    [size]     => 204800             ← file size in bytes
  )
  */
?>
```

### Processing a File Upload — The Full Pattern

This is the exact pattern from the training code:

```php
<?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $img      = $_FILES['image'];          // The file info array
    $img_name = $img['name'];              // Original filename: photo.jpg
    $tmp_name = $img['tmp_name'];          // Temporary path on server
    $ext      = pathinfo($img_name, PATHINFO_EXTENSION);  // Get extension: jpg

    $new_name = uniqid() . "." . $ext;    // Generate unique filename: 63f8a2b1c4d9e.jpg

    move_uploaded_file($tmp_name, "uploads/" . $new_name);  // Move from temp to permanent location

    echo "<img src='uploads/$new_name'>";  // Display the uploaded image
  }
?>
```

### Why Each Step Matters

**`pathinfo($img_name, PATHINFO_EXTENSION)`**
Extracts just the file extension (`jpg`, `png`, `pdf`) from the filename.

```php
$ext = pathinfo("photo.jpg", PATHINFO_EXTENSION);   // jpg
$ext = pathinfo("document.pdf", PATHINFO_EXTENSION); // pdf
```

**`uniqid()`**
Generates a unique ID based on the current time in microseconds. Used to create a unique filename so uploads never overwrite each other.

```php
echo uniqid();   // 63f8a2b1c4d9e  (different every call)
```

**`move_uploaded_file($tmp_name, $destination)`**
Moves the file from PHP's temporary storage to a permanent location in your project. Until you call this, the file only exists in a temp folder and will be deleted when the script ends.

```php
move_uploaded_file($tmp_name, "uploads/" . $new_name);
// From: /tmp/phpXXXXXX
// To:   /your-project/uploads/63f8a2b1c4d9e.jpg
```

### Validating File Uploads

```php
<?php
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $img    = $_FILES['image'];
    $errors = [];

    // Check for upload errors
    if ($img['error'] !== 0) {
      $errors[] = "Upload failed with error code: " . $img['error'];
    }

    // Check file size — limit to 2MB
    $max_size = 2 * 1024 * 1024;  // 2MB in bytes
    if ($img['size'] > $max_size) {
      $errors[] = "File is too large. Maximum size is 2MB";
    }

    // Check file extension — whitelist allowed types
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
      $errors[] = "Only JPG, PNG, GIF, and WEBP files are allowed";
    }

    if (empty($errors)) {
      $new_name = uniqid() . "." . $ext;
      move_uploaded_file($img['tmp_name'], "uploads/" . $new_name);
      echo "Upload successful: <img src='uploads/$new_name'>";
    } else {
      foreach ($errors as $error) {
        echo $error . "<br>";
      }
    }
  }
?>
```

### $_FILES Error Codes

| Code | Meaning |
|---|---|
| `0` | No error — success |
| `1` | File exceeds upload_max_filesize in php.ini |
| `2` | File exceeds MAX_FILE_SIZE in the HTML form |
| `3` | File only partially uploaded |
| `4` | No file was uploaded |
| `6` | No temp folder found |
| `7` | Failed to write to disk |

---

## Built-in Functions — The Most Useful Ones

### String Functions

```php
<?php
  $str = "  Hello, World!  ";

  echo strlen($str);               // 18  — string length
  echo strtolower($str);           // "  hello, world!  "
  echo strtoupper($str);           // "  HELLO, WORLD!  "
  echo trim($str);                 // "Hello, World!"  — remove whitespace from both ends
  echo ltrim($str);                // "Hello, World!  " — left trim
  echo rtrim($str);                // "  Hello, World!" — right trim
  echo str_replace("World", "PHP", $str);  // "  Hello, PHP!  "
  echo strrev("Hello");            // "olleH"
  echo str_repeat("ab", 3);       // "ababab"
  echo str_word_count("Hello World PHP");  // 3

  // Check if string contains a substring
  echo strpos("Hello World", "World");     // 6  — position of "World"
  echo strpos("Hello World", "xyz");       // false — not found

  // PHP 8: str_contains, str_starts_with, str_ends_with
  var_dump(str_contains("Hello World", "World"));     // true
  var_dump(str_starts_with("Hello World", "Hello"));  // true
  var_dump(str_ends_with("Hello World", "World"));    // true

  // Split string into array
  $parts = explode(",", "apple,banana,cherry");
  print_r($parts);   // ["apple", "banana", "cherry"]

  // Join array into string
  $str = implode(", ", ["apple", "banana", "cherry"]);
  echo $str;   // apple, banana, cherry

  // Substring — extract part of a string
  echo substr("Hello World", 6);     // "World"  — from position 6 to end
  echo substr("Hello World", 6, 3);  // "Wor"    — from position 6, length 3

  // Pad a string
  echo str_pad("5", 3, "0", STR_PAD_LEFT);  // "005"
?>
```

### Number Functions

```php
<?php
  echo round(4.6);        // 5   — round to nearest integer
  echo round(4.567, 2);   // 4.57 — round to 2 decimal places
  echo ceil(4.1);         // 5   — always round up
  echo floor(4.9);        // 4   — always round down
  echo abs(-42);          // 42  — absolute value
  echo max(3, 7, 2, 9);   // 9   — largest value
  echo min(3, 7, 2, 9);   // 2   — smallest value
  echo pow(2, 8);         // 256 — 2 to the power of 8
  echo sqrt(16);          // 4   — square root
  echo rand(1, 100);      // Random integer between 1 and 100
  echo number_format(1234567.891, 2, '.', ',');  // 1,234,567.89
?>
```

### Array Functions

```php
<?php
  $nums = [3, 1, 4, 1, 5, 9, 2, 6];

  echo count($nums);              // 8   — number of elements
  echo array_sum($nums);         // 31  — sum of all values
  echo implode(", ", $nums);     // 3, 1, 4, 1, 5, 9, 2, 6

  sort($nums);                   // Sort ascending (modifies original)
  rsort($nums);                  // Sort descending
  $sorted = array_unique($nums); // Remove duplicate values

  array_push($nums, 10);         // Add to end
  array_pop($nums);              // Remove from end
  array_unshift($nums, 0);       // Add to beginning
  array_shift($nums);            // Remove from beginning

  array_reverse($nums);          // Reverse the array
  array_slice($nums, 1, 3);      // Extract 3 elements starting at index 1
  array_splice($nums, 1, 2, [10, 20]);  // Remove 2 at index 1, insert [10,20]

  in_array(5, $nums);            // true/false — check if value exists
  array_search(5, $nums);        // Returns index of value, or false

  // For associative arrays
  ksort($assoc);                 // Sort by key
  asort($assoc);                 // Sort by value, preserve keys
  array_keys($assoc);            // Get all keys
  array_values($assoc);          // Get all values
  array_key_exists('name', $assoc);  // Check if key exists

  // Functional array operations
  $doubled = array_map(fn($n) => $n * 2, $nums);       // Apply function to each
  $evens   = array_filter($nums, fn($n) => $n % 2 == 0); // Keep matching elements
  $sum     = array_reduce($nums, fn($carry, $n) => $carry + $n, 0); // Reduce to one value
?>
```

### Date and Time Functions

```php
<?php
  echo date("Y");               // 2024       — current year
  echo date("m");               // 03         — current month (with leading zero)
  echo date("d");               // 15         — current day
  echo date("H:i:s");           // 14:30:00   — hours:minutes:seconds
  echo date("Y-m-d");           // 2024-03-15 — standard date format
  echo date("D, d M Y");        // Fri, 15 Mar 2024

  echo time();                  // Unix timestamp — seconds since Jan 1, 1970
  echo date("Y", time() + 3600);  // Current year — timestamp can be modified

  // Create a timestamp for a specific date
  $ts = mktime(0, 0, 0, 12, 25, 2024);  // Dec 25, 2024
  echo date("D, d M Y", $ts);            // Wed, 25 Dec 2024
?>
```

### Math Functions (already in numbers above, but for reference)

```php
<?php
  echo M_PI;         // 3.14159265...
  echo PHP_INT_MAX;  // Largest integer: 9223372036854775807
  echo PHP_FLOAT_MAX; // Largest float
?>
```

---

## Complete Real-World Example — Login Form with Sessions

This ties together everything from the training code:

**home.php — The Login Form**

```php
<?php
  session_start();

  // Show errors if any (flash messages from previous submit)
  if (isset($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
      echo "<div class='alert'>" . $error . "</div>";
    }
    unset($_SESSION['errors']);   // Clear after showing
  }
?>
<!DOCTYPE html>
<html>
<body>
  <form method="post" action="index.php">
    <input type="text" name="name" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <button type="submit" name="submit">Login</button>
  </form>
</body>
</html>
```

**index.php — Process the Form**

```php
<?php
  session_start();

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $password = trim($_POST['password']);
    $errors   = [];

    // Validate
    if (strlen($name) < 5 || strlen($name) > 12) {
      $errors[] = "Name must be between 5 and 12 characters";
    }
    if (strlen($password) < 3 || strlen($password) > 9) {
      $errors[] = "Password must be between 3 and 9 characters";
    }

    // If errors — go back to form
    if ($errors) {
      $_SESSION['errors'] = $errors;
      header('location: home.php');
      exit();
    }

    // No errors — save to session and redirect
    $_SESSION['name'] = $name;
    header('location: welcome.php');
    exit();
  }
?>
```

**welcome.php — Protected Page**

```php
<?php
  session_start();

  // Redirect if not logged in
  if (!isset($_SESSION['name'])) {
    header('location: home.php');
    exit();
  }

  echo "Welcome, " . htmlspecialchars($_SESSION['name']);
?>
<a href="logout.php">Logout</a>
```

**logout.php**

```php
<?php
  session_start();
  session_destroy();
  header('location: home.php');
  exit();
?>
```




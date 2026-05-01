# PHP — Validation, Errors, Include, Files & Regex

---

## Types of Errors in PHP

PHP has several categories of errors. Understanding them helps you debug faster because each type tells you exactly what kind of problem you are dealing with.

---

### Syntax Error (Parse Error)

PHP could not even understand your code. The script does not run at all.

```php
<?php
  echo "Hello"    // Missing semicolon
  $name = "Ahmed"
?>
```

```
Parse error: syntax error, unexpected '$name' in index.php on line 3
```

> Fix it before anything else. A syntax error stops the entire file from executing.

---

### Fatal Error

PHP understood your code but hit something it cannot recover from — calling a function that does not exist, running out of memory, calling a method on null.

```php
<?php
  undefinedFunction();   // This function does not exist
?>
```

```
Fatal error: Uncaught Error: Call to undefined function undefinedFunction()
```

> The script stops at the fatal error. Everything after it does not run.

---

### Warning

Something is wrong but PHP can keep going. The script continues executing.

```php
<?php
  include 'file_that_does_not_exist.php';   // Warning — but script continues
  echo "This still runs";
?>
```

```
Warning: include(file_that_does_not_exist.php): failed to open stream
```

> Warnings should not be ignored. They often indicate logic problems that will cause incorrect behavior even though the script keeps running.

---

### Notice

PHP spotted something that might be a mistake but is technically allowed. The most common notice is accessing an undefined variable.

```php
<?php
  echo $undefined_variable;   // Never assigned — triggers a notice
?>
```

```
Notice: Undefined variable: undefined_variable
```

> Notices are easy to ignore but often indicate bugs — especially when you read from `$_POST` or `$_GET` without checking `isset()` first.

---

### Deprecated Error

You are using a feature that still works now but will be removed in a future PHP version.

```php
<?php
  // mysql_connect() was removed in PHP 7
  // Using it in PHP 5 gives a deprecated warning
  mysql_connect("localhost", "root", "");
?>
```

```
Deprecated: Function mysql_connect() is deprecated
```

> When you see this, update your code. Deprecated features disappear in the next major version.

---

### Error Summary Table

| Type | Script Stops? | Common Cause |
|---|---|---|
| Syntax / Parse Error | Yes — immediately | Typo, missing semicolon, unmatched bracket |
| Fatal Error | Yes — at that line | Calling undefined function, null pointer |
| Warning | No — continues | Missing file (include), wrong argument type |
| Notice | No — continues | Undefined variable, missing array key |
| Deprecated | No — continues | Using a removed or outdated function |

---

### Turning Error Display On/Off

During development, show all errors:

```php
<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
?>
```

In production, hide errors from users (log them instead):

```php
<?php
  error_reporting(0);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);
?>
```

> Never show error details to users in production. Error messages can reveal your file structure, database names, and code logic to attackers.

---

## `echo` vs `print`

Both output text to the browser. The differences are minor but worth knowing.

```php
<?php
  echo "Hello World";
  print "Hello World";
?>
```

| | `echo` | `print` |
|---|---|---|
| Return value | None | Always returns 1 |
| Multiple values | Yes — `echo "a", "b"` | No — one value only |
| Speed | Marginally faster | Marginally slower |
| Use in expression | No | Yes — `$x = print "Hello"` |

```php
<?php
  // echo can output multiple values separated by comma
  echo "Hello", " ", "World";   // Hello World

  // print cannot
  // print "Hello", " ", "World";   // Syntax error

  // print returns 1 — can be used in expressions (rare)
  $result = print "Hello";
  echo $result;   // 1
?>
```

> In practice, always use `echo`. It is the standard. `print` exists but you will almost never need it.

---

## Single Quotes vs Double Quotes (Revisited in Context)

This was introduced in the variables section. Here is the full practical impact:

```php
<?php
  $name = "Ahmed";

  // Double quotes — variables are expanded
  echo "Hello $name";          // Hello Ahmed
  echo "Hello {$name}s";       // Hello Ahmeds  — curly braces for clarity

  // Single quotes — everything literal
  echo 'Hello $name';          // Hello $name  ($ is printed as-is)
  echo 'Hello \n World';       // Hello \n World  (\n is NOT a newline)
?>
```

### `\n` Does Not Work in the Browser

`\n` (newline) creates a line break in the source code and terminal output — but the browser ignores whitespace and newlines in HTML. To create a visible line break in the browser, you need `<br>`.

```php
<?php
  // This creates a newline in the HTML source — not visible in browser
  echo "Line 1\nLine 2";

  // This creates a visible line break in the browser
  echo "Line 1<br>Line 2";

  // When writing to files or terminal, \n is correct
  // When outputting HTML to browser, use <br>
?>
```

---

## `include` and `require`

Both pull the content of another PHP file into the current file — as if you had typed that code directly there. They are the original PHP way to reuse code across pages.

```php
<?php
  include 'header.php';    // Insert the content of header.php here
  include 'footer.php';
?>
```

### The Difference Between `include` and `require`

```php
<?php
  include 'navbar.php';
  // If navbar.php is missing → Warning → script continues

  require 'config.php';
  // If config.php is missing → Fatal Error → script stops completely
?>
```

> Use `require` for files your script absolutely cannot function without — database config, core functions, authentication checks. Use `include` for optional pieces like sidebars or widgets.

### `include_once` and `require_once`

Prevents the same file from being included more than once — useful when multiple files all try to include the same utility file.

```php
<?php
  include_once 'functions.php';   // Only included the first time — ignored if already included
  require_once 'config.php';      // Same behavior but fatal if missing
?>
```

> Use `_once` versions for files that define functions or classes. Including a file that defines a function twice causes a fatal error because you cannot define the same function twice in PHP.

### Practical Pattern — Splitting a Page into Parts

```
project/
  header.php     ← HTML head, nav
  footer.php     ← closing tags, scripts
  config.php     ← database credentials
  functions.php  ← reusable functions
  index.php      ← the main page
```

```php
<?php require_once 'config.php'; ?>
<?php require_once 'functions.php'; ?>
<?php include 'header.php'; ?>

<main>
  <!-- page content -->
</main>

<?php include 'footer.php'; ?>
```

### Sharing Variables Across Included Files

Variables from the parent file are accessible in the included file, and vice versa:

```php
<?php
  // index.php
  $pageTitle = "Home Page";
  include 'header.php';
?>
```

```php
<?php
  // header.php — can use $pageTitle from index.php
?>
<title><?= $pageTitle ?></title>
```

---

## Files — Reading and Writing

Before databases existed, data was stored in plain text files. File handling is still useful for logs, configuration, exports, and data import/export. This was the original answer to data persistence before databases became standard.

---

### Reading an Entire File — `readfile()`

The simplest way. Reads a file and outputs it directly.

```php
<?php
  readfile('data.txt');
  // Reads data.txt and echoes it immediately
?>
```

---

### File Modes

When you open a file with `fopen()`, you specify a mode:

| Mode | Meaning | File must exist? | Creates file? | Position |
|---|---|---|---|---|
| `r` | Read only | Yes | No | Start |
| `w` | Write only — overwrites everything | No | Yes | Start |
| `a` | Append — adds to end | No | Yes | End |
| `r+` | Read and write | Yes | No | Start |
| `w+` | Read and write — overwrites | No | Yes | Start |
| `a+` | Read and append | No | Yes | End |

> `w` is dangerous on existing files — it deletes all existing content and starts fresh. Use `a` to add to an existing file without destroying it.

---

### Reading a File — `fopen` + `fread`

```php
<?php
  $file = fopen('data.txt', 'r');   // Open in read mode — returns a resource

  // Read a specific number of characters
  echo fread($file, 200);           // Read first 200 characters

  // Read the entire file
  echo fread($file, filesize('data.txt'));   // filesize() returns bytes in the file

  fclose($file);   // Always close the file when done
?>
```

> `fopen()` returns a **resource** — a reference to the open file. You pass this resource to every subsequent file function. Always call `fclose()` when finished — leaving files open wastes server resources.

---

### Reading Line by Line — `fgets()`

`fgets()` reads one line at a time. Useful for processing large files without loading everything into memory.

```php
<?php
  $file = fopen('data.txt', 'r');

  echo fgets($file);   // Reads the first line

  fclose($file);
?>
```

### Reading All Lines with a While Loop

```php
<?php
  $file = fopen('data.txt', 'r');

  while (!feof($file)) {          // feof() returns true when end of file is reached
    $line = fgets($file);
    echo $line . "<br>";
  }

  fclose($file);
?>
```

> `feof()` checks if you have reached the End Of File. The loop keeps going until there are no more lines.

---

### Writing to a File — `fwrite()`

```php
<?php
  // Mode 'w' — creates file if it doesn't exist, overwrites if it does
  $file = fopen('data.txt', 'w');
  fwrite($file, "Hello, this is new content");
  fclose($file);
  // Previous content is gone — file now only contains what you just wrote
?>
```

---

### Appending to a File — Mode `a`

```php
<?php
  // Mode 'a' — creates file if it doesn't exist, adds to the END if it does
  $file = fopen('data.txt', 'a');
  fwrite($file, "\nNew line added at the end");
  fclose($file);
  // Previous content is preserved — new content is added at the bottom
?>
```

---

### Useful File Functions

```php
<?php
  // Check if a file exists before working with it
  if (file_exists('data.txt')) {
    echo "File exists";
  }

  // Read entire file into a string
  $content = file_get_contents('data.txt');
  echo $content;

  // Write a string directly to a file (no fopen needed)
  file_put_contents('data.txt', "New content");          // Overwrites
  file_put_contents('data.txt', "More content", FILE_APPEND);  // Appends

  // Delete a file
  unlink('data.txt');

  // File information
  echo filesize('data.txt');   // Size in bytes
  echo filetype('data.txt');   // "file" or "dir"
?>
```

> `file_get_contents()` and `file_put_contents()` are the modern shortcuts. They do the `fopen` + `fread/fwrite` + `fclose` all in one call. Use these for simple read/write. Use `fopen` when you need more control — like reading line by line or appending to large files.

---

## Validation in a Separate File

In real projects, validation logic goes in its own file so it can be reused across multiple pages without repeating code.

```
project/
  validate.php     ← all validation functions live here
  index.php        ← includes validate.php and uses its functions
```

**validate.php**

```php
<?php
  function validateName($name) {
    $errors = [];
    if (empty(trim($name))) {
      $errors[] = "Name is required";
    }
    if (strlen($name) < 3 || strlen($name) > 20) {
      $errors[] = "Name must be between 3 and 20 characters";
    }
    return $errors;
  }

  function validateEmail($email) {
    $errors = [];
    if (empty(trim($email))) {
      $errors[] = "Email is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email format";
    }
    return $errors;
  }

  function validatePassword($password) {
    $errors = [];
    if (strlen($password) < 6) {
      $errors[] = "Password must be at least 6 characters";
    }
    return $errors;
  }
?>
```

**index.php**

```php
<?php
  require_once 'validate.php';   // Load the validation functions

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $errors = array_merge($errors, validateName($_POST['name']));
    $errors = array_merge($errors, validateEmail($_POST['email']));
    $errors = array_merge($errors, validatePassword($_POST['password']));

    if (empty($errors)) {
      // All valid — proceed
    } else {
      foreach ($errors as $error) {
        echo $error . "<br>";
      }
    }
  }
?>
```

---

## Validation in a Separate File V2

### `validation.php`

```php
<?php

$validates = [

    'user_name' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'error'  => "invalid user_name",
        'my_option' => [
            'options' => ['regexp' => '/[a-z]{3,9}$/']
        ]
    ],

    'password' => [
        'filter' => FILTER_VALIDATE_REGEXP,
        'error'  => "invalid password",
        'my_option' => [
            'options' => ['regexp' => '/[a-z0-9]{3,9}$/']
        ]
    ],

    'age' => [
        'filter' => FILTER_VALIDATE_INT,
        'error'  => "invalid age",
        'my_option' => [
            'options' => ['min_range' => 16, 'max_range' => 60]
        ]
    ],

    'email' => [
        'filter' => FILTER_VALIDATE_EMAIL,
        'error'  => "invalid email",
    ],

];
```

### `index.php`

```php
<?php

session_start();

include('validate.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];

    foreach ($validates as $validate_name => $validate_value) {

        $value = filter_input(
            INPUT_POST,
            $validate_name,
            $validate_value['filter'],
            $validate_value['my_option']
        );

        if (empty($_POST[$validate_name])) {
            $errors[] = "you must full " . $validate_name;
        } elseif ($value == false) {
            $errors[] = $validate_value['error'];
        }
    }

    if ($errors) {
        $_SESSION['error'] = $errors;
        header('location:home.php');
        exit();
    }

    header('location:welcome.php');
}
```

### `welcome.php`

```php
<?php

echo "welcome";
```

---


## `filter_input()` — Reading and Validating in One Step

`filter_input()` reads from a superglobal (`$_GET`, `$_POST`, etc.) and applies a filter at the same time — safer than reading first and validating separately.

```php
<?php
  // filter_input(type, variable_name, filter)

  // Read from POST and validate as email
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  // Returns the valid email string, or false if invalid, or null if not set

  // Read from GET and sanitize as integer
  $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);

  // Read from POST and sanitize string
  $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

  // Usage
  if ($email === false) {
    echo "Invalid email";
  } elseif ($email === null) {
    echo "Email not provided";
  } else {
    echo "Valid email: " . $email;
  }
?>
```

### Input Type Constants

| Constant | Reads From |
|---|---|
| `INPUT_GET` | `$_GET` |
| `INPUT_POST` | `$_POST` |
| `INPUT_COOKIE` | `$_COOKIE` |
| `INPUT_SERVER` | `$_SERVER` |
| `INPUT_ENV` | Environment variables |

### Common Filters

| Filter | Purpose |
|---|---|
| `FILTER_VALIDATE_EMAIL` | Returns email or false |
| `FILTER_VALIDATE_URL` | Returns URL or false |
| `FILTER_VALIDATE_INT` | Returns int or false |
| `FILTER_VALIDATE_FLOAT` | Returns float or false |
| `FILTER_VALIDATE_IP` | Returns IP or false |
| `FILTER_SANITIZE_NUMBER_INT` | Strips non-numeric characters |
| `FILTER_SANITIZE_EMAIL` | Strips illegal email characters |
| `FILTER_SANITIZE_URL` | Strips illegal URL characters |
| `FILTER_SANITIZE_SPECIAL_CHARS` | Converts `<`, `>`, `"` to HTML entities |

---

## Regex — Regular Expressions

A regular expression (regex) is a pattern used to search, match, or replace text. It is the most powerful validation tool — it can match any text pattern you can describe.

---

### The Two Main PHP Regex Functions

```php
<?php
  // preg_match — returns 1 if pattern found, 0 if not
  preg_match($pattern, $subject);
  preg_match($pattern, $subject, $matches);   // $matches captures found groups

  // preg_replace — replaces matches with a replacement string
  preg_replace($pattern, $replacement, $subject);
?>
```

---

### Pattern Syntax

A regex pattern in PHP is written as a string between two delimiters — usually `/`:

```php
'/pattern/'
'/pattern/flags'
```

---

### Character Classes

| Pattern | Matches |
|---|---|
| `.` | Any single character except newline |
| `\d` | Any digit: 0–9 |
| `\D` | Any non-digit |
| `\w` | Any word character: a–z, A–Z, 0–9, _ |
| `\W` | Any non-word character |
| `\s` | Any whitespace: space, tab, newline |
| `\S` | Any non-whitespace |
| `[abc]` | Any one of: a, b, or c |
| `[^abc]` | Any character except a, b, c |
| `[a-z]` | Any lowercase letter |
| `[A-Z]` | Any uppercase letter |
| `[0-9]` | Any digit |
| `[a-zA-Z0-9]` | Any letter or digit |

---

### Quantifiers — How Many Times

| Quantifier | Meaning |
|---|---|
| `*` | 0 or more |
| `+` | 1 or more |
| `?` | 0 or 1 (optional) |
| `{3}` | Exactly 3 times |
| `{3,}` | 3 or more times |
| `{3,6}` | Between 3 and 6 times |

---

### Anchors — Position in the String

| Anchor | Meaning |
|---|---|
| `^` | Start of string |
| `$` | End of string |

```php
// ^ and $ together mean the pattern must match the ENTIRE string
'/^[a-z]+$/'   // Must be all lowercase letters, nothing else
```

---

### Flags

| Flag | Meaning |
|---|---|
| `i` | Case insensitive |
| `m` | Multiline — `^` and `$` match start/end of each line |
| `s` | `.` matches newlines too |

```php
'/hello/i'    // Matches "hello", "Hello", "HELLO"
```

---

### Real Validation Examples

```php
<?php

  // Username: 3-16 characters, letters, numbers, underscores only
  function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,16}$/', $username);
  }

  var_dump(validateUsername("Ahmed_99"));   // true
  var_dump(validateUsername("a b"));        // false — space not allowed
  var_dump(validateUsername("ab"));         // false — too short


  // Phone number: optional + then digits, spaces, hyphens — 10 to 15 chars
  function validatePhone($phone) {
    return preg_match('/^\+?[0-9\s\-]{10,15}$/', $phone);
  }

  var_dump(validatePhone("+20 100 000 0000"));   // true
  var_dump(validatePhone("01000000000"));         // true
  var_dump(validatePhone("abc"));                 // false


  // Password: at least 8 chars, must contain letter AND number
  function validatePassword($password) {
    if (strlen($password) < 8) return false;
    if (!preg_match('/[a-zA-Z]/', $password)) return false;  // Must have a letter
    if (!preg_match('/[0-9]/', $password)) return false;     // Must have a number
    return true;
  }

  var_dump(validatePassword("Ahmed123"));   // true
  var_dump(validatePassword("ahmed"));      // false — too short
  var_dump(validatePassword("12345678"));   // false — no letters


  // Only letters and spaces — for names
  function validateName($name) {
    return preg_match('/^[a-zA-Z\s]+$/', trim($name));
  }

  var_dump(validateName("Ahmed Mohamed"));   // true
  var_dump(validateName("Ahmed123"));        // false — numbers not allowed


  // Egyptian mobile: starts with 01 followed by 0, 1, 2, or 5 then 8 digits
  function validateEgyptianPhone($phone) {
    return preg_match('/^01[0125][0-9]{8}$/', $phone);
  }

  var_dump(validateEgyptianPhone("01012345678"));   // true
  var_dump(validateEgyptianPhone("01234567890"));   // false

?>
```

---

### `preg_replace` — Replace Matches

```php
<?php
  // Remove all non-numeric characters from a phone number
  $phone = "+20 (100) 000-0000";
  $clean = preg_replace('/[^0-9]/', '', $phone);
  echo $clean;   // 20100000000

  // Replace multiple spaces with a single space
  $text = "Hello    World    PHP";
  $clean = preg_replace('/\s+/', ' ', $text);
  echo $clean;   // "Hello World PHP"

  // Remove HTML tags
  $html = "<p>Hello <b>World</b></p>";
  $clean = preg_replace('/<[^>]+>/', '', $html);
  echo $clean;   // Hello World
?>
```

---

### `preg_match` with Capture Groups

Parentheses `()` create capture groups — they save the matched part for later use.

```php
<?php
  $date = "2024-03-15";

  if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
    echo "Full match: "  . $matches[0];   // 2024-03-15
    echo "Year: "        . $matches[1];   // 2024
    echo "Month: "       . $matches[2];   // 03
    echo "Day: "         . $matches[3];   // 15
  }
?>
```

---

### Complete Validation File with Regex

```php
<?php
  // validate.php — centralized validation using regex and filter_var

  function validate($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  function validateUsername($username) {
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username);
  }

  function validatePassword($password) {
    // Min 8 chars, at least one letter, one number
    return preg_match('/^(?=.*[a-zA-Z])(?=.*\d).{8,}$/', $password);
  }

  function validatePhone($phone) {
    return preg_match('/^\+?[0-9\s\-]{10,15}$/', $phone);
  }

  function validateAge($age) {
    return filter_var($age, FILTER_VALIDATE_INT, [
      'options' => ['min_range' => 1, 'max_range' => 120]
    ]);
  }
?>
```

---

## Quick Reference

| Concept | Key Point |
|---|---|
| Syntax error | Script does not run at all — fix first |
| Fatal error | Script stops at that line |
| Warning | Script continues — missing file, wrong argument |
| Notice | Script continues — undefined variable |
| Deprecated | Feature works now but will be removed — update your code |
| `echo` vs `print` | Use `echo` — `print` returns 1 but is otherwise the same |
| Double quotes | Variables expanded: `"Hello $name"` works |
| Single quotes | Everything literal: `'Hello $name'` prints `$` |
| `\n` in browser | Use `<br>` for visible line breaks in browser output |
| `include` | Warning if file missing — script continues |
| `require` | Fatal error if file missing — script stops |
| `include_once` / `require_once` | Prevents double inclusion — use for functions/classes |
| `readfile()` | Read and output a file in one line |
| `fopen($f, 'r')` | Open for reading — file must exist |
| `fopen($f, 'w')` | Open for writing — overwrites existing content |
| `fopen($f, 'a')` | Open for appending — adds to end, preserves content |
| `fread($f, n)` | Read n bytes from open file |
| `fgets($file)` | Read one line at a time |
| `fwrite($f, $str)` | Write string to open file |
| `fclose($file)` | Always close after opening |
| `feof($file)` | True when end of file is reached |
| `file_get_contents()` | Read entire file into string — no fopen needed |
| `file_put_contents()` | Write to file — no fopen needed |
| `filter_input()` | Read from superglobal and filter in one step |
| `preg_match($p, $s)` | Returns 1 if pattern matches, 0 if not |
| `preg_replace($p, $r, $s)` | Replace pattern matches with replacement |
| `^` and `$` anchors | Match start and end — validates the ENTIRE string |
| `\d` | Any digit |
| `\w` | Any word character (letter, digit, underscore) |
| `{3,16}` | Between 3 and 16 occurrences |
| `/i` flag | Case insensitive matching |





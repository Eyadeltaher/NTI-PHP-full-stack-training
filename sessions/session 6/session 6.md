# PHP — Syntax & Core Concepts

---

## PHP Tags

PHP code lives inside special tags. The server reads the file, processes everything inside the tags, and sends only the output to the browser. The user never sees PHP code.

```php
<?php
  // your PHP code here
?>
```

You can mix PHP and HTML freely in the same file:

```php
<!DOCTYPE html>
<html>
<body>
  <h1>Welcome</h1>
  <?php echo "This line came from PHP"; ?>
  <p>This is plain HTML again</p>
</body>
</html>
```

Short echo tag — shorthand for `<?php echo ?>`:

```php
<?= "Hello" ?>
<!-- Exactly the same as: <?php echo "Hello"; ?> -->
```

> Every PHP statement must end with a semicolon `;` — forgetting it is one of the most common errors.

> If a file contains only PHP with no HTML, you can omit the closing `?>` tag. This is actually recommended because a stray space or newline after `?>` can cause bugs with headers.

```php
<?php
  // Pure PHP file — no closing tag needed
  $name = "Ahmed";
  echo $name;
```

---

## Variables

Variables in PHP start with a `$` sign. PHP is dynamically typed — you do not declare the type, PHP figures it out from what you assign.

```php
<?php
  $name    = "Ahmed";     // String
  $age     = 25;          // Integer
  $price   = 19.99;       // Float
  $active  = true;        // Boolean
  $nothing = null;        // Null

  echo $name;   // Ahmed
  echo $age;    // 25
?>
```

### What Makes PHP Different

In most languages like JavaScript you write `let name = "Ahmed"` or in Python just `name = "Ahmed"`. In PHP the `$` is always required — on every variable, every time you use it.

```php
$name = "Ahmed";
echo $name;        // $ required both when assigning and when using
```

### Naming Rules

- Must start with `$` followed by a letter or underscore — never a number
- Case sensitive: `$name` and `$Name` are two completely different variables
- Convention: use `camelCase` or `snake_case`

```php
$firstName = "Ahmed";    // camelCase
$first_name = "Ahmed";   // snake_case — both valid, be consistent
```

### Variable Variables — A PHP-Specific Feature

PHP lets you use the value of a variable as the name of another variable:

```php
$varName = "city";
$$varName = "Cairo";   // Creates a variable called $city

echo $city;     // Cairo
echo $$varName; // Cairo — both access the same thing
```

> This is unique to PHP and rarely used in practice, but good to know it exists.

---

## Data Types

| Type | Example | Notes |
|---|---|---|
| String | `"Hello"`, `'Hello'` | Double or single quotes |
| Integer | `42`, `-7` | Whole numbers only |
| Float | `3.14`, `-0.5` | Decimal numbers |
| Boolean | `true`, `false` | Case insensitive — `TRUE` works too |
| Null | `null` | Represents no value |
| Array | `[1, 2, 3]` | Ordered list |
| Object | `new ClassName()` | Instance of a class |

### Double Quotes vs Single Quotes — A Key PHP Difference

This is one of the most important PHP-specific behaviors.

**Double quotes** parse variables and escape sequences inside them:

```php
$name = "Ahmed";
echo "Hello, $name";       // Hello, Ahmed  ← variable is replaced
echo "Hello, {$name}s";    // Hello, Ahmeds ← curly braces for complex expressions
echo "Line one\nLine two"; // \n becomes a real newline
```

**Single quotes** treat everything as a literal string — no variable parsing, no escape sequences:

```php
$name = "Ahmed";
echo 'Hello, $name';       // Hello, $name  ← $ is printed literally
echo 'Line one\nLine two'; // Line one\nLine two  ← \n is NOT a newline
```

> Use double quotes when you need variables inside the string. Use single quotes for plain text where you want no processing — it is very slightly faster because PHP does not scan the string.

---

## Casting — Type Conversion

Casting forces a variable to be treated as a specific type.

```php
<?php
  $value = "42";             // String

  $asInt   = (int) $value;   // → 42
  $asFloat = (float) $value; // → 42.0
  $asBool  = (bool) $value;  // → true
  $asStr   = (string) 100;   // → "100"

  var_dump($asInt);    // int(42)
  var_dump($asFloat);  // float(42)
  var_dump($asBool);   // bool(true)
?>
```

`var_dump()` is more useful than `echo` for debugging because it shows the type and value together.

### PHP's Automatic Type Juggling

PHP converts types automatically in operations — this is unique to PHP and can cause surprising behavior:

```php
<?php
  echo "5" + 3;          // 8  — PHP converts "5" to integer
  echo "5 apples" + 3;   // 8  — PHP reads the number at the start
  echo "apples" + 3;     // 3  — no number found, string becomes 0
  echo true + true;      // 2  — booleans become 1 and 1
  echo false + 5;        // 5  — false becomes 0
?>
```

> PHP's type juggling is helpful but can cause hard-to-find bugs. When you need a specific type, cast explicitly. Never rely on PHP to figure it out in critical logic.

### Checking Types

```php
<?php
  $value = 42;

  var_dump(is_int($value));     // bool(true)
  var_dump(is_string($value));  // bool(false)
  var_dump(is_float($value));   // bool(false)
  var_dump(is_bool($value));    // bool(false)
  var_dump(is_null($value));    // bool(false)
  var_dump(is_array($value));   // bool(false)

  echo gettype($value);         // integer
?>
```

---

## Comments

```php
<?php
  // Single line comment

  # Single line comment (alternative — less common)

  /*
    Multi-line comment
    spans multiple lines
  */

  /**
   * DocBlock comment — used to document functions and classes
   * @param string $name The user's name
   * @return string A greeting message
   */
  function greet($name) {
    return "Hello, $name";
  }
?>
```

> `//` is the standard for single-line comments in PHP. The `#` style works but is rarely used. DocBlock comments (`/** */`) are used by IDEs and documentation generators to understand your code.

---

## Operators

### Arithmetic Operators

```php
<?php
  $a = 10;
  $b = 3;

  echo $a + $b;    // 13  — Addition
  echo $a - $b;    // 7   — Subtraction
  echo $a * $b;    // 30  — Multiplication
  echo $a / $b;    // 3.333... — Division (always returns float if not exact)
  echo $a % $b;    // 1   — Modulus: remainder of 10 divided by 3
  echo $a ** $b;   // 1000 — Exponentiation: 10 to the power of 3
?>
```

> `**` for exponentiation was added in PHP 5.6. Before that you needed `pow($a, $b)`.

---

### Comparison Operators

```php
<?php
  $a = 10;
  $b = "10";

  var_dump($a == $b);   // true  — equal value (type is ignored)
  var_dump($a === $b);  // false — identical: value AND type must match
  var_dump($a != $b);   // false — not equal (loose)
  var_dump($a !== $b);  // true  — not identical

  var_dump($a > 5);     // true
  var_dump($a < 5);     // false
  var_dump($a >= 10);   // true
  var_dump($a <= 10);   // true
?>
```

### == vs === — The Most Important PHP Rule

```php
<?php
  var_dump(0 == "hello");   // true  in PHP — "hello" converts to 0
  var_dump(0 === "hello");  // false — types are different

  var_dump(1 == "1");       // true
  var_dump(1 === "1");      // false — int vs string

  var_dump(null == false);  // true
  var_dump(null === false); // false
?>
```

> `==` compares only value after converting types. This causes unexpected bugs — `0 == "hello"` being `true` is a well-known PHP trap. Always use `===` unless you specifically need loose comparison.

### Spaceship Operator `<=>` — PHP 7+

Returns -1, 0, or 1 depending on whether left is less than, equal to, or greater than right. Used for sorting.

```php
<?php
  echo (1 <=> 2);   // -1  (1 is less than 2)
  echo (2 <=> 2);   //  0  (equal)
  echo (3 <=> 2);   //  1  (3 is greater than 2)
?>
```

### Null Coalescing Operator `??` — PHP 7+

Returns the left side if it exists and is not null, otherwise returns the right side.

```php
<?php
  $username = $_GET['user'] ?? "Guest";
  // If $_GET['user'] exists and is not null → use it
  // Otherwise → use "Guest"

  // Chaining
  $value = $a ?? $b ?? $c ?? "default";
?>
```

> This replaces the old pattern of `isset($x) ? $x : "default"` which was verbose and repeated the variable name. `??` is cleaner and safer.

---

### String Operators

```php
<?php
  $first = "Hello";
  $last  = " World";

  echo $first . $last;      // Hello World — dot concatenates strings

  $message = "Hello";
  $message .= " World";     // Append: same as $message = $message . " World"
  echo $message;            // Hello World
?>
```

> PHP uses `.` for string concatenation — not `+`. This is different from JavaScript. Using `+` on strings in PHP tries to convert them to numbers and adds them arithmetically.

---

### Logical Operators

```php
<?php
  $loggedIn = true;
  $isAdmin  = false;

  var_dump($loggedIn && $isAdmin);  // false — AND
  var_dump($loggedIn || $isAdmin);  // true  — OR
  var_dump(!$loggedIn);             // false — NOT

  // Word versions — same logic, lower precedence
  var_dump($loggedIn and $isAdmin); // false
  var_dump($loggedIn or $isAdmin);  // true
  var_dump($loggedIn xor $isAdmin); // true — XOR: true only if exactly one is true
?>
```

> `&&` and `and` do the same thing but `&&` has higher precedence. In complex expressions, use `&&` and `||` to avoid precedence surprises.

---

### Assignment Operators

```php
<?php
  $x = 10;

  $x += 5;   // $x = $x + 5  → 15
  $x -= 3;   // $x = $x - 3  → 12
  $x *= 2;   // $x = $x * 2  → 24
  $x /= 4;   // $x = $x / 4  → 6
  $x %= 4;   // $x = $x % 4  → 2
  $x **= 3;  // $x = $x ** 3 → 8
  $x .= "!"; // $x = $x . "!"→ "8!"
?>
```

---

## Increment and Decrement

```php
<?php
  $a = 5;

  echo $a++;   // 5  — post-increment: returns value THEN adds 1
  echo $a;     // 6  — now it is 6

  echo ++$a;   // 7  — pre-increment: adds 1 THEN returns value
  echo $a;     // 7

  echo $a--;   // 7  — post-decrement: returns value THEN subtracts 1
  echo $a;     // 6

  echo --$a;   // 5  — pre-decrement: subtracts 1 THEN returns value
?>
```

> The position of `++` or `--` matters when the expression is used as a value. If you are just incrementing on its own line (`$i++;`), the position makes no difference.

### PHP-Specific Increment Behavior on Strings

PHP allows incrementing strings — a feature almost no other language has:

```php
<?php
  $char = "a";
  $char++;
  echo $char;   // b

  $char = "z";
  $char++;
  echo $char;   // aa

  $char = "A9";
  $char++;
  echo $char;   // B0

  $char = "Az";
  $char++;
  echo $char;   // Ba
?>
```

> Decrement on strings does nothing in PHP — only increment works on them.

---

## If Statements

```php
<?php
  $age = 20;

  if ($age >= 18) {
    echo "Adult";
  } elseif ($age >= 13) {
    echo "Teenager";
  } else {
    echo "Child";
  }
?>
```

> PHP uses `elseif` (one word) — though `else if` (two words) also works inside curly-brace syntax.

### Ternary Operator

```php
<?php
  $age = 20;
  $status = ($age >= 18) ? "Adult" : "Minor";
  echo $status;   // Adult
?>
```

### Short Ternary (Elvis Operator) — PHP 5.3+

```php
<?php
  $name = "" ?: "Anonymous";
  // If left side is falsy → use right side
  // "" is falsy, so $name = "Anonymous"

  $name = "Ahmed" ?: "Anonymous";
  // "Ahmed" is truthy, so $name = "Ahmed"
?>
```

---

## Switch

Switch compares one value against multiple possible cases. Cleaner than many `elseif` blocks when testing the same variable.

```php
<?php
  $day = "Monday";

  switch ($day) {
    case "Monday":
      echo "Start of the work week";
      break;
    case "Friday":
      echo "End of the work week";
      break;
    case "Saturday":
    case "Sunday":                // Two cases, same result
      echo "Weekend";
      break;
    default:
      echo "Midweek";
  }
?>
```

### The `break` is Required

Without `break`, PHP **falls through** to the next case and executes it too — even if the condition does not match:

```php
<?php
  $x = 1;

  switch ($x) {
    case 1:
      echo "One";
      // No break — falls through!
    case 2:
      echo "Two";   // This also runs when $x = 1
      break;
    case 3:
      echo "Three";
      break;
  }
  // Output: OneTwo
?>
```

> Fall-through is sometimes intentional (grouping cases), but usually a bug. Always add `break` unless you explicitly want fall-through.

### Match Expression — PHP 8+ (Better Switch)

`match` is the modern replacement for `switch`. It uses strict comparison (`===`), requires no `break`, and returns a value.

```php
<?php
  $status = 2;

  $label = match($status) {
    1       => "Active",
    2       => "Inactive",
    3       => "Banned",
    default => "Unknown",
  };

  echo $label;  // Inactive
?>
```

Key differences from `switch`:
- Uses `===` strict comparison (switch uses `==` loose)
- No `break` needed — no fall-through
- Each arm returns a value
- Throws an error if no match and no default (switch silently does nothing)

---

## Loops

### `while` Loop

Runs as long as the condition is true. Checks condition before each iteration.

```php
<?php
  $i = 1;

  while ($i <= 5) {
    echo $i . " ";
    $i++;
  }
  // Output: 1 2 3 4 5
?>
```

---

### `do...while` Loop

Runs the block first, then checks the condition. Guarantees at least one execution.

```php
<?php
  $i = 10;

  do {
    echo $i . " ";
    $i++;
  } while ($i <= 5);
  // Output: 10 — runs once even though condition is already false
?>
```

> This is different from `while` — `do...while` always executes at least once. Useful when you need to run something once before checking a condition, like showing a menu that should always appear at least once.

---

### `for` Loop

Used when you know how many times to loop.

```php
<?php
  for ($i = 0; $i < 5; $i++) {
    echo $i . " ";
  }
  // Output: 0 1 2 3 4
?>
```

Structure: `for (initializer; condition; increment)`

---

### `foreach` Loop

Designed specifically for arrays. Loops over every element automatically.

```php
<?php
  $colors = ["red", "green", "blue"];

  foreach ($colors as $color) {
    echo $color . " ";
  }
  // Output: red green blue
?>
```

With index:

```php
<?php
  foreach ($colors as $index => $color) {
    echo "$index: $color \n";
  }
  // 0: red
  // 1: green
  // 2: blue
?>
```

---

### `break` and `continue`

`break` — exits the loop immediately:

```php
<?php
  for ($i = 0; $i < 10; $i++) {
    if ($i == 5) break;   // Stop when i reaches 5
    echo $i . " ";
  }
  // Output: 0 1 2 3 4
?>
```

`continue` — skips the current iteration and moves to the next:

```php
<?php
  for ($i = 0; $i < 10; $i++) {
    if ($i % 2 == 0) continue;  // Skip even numbers
    echo $i . " ";
  }
  // Output: 1 3 5 7 9
?>
```

### PHP-Specific: `break` with a Number

PHP lets you break out of multiple nested loops at once by passing a number to `break`:

```php
<?php
  for ($i = 0; $i < 3; $i++) {
    for ($j = 0; $j < 3; $j++) {
      if ($j == 1) break 2;  // Break out of BOTH loops
      echo "$i,$j ";
    }
  }
  // Output: 0,0
?>
```

---

## Arrays

PHP arrays can hold any mix of types. They are ordered, indexed starting from 0.

```php
<?php
  $fruits = ["apple", "banana", "cherry"];

  echo $fruits[0];   // apple
  echo $fruits[1];   // banana
  echo $fruits[2];   // cherry

  // Old syntax (still valid)
  $fruits = array("apple", "banana", "cherry");
?>
```

### Useful Array Functions

```php
<?php
  $nums = [3, 1, 4, 1, 5, 9];

  echo count($nums);           // 6 — number of elements
  echo implode(", ", $nums);   // 3, 1, 4, 1, 5, 9 — join into string

  array_push($nums, 10);       // Add to end
  array_pop($nums);            // Remove from end
  array_unshift($nums, 0);     // Add to beginning
  array_shift($nums);          // Remove from beginning

  sort($nums);                 // Sort ascending
  rsort($nums);                // Sort descending

  echo in_array(4, $nums);     // true/false — check if value exists
  print_r($nums);              // Print array contents (for debugging)
  var_dump($nums);             // Print with types (for debugging)
?>
```

---

## foreach

The right way to loop over arrays in PHP. `for` with index works too, but `foreach` is cleaner and less error-prone.

```php
<?php
  $students = ["Ahmed", "Sara", "Omar"];

  foreach ($students as $student) {
    echo "Hello, $student \n";
  }
?>
```

### Modifying Array Values with `&` (Pass by Reference)

```php
<?php
  $prices = [10, 20, 30];

  foreach ($prices as &$price) {   // & makes $price a reference
    $price *= 2;                   // Modifies the original array
  }
  unset($price);   // Important: break the reference after the loop

  print_r($prices);   // [20, 40, 60]
?>
```

> The `unset($price)` after the loop is important. Without it, `$price` still refers to the last element of the array, and any later code that uses `$price` will unexpectedly modify the array.

---

## Associative Arrays

Associative arrays use named keys (strings) instead of numbered indexes. Like a dictionary or object in other languages.

```php
<?php
  $person = [
    "name"  => "Ahmed",
    "age"   => 25,
    "city"  => "Cairo",
  ];

  echo $person["name"];   // Ahmed
  echo $person["age"];    // 25

  // Add a new key
  $person["email"] = "ahmed@example.com";

  // Modify a value
  $person["age"] = 26;

  // Remove a key
  unset($person["city"]);
?>
```

### Looping Over Associative Arrays

```php
<?php
  $person = ["name" => "Ahmed", "age" => 25, "city" => "Cairo"];

  foreach ($person as $key => $value) {
    echo "$key: $value \n";
  }
  // name: Ahmed
  // age: 25
  // city: Cairo
?>
```

### Useful Associative Array Functions

```php
<?php
  $person = ["name" => "Ahmed", "age" => 25];

  print_r(array_keys($person));     // ["name", "age"]
  print_r(array_values($person));   // ["Ahmed", 25]

  var_dump(array_key_exists("name", $person));  // true
  var_dump(isset($person["name"]));             // true

  // Merge two arrays
  $extra = ["city" => "Cairo"];
  $merged = array_merge($person, $extra);
?>
```

---

## Multidimensional Arrays

An array where each element is itself an array. Used for tables of data, lists of objects.

```php
<?php
  $students = [
    ["name" => "Ahmed", "grade" => 90],
    ["name" => "Sara",  "grade" => 85],
    ["name" => "Omar",  "grade" => 92],
  ];

  // Access a specific value
  echo $students[0]["name"];    // Ahmed
  echo $students[1]["grade"];   // 85
  echo $students[2]["name"];    // Omar
?>
```

### Looping Over a Multidimensional Array

```php
<?php
  foreach ($students as $student) {
    echo $student["name"] . " scored " . $student["grade"] . "\n";
  }
  // Ahmed scored 90
  // Sara scored 85
  // Omar scored 92
?>
```

### Three-Dimensional Array

```php
<?php
  $school = [
    "classA" => [
      ["name" => "Ahmed", "grade" => 90],
      ["name" => "Sara",  "grade" => 85],
    ],
    "classB" => [
      ["name" => "Omar",  "grade" => 78],
    ],
  ];

  echo $school["classA"][0]["name"];   // Ahmed
  echo $school["classB"][0]["grade"];  // 78
?>
```

---

## Pass by Value vs Pass by Reference

This is a critical concept that affects how PHP handles data in functions and loops.

### Pass by Value (Default)

By default, PHP copies the value when you assign a variable or pass it to a function. Changes to the copy do not affect the original.

```php
<?php
  $a = 10;
  $b = $a;    // $b gets a COPY of $a's value

  $b = 20;

  echo $a;    // 10 — original unchanged
  echo $b;    // 20 — only the copy changed
?>
```

```php
<?php
  function addTen($num) {
    $num += 10;          // Modifies the local copy
    echo $num;           // 20
  }

  $value = 10;
  addTen($value);
  echo $value;           // 10 — original unchanged
?>
```

---

### Pass by Reference — Using `&`

Adding `&` before the variable name passes the actual variable — not a copy. Changes inside the function affect the original.

```php
<?php
  function addTen(&$num) {   // & means: pass the actual variable
    $num += 10;
  }

  $value = 10;
  addTen($value);
  echo $value;   // 20 — original was modified
?>
```

```php
<?php
  $a = 10;
  $b = &$a;   // $b is now a reference to $a — same variable, two names

  $b = 20;

  echo $a;    // 20 — $a changed because $b and $a point to the same value
  echo $b;    // 20
?>
```

> Use pass by reference when you need a function to modify the original variable, or when passing a large array that you do not want PHP to copy (for performance). In most cases, returning a value is cleaner than modifying by reference.

---

## Functions

Functions let you group reusable code. In PHP, functions are defined with `function` and called by name.

```php
<?php
  function greet($name) {
    echo "Hello, $name!";
  }

  greet("Ahmed");   // Hello, Ahmed!
  greet("Sara");    // Hello, Sara!
?>
```

### Return Values

```php
<?php
  function add($a, $b) {
    return $a + $b;
  }

  $result = add(3, 7);
  echo $result;   // 10
?>
```

### Default Parameter Values

```php
<?php
  function greet($name, $greeting = "Hello") {
    echo "$greeting, $name!";
  }

  greet("Ahmed");              // Hello, Ahmed!
  greet("Ahmed", "Welcome");   // Welcome, Ahmed!
?>
```

> Default parameters must come after required parameters.

### Type Declarations — PHP 7+

You can declare what type parameters and return values must be:

```php
<?php
  function add(int $a, int $b): int {
    return $a + $b;
  }

  echo add(3, 4);       // 7
  echo add(3.7, 4.2);   // 7 — floats are cast to int automatically

  // Strict mode — at top of file, throw error instead of casting
  declare(strict_types=1);
?>
```

### Variadic Functions — Accept Any Number of Arguments

```php
<?php
  function sum(...$numbers) {
    return array_sum($numbers);
  }

  echo sum(1, 2, 3);         // 6
  echo sum(1, 2, 3, 4, 5);   // 15
?>
```

### Anonymous Functions (Closures)

Functions without a name, assigned to a variable or passed as an argument:

```php
<?php
  $double = function($n) {
    return $n * 2;
  };

  echo $double(5);   // 10

  // Passing a function as an argument
  $nums = [1, 2, 3, 4, 5];
  $doubled = array_map(function($n) { return $n * 2; }, $nums);
  print_r($doubled);   // [2, 4, 6, 8, 10]
?>
```

### Arrow Functions — PHP 7.4+

Shorter syntax for anonymous functions. Automatically captures variables from the outer scope.

```php
<?php
  $double = fn($n) => $n * 2;
  echo $double(5);   // 10

  $multiplier = 3;
  $multiply = fn($n) => $n * $multiplier;  // No need to use 'use' — auto-captured
  echo $multiply(5);   // 15
?>
```

---

## Variable Scope

Scope is the part of the program where a variable is accessible. PHP has strict scope rules that are different from many other languages.

### Global Scope

Variables declared outside any function are global — but unlike JavaScript, they are NOT automatically accessible inside functions:

```php
<?php
  $message = "Hello";

  function showMessage() {
    echo $message;   // Error! $message is not accessible here
  }

  showMessage();
?>
```

> This is one of the most surprising PHP behaviors for beginners coming from JavaScript. In JavaScript, inner functions can access outer variables. In PHP, they cannot.

### The `global` Keyword

To access a global variable inside a function, you must explicitly declare it with `global`:

```php
<?php
  $message = "Hello";

  function showMessage() {
    global $message;       // Now it is accessible
    echo $message;         // Hello
    $message = "Changed";  // Also changes the global variable
  }

  showMessage();
  echo $message;   // Changed
?>
```

### `$GLOBALS` Array

PHP stores all global variables in the `$GLOBALS` superglobal array. You can access them without the `global` keyword:

```php
<?php
  $count = 10;

  function addFive() {
    $GLOBALS["count"] += 5;  // Access global without 'global' keyword
  }

  addFive();
  echo $count;   // 15
?>
```

### Local Scope

Variables created inside a function exist only inside that function. They are destroyed when the function ends.

```php
<?php
  function calculate() {
    $result = 100;
    echo $result;   // 100
  }

  calculate();
  echo $result;   // Error! $result does not exist here
?>
```

### Static Variables

A local variable that **keeps its value** between function calls. It is initialized only the first time the function runs.

```php
<?php
  function counter() {
    static $count = 0;   // Initialized once, then preserved
    $count++;
    echo $count . "\n";
  }

  counter();   // 1
  counter();   // 2
  counter();   // 3
?>
```

> Static variables are useful for counters, caching, or tracking how many times a function has been called — without using a global variable.

---

## Constants

Constants are values that never change. Once defined, they cannot be modified or undefined.

```php
<?php
  define("MAX_SIZE", 100);
  define("SITE_NAME", "My Website");

  echo MAX_SIZE;    // 100
  echo SITE_NAME;   // My Website
?>
```

### What Makes Constants Different from Variables

- No `$` sign — `MAX_SIZE` not `$MAX_SIZE`
- Cannot be changed after definition
- Accessible everywhere — no scope rules, no `global` keyword needed
- By convention, written in ALL_CAPS with underscores

```php
<?php
  define("DB_HOST", "localhost");

  function connectDB() {
    echo DB_HOST;   // Works directly — no 'global' needed
  }

  connectDB();   // localhost
?>
```

### `const` Keyword — PHP 5.3+

An alternative to `define()`. Written outside functions at the top level, or inside classes.

```php
<?php
  const MAX_SIZE = 100;
  const SITE_NAME = "My Website";

  echo MAX_SIZE;   // 100
?>
```

### `define()` vs `const`

| | `define()` | `const` |
|---|---|---|
| Where | Can be used inside functions and conditionals | Must be at top level or inside a class |
| Runtime | Defined at runtime | Defined at compile time |
| Dynamic names | `define($name, $value)` — name can be a variable | Name must be a literal string |
| Arrays | Supports array values (PHP 7+) | Supports array values |

```php
<?php
  // define() can be conditional
  if ($debug) {
    define("LOG_LEVEL", "verbose");
  } else {
    define("LOG_LEVEL", "minimal");
  }

  // const cannot be conditional
  // const LOG_LEVEL = "verbose"; // This must be at top level
?>
```

### PHP Built-in Constants

PHP provides many ready-made constants:

```php
<?php
  echo PHP_VERSION;    // Current PHP version (e.g., 8.2.0)
  echo PHP_INT_MAX;    // Largest integer PHP can hold
  echo PHP_EOL;        // End of line character for the current OS
  echo PHP_OS;         // Operating system name
  echo DIRECTORY_SEPARATOR; // / on Linux/Mac, \ on Windows
  echo true;           // 1
  echo false;          // (empty string)
  echo null;           // (empty string)
  echo M_PI;           // 3.14159265...
?>
```


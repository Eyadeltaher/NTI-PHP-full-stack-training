# PHP OOP — Final, Namespace, PDO, MySQLi OOP & SQL Injection Prevention

---

## `final` — Preventing Extension or Override

The `final` keyword locks a class or method so it cannot be changed by child classes.

### Final Class — Cannot Be Extended

```php
<?php
  final class PaymentProcessor {
    public function process(float $amount): void {
      echo "Processing $amount";
    }
  }

  // class CustomProcessor extends PaymentProcessor {} 
  // Fatal Error — cannot extend a final class
?>
```

Real situation: A security-critical class like an authentication handler or encryption utility. You mark it `final` so no developer can accidentally extend it and override its security logic.

### Final Method — Cannot Be Overridden

The class can be extended but this specific method cannot be changed:

```php
<?php
  class Animal {
    public function eat(): void {
      echo "eating";
    }

    final public function breathe(): void {
      echo "breathing oxygen"; // This behavior must never change
    }
  }

  class Dog extends Animal {
    public function eat(): void {
      echo "eating dog food"; // Allowed — eat() is not final
    }

    // public function breathe(): void {} 
    // Fatal Error — breathe() is final
  }
?>
```

> Use `final` on classes when a class represents a complete, closed concept that should never be changed. Use `final` on methods when a specific behavior must stay consistent across all child classes regardless of what else they override.

---

## Namespace — Organizing Code and Avoiding Name Conflicts

A namespace is a way to group related classes under a named scope. Without namespaces, if two files both define a class called `User`, PHP throws a fatal error. Namespaces solve this by giving each `User` a unique full name.

### Declaring a Namespace

The namespace declaration must be the very first statement in the file:

```php
<?php
  namespace Admin;

  class User {
    public function isAdmin(): void {
      echo "yes — admin";
    }
  }
?>
```

```php
<?php
  namespace Customer;

  class User {
    public function isCustomer(): void {
      echo "yes — customer";
    }
  }
?>
```

Both files define a class called `User` but they live in different namespaces — `Admin\User` and `Customer\User`. No conflict.

### Using Namespaced Classes

```php
<?php
  require_once 'Admin/User.php';
  require_once 'Customer/User.php';

  use Admin\User;
  use Customer\User as CustomerUser;  // Alias to avoid ambiguity

  $admin    = new User;           // Admin\User
  $customer = new CustomerUser;   // Customer\User

  $admin->isAdmin();       // yes — admin
  $customer->isCustomer(); // yes — customer
?>
```

### Namespace in a File Structure

```
project/
  Admin/
    User.php          namespace Admin;    class User {}
  Customer/
    User.php          namespace Customer; class User {}
  Session/
    Session.php       namespace Session;  class Session {}
  index.php
```

From the training code:

```php
<?php
  // Session/Session.php
  namespace Session;

  class Session {
    public function __construct() {
      session_start();
    }

    public function setSession(string $key, mixed $value): void {
      $_SESSION[$key] = $value;
    }

    public function getSession(string $key): mixed {
      return $_SESSION[$key];
    }

    public function removeSession(string $key): void {
      unset($_SESSION[$key]);
    }

    public function destroySession(): void {
      session_destroy();
    }
  }
?>
```

Using it from another file:

```php
<?php
  require_once 'Session/Session.php';
  use Session\Session;

  $session = new Session;
  $session->setSession('username', 'Ahmed');
  echo $session->getSession('username');   // Ahmed
  $session->destroySession();
?>
```

### Sub-Namespaces

Namespaces can be nested like directory paths:

```php
<?php
  namespace App\Models\Database;

  class Connection {}
?>
```

```php
<?php
  use App\Models\Database\Connection;
  $conn = new Connection;
?>
```

---

## Three Ways to Connect to MySQL in PHP

PHP offers three approaches. All three are valid but PDO is the modern standard.

### Method 1 — MySQLi Procedural (Functions)

```php
<?php
  $connection = mysqli_connect('localhost', 'root', '', 'nti11');
  $query      = "SELECT * FROM customers";
  $result     = mysqli_query($connection, $query);
  $data       = mysqli_fetch_all($result, MYSQLI_ASSOC);
  print_r($data);
?>
```

### Method 2 — MySQLi Object-Oriented

```php
<?php
  $connection = new mysqli('localhost', 'root', '', 'nti11');
  $query      = "SELECT * FROM customers";
  $result     = $connection->query($query);
  $data       = $result->fetch_all(MYSQLI_ASSOC);
  print_r($data);
?>
```

### Method 3 — PDO (PHP Data Objects)

```php
<?php
  $pdo    = new PDO("mysql:host=localhost;dbname=nti11", 'root', '');
  $query  = "SELECT * FROM customers";
  $result = $pdo->query($query);
  $data   = $result->fetchAll(PDO::FETCH_ASSOC);
  print_r($data);
?>
```

### Comparison — Which to Use?

|                     | MySQLi Procedural | MySQLi OOP     | PDO                                          |
| ------------------- | ----------------- | -------------- | -------------------------------------------- |
| Style               | Functions         | Object methods | Object methods                               |
| Databases supported | MySQL only        | MySQL only     | 12+ databases (MySQL, PostgreSQL, SQLite...) |
| Prepared statements | Yes               | Yes            | Yes — cleaner syntax                         |
| Named placeholders  | No — `?` only     | No — `?` only  | Yes — `:name` style                          |
| Modern standard     | No                | No             | Yes                                          |

> Use **PDO** for all new projects. It works with any database driver — if you ever switch from MySQL to PostgreSQL, you change the connection string only, not the entire codebase.

---

## PDO — Full Reference

### Connecting

```php
<?php
  // DSN format: "driver:host=hostname;dbname=database_name"
  $pdo = new PDO("mysql:host=localhost;dbname=nti11", 'root', '');

  // Enable exceptions — PDO is silent by default, this makes errors visible
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Return associative arrays by default
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
?>
```

> Always set `ERRMODE_EXCEPTION`. Without it, PDO silently fails and you get no error message — very hard to debug.

### SELECT All Rows

```php
<?php
  $result = $pdo->query("SELECT * FROM customers");
  $data   = $result->fetchAll(PDO::FETCH_ASSOC);
  // fetchAll returns all rows at once as an array of arrays
?>
```

### SELECT One Row

```php
<?php
  $result = $pdo->query("SELECT * FROM customers WHERE id = 5");
  $row    = $result->fetch(PDO::FETCH_ASSOC);
  // fetch returns one row, moves the internal pointer forward
?>
```

### Fetch Modes

| Mode | Returns |
|---|---|
| `PDO::FETCH_ASSOC` | Associative array: `$row['name']` |
| `PDO::FETCH_NUM` | Indexed array: `$row[0]` |
| `PDO::FETCH_BOTH` | Both associative and indexed |
| `PDO::FETCH_OBJ` | Object: `$row->name` |
| `PDO::FETCH_CLASS` | Maps to a class directly |

---

## SQL Injection Prevention — Prepared Statements

### The Problem — Raw Queries Are Dangerous

```php
<?php
  $id    = $_GET['id'];   // User types: 1 OR 1=1
  $query = "SELECT * FROM users WHERE id = $id";
  // Becomes: SELECT * FROM users WHERE id = 1 OR 1=1
  // Returns ALL users — the WHERE condition is bypassed
?>
```

Worse:
```php
  $name  = "'; DROP TABLE users; --";
  $query = "INSERT INTO users (name) VALUES ('$name')";
  // Becomes: INSERT INTO users (name) VALUES (''; DROP TABLE users; --')
  // The DROP TABLE executes and destroys your data
```

### The Fix — Prepared Statements with PDO

PDO supports two placeholder styles:

#### Positional Placeholders — `?`

```php
<?php
  $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
  $stmt->execute([5]);
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
?>
```

#### Named Placeholders — `:name` (Preferred — More Readable)

```php
<?php
  $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = :id");
  $stmt->execute(['id' => 5]);
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
?>
```

### Prepared Statements for All Operations

```php
<?php
  // SELECT
  $stmt = $pdo->prepare("SELECT * FROM customers WHERE country = :country AND city = :city");
  $stmt->execute(['country' => 'Egypt', 'city' => 'Cairo']);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // INSERT
  $stmt = $pdo->prepare("INSERT INTO customers (first_name, last_name, email) VALUES (:first, :last, :email)");
  $stmt->execute([
    'first' => 'Ahmed',
    'last'  => 'Mohamed',
    'email' => 'ahmed@example.com'
  ]);
  $newId = $pdo->lastInsertId();   // Get the auto-generated id

  // UPDATE
  $stmt = $pdo->prepare("UPDATE customers SET first_name = :name WHERE id = :id");
  $stmt->execute(['name' => 'Sara', 'id' => 5]);

  // DELETE
  $stmt = $pdo->prepare("DELETE FROM customers WHERE id = :id");
  $stmt->execute(['id' => 5]);
?>
```

### Alternative — `bindParam()` and `bindValue()`

```php
<?php
  $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");

  // bindParam — binds a variable reference (value is read when execute() is called)
  $name  = 'Ahmed';
  $email = 'ahmed@example.com';
  $stmt->bindParam(':name',  $name);
  $stmt->bindParam(':email', $email);
  $stmt->execute();

  // bindValue — binds the value immediately
  $stmt->bindValue(':name',  'Sara');
  $stmt->bindValue(':email', 'sara@example.com');
  $stmt->execute();
?>
```

| | `bindParam()` | `bindValue()` | `execute([...])` |
|---|---|---|---|
| Binds | A variable reference | The value at that moment | Values in the execute call |
| Use in loops | Best — variable updates each iteration | Rebind needed each iteration | Cleanest in loops |
| Most common | — | — | Yes — simplest syntax |

### SQL Injection Prevention with MySQLi

```php
<?php
  // MySQLi prepared statements use ? placeholders only
  $stmt = $connection->prepare("SELECT * FROM customers WHERE id = ?");
  $stmt->bind_param("i", $id);   // "i" = integer, "s" = string, "d" = double
  $id = 5;
  $stmt->execute();
  $result = $stmt->get_result();
  $data   = $result->fetch_all(MYSQLI_ASSOC);
?>
```

> `"issd"` in `bind_param` means the first value is integer, second is string, third is string, fourth is double. The order must match the `?` positions exactly.

---

## OOP Database Class — Interface + PDO (From Training Code)

The training code builds a database abstraction layer — an interface that defines what operations a database class must support, and a concrete class that implements it with PDO.

### The Interface — The Contract

```php
<?php
  // Database.php
  interface Database {
    public function select($columns, $table, $condition, $operator, $value);
    public function selectAll($columns, $table);
    public function insert($table, $columns, $value);
    public function update($table, $columns, $columnsValue, $condition, $operator, $value);
    public function delete($table, $condition, $operator, $value);
  }
?>
```

> The interface defines the shape — what methods must exist and what parameters they take. Any class that `implements Database` must provide all five methods or PHP throws a fatal error.

### The Implementation — PDO Class

```php
<?php
  // Mysql.php
  require_once 'Database.php';

  class Mysql implements Database {
    private PDO $connection;

    public function __construct(string $dsn, string $username, string $password) {
      $this->connection = new PDO($dsn, $username, $password);
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function selectAll(string $columns, string $table): array {
      $query  = "SELECT $columns FROM $table";
      $result = $this->connection->query($query);
      return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select(string $columns, string $table, string $condition, string $operator, mixed $value): array {
      $query  = "SELECT $columns FROM $table WHERE $condition $operator :value";
      $stmt   = $this->connection->prepare($query);
      $stmt->execute(['value' => $value]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert(string $table, string $columns, mixed $value): bool {
      $query = "INSERT INTO $table ($columns) VALUES (:value)";
      $stmt  = $this->connection->prepare($query);
      return $stmt->execute(['value' => $value]);
    }

    public function update(string $table, string $columns, mixed $columnsValue, string $condition, string $operator, mixed $value): bool {
      $query = "UPDATE $table SET $columns = :colVal WHERE $condition $operator :val";
      $stmt  = $this->connection->prepare($query);
      return $stmt->execute(['colVal' => $columnsValue, 'val' => $value]);
    }

    public function delete(string $table, string $condition, string $operator, mixed $value): bool {
      $query = "DELETE FROM $table WHERE $condition $operator :value";
      $stmt  = $this->connection->prepare($query);
      return $stmt->execute(['value' => $value]);
    }
  }
?>
```

### Using the Database Class

```php
<?php
  $db = new Mysql("mysql:host=localhost;dbname=nti11", 'root', '');

  // Select all
  $all = $db->selectAll('*', 'customers');
  print_r($all);

  // Select one
  $one = $db->select('first_name', 'customers', 'id', '=', 5);
  print_r($one);

  // Insert
  $db->insert('employees', 'name', 'Ahmed');

  // Update
  $db->update('employees', 'name', 'Mohamed', 'id', '=', 13);

  // Delete
  $db->delete('employees', 'id', '=', 13);
?>
```

### Static Database Connection Class

```php
<?php
  class Database {
    private static string $dsn = "mysql:host=localhost;dbname=nti11";

    // Static method — call without creating an object
    public static function getConnection(): PDO {
      return new PDO(self::$dsn, 'root', '');
    }
  }

  // Usage — no need to instantiate
  $pdo = Database::getConnection();
?>
```

> The static approach is useful as a connection factory — anywhere in your code you can get a database connection without carrying a `$pdo` variable around or creating multiple connections.

---

## Overloading — PHP Does Not Have It (Use Default Values Instead)

In languages like Java and C++, you can define the same method multiple times with different parameter signatures — this is called method overloading.

PHP does not support this. Defining the same method name twice causes a fatal error:

```php
<?php
  class Calculator {
    // Fatal Error — cannot redeclare sum()
    public function sum(int $a, int $b): int {
      return $a + $b;
    }

    public function sum(int $a, int $b, int $c): int {  // ← Error
      return $a + $b + $c;
    }
  }
?>
```

### The PHP Solution — Default Parameter Values

```php
<?php
  class Calculator {
    // One method handles all cases with default values
    public function sum(int $a, int $b, int $c = 0, int $d = 0): int {
      return $a + $b + $c + $d;
    }
  }

  $calc = new Calculator;
  echo $calc->sum(5, 10);           // 15 — c and d default to 0
  echo $calc->sum(5, 10, 3);        // 18
  echo $calc->sum(5, 10, 3, 2);     // 20
?>
```

### Using Variadic Parameters for Unknown Number of Arguments

```php
<?php
  class Calculator {
    public function sum(int ...$numbers): int {
      return array_sum($numbers);
    }
  }

  $calc = new Calculator;
  echo $calc->sum(5, 10);            // 15
  echo $calc->sum(5, 10, 3);         // 18
  echo $calc->sum(1, 2, 3, 4, 5);    // 15
?>
```

---

## Method Chaining — Full Working Example

Returning `$this` from each method allows calling multiple methods in sequence on the same object.

```php
<?php
  class Calculator {
    private float $result = 0;

    public function sum(float $a, float $b): static {
      $this->result = $a + $b;
      return $this;
    }

    public function sub(float $a): static {
      $this->result -= $a;
      return $this;
    }

    public function div(float $a): static {
      $this->result /= $a;
      return $this;
    }

    public function multiply(float $a): static {
      $this->result *= $a;
      return $this;
    }

    public function result(): float {
      return $this->result;
    }
  }

  $calc = new Calculator;
  $answer = $calc->sum(50, 60)->sub(50)->multiply(5)->div(2)->result();
  echo $answer;   // 75
?>
```

> Every method except `result()` returns `$this` — the same object. This lets you keep calling methods on it without storing intermediate variables. `result()` is the terminal method that ends the chain and returns the final value.

---

## OOP Session Class — Wrapping Superglobals

Wrapping PHP superglobals in a class gives you a clean, reusable, testable interface:

```php
<?php
  namespace Session;

  class Session {
    public function __construct() {
      session_start();
    }

    public function set(string $key, mixed $value): void {
      $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed {
      return $_SESSION[$key] ?? null;
    }

    public function remove(string $key): void {
      unset($_SESSION[$key]);
    }

    public function has(string $key): bool {
      return isset($_SESSION[$key]);
    }

    public function destroy(): void {
      session_destroy();
    }
  }
?>
```

Usage:

```php
<?php
  require_once 'Session/Session.php';
  use Session\Session;

  $session = new Session;
  $session->set('username', 'Ahmed');

  if ($session->has('username')) {
    echo $session->get('username');   // Ahmed
  }

  $session->remove('username');
  $session->destroy();
?>
```

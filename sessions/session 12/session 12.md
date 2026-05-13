# PHP — APIs, REST, Postman & Object-Oriented Programming

---

## What is an API?

An API (Application Programming Interface) is a way for two systems to talk to each other. In web development, a web API is a URL that your server exposes — instead of returning HTML for a browser, it returns data (usually JSON) that another application can use.

```
Browser request  →  Server returns HTML  →  Browser renders a page
API request      →  Server returns JSON  →  App uses the data
```

Real situations:
- A mobile app fetches user data from your PHP backend
- A React frontend loads products from a PHP API
- Another company's system sends orders to your API
- Postman tests your API during development before a frontend exists

---

## REST — The Standard for Web APIs

REST (Representational State Transfer) is a set of conventions for designing APIs. A REST API uses HTTP methods to indicate what operation you want to perform, and URLs to indicate what resource you are working with.

### HTTP Methods and Their Meaning

| Method | Purpose | SQL Equivalent |
|---|---|---|
| `GET` | Read data — retrieve a resource | `SELECT` |
| `POST` | Create data — add a new resource | `INSERT` |
| `PUT` | Update data — replace an entire resource | `UPDATE` (all fields) |
| `PATCH` | Update data — modify specific fields only | `UPDATE` (partial) |
| `DELETE` | Delete a resource | `DELETE` |

### PUT vs PATCH

```
PUT   → Send ALL fields. Replaces the entire record.
        If you omit a field, it becomes NULL or default.

PATCH → Send ONLY the fields you want to change.
        Other fields remain untouched.
```

Real situation: Updating a user's email only.
- `PUT` — you must send name, email, age, gender, salary... everything
- `PATCH` — you send only email

### REST URL Conventions

```
GET    /users          → Get all users
GET    /users/5        → Get user with id 5
POST   /users          → Create a new user
PUT    /users/5        → Replace user with id 5
PATCH  /users/5        → Partially update user with id 5
DELETE /users/5        → Delete user with id 5
```

---

## JSON — The Language of APIs

JSON (JavaScript Object Notation) is the standard data format for APIs. PHP converts arrays to JSON for output and converts JSON back to arrays for input.

```php
<?php
  // Array to JSON — for sending data out
  $data = ['name' => 'Ahmed', 'age' => 25];
  echo json_encode($data);
  // Output: {"name":"Ahmed","age":25}

  // JSON to Array — for receiving data in
  $json = '{"name":"Ahmed","age":25}';
  $data = json_decode($json, true);   // true = associative array
  echo $data['name'];   // Ahmed
?>
```

### Always Set the Content-Type Header

```php
<?php
  header('Content-Type: application/json');
  // Tells the client: "what I am sending back is JSON"
  // Must be set before any echo output
?>
```

---

## Postman — Testing APIs

Postman is a desktop application for sending HTTP requests to your API and inspecting the responses. It replaces the need for a frontend during development — you test your API endpoints directly.

### How to Use Postman

1. Open Postman and create a new request
2. Choose the HTTP method (GET, POST, PUT, DELETE)
3. Enter the URL: `http://localhost/project/selectAll.php`
4. For POST/PUT: go to the Body tab → select `form-data` or `raw JSON`
5. Add your parameters or JSON body
6. Click Send
7. The response appears in the bottom panel with status code and body

### Sending Different Data Types in Postman

```
GET request:
  Params tab → add key-value pairs → appended to URL as query string
  e.g. ?id=5

POST with form fields:
  Body tab → form-data → add field names and values
  Matches $_POST in PHP

POST with raw JSON:
  Body tab → raw → JSON → type JSON object
  PHP reads this with: json_decode(file_get_contents('php://input'), true)

PUT request:
  Body tab → form-data or x-www-form-urlencoded
  PHP reads this with: parse_str(file_get_contents('php://input'), $_PUT)
```

### HTTP Status Codes in API Responses

A proper API always returns the correct status code so the client knows what happened:

| Code | Meaning | Use When |
|---|---|---|
| `200 OK` | Success | GET, PUT, PATCH succeeded |
| `201 Created` | New resource created | POST succeeded |
| `400 Bad Request` | Client sent bad data | Validation failed |
| `401 Unauthorized` | Not authenticated | No token / wrong token |
| `403 Forbidden` | Authenticated but not allowed | No permission |
| `404 Not Found` | Resource does not exist | ID not in database |
| `405 Method Not Allowed` | Wrong HTTP method | POST to a GET-only endpoint |
| `500 Internal Server Error` | Server broke | Unhandled database error |

```php
<?php
  header('Content-Type: application/json');
  http_response_code(404);
  echo json_encode(['message' => 'Record not found']);
?>
```

---

## The Four API Files — Full Explained

### selectAll.php — GET All Records

```php
<?php
  require('db_connection.php');
  header('Content-Type: application/json');

  $query  = "SELECT * FROM MOCK_DATA";
  $result = mysqli_query($connection, $query);
  $data   = mysqli_fetch_all($result, MYSQLI_ASSOC);

  echo json_encode($data);
  // Returns: [{"id":1,"first_name":"Ahmed",...}, {"id":2,...}, ...]
?>
```

---

### selectOne.php — GET a Single Record by ID

```php
<?php
  require('db_connection.php');
  header('Content-Type: application/json');

  // 1. Check that id was provided
  if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['message' => 'id is required']);
    exit();
  }

  $id = $_GET['id'];

  // 2. Check that id is numeric
  if (!is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['message' => 'id must be a number']);
    exit();
  }

  // 3. Query the database
  $query  = "SELECT * FROM MOCK_DATA WHERE id = $id";
  $result = mysqli_query($connection, $query);

  // 4. Check that a record was found
  if (mysqli_num_rows($result) == 0) {
    http_response_code(404);
    echo json_encode(['message' => 'no record found with this id']);
    exit();
  }

  // 5. Return the record
  $data = mysqli_fetch_assoc($result);
  echo json_encode($data);
?>
```

> The pattern: validate input → query → check result → return. Every step has its own response if something is wrong. The client always knows exactly what happened and why.

---

### insert.php — POST: Create a New Record

```php
<?php
  require('db_connection.php');
  header('Content-Type: application/json');

  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate each field
    if (empty($_POST['first_name'])) {
      $errors[] = "First Name is required";
    } else {
      $first_name = trim($_POST['first_name']);
      if (strlen($first_name) < 2)                           $errors[] = "First Name must be at least 2 characters";
      elseif (strlen($first_name) > 50)                      $errors[] = "First Name must not exceed 50 characters";
      elseif (!preg_match("/^[a-zA-Z\s]+$/", $first_name))   $errors[] = "First Name can only contain letters and spaces";
      $first_name = mysqli_real_escape_string($connection, $first_name);
    }

    if (empty($_POST['email'])) {
      $errors[] = "Email is required";
    } else {
      $email = trim($_POST['email']);
      if (!filter_var($email, FILTER_VALIDATE_EMAIL))   $errors[] = "Invalid email format";
      $email = mysqli_real_escape_string($connection, $email);
    }

    // Insert if no errors
    if (empty($errors)) {
      $query  = "INSERT INTO MOCK_DATA (first_name, email) VALUES ('$first_name', '$email')";
      $result = mysqli_query($connection, $query);

      if ($result) {
        http_response_code(201);
        echo json_encode(['message' => 'Record inserted successfully']);
      } else {
        http_response_code(500);
        echo json_encode(['errors' => ['Database error: ' . mysqli_error($connection)]]);
      }

    } else {
      http_response_code(400);
      echo json_encode(['errors' => $errors]);
    }

  } else {
    http_response_code(405);
    echo json_encode(['message' => 'Invalid request method']);
  }
?>
```

---

### delete.php — DELETE a Record

```php
<?php
  require('db_connection.php');
  header('Content-Type: application/json');

  if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['errors' => ['No ID provided']]);
    exit();
  }

  $id     = mysqli_real_escape_string($connection, $_GET['id']);
  $query  = "DELETE FROM MOCK_DATA WHERE id = $id";
  $result = mysqli_query($connection, $query);

  if ($result) {
    echo json_encode(['message' => 'Record deleted successfully']);
  } else {
    http_response_code(500);
    echo json_encode(['errors' => ['Error deleting record: ' . mysqli_error($connection)]]);
  }
?>
```

---

### update.php — PUT: Update a Record

PHP does not automatically parse PUT request data into a superglobal like it does for POST. You have to read and parse the raw request body manually.

```php
<?php
  require('db_connection.php');
  header('Content-Type: application/json');

  // Read and parse PUT data manually
  $putData = [];
  parse_str(file_get_contents('php://input'), $putData);
  // php://input = the raw request body
  // parse_str = converts URL-encoded string to an array

  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] == 'PUT') {

    $id         = $putData['id']         ?? null;
    $first_name = $putData['first_name'] ?? null;
    $last_name  = $putData['last_name']  ?? null;
    $email      = $putData['email']      ?? null;
    $salary     = $putData['salary']     ?? null;

    // Validate...
    if (empty($id) || !is_numeric($id))        $errors[] = "Valid ID is required";
    if (empty($first_name))                    $errors[] = "First Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
                                               $errors[] = "Valid email is required";

    if (empty($errors)) {
      $first_name = mysqli_real_escape_string($connection, trim($first_name));
      $last_name  = mysqli_real_escape_string($connection, trim($last_name));
      $email      = mysqli_real_escape_string($connection, trim($email));
      $salary     = mysqli_real_escape_string($connection, $salary);

      $query  = "UPDATE MOCK_DATA SET first_name='$first_name', last_name='$last_name', email='$email', salary='$salary' WHERE id=$id";
      $result = mysqli_query($connection, $query);

      if ($result) {
        echo json_encode(['message' => 'Record updated successfully']);
      } else {
        http_response_code(500);
        echo json_encode(['errors' => ['Error: ' . mysqli_error($connection)]]);
      }
    } else {
      http_response_code(400);
      echo json_encode(['errors' => $errors]);
    }

  } else {
    http_response_code(405);
    echo json_encode(['message' => 'Invalid request method']);
  }
?>
```

---

### Receiving JSON Body (Not Form Data)

When the client sends JSON in the request body (common with React, mobile apps, other APIs):

```php
<?php
  // Read the raw body and decode it
  $input = json_decode(file_get_contents('php://input'), true);
  // true = return associative array instead of object

  $first_name = $input['first_name'] ?? null;
  $email      = $input['email']      ?? null;
?>
```

> Use `file_get_contents('php://input')` for any request method that is not GET or POST with a form. PUT, PATCH, and JSON-body POST all need this.

---

## `mysqli_real_escape_string()` vs Prepared Statements

The training code uses `mysqli_real_escape_string()` as a security measure. It escapes special characters so they cannot break out of the SQL string.

```php
// Without escaping — dangerous
$name  = "O'Brien";
$query = "SELECT * FROM users WHERE name = '$name'";
// Becomes: SELECT * FROM users WHERE name = 'O'Brien' — SQL error or injection

// With escaping
$name  = mysqli_real_escape_string($connection, "O'Brien");
$query = "SELECT * FROM users WHERE name = '$name'";
// Becomes: SELECT * FROM users WHERE name = 'O\'Brien' — safe
```

`mysqli_real_escape_string()` is better than nothing but prepared statements are the correct modern approach. The function can be bypassed with certain character encodings. Prepared statements cannot be bypassed at all.

---

## Object-Oriented Programming (OOP) in PHP

OOP is a programming paradigm that organizes code into objects — self-contained units that combine data (properties) and behavior (methods).

---

## Classes and Objects

A **class** is the blueprint. An **object** is an instance created from that blueprint.

```php
<?php
  class Car {
    // Properties — data the object holds
    public string $brand;
    public string $color;
    public int    $speed = 0;

    // Constructor — runs when object is created
    public function __construct(string $brand, string $color) {
      $this->brand = $brand;
      $this->color = $color;
    }

    // Methods — behavior the object can perform
    public function accelerate(int $amount): void {
      $this->speed += $amount;
    }

    public function describe(): string {
      return "$this->color $this->brand going $this->speed km/h";
    }
  }

  // Create objects from the class
  $car1 = new Car('Toyota', 'Red');
  $car2 = new Car('BMW', 'Black');

  $car1->accelerate(60);
  echo $car1->describe();   // Red Toyota going 60 km/h
  echo $car2->describe();   // Black BMW going 0 km/h
?>
```

> `$this` refers to the current object. Inside a method, `$this->brand` means "this particular object's brand property."

---

## 1. Encapsulation

Encapsulation is controlling access to an object's internal data. You hide the internal state and expose only what is needed through methods. This prevents outside code from accidentally or maliciously breaking the object's data.

### Access Modifiers

| Modifier | Accessible From |
|---|---|
| `public` | Anywhere — inside the class, outside, in child classes |
| `protected` | Inside the class and child classes only |
| `private` | Inside the class only — completely hidden |

```php
<?php
  class BankAccount {
    private float $balance;   // Hidden — cannot be accessed directly from outside

    public function __construct(float $initial) {
      $this->balance = $initial;
    }

    // Getter — controlled read access
    public function getBalance(): float {
      return $this->balance;
    }

    // Setter — controlled write access with validation
    public function deposit(float $amount): void {
      if ($amount <= 0) {
        throw new Exception("Deposit amount must be positive");
      }
      $this->balance += $amount;
    }

    public function withdraw(float $amount): void {
      if ($amount > $this->balance) {
        throw new Exception("Insufficient funds");
      }
      $this->balance -= $amount;
    }
  }

  $account = new BankAccount(1000);
  $account->deposit(500);
  echo $account->getBalance();   // 1500

  // $account->balance = -9999;  ← Error — balance is private
?>
```

> Without encapsulation: `$account->balance = -99999` — anyone can set any value, breaking the object. With `private`, the only way to change the balance is through `deposit()` and `withdraw()` which validate the input first.

---

## 2. Inheritance

Inheritance lets a child class reuse the properties and methods of a parent class, and add or override behavior on top.

```php
<?php
  class Animal {
    public string $name;

    public function __construct(string $name) {
      $this->name = $name;
    }

    public function eat(): void {
      echo "$this->name is eating\n";
    }

    public function sleep(): void {
      echo "$this->name is sleeping\n";
    }
  }

  class Dog extends Animal {
    // Dog inherits eat() and sleep() from Animal automatically

    // Add new behavior specific to Dog
    public function bark(): void {
      echo "$this->name says: Woof!\n";
    }

    // Override parent behavior
    public function eat(): void {
      echo "$this->name is eating dog food\n";
    }
  }

  class Cat extends Animal {
    public function meow(): void {
      echo "$this->name says: Meow!\n";
    }
  }

  $dog = new Dog('Rex');
  $dog->eat();     // Rex is eating dog food  ← overridden
  $dog->sleep();   // Rex is sleeping         ← inherited
  $dog->bark();    // Rex says: Woof!         ← new method

  $cat = new Cat('Whiskers');
  $cat->eat();     // Whiskers is eating      ← inherited from Animal
  $cat->meow();    // Whiskers says: Meow!
?>
```

### `parent::` — Calling the Parent Method

```php
<?php
  class Dog extends Animal {
    public function eat(): void {
      parent::eat();   // Call Animal's eat() first
      echo "...specifically dog food\n";
    }
  }
?>
```

### `parent::__construct()` — Calling Parent Constructor

```php
<?php
  class Employee extends Person {
    public string $company;

    public function __construct(string $name, int $age, string $company) {
      parent::__construct($name, $age);   // Run Person's constructor
      $this->company = $company;          // Then add Employee-specific setup
    }
  }
?>
```

---

## 3. Polymorphism

Polymorphism means "many forms." The same method name behaves differently depending on which object calls it.

```php
<?php
  class Shape {
    public function area(): float {
      return 0;
    }
  }

  class Circle extends Shape {
    public function __construct(private float $radius) {}

    public function area(): float {
      return M_PI * $this->radius ** 2;
    }
  }

  class Rectangle extends Shape {
    public function __construct(
      private float $width,
      private float $height
    ) {}

    public function area(): float {
      return $this->width * $this->height;
    }
  }

  class Triangle extends Shape {
    public function __construct(
      private float $base,
      private float $height
    ) {}

    public function area(): float {
      return 0.5 * $this->base * $this->height;
    }
  }

  // Polymorphism in action
  $shapes = [
    new Circle(5),
    new Rectangle(4, 6),
    new Triangle(3, 8),
  ];

  foreach ($shapes as $shape) {
    echo $shape->area() . "\n";
    // Each calls ITS OWN area() — same method name, different behavior
  }
  // 78.54
  // 24
  // 12
?>
```

> The power: your loop does not need to know what kind of shape it has. It just calls `area()` and gets the correct result for whatever shape it is. Adding a `Pentagon` class later requires no changes to the loop.

---

## 4. Abstraction

Abstraction hides implementation complexity and shows only what is necessary. In PHP, abstraction is implemented with abstract classes and interfaces.

### Abstract Class

**An abstract class cannot be instantiated directly** — you cannot do `new AbstractClass()`. It exists only to be extended. It can define abstract methods that child classes must implement.

```php
<?php
  abstract class Vehicle {
    public string $brand;

    public function __construct(string $brand) {
      $this->brand = $brand;
    }

    // Concrete method — all vehicles share this behavior
    public function start(): void {
      echo "$this->brand engine starting...\n";
    }

    // Abstract method — every vehicle must define HOW it moves
    abstract public function move(): void;
  }

  class Car extends Vehicle {
    public function move(): void {
      echo "$this->brand is driving on the road\n";
    }
  }

  class Boat extends Vehicle {
    public function move(): void {
      echo "$this->brand is sailing on water\n";
    }
  }

  // $v = new Vehicle('X');   ← Error — cannot instantiate abstract class

  $car  = new Car('Toyota');
  $boat = new Boat('Yamaha');

  $car->start();    // Toyota engine starting...
  $car->move();     // Toyota is driving on the road
  $boat->move();    // Yamaha is sailing on water
?>
```

### Interface

An interface defines a **contract** — a list of methods that a class must implement. Unlike abstract classes, **interfaces have no implementation and no properties (only constants).**

A **class can implement multiple interfaces but can only extend one class.**

```php
<?php
  interface Printable {
    public function print(): void;
  }

  interface Exportable {
    public function exportToPDF(): string;
    public function exportToCSV(): string;
  }

  // A class can implement multiple interfaces
  class Invoice implements Printable, Exportable {
    public function print(): void {
      echo "Printing invoice...\n";
    }

    public function exportToPDF(): string {
      return "invoice.pdf";
    }

    public function exportToCSV(): string {
      return "invoice.csv";
    }
  }
?>
```

### Abstract Class vs Interface

| | Abstract Class | Interface |
|---|---|---|
| Can have implemented methods | Yes | No (PHP 8 allows default methods) |
| Can have properties | Yes | No (only constants) |
| A class can extend | One only | Multiple |
| Use when | Sharing base behavior among related classes | Defining a contract any unrelated class can fulfill |

---

## Traits

A trait is a reusable block of methods that can be mixed into any class. It solves the problem of wanting to share methods across classes that do not share the same inheritance chain — PHP only allows one parent class.

```php
<?php
  trait Timestampable {
    public string $createdAt;
    public string $updatedAt;

    public function setTimestamps(): void {
      $this->createdAt = date('Y-m-d H:i:s');
      $this->updatedAt = date('Y-m-d H:i:s');
    }

    public function touch(): void {
      $this->updatedAt = date('Y-m-d H:i:s');
    }
  }

  trait Loggable {
    public function log(string $message): void {
      echo "[LOG] " . date('H:i:s') . ": $message\n";
    }
  }

  class User {
    use Timestampable, Loggable;   // Use multiple traits

    public function __construct(public string $name) {
      $this->setTimestamps();
    }
  }

  class Product {
    use Timestampable;   // Same trait, different class

    public function __construct(public string $title) {
      $this->setTimestamps();
    }
  }

  $user = new User('Ahmed');
  $user->log("User created");    // [LOG] 14:30:00: User created
  echo $user->createdAt;
?>
```

> Traits are copy-paste at the language level. When you use a trait, PHP copies its methods into your class. A trait is not a type — you cannot type-hint a trait.

---

## Static Properties and Methods

Static members belong to the class itself — not to any specific object. You access them without creating an instance.

```php
<?php
  class Counter {
    private static int $count = 0;

    public static function increment(): void {
      self::$count++;
    }

    public static function getCount(): int {
      return self::$count;
    }
  }

  Counter::increment();
  Counter::increment();
  Counter::increment();

  echo Counter::getCount();   // 3
  // No object created — called directly on the class
?>
```

> `self::` refers to the current class (like `$this` for static context). `static::` refers to the called class — important for late static binding in inheritance.

---

## Magic Methods

PHP has built-in methods that are called automatically in special situations. They all start with `__`.

```php
<?php
  class Person {
    public function __construct(
      private string $name,
      private int    $age
    ) {}

    // Called when object is used as a string
    public function __toString(): string {
      return "Person: $this->name, age $this->age";
    }

    // Called when accessing a non-existent property
    public function __get(string $name): mixed {
      return "Property '$name' does not exist";
    }

    // Called when object is cloned
    public function __clone(): void {
      echo "Object was cloned\n";
    }
  }

  $person = new Person('Ahmed', 25);
  echo $person;            // Person: Ahmed, age 25  ← __toString called
  echo $person->phone;     // Property 'phone' does not exist  ← __get called
  $copy = clone $person;   // Object was cloned  ← __clone called
?>
```

| Magic Method | Called When |
|---|---|
| `__construct()` | Object is created |
| `__destruct()` | Object is destroyed (script ends) |
| `__toString()` | Object is used as a string |
| `__get($name)` | Reading a non-existent property |
| `__set($name, $value)` | Writing to a non-existent property |
| `__isset($name)` | `isset()` called on a non-existent property |
| `__clone()` | Object is cloned |

---

## Common OOP Interview Questions and Answers

---

**Q: What is the difference between a class and an object?**

A class is the blueprint or template. An object is a specific instance created from that blueprint. A class exists in code. An object exists in memory at runtime. You can create many objects from one class, each with its own property values.

---

**Q: What are the four pillars of OOP?**

Encapsulation, Inheritance, Polymorphism, and Abstraction.
- Encapsulation: hiding internal data and controlling access through methods
- Inheritance: a child class reusing and extending parent class behavior
- Polymorphism: the same method name behaving differently depending on the object
- Abstraction: hiding implementation complexity and exposing only what is needed

---

**Q: What is the difference between `public`, `protected`, and `private`?**

`public` — accessible from anywhere: inside the class, outside, and in child classes.
`protected` — accessible inside the class and inside child classes. Not accessible from outside.
`private` — accessible only inside the class where it is defined. Not accessible in child classes or from outside.

---

**Q: What is the difference between an abstract class and an interface?**

An abstract class can have both implemented and abstract methods, can have properties, and a class can only extend one abstract class. An interface defines only method signatures with no implementation, has no regular properties, and a class can implement multiple interfaces. Use an abstract class when sharing code among related classes. Use an interface when defining a contract that unrelated classes need to fulfill.

---

**Q: Can a class extend multiple classes in PHP?**

No. PHP does not support multiple inheritance — a class can only extend one parent class. However, a class can implement multiple interfaces, and multiple traits can be used to share method code across classes.

---

**Q: What is a trait and why is it used?**

A trait is a reusable block of methods that can be included in any class using the `use` keyword. It solves the limitation of single inheritance — when you want to share behavior between classes that do not share a parent class. A trait is not a class and cannot be instantiated.

---

**Q: What is `$this` in PHP?**

`$this` is a reference to the current object instance inside a method. It allows the method to access and modify the object's own properties and call its own methods.

---

**Q: What is the difference between `self::` and `static::`?**

`self::` always refers to the class where the method is physically written. `static::` refers to the class that was actually called at runtime — it respects inheritance. The difference matters in child classes when a parent class uses `self::` — it still points to the parent. With `static::` it points to the child class that called it.

---

**Q: What is method overriding?**

Method overriding is when a child class defines a method with the same name as a method in the parent class. The child's version replaces the parent's version for objects of that child class. You can still call the parent's version using `parent::methodName()`.

---

**Q: What is the difference between `==` and `===` when comparing objects?**

`==` returns true if two objects are instances of the same class with the same property values. `===` returns true only if both variables refer to the exact same object instance in memory.

---

**Q: What is a constructor and what is a destructor?**

A constructor (`__construct`) is called automatically when an object is created with `new`. It initializes the object's properties and runs any setup code. A destructor (`__destruct`) is called automatically when the object is no longer referenced — when the script ends or the variable is unset. It is used for cleanup: closing file handles, database connections, etc.

---

**Q: What is method chaining?**

Method chaining is calling multiple methods on the same object in sequence by returning `$this` from each method.

```php
class QueryBuilder {
  private string $query = '';

  public function select(string $cols): static {
    $this->query .= "SELECT $cols ";
    return $this;
  }

  public function from(string $table): static {
    $this->query .= "FROM $table ";
    return $this;
  }

  public function build(): string {
    return $this->query;
  }
}

$sql = (new QueryBuilder)->select('*')->from('users')->build();
```

---

**Q: What is the difference between an API and a web page?**

A web page returns HTML that the browser renders into a visual interface for humans. An API returns data (usually JSON) for other programs to consume. A web page is for humans. An API is for machines.

---

**Q: What is REST?**

REST is a set of conventions for designing APIs that use HTTP. A REST API uses HTTP methods (GET, POST, PUT, DELETE) to indicate the operation, URLs to identify resources, and status codes to communicate the result. REST is stateless — each request contains all the information needed to process it.

---

**Q: What is the difference between PUT and PATCH?**

PUT replaces an entire resource. You must send all fields or they become empty. PATCH updates only specific fields. You send only what you want to change. Use PUT when replacing a complete record. Use PATCH when updating one or two fields.

---

**Q: Why does PHP not have `$_PUT` like it has `$_POST`?**

PHP was designed primarily for web form processing, and HTML forms only support GET and POST. PUT, PATCH, and DELETE were not part of the original web browser model. PHP never added built-in parsing for these methods. You must read the raw input manually with `file_get_contents('php://input')` and parse it yourself.

---

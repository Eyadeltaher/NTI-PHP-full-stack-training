# Setting Up Your MVC Project
Now that Composer is globally installed, you can use it to set up an MVC project. You have two routes depending on whether you want a pre-built framework or a custom architecture.

### Route A: Using an established MVC Framework (e.g., Laravel)
If you want to use a powerhouse MVC framework, navigate to your development directory (e.g., `~/Projects`) and spin up a new project:

``` bash
composer create-project laravel/laravel my-mvc-app
cd my-mvc-app
php artisan serve
```

### Route B: Creating a Custom MVC Structure from Scratch
If you want to understand how MVC works fundamentally by writing your own, you can use Composer to handle **Autoloading** (mapping your directories dynamically).

1. Create your project folder structure:
```bash
   mkdir custom-mvc && cd custom-mvc
   mkdir -p app/Controllers app/Models app/Views public
```

2. Initialize Composer in that folder:    
    ```bash
    composer init
    ```
    _(You can hit Enter to accept the defaults for most of the prompts)._
    
3. Set up **PSR-4 Autoloading** so your MVC files can find each other. Open the newly generated `composer.json` file and make sure the `autoload` block maps to your `app/` folder like this:

```JSON
   {
       "name": "vendor/custom-mvc",
       "autoload": {
           "psr-4": {
               "App\\": "app/"
           }
       },
       "require": {}
   }
```

4. Tell Composer to generate the autoload mapping files:
    ```bash
    composer dump-autoload
    ```
    
5. Now, at the top of your front controller (`public/index.php`), you just have to include the autoloader, and your custom MVC architecture is active:

```php
   <?php
   require_once __DIR__ . '/../vendor/autoload.php';

   // Your routing logic to Controllers goes here...
```

---

# MVC — Model View Controller

## What is MVC?

MVC is an architectural pattern — a way of organizing your code into three distinct responsibilities so that each part of your application has one job and one job only.

```
User makes a request
       ↓
   CONTROLLER  ← decides what to do
       ↓
     MODEL     ← gets or saves data
       ↓
      VIEW      ← displays the result to the user
```

Before MVC, PHP files mixed everything together — database queries, business logic, and HTML all in the same file. This is called **spaghetti code**. It works for tiny projects but becomes impossible to maintain as the project grows.

MVC solves this by enforcing a strict separation of concerns.

---

## The Three Parts

### Model — Data Layer

The Model is responsible for everything related to data. It talks to the database, performs queries, and returns results. It knows nothing about HTML or how data will be displayed.

**What belongs in a Model:**
- Database queries (SELECT, INSERT, UPDATE, DELETE)
- Business logic related to data (calculating totals, checking stock)
- Data validation rules

**What does NOT belong in a Model:**
- HTML
- Redirects
- Anything about how the page looks

---

### View — Presentation Layer

The View is responsible for displaying data to the user. It receives data from the Controller and renders it as HTML. It contains no business logic and no database queries.

**What belongs in a View:**
- HTML structure
- CSS links
- Looping through data to display rows
- Minimal PHP for echoing values and foreach loops

**What does NOT belong in a View:**
- Database queries
- Complex PHP logic
- Business decisions

---

### Controller — Traffic Director

The Controller sits between the Model and the View. It receives the request, decides what data is needed, asks the Model for it, and passes it to the correct View to display.

**What belongs in a Controller:**
- Handling the incoming request
- Calling Model methods to get data
- Passing data to a View
- Redirects

**What does NOT belong in a Controller:**
- Raw SQL queries
- HTML markup

---

## The Flow of a Request

```
1. Browser: GET http://localhost?home/index

2. index.php (entry point)
   → Creates a Request object
   → Creates an App object
   → App reads the URL and figures out: controller=home, method=index

3. App boots the URL
   → Parses "home/index" into controller="home" and method="index"
   → Builds full class name: Marwa\Mvc\Controllers\HomeController

4. App calls the method
   → Creates an instance of HomeController
   → Calls $homeController->index()

5. HomeController::index()
   → Creates a Customer model
   → Calls $customer->all() to get data from the database
   → Calls View::render('home.php', $customers)

6. View::render()
   → Finds the home.php file in the View directory
   → Includes it — the data is available as $data inside the template

7. home.php renders the HTML with the customer data
   → Browser receives the final HTML page
```

---

## Project Structure

```
project/
│
├── index.php                ← Single entry point — all requests go here
│
└── src/
    ├── app.php              ← Router — reads URL, finds controller, calls method
    ├── model.php            ← Base Model — shared database connection
    ├── view.php             ← View renderer — finds and includes template files
    ├── request.php          ← Wraps $_SERVER — reads URL from request
    │
    ├── controllers/
    │   └── HomeController.php   ← Handles home page requests
    │
    ├── model/
    │   └── customer.php         ← Customer-specific database queries
    │
    └── view/
        └── home.php             ← HTML template for the home page
```

---

## index.php — The Single Entry Point

```php
<?php
  require_once "../vendor/autoload.php";   // Load all classes automatically via Composer

  use Marwa\Mvc\Request;
  use Marwa\Mvc\App;

  $request = new Request;   // Wrap the incoming HTTP request
  $app     = new App($request);  // Start the application — routes the request
```

### Why a Single Entry Point?

Every request goes through `index.php`. This is called the **Front Controller** pattern. It gives you one place to:
- Load dependencies
- Start sessions
- Check authentication before anything runs
- Handle errors globally

Without a single entry point, each PHP file is independently accessible — there is no central place to enforce rules.

### How Routing Works via URL

```
http://localhost?home/index
                 ↑         ↑
            controller   method
```

The URL query string `home/index` tells the application which controller and which method to call. This is a simple custom router built from scratch.

---

## request.php — Wrapping the HTTP Request

```php
<?php
  namespace Marwa\Mvc;

  class Request {
    public function QueryString(): string {
      return $_SERVER['QUERY_STRING'];
      // $_SERVER['QUERY_STRING'] returns everything after ? in the URL
      // For: http://localhost?home/index  → returns "home/index"
    }
  }
```

### Why Wrap `$_SERVER` in a Class?

Direct access to `$_SERVER` anywhere in the code creates a hidden dependency — your code relies on a global superglobal. Wrapping it in a `Request` class means:
- You can mock it in tests — pass a fake Request with any URL you want
- All request-related logic lives in one place
- If PHP changes how requests work, you update one class not the whole codebase

---

## app.php — The Router

```php
<?php
  namespace Marwa\Mvc;

  class App {
    private string $url;
    private string $controller;
    private string $method;

    public function __construct(Request $request) {
      $this->url = $request->QueryString();
      // Gets "home/index" from the URL

      $this->bootUrl();
      // Splits "home/index" into controller="home" and method="index"

      $this->callMethod();
      // Finds the class, creates it, calls the method
    }

    public function bootUrl(): void {
      $urlArray = explode('/', $this->url);
      // explode splits "home/index" by "/" → ["home", "index"]

      $this->controller = $urlArray[0];   // "home"
      $this->method     = $urlArray[1];   // "index"
    }

    public function callMethod(): void {
      // Build the full namespaced class name
      // "home" → "Marwa\Mvc\Controllers\HomeController"
      $this->controller = "Marwa\\Mvc\\Controllers\\" . ucfirst($this->controller) . "Controller";

      if (class_exists($this->controller)) {
        $object = new $this->controller;
        // Dynamically create an instance of HomeController

        if (method_exists($this->controller, $this->method)) {
          call_user_func([$object, $this->method]);
          // Dynamically call $homeController->index()
        } else {
          echo "Method not found: " . $this->method;
        }

      } else {
        echo "Controller class not found: " . $this->controller;
      }
    }
  }
```

### Key PHP Functions Used

**`explode('/', $string)`**
Splits a string by a delimiter into an array.
```php
explode('/', 'home/index')  → ['home', 'index']
explode('/', 'product/show/5')  → ['product', 'show', '5']
```

**`class_exists($className)`**
Checks whether a class with that fully qualified name has been loaded. Returns true or false. Used here to verify the controller exists before trying to instantiate it.

**`method_exists($class, $method)`**
Checks whether a method exists on a class or object. Returns true or false. Used to verify the action method exists before calling it.

**`call_user_func([$object, $method])`**
Calls a method dynamically — when you do not know the method name until runtime. Equivalent to `$object->$method()` but safer and more explicit.

---

## model.php — The Base Model

```php
<?php
  namespace Marwa\Mvc;

  use PDO;

  class Model {
    protected PDO $connection;
    protected string $tableName;

    public function __construct() {
      $this->connection = new PDO(
        "mysql:host=localhost;dbname=session_9_test",
        'root',
        ''
      );
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
  }
```

### Why a Base Model?

Every model in the application needs a database connection. Without a base class, every model would repeat the same `new PDO(...)` connection code. The base `Model` class:
- Creates the connection once
- Makes it available to every child model via `$this->connection` (which is `protected` — accessible to child classes)
- Is the right place to set PDO attributes, error modes, and fetch modes once

`protected` means `$connection` is accessible inside `Model` and any class that extends it, but not from outside.

---

## customer.php — A Specific Model

```php
<?php
  namespace Marwa\Mvc\Model;

  use Marwa\Mvc\Model;
  use PDO;

  class Customer extends Model {
    protected string $tableName = 'customers';
    // Each model declares its own table name — no hardcoding the table name in queries

    public function all(): array {
      $query    = "SELECT * FROM $this->tableName";
      $result   = $this->connection->query($query);
      // $this->connection comes from the parent Model class
      $customers = $result->fetchAll(PDO::FETCH_ASSOC);
      return $customers;
      // Returns an array of all rows — does not echo or display anything
    }
  }
```

### What This Pattern Enables

Every table in your database gets its own Model class:

```
customers table → Customer model  → Customer::all(), Customer::find($id), Customer::create($data)
employees table → Employee model  → Employee::all(), Employee::find($id)
products table  → Product model   → Product::all(), Product::inStock()
```

Each model only knows about its own table. The controller asks the model for data and the model returns it — no HTML, no redirects, no display logic.

---

## HomeController.php — A Controller

```php
<?php
  namespace Marwa\Mvc\Controllers;

  use Marwa\Mvc\Model\Customer;
  use Marwa\Mvc\View;

  class HomeController {
    public function index(): void {
      $customer  = new Customer;
      // Create an instance of the Customer model

      $customers = $customer->all();
      // Ask the model for all customers — returns a plain PHP array

      View::render('home.php', $customers);
      // Pass the data to the View — tell it which template file to use
    }
  }
```

### The Controller's Single Responsibility

The controller does exactly three things and nothing else:
1. Get the data it needs (`$customer->all()`)
2. Pass it to the right view (`View::render(...)`)
3. Handle any redirects if needed

The controller does not write the query — that is the model's job.
The controller does not write the HTML — that is the view's job.

---

## view.php — The View Renderer

```php
<?php
  namespace Marwa\Mvc;

  class View {
    public static function render(string $fileName, array $data): void {
      $viewFile = __DIR__ . "/view/" . $fileName;
      // __DIR__ = the directory of the current file (src/)
      // Full path becomes: /path/to/project/src/view/home.php

      if (file_exists($viewFile)) {
        include($viewFile);
        // include makes $data available inside home.php as a local variable
      } else {
        echo "View file not found: " . $fileName;
      }
    }
  }
```

### Why Static?

`View::render()` is a static method — you call it on the class itself without creating an object. This is appropriate because `render` is a utility action — it does not need any object state. You just need to call it and pass data.

### How `include` Shares Data with the Template

When `include($viewFile)` runs, the included file has access to all local variables in scope at the point of the `include` call. Since `$data` is a local variable at that point, `home.php` can use `$data` directly.

```php
// view.php at the include point:
// $data = [['id'=>1,'first_name'=>'Ahmed',...], ...]
include($viewFile);
// home.php now has access to $data
```

---

## home.php — The View Template

```php
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Customers</title>
</head>
<body>

  <a href="?home/create" class="btn btn-warning m-4">Add Customer</a>

  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Money</th>
        <th>City</th>
        <th>Country</th>
        <th>Code</th>
        <th>Update</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; foreach ($data as $customer): ?>
        <tr>
          <th><?php echo $i++; ?></th>
          <td><?php echo htmlspecialchars($customer['first_name']); ?></td>
          <td><?php echo htmlspecialchars($customer['last_name']); ?></td>
          <td><?php echo htmlspecialchars($customer['email']); ?></td>
          <td><?php echo htmlspecialchars($customer['gender']); ?></td>
          <td><?php echo htmlspecialchars($customer['money']); ?></td>
          <td><?php echo htmlspecialchars($customer['city']); ?></td>
          <td><?php echo htmlspecialchars($customer['country']); ?></td>
          <td><?php echo htmlspecialchars($customer['country_code']); ?></td>
          <td>
            <a href="?home/update&id=<?php echo $customer['id']; ?>" class="btn btn-success btn-sm">Update</a>
          </td>
          <td>
            <a href="?home/delete&id=<?php echo $customer['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>
```

### Fixes Applied to the Original View

**1. `htmlspecialchars()` added to every echo**

The original code echoed raw database values directly into HTML. If a customer's name contains `<script>alert('xss')</script>`, it executes in the browser. `htmlspecialchars()` converts special characters to safe HTML entities — `<` becomes `&lt;` — so it displays as text, never executes.

**2. Links updated to MVC-style URLs**

The original links pointed to `updateCustomer.php` and `deleteCustomer.php` — direct file access, not MVC. In this architecture, everything goes through the router:
```
?home/update&id=5   → HomeController → update() method
?home/delete&id=5   → HomeController → delete() method
```

---

## Autoloading with Composer

The training code uses `require_once "../vendor/autoload.php"`. This is Composer's autoloader — it automatically finds and loads class files based on their namespace without you writing a single `require_once` for each class.

### How It Knows Where to Find Classes

In `composer.json`:

```json
{
  "autoload": {
    "psr-4": {
      "Marwa\\Mvc\\": "src/"
    }
  }
}
```

This tells Composer: "when you see the namespace `Marwa\Mvc`, look for files in the `src/` directory."

```
Namespace                          → File path
Marwa\Mvc\App                      → src/app.php
Marwa\Mvc\Model                    → src/model.php
Marwa\Mvc\Controllers\HomeController → src/controllers/HomeController.php
Marwa\Mvc\Model\Customer           → src/model/customer.php
```

Run `composer dump-autoload` after adding new classes to regenerate the autoload map.

---

## What Happens at Each URL

```
?home/index     → HomeController → index()   → show all customers
?home/create    → HomeController → create()  → show create form
?home/store     → HomeController → store()   → process form and insert
?home/update&id=5 → HomeController → update()  → show edit form for id 5
?home/delete&id=5 → HomeController → delete()  → delete id 5, redirect
```

---

## Extending the Application — Adding a New Feature

To add a product listing page:

**1. Create the Model**
```php
// src/model/Product.php
namespace Marwa\Mvc\Model;
use Marwa\Mvc\Model;
use PDO;

class Product extends Model {
  protected string $tableName = 'products';

  public function all(): array {
    $result = $this->connection->query("SELECT * FROM $this->tableName");
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }
}
```

**2. Create the Controller**
```php
// src/controllers/ProductController.php
namespace Marwa\Mvc\Controllers;
use Marwa\Mvc\Model\Product;
use Marwa\Mvc\View;

class ProductController {
  public function index(): void {
    $product  = new Product;
    $products = $product->all();
    View::render('products.php', $products);
  }
}
```

**3. Create the View**
```php
// src/view/products.php
<?php foreach ($data as $product): ?>
  <p><?php echo htmlspecialchars($product['name']); ?></p>
<?php endforeach; ?>
```

**4. Navigate to it**
```
http://localhost?product/index
```

No changes to any existing file. The router handles it automatically.

---

## Bugs Fixed in the Original Code

| Location | Bug | Fix |
|---|---|---|
| `app.php` | Controller name built without `ucfirst()` — URL `home` would look for `homecontroller` not `HomeController` | Added `ucfirst()` before building class name |
| `view.php` | Path used backslash `\View\\` — breaks on Linux/Mac servers | Changed to forward slash `/view/` |
| `home.php` | All values echoed without `htmlspecialchars()` — XSS vulnerability | Wrapped every echo in `htmlspecialchars()` |
| `home.php` | Links pointed to `updateCustomer.php` and `deleteCustomer.php` — bypasses MVC | Updated to `?home/update&id=` format |
| `model.php` | No PDO error mode set — silent failures | Added `setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION)` |

---

## MVC vs No-MVC Comparison

```
Without MVC (spaghetti code):
  create.php        ← has HTML, SQL query, redirect, validation all mixed
  delete.php        ← has HTML, SQL query, redirect mixed
  index.php         ← has HTML, SQL query mixed
  update.php        ← has HTML, SQL query, redirect, validation all mixed

With MVC:
  Controllers/HomeController.php   ← only: get data, call view, redirect
  Model/Customer.php               ← only: SQL queries, return data
  View/home.php                    ← only: HTML + echo variables
```

When a database column is renamed, in spaghetti code you search every file. In MVC you update one model method and the change propagates everywhere that model is used.

---

## Quick Reference

| File | Namespace | Responsibility |
|---|---|---|
| `index.php` | none | Entry point — boots the application |
| `request.php` | `Marwa\Mvc` | Reads URL from `$_SERVER` |
| `app.php` | `Marwa\Mvc` | Routes the URL to the right controller and method |
| `model.php` | `Marwa\Mvc` | Base class — provides shared database connection |
| `view.php` | `Marwa\Mvc` | Finds and includes view template files |
| `HomeController.php` | `Marwa\Mvc\Controllers` | Handles home page requests — calls model, calls view |
| `customer.php` | `Marwa\Mvc\Model` | Customer table queries — extends base Model |
| `home.php` | none | HTML template — displays customer data |

| Concept | Key Point |
|---|---|
| Single entry point | All requests go through `index.php` — one place to enforce rules |
| Router (`App` class) | Parses URL → finds controller → calls method dynamically |
| `class_exists()` | Check class is loaded before instantiating — prevents fatal errors |
| `method_exists()` | Check method exists before calling — prevents fatal errors |
| `call_user_func()` | Call a method dynamically when name is not known until runtime |
| `__DIR__` | Absolute path to the directory of the current file |
| `include` in View | Shares local variables with the included template |
| `protected` in Model | `$connection` accessible in child models but not from outside |
| `extends Model` | Child model inherits database connection automatically |
| `static` on View::render | No object state needed — utility method, call on the class directly |
| `htmlspecialchars()` | Always wrap user/database data before echoing into HTML |
| Composer autoload | Maps namespaces to directories — no manual require_once per class |
| Adding a feature | New model + new controller + new view — zero changes to existing files |
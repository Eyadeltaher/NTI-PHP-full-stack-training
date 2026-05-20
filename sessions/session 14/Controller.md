# Laravel — Controllers

---

## What is a Controller?

A controller is a class that handles requests. Instead of writing all your request logic directly in `routes/web.php` as closures, you organize it into controller classes — each class grouping related logic together.

```php
// Without controller — logic in the route file (messy for real apps)
Route::get('/cars', function () {
    $cars = Car::all();
    return view('cars.index', compact('cars'));
});

// With controller — route file stays clean
Route::get('/cars', [CarController::class, 'index']);
```

The rule: a controller should only handle logic for one type of resource. `CarController` handles car-related requests. `ProductController` handles products. They should never mix.

---

## Basics of Controllers

### Creating a Controller

Always use Artisan — it creates the file in the right location with the correct namespace and class structure:

```bash
php artisan make:controller CarController
```

Generated file at `app/Http/Controllers/CarController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarController extends Controller
{
    // Your methods go here
}
```

> Controller class names must end with `Controller` by convention. `CarController`, `ProductController`, `UserController` — this is not enforced by Laravel but it is expected by every Laravel developer who reads your code.

### Adding Methods to a Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        return "Index method from CarController";
    }

    public function myCars()
    {
        return "My Cars method from CarController";
    }
}
```

### Connecting a Route to a Controller Method

In `routes/web.php`, pass an array with the controller class and method name:

```php
use App\Http\Controllers\CarController;

Route::get('/cars', [CarController::class, 'index']);
Route::get('/my-cars', [CarController::class, 'myCars']);
```

The first element is the controller class, the second is the method name as a string.

---

## Group Routes by Controller

When multiple routes all belong to the same controller, you can group them using `Route::controller()` instead of repeating the class name on every route:

```php
use App\Http\Controllers\CarController;

// Without grouping — repetitive
Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/my', [CarController::class, 'myCars']);
Route::get('/cars/{id}', [CarController::class, 'show']);
Route::post('/cars', [CarController::class, 'store']);

// With grouping — cleaner
Route::controller(CarController::class)->group(function () {
    Route::get('/cars', 'index');
    Route::get('/cars/my', 'myCars');
    Route::get('/cars/{id}', 'show');
    Route::post('/cars', 'store');
});
```

Inside the group, you only provide the method name — the controller is assumed from the group definition. The result is identical.

---

## Single Action Controllers

A single action controller is a controller that handles exactly one route. Instead of having multiple methods, it has one special method called `__invoke`.

Use single action controllers when one action is complex enough to deserve its own file, but it does not naturally belong inside a larger controller.

### Creating a Single Action Controller

```bash
php artisan make:controller ShowCarController --invokable
```

Generated file:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShowCarController extends Controller
{
    public function __invoke(Request $request)
    {
        return "__invoke method from ShowCarController";
    }
}
```

The `__invoke` method is a PHP magic method — it runs automatically when you call an object as if it were a function.

### Connecting a Single Action Controller to a Route

When using an invokable controller, you do not provide a method name — just the class:

```php
use App\Http\Controllers\ShowCarController;

// Regular controller — needs method name
Route::get('/car', [CarController::class, 'index']);

// Invokable controller — no method name needed
Route::get('/car', ShowCarController::class);
```

### Adding `__invoke` to a Regular Controller

You can also add `__invoke` to an existing regular controller alongside other methods. It coexists with the other methods:

```php
class CarController extends Controller
{
    public function index()
    {
        return "Index method";
    }

    public function __invoke()
    {
        return "__invoke method";
    }
}
```

```php
// Calls index()
Route::get('/cars', [CarController::class, 'index']);

// Calls __invoke()
Route::get('/car', CarController::class);
```

Real situations for single action controllers:

- A complex payment processing handler
- A report generator that runs a long calculation
- A webhook receiver that handles incoming data from a third-party service
- Any action that has enough logic to deserve isolation but does not fit a resource pattern

---

## Resource Controllers

A resource controller is a controller that has all seven standard CRUD methods pre-generated. Laravel follows a convention for CRUD operations, and resource controllers implement that convention.

### Creating a Resource Controller

```bash
php artisan make:controller ProductController --resource
```

Generated file with all seven methods:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /products — show a list of all products
    public function index()
    {
        //
    }

    // GET /products/create — show the form to create a new product
    public function create()
    {
        //
    }

    // POST /products — save the new product to the database
    public function store(Request $request)
    {
        //
    }

    // GET /products/{product} — show one specific product
    public function show(string $id)
    {
        //
    }

    // GET /products/{product}/edit — show the form to edit a product
    public function edit(string $id)
    {
        //
    }

    // PUT/PATCH /products/{product} — save the updated product
    public function update(Request $request, string $id)
    {
        //
    }

    // DELETE /products/{product} — delete the product
    public function destroy(string $id)
    {
        //
    }
}
```

### The Seven Methods and Their Purpose

|Method|HTTP|URL|Purpose|
|---|---|---|---|
|`index()`|GET|`/products`|Show all products|
|`create()`|GET|`/products/create`|Show the create form|
|`store()`|POST|`/products`|Save new product|
|`show()`|GET|`/products/{id}`|Show one product|
|`edit()`|GET|`/products/{id}/edit`|Show the edit form|
|`update()`|PUT/PATCH|`/products/{id}`|Save updated product|
|`destroy()`|DELETE|`/products/{id}`|Delete the product|

### Registering a Resource Route

One line generates all seven routes:

```php
use App\Http\Controllers\ProductController;

Route::resource('products', ProductController::class);
```

Equivalent to writing all seven routes manually. Run `php artisan route:list` to see what was generated:

```
GET    /products              products.index   ProductController@index
GET    /products/create       products.create  ProductController@create
POST   /products              products.store   ProductController@store
GET    /products/{product}    products.show    ProductController@show
GET    /products/{product}/edit products.edit  ProductController@edit
PUT    /products/{product}    products.update  ProductController@update
DELETE /products/{product}    products.destroy ProductController@destroy
```

Notice the auto-generated route names: `products.index`, `products.create`, `products.store`, etc. These follow the `resource.method` naming convention consistently.

### Excluding Specific Methods — `except`

```php
// Register all routes EXCEPT destroy
Route::resource('products', ProductController::class)->except(['destroy']);

// Register all routes EXCEPT create and edit (useful when no HTML form is needed)
Route::resource('products', ProductController::class)->except(['create', 'edit']);
```

### Including Only Specific Methods — `only`

```php
// Register ONLY index and show
Route::resource('products', ProductController::class)->only(['index', 'show']);
```

### API Resource Controllers

When building an API, the `create` and `edit` methods are not needed — those exist to show HTML forms, and an API returns JSON, not forms.

```php
// Register only the 5 API-relevant routes
Route::apiResource('products', ProductController::class);
```

This generates five routes instead of seven — everything except `create` and `edit`.

### Generating an API Controller

If you know from the start that a controller is for an API, generate it without the `create` and `edit` methods:

```bash
php artisan make:controller CarController --api
```

Generated controller has five methods: `index`, `store`, `show`, `update`, `destroy`. The `create` and `edit` methods are not included.

### Multiple Resource Controllers at Once

```php
Route::resources([
    'cars'     => CarController::class,
    'products' => ProductController::class,
]);

// Or for API resources
Route::apiResources([
    'cars'     => CarController::class,
    'products' => ProductController::class,
]);
```

---

## Controller Commands — Full Reference

```bash
# Basic controller
php artisan make:controller CarController

# Single action controller (generates __invoke method)
php artisan make:controller ShowCarController --invokable

# Resource controller (generates all 7 CRUD methods)
php artisan make:controller ProductController --resource

# API resource controller (generates 5 methods — no create/edit)
php artisan make:controller CarController --api

# Resource controller + Model in one command
php artisan make:controller ProductController --resource --model=Product
```



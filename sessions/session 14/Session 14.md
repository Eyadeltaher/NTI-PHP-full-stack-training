# Laravel 12  Notes

---

## What is Laravel?

Laravel is a PHP framework — a pre-built structure that handles all the repetitive, boring parts of building a web application so you can focus on writing the actual features of your project.

Before Laravel, building a PHP application meant writing a router from scratch, building a database layer from scratch, setting up sessions, handling errors, organizing files — all manually. You built the MVC pattern yourself (like the custom one in the previous notes). Laravel gives you all of that built-in, professionally tested, and production-ready from day one.

Laravel follows the MVC pattern (Model-View-Controller), provides its own templating engine called Blade, its own database ORM called Eloquent, a command-line tool called Artisan, and a migration system for managing your database structure — all in one package.

The difference between your custom MVC and Laravel:
```
Custom MVC:   You built the router, the view renderer, the database class, namespaces — everything
Laravel:      All of that is already built — you just use it
```

Laravel requires PHP 8.2 or higher.

---

## Creating a New Project

### Requirements

- PHP 8.2+
- Composer installed globally
- MySQL (via XAMPP or standalone)
- A terminal (CMD, PowerShell, or any terminal)

### Create the Project

```bash
composer create-project laravel/laravel my-project
```

This downloads Laravel and all its dependencies into a folder called `my-project`.

Or using the Laravel installer (if you installed it globally):

```bash
composer global require laravel/installer
laravel new my-project
```

### Start the Development Server

```bash
cd my-project
php artisan serve
```

Open your browser at `http://127.0.0.1:8000` — you will see the Laravel welcome page.

> `php artisan serve` starts a development server built into PHP. You do not need Apache or XAMPP running for this. However, you still need MySQL running if your application uses a database.

---

## The `.env` File — Your First Stop

Before anything else, open `.env` in the root of your project. This file holds all environment-specific configuration — database credentials, app name, mail settings.

```env
APP_NAME=MyApp
APP_ENV=local
APP_KEY=base64:...          # Auto-generated — never change this manually
APP_DEBUG=true              # Shows errors — set to false in production
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=my_database     # Change this to your database name
DB_USERNAME=root            # Your MySQL username
DB_PASSWORD=                # Your MySQL password (blank for XAMPP default)
```

> Change `DB_DATABASE` to the name of the database you created in MySQL. Laravel reads this file to know how to connect.

Create the database in MySQL first:
```sql
CREATE DATABASE my_database;
```

---

## Directory Structure — Where Everything Lives

```
my-project/
├── app/
│   ├── Http/
│   │   └── Controllers/     ← Your controllers go here
│   └── Models/              ← Your Eloquent models go here
│
├── bootstrap/               ← Framework initialization (do not touch)
├── config/                  ← Configuration files (database, mail, auth...)
│
├── database/
│   ├── migrations/          ← Database table definitions — version control for your schema
│   ├── seeders/             ← Populate tables with sample data
│   └── factories/           ← Generate fake data for testing
│
├── public/                  ← The ONLY folder the web server points to
│   └── index.php            ← Single entry point — all requests come here
│
├── resources/
│   └── views/               ← Your Blade template files (.blade.php) go here
│
├── routes/
│   ├── web.php              ← All your web routes go here
│   └── api.php              ← API routes (for JSON responses)
│
├── storage/                 ← Logs, uploaded files, cached views (do not touch)
├── vendor/                  ← Composer packages (do not touch)
│
├── .env                     ← Environment configuration — database, app key, etc.
├── artisan                  ← The Artisan command-line tool
└── composer.json            ← Project dependencies list
```

---

## Where to Write What

| What you want to do                     | Where to write it       |
| --------------------------------------- | ----------------------- |
| Define URL routes                       | `routes/web.php`        |
| Handle a request, get data, return view | `app/Http/Controllers/` |
| Database queries, model logic           | `app/Models/`           |
| HTML templates                          | `resources/views/`      |
| Database table structure                | `database/migrations/`  |
| App configuration (timezone, name)      | `config/app.php`        |
| Database credentials                    | `.env`                  |
| Static files (images, CSS, JS)          | `public/`               |

---

## Artisan — Laravel's Command Line Tool

Artisan is the most important tool in Laravel. You use it to generate files, run migrations, start the server, and much more. You always run it from your project root.

```bash
# Start development server
php artisan serve

# Create a controller
php artisan make:controller CustomerController

# Create a model
php artisan make:model Customer

# Create a model AND its migration file together
php artisan make:model Customer -m

# Create a resource controller (all CRUD methods pre-generated)
php artisan make:controller CustomerController --resource

# Run all pending migrations (create tables in database)
php artisan migrate

# Rollback the last migration
php artisan migrate:rollback

# Drop all tables and re-run all migrations fresh
php artisan migrate:fresh

# See all registered routes
php artisan route:list

# Create a migration file manually
php artisan make:migration create_customers_table
```

> You will use `php artisan make:` commands constantly. Never create controller or model files manually — always use Artisan so the namespace, class name, and file location are all set up correctly.

---
## Routes — The Entry Points of Your Application

All web routes live in `routes/web.php`. A route maps a URL to a piece of code — either a closure or a controller method.

### Basic Routes

```php
// routes/web.php

use Illuminate\Support\Facades\Route;

// Simple route — return a string
Route::get('/', function () {
    return 'Hello World';
});

// Return a view
Route::get('/', function () {
    return view('welcome');
    // Looks for: resources/views/welcome.blade.php
});

// Return a view with data
Route::get('/customers', function () {
    $name = 'Ahmed';
    return view('customers.index', ['name' => $name]);
    // Looks for: resources/views/customers/index.blade.php
});
```

### Route with a Parameter

```php
Route::get('/customers/{id}', function ($id) {
    return "Customer ID: " . $id;
});
```

### Route Pointing to a Controller

```php
use App\Http\Controllers\CustomerController;

// لعرض كل العملاء
Route::get('/customers', [CustomerController::class, 'index']);

// لعرض عميل واحد بناءً على الـ ID
Route::get('/customers/{id}', [CustomerController::class, 'show']);

// لإنشاء عميل جديد (طلب POST)
Route::post('/customers', [CustomerController::class, 'store']);

// لتعديل بيانات عميل (طلب PUT)
Route::put('/customers/{id}', [CustomerController::class, 'update']);

// لحذف عميل (طلب DELETE)
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
```

### Resource Route — All CRUD Routes in One Line

```php
Route::resource('customers', CustomerController::class);
```

This single line creates all seven standard routes automatically:

| Method    | URL                    | Controller Method | Purpose          |
| --------- | ---------------------- | ----------------- | ---------------- |
| GET       | `/customers`           | `index()`         | Show all         |
| GET       | `/customers/create`    | `create()`        | Show create form |
| POST      | `/customers`           | `store()`         | Save new record  |
| GET       | `/customers/{id}`      | `show()`          | Show one record  |
| GET       | `/customers/{id}/edit` | `edit()`          | Show edit form   |
| PUT/PATCH | `/customers/{id}`      | `update()`        | Save update      |
| DELETE    | `/customers/{id}`      | `destroy()`       | Delete record    |

Check all routes at any time:
```bash
php artisan route:list
```

---

## Controllers

Controllers live in `app/Http/Controllers/`. Generate one with Artisan:

```bash
php artisan make:controller CustomerController
```

Generated file:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Your methods go here
}
```

### A Controller with CRUD Methods

```php
<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // GET /customers
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', ['customers' => $customers]);
    }

    // GET /customers/create
    public function create()
    {
        return view('customers.create');
    }

    // POST /customers
    public function store(Request $request)
    {
        Customer::create($request->all());
        return redirect('/customers');
    }

    // GET /customers/{id}
    public function show($id)
    {
        $customer = Customer::find($id);
        return view('customers.show', ['customer' => $customer]);
    }

    // GET /customers/{id}/edit
    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('customers.edit', ['customer' => $customer]);
    }

    // PUT /customers/{id}
    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        $customer->update($request->all());
        return redirect('/customers');
    }

    // DELETE /customers/{id}
    public function destroy($id)
    {
        Customer::destroy($id);
        return redirect('/customers');
    }
}
```

> Notice there is no SQL anywhere. The controller asks the Model for data using Eloquent methods like `Customer::all()`, `Customer::find($id)`, `Customer::create()`. The Model handles all database interaction.

---

## Eloquent — The ORM (Object Relational Mapper)

Eloquent is Laravel's database layer. Instead of writing SQL by hand, you interact with your database through PHP objects. Each database table has a corresponding Model class.

Generate a model:

```bash
php artisan make:model Customer
```

Generated file:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
}
```

That is it. Laravel automatically assumes:
- The table name is the plural lowercase of the class name: `Customer` → `customers`
- The primary key column is `id`
- The table has `created_at` and `updated_at` timestamp columns

### Eloquent Methods

```php
// Get all rows
$customers = Customer::all();

// Find by primary key
$customer = Customer::find(5);         // Returns null if not found
$customer = Customer::findOrFail(5);   // Returns 404 if not found

// Get the first match
$customer = Customer::where('email', 'ahmed@example.com')->first();

// All rows matching a condition
$customers = Customer::where('country', 'Egypt')->get();

// Create a new record
Customer::create([
    'first_name' => 'Ahmed',
    'last_name'  => 'Mohamed',
    'email'      => 'ahmed@example.com',
]);

// Update a record
$customer = Customer::find(5);
$customer->update(['first_name' => 'Sara']);

// Delete a record
Customer::destroy(5);
// Or:
$customer = Customer::find(5);
$customer->delete();

// Count rows
$total = Customer::count();

// Order
$customers = Customer::orderBy('first_name', 'asc')->get();

// Paginate — 10 per page
$customers = Customer::paginate(10);
```

### Mass Assignment — `$fillable`

By default, Eloquent protects you from accidentally saving fields you did not intend to. To allow mass assignment (like `Customer::create($request->all())`), you declare which fields are allowed:


لما بنكتب أمر زي: `Customer::create($request->all());` احنا بنقول لـ Laravel خُد كل البيانات اللي جاية من الفورم اللي في المتصفح واحفظها في قاعدة البيانات.

تخيل لو الهكر (Hacker) فتح الـ Developer Tools في المتصفح، وزود field من عنده في الفورم سماه `is_admin` وخلى قيمته `1` وعمل Submit؟ لو لارفيل سابها تعدي، الهكر كده اخترق السيستم

عشان كده الـ Eloquent بيحميك تلقائياً وبيرفض يعمل حرك الـ `create()` دي إلا لما أنت تحدد له بوضوح إيه هي الأعمدة المسموح للمستخدم العادي إنه يكتب فيها جوه الـ Model، وده اللي بنسميه الـ **White-list** عن طريق variable اسمه `$fillable`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'gender',
        'money',
        'city',
        'country',
        'country_code',
    ];
}
```

---

## Migrations — Version Control for Your Database

تخيل لو أنت شغال في مشروع مع 4 مهندسين تانين، وجيت أنت عدلت في قاعدة البيانات وعملت جدول جديد يدويًا من الـ phpMyAdmin أو MySQL Workbench. عشان زمايلك يكملوا شغل، لازم تبعت لهم ملف `.sql` عشان يعملوا له Import عندهم، ولو حد نسى أو اتلخبط، المشروع كله هيضرب!

الـ **Migrations** هي الحل السحري للمشكلة دي، بنسميها **Version Control for Your Database** (زي الـ Git بس لقواعد البيانات). بدل ما تبني الجداول بإيدك، بتكتب كود PHP بيوصف شكل الجدول، والكود ده بيترفع على الـ Git عادي، وزميلك بمجرد ما يعمل `git pull` ويكتب أمر واحد في الـ Terminal، الجدول بيتكريه عنده في ثانية بنفس المواصفات بالضبط.

	Instead of creating tables manually in phpMyAdmin or MySQL CLI, you define them in PHP migration files. This way your database structure is tracked in code alongside your application.

Create a migration:
```bash
php artisan make:migration create_customers_table
```

Generated file in `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
//بنكتب جواها الحاجة اللي عايزين ننشئها أو نضيفها لقاعدة البيانات
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();                        // id INT AUTO_INCREMENT PRIMARY KEY
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 100)->unique();
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->decimal('money', 10, 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->timestamps();                // created_at and updated_at
        });
    }

// عكس اللي فوقيها --> بنكتب جواها إزاي نمسح أو نلغي اللي الميثود اب عملته
    public function down(): void
    {
        Schema::dropIfExists('customers');       // Runs on rollback
    }
};
```

Run the migration — actually creates the table:
```bash
php artisan migrate
```

### Common Column Types

| Method | SQL Equivalent |
|---|---|
| `$table->id()` | `INT AUTO_INCREMENT PRIMARY KEY` |
| `$table->string('name', 100)` | `VARCHAR(100)` |
| `$table->text('description')` | `TEXT` |
| `$table->integer('age')` | `INT` |
| `$table->decimal('price', 8, 2)` | `DECIMAL(8,2)` |
| `$table->boolean('active')` | `TINYINT(1)` |
| `$table->date('birth_date')` | `DATE` |
| `$table->timestamp('created_at')` | `TIMESTAMP` |
| `$table->timestamps()` | `created_at` + `updated_at` |
| `->nullable()` | Allows NULL values |
| `->unique()` | Adds UNIQUE constraint |
| `->default('value')` | Sets default value |

---

## Blade — The Templating Engine

Blade is Laravel's template engine. Blade files end in `.blade.php` and live in `resources/views/`. They are HTML files with special Blade syntax for displaying data and logic.

### Displaying Data

```blade
{{-- This is a Blade comment — not shown in browser --}}

{{-- Output a variable — htmlspecialchars applied automatically --}}
{{ $name }}

{{-- Output raw HTML without escaping (be careful) --}}
{!! $htmlContent !!}
```

> `{{ }}` in Blade automatically applies `htmlspecialchars()` — the same protection you had to write manually in raw PHP. You never need to worry about XSS when using `{{ }}`.

### Control Structures

```blade
{{-- If --}}
@if ($age >= 18)
    <p>Adult</p>
@elseif ($age >= 13)
    <p>Teenager</p>
@else
    <p>Child</p>
@endif

{{-- Foreach --}}
@foreach ($customers as $customer)
    <p>{{ $customer['first_name'] }}</p>
@endforeach

{{-- For --}}
@for ($i = 0; $i < 10; $i++)
    <p>{{ $i }}</p>
@endfor

{{-- While --}}
@while ($condition)
    ...
@endwhile

{{-- Check if variable is empty --}}
@forelse ($customers as $customer)
    <p>{{ $customer['first_name'] }}</p>
@empty
    <p>No customers found</p>
@endforelse
```

### Layouts — Blade Inheritance

Instead of copying the HTML `<head>`, navbar, and footer into every view, you define one master layout and extend it.

**Master layout — `resources/views/layouts/app.blade.php`**

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'My App')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">MyApp</a>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

</body>
</html>
```

**A page that extends the layout — `resources/views/customers/index.blade.php`**

```blade
@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <h1>All Customers</h1>

    <a href="/customers/create" class="btn btn-primary mb-3">Add Customer</a>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $customer)
            <tr>
                <td>{{ $customer->id }}</td>
                <td>{{ $customer->first_name }}</td>
                <td>{{ $customer->last_name }}</td>
                <td>{{ $customer->email }}</td>
                <td>
                    <a href="/customers/{{ $customer->id }}/edit" class="btn btn-sm btn-success">Edit</a>
                    <form action="/customers/{{ $customer->id }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
```

### `@csrf` — Cross-Site Request Forgery Protection

Every HTML form that submits data (POST, PUT, DELETE) must include `@csrf`:

```blade
<form method="POST" action="/customers">
    @csrf
    {{-- @csrf generates a hidden input with a security token --}}
    {{-- Laravel rejects any POST request that does not have this token --}}
    <input type="text" name="first_name">
    <button type="submit">Save</button>
</form>
```

### `@method` — HTML Forms Only Support GET and POST

HTML forms can only submit GET and POST. To send PUT or DELETE from a form, use `@method`:

```blade
<form method="POST" action="/customers/{{ $customer->id }}">
    @csrf
    @method('DELETE')
    {{-- Generates: <input type="hidden" name="_method" value="DELETE"> --}}
    <button type="submit">Delete</button>
</form>
```

متصفحات الإنترنت في العالم كله (زي Chrome و Firefox) لحد النهارده مش بتفهم في الـ HTML Forms غير نوعين بس من الـ Methods: وهما الـ GET والـ POST. مش بتفهم يعني إيه فورم بيبعت طلب PUT للتعديل أو DELETE للحذف.

طيب والعمل؟ لارفيل حلت المشكلة دي بذكاء:

    بنخلي الـ Method الأساسية للفورم دايماً method="POST".

    بنحط جواها @method('DELETE') أو @method('PUT').

البليد هيحولها لـ Hidden input بالشكل ده: <input type="hidden" name="_method" value="DELETE">. أول ما الطلب يوصل للـ Laravel، الـ Router بيقرأ السطر المخفي ده ويفهم إن المستخدم يقصد طلب DELETE حقيقي، فيوجهه للميثود الصح جوه الـ Controller!

---

## Passing Data from Controller to View

```php
// In the controller:
public function index()
{
    $customers = Customer::all();

    // Pass as second argument to view() — an associative array
    return view('customers.index', ['customers' => $customers]);

    // Alternative — compact() creates the array from variable names
    return view('customers.index', compact('customers'));
    // Same as: ['customers' => $customers]
}
```

In the view, each key becomes a variable:

```blade
@foreach ($customers as $customer)
    {{ $customer->first_name }}
@endforeach
```

---

## Named Routes and the `route()` Helper

Named routes let you generate URLs by name instead of hardcoding paths. If you change the URL later, you only update the route definition — not every link in every view.

```php
// routes/web.php
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
```

```blade
{{-- In a Blade template --}}
<a href="{{ route('customers.index') }}">All Customers</a>
<a href="{{ route('customers.show', ['id' => $customer->id]) }}">View</a>
```

Resource routes automatically generate named routes: `customers.index`, `customers.create`, `customers.store`, `customers.show`, `customers.edit`, `customers.update`, `customers.destroy`.

---

## Form Validation

Laravel's validation is built into the Request object:

```php
public function store(Request $request)
{
    $request->validate([
        'first_name' => 'required|min:2|max:100',
        'last_name'  => 'required|min:2|max:100',
        'email'      => 'required|email|unique:customers',
        'money'      => 'nullable|numeric|min:0',
    ]);

    // If validation fails, Laravel automatically redirects back with errors
    // If validation passes, code continues here

    Customer::create($request->all());
    return redirect()->route('customers.index');
}
```

Display errors in the view:

```blade
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="/customers">
    @csrf
    <input type="text" name="first_name" value="{{ old('first_name') }}">
    {{-- old() repopulates the field after a failed validation --}}
    @error('first_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</form>
```

---

## The `dd()` and `dump()` Helpers — Debugging

Laravel includes powerful debugging helpers:

```php
dd($variable);           // dump and die — shows the value and stops execution
dump($variable);         // shows the value and continues
dd($customers->toArray()); // convert Eloquent collection to array first
```

---


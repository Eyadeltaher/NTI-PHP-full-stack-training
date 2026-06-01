# Laravel — Migrations, Eloquent CRUD, Query Builder & Relationships

---

## Migrations

### What is a Migration?

A migration is a PHP file that defines a database table structure using code. Instead of opening phpMyAdmin and creating tables by clicking, you write the table definition in PHP and run one Artisan command to create it.

Migrations are version control for your database. Every developer on a team runs the same migrations and ends up with the identical database structure — no more "it works on my machine" database issues.

```
Without migrations:
  Developer A manually creates table in phpMyAdmin
  Developer B does not know the exact column names and types
  Production server has a different structure from development
  No history of what changed or when

With migrations:
  All developers run: php artisan migrate
  Every environment has identical structure
  The migration files are in git — full history of every change
```

---

### Creating a Migration

```bash
# Create a migration for a new table
php artisan make:migration create_customers_table

# Create a migration AND a model at the same time
php artisan make:model Customer -m

# Create a migration for modifying an existing table
php artisan make:migration add_phone_to_customers_table
php artisan make:migration add_status_column_to_orders_table
```

> Laravel reads the migration name to guess what you want. A name starting with `create_` automatically generates `Schema::create()`. A name starting with `add_` or `modify_` does not — you write the content yourself.

Generated file in `database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This runs when you migrate — creates or modifies the table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // This runs when you rollback — undoes what up() did
        Schema::dropIfExists('customers');
    }
};
```

---

### Building a Real Table

A complete customers table from a real project:

```php
public function up(): void
{
    Schema::create('customers', function (Blueprint $table) {
        $table->id();                                      // id BIGINT AUTO_INCREMENT PRIMARY KEY
        $table->string('first_name', 100);                 // VARCHAR(100) NOT NULL
        $table->string('last_name', 100);                  // VARCHAR(100) NOT NULL
        $table->string('email', 150)->unique();            // VARCHAR(150) UNIQUE NOT NULL
        $table->string('phone', 20)->nullable();           // VARCHAR(20) NULL
        $table->string('password');                        // VARCHAR(255) NOT NULL
        $table->enum('gender', ['male', 'female'])
              ->default('male');                           // ENUM
        $table->decimal('balance', 10, 2)->default(0.00); // DECIMAL(10,2)
        $table->string('city', 100)->nullable();
        $table->string('country', 100)->nullable();
        $table->string('country_code', 5)->nullable();
        $table->boolean('is_active')->default(true);       // TINYINT(1)
        $table->date('birth_date')->nullable();            // DATE
        $table->timestamp('email_verified_at')->nullable();
        $table->rememberToken();                           // VARCHAR(100) for "remember me"
        $table->timestamps();                              // created_at and updated_at
    });
}
```

---

### Column Types Reference

```php
// Numeric
$table->id();                          // Auto-incrementing BIGINT primary key
$table->integer('quantity');           // INT
$table->tinyInteger('status');         // TINYINT
$table->bigInteger('views');           // BIGINT
$table->float('rating', 3, 1);        // FLOAT (total digits, decimal digits)
$table->decimal('price', 8, 2);       // DECIMAL — use for money, never float
$table->boolean('is_active');          // TINYINT(1)
$table->unsignedInteger('count');      // INT no negatives

// String / Text
$table->string('name', 100);           // VARCHAR(100)
$table->string('email');               // VARCHAR(255) default length
$table->char('code', 5);              // CHAR(5) fixed length
$table->text('description');           // TEXT
$table->longText('content');           // LONGTEXT — for articles
$table->enum('status', ['active', 'inactive', 'banned']);

// Date and Time
$table->date('birth_date');            // DATE: 2024-03-15
$table->time('start_time');            // TIME: 14:30:00
$table->dateTime('published_at');      // DATETIME: 2024-03-15 14:30:00
$table->timestamp('verified_at');      // TIMESTAMP
$table->timestamps();                  // created_at + updated_at (both TIMESTAMP)
$table->softDeletes();                 // deleted_at TIMESTAMP NULL — for soft delete

// Special
$table->foreignId('user_id');          // BIGINT UNSIGNED — for foreign keys
$table->json('settings');              // JSON column
$table->uuid('uuid');                  // UUID string
$table->ipAddress('ip');               // VARCHAR(45)
$table->rememberToken();               // VARCHAR(100) for auth remember me
```

### Column Modifiers

```php
$table->string('name')->nullable();                  // Allow NULL
$table->string('status')->default('pending');        // Default value
$table->decimal('price')->unsigned();                // No negatives
$table->string('email')->unique();                   // Unique constraint
$table->string('name')->after('id');                 // Position after another column
$table->string('code')->first();                     // First column in table
$table->text('bio')->nullable()->default(null);      // Chain multiple modifiers
```

---

### Modifying an Existing Table

After a table is live, you never edit the original migration. You create a new migration that adds, modifies, or removes columns:

```bash
php artisan make:migration add_phone_to_customers_table
```

```php
public function up(): void
{
    Schema::table('customers', function (Blueprint $table) {
        // Add a column
        $table->string('phone', 20)->nullable()->after('email');

        // Modify an existing column
        $table->string('first_name', 150)->change();  // Increase length

        // Drop a column
        $table->dropColumn('country_code');

        // Rename a column
        $table->renameColumn('name', 'full_name');

        // Add an index
        $table->index('email');

        // Add a foreign key
        $table->foreignId('category_id')
              ->constrained('categories')
              ->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('customers', function (Blueprint $table) {
        $table->dropColumn('phone');
    });
}
```

---

### Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Check which migrations have run
php artisan migrate:status

# Rollback the last batch of migrations
php artisan migrate:rollback

# Rollback a specific number of batches
php artisan migrate:rollback --step=3

# Drop all tables and re-run all migrations from scratch
php artisan migrate:fresh

# Re-migrate and run seeders
php artisan migrate:fresh --seed

# Run a single specific migration file
php artisan migrate --path=database/migrations/2024_01_01_create_customers_table.php
```

> `migrate:fresh` is for development only. It drops every table and starts over. Never run it on a production server — it destroys all your data.

---

## The Eloquent Model

### What is Eloquent?

Eloquent is Laravel's ORM (Object Relational Mapper). It maps each database table to a PHP class. Instead of writing SQL, you interact with PHP objects. Eloquent translates your PHP code into SQL behind the scenes.

```
Table: customers
Model: Customer class

$customer = Customer::find(5);
// Eloquent runs: SELECT * FROM customers WHERE id = 5 LIMIT 1
```

### Creating a Model

```bash
php artisan make:model Customer
php artisan make:model Customer -m       # With migration
php artisan make:model Customer -mc      # With migration and controller
php artisan make:model Customer -mrc     # With migration, resource controller, and factory
```

Generated `app/Models/Customer.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
}
```

By default Eloquent assumes:
- Table name: plural snake_case of class name → `Customer` → `customers`
- Primary key: `id`
- Table has `created_at` and `updated_at` columns

### Customizing Model Conventions

```php
class Customer extends Model
{
    // Override table name if it does not follow convention
    protected $table = 'tbl_customers';

    // Override primary key
    protected $primaryKey = 'customer_id';

    // If primary key is not auto-incrementing
    public $incrementing = false;

    // If primary key is not an integer
    protected $keyType = 'string';

    // If table has no timestamps columns
    public $timestamps = false;

    // Control mass assignment — which fields can be filled via create() or fill()
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'balance',
        'city',
        'country',
        'is_active',
    ];

    // OR — blacklist approach — everything is fillable EXCEPT these
    protected $guarded = ['id', 'password'];

    // Cast column values to specific PHP types
    protected $casts = [
        'is_active'         => 'boolean',
        'balance'           => 'decimal:2',
        'birth_date'        => 'date',
        'email_verified_at' => 'datetime',
        'settings'          => 'array',   // JSON column → PHP array
    ];

    // Hidden from array/JSON output (e.g., API responses)
    protected $hidden = ['password', 'remember_token'];
}
```

---

## Eloquent CRUD

### CREATE — Inserting Records

#### `create()` — Mass Assignment in One Line

```php
$customer = Customer::create([
    'first_name' => 'Ahmed',
    'last_name'  => 'Mohamed',
    'email'      => 'ahmed@example.com',
    'gender'     => 'male',
    'balance'    => 1500.00,
]);

// $customer now contains the saved record including its auto-generated id
echo $customer->id;         // e.g., 7
echo $customer->first_name; // Ahmed
echo $customer->created_at; // 2024-03-15 14:30:00
```

#### `insert()` — Raw Array Insert

```php
// Insert a single row — no model instance returned
Customer::insert([
    'first_name' => 'Sara',
    'last_name'  => 'Ahmed',
    'email'      => 'sara@example.com',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Insert multiple rows at once — very fast
Customer::insert([
    ['first_name' => 'Omar',   'email' => 'omar@example.com',   'created_at' => now(), 'updated_at' => now()],
    ['first_name' => 'Fatima', 'email' => 'fatima@example.com', 'created_at' => now(), 'updated_at' => now()],
    ['first_name' => 'Zain',   'email' => 'zain@example.com',   'created_at' => now(), 'updated_at' => now()],
]);
```

#### `new` + `save()` — Step by Step

```php
$customer = new Customer;
$customer->first_name = 'Ahmed';
$customer->last_name  = 'Mohamed';
$customer->email      = 'ahmed@example.com';
$customer->save();

// Useful when you need to set properties conditionally
$customer = new Customer;
$customer->first_name = $request->first_name;
if ($request->has('phone')) {
    $customer->phone = $request->phone;
}
$customer->save();
```

#### `firstOrCreate()` — Find or Create

```php
// Find a customer with this email, or create one if not found
$customer = Customer::firstOrCreate(
    ['email' => 'ahmed@example.com'],         // Search conditions
    ['first_name' => 'Ahmed', 'last_name' => 'Mohamed']  // Additional data if creating
);

echo $customer->wasRecentlyCreated; // true if just created, false if found
```

Real situation: User social login — find the user by their Google email or create a new account.

#### `updateOrCreate()` — Update if Exists, Create if Not

```php
// Find and update, or create if not found
$customer = Customer::updateOrCreate(
    ['email' => 'ahmed@example.com'],          // Search conditions
    ['first_name' => 'Ahmed Updated', 'phone' => '+20 100 000 0000']  // Values to set
);
```

Real situation: Importing data from a CSV — for each row, update if it exists, insert if it does not.

---

### `insert()` vs `create()` — The Critical Difference

This is one of the most important things to understand in Eloquent:

| | `create()` | `insert()` |
|---|---|---|
| Returns | Model instance | Boolean (true/false) |
| Fires model events | Yes | No |
| Fills `created_at` / `updated_at` | Yes — automatically | No — you must add them manually |
| Uses `$fillable` protection | Yes | No — bypasses it |
| Returns the new ID | Yes — `$model->id` | No |
| Speed | Slightly slower (one query) | Faster for bulk inserts |
| Use when | Creating one record and needing the model | Bulk inserting many rows fast |

```php
// create() — gets back the model with id and timestamps
$customer = Customer::create(['first_name' => 'Ahmed', 'email' => 'ahmed@example.com']);
echo $customer->id;         // 7
echo $customer->created_at; // 2024-03-15 14:30:00

// insert() — returns true, no model, no timestamps added automatically
$result = Customer::insert(['first_name' => 'Ahmed', 'email' => 'ahmed@example.com']);
// created_at and updated_at will be NULL unless you set them manually
// $result is just true — no id, no model
```

---

### READ — Retrieving Records

#### Get All Records

```php
$customers = Customer::all();
// Returns: Eloquent Collection of all Customer models
// SQL: SELECT * FROM customers
```

#### Find by Primary Key

```php
$customer = Customer::find(5);
// Returns: Customer model or null if not found
// SQL: SELECT * FROM customers WHERE id = 5 LIMIT 1

$customer = Customer::findOrFail(5);
// Returns: Customer model or throws 404 ModelNotFoundException
// Use this in controllers — stops execution with a proper 404

// Find multiple by IDs
$customers = Customer::find([1, 5, 10]);
// Returns: Collection of three customers
```

#### `first()` — First Match

```php
$customer = Customer::where('email', 'ahmed@example.com')->first();
// Returns: first matching Customer or null

$customer = Customer::where('email', 'ahmed@example.com')->firstOrFail();
// Returns: first matching Customer or throws 404
```

#### `get()` — All Matches

```php
$customers = Customer::where('country', 'Egypt')->get();
// Returns: Collection of all matching customers
// SQL: SELECT * FROM customers WHERE country = 'Egypt'
```

#### Chaining Conditions

```php
$customers = Customer::where('country', 'Egypt')
    ->where('is_active', true)
    ->where('balance', '>', 1000)
    ->orderBy('first_name', 'asc')
    ->limit(10)
    ->get();

// orWhere
$customers = Customer::where('country', 'Egypt')
    ->orWhere('country', 'Saudi Arabia')
    ->get();

// whereBetween
$customers = Customer::whereBetween('balance', [500, 5000])->get();

// whereIn
$customers = Customer::whereIn('country', ['Egypt', 'Jordan', 'UAE'])->get();

// whereNull / whereNotNull
$customers = Customer::whereNull('phone')->get();
$customers = Customer::whereNotNull('email_verified_at')->get();

// whereLike
$customers = Customer::where('first_name', 'like', 'Ah%')->get();
```

#### Selecting Specific Columns

```php
$customers = Customer::select('id', 'first_name', 'email')->get();
// SQL: SELECT id, first_name, email FROM customers
// Access as $customer->id, $customer->first_name — other columns not loaded
```

#### Counting and Aggregates

```php
$total    = Customer::count();
$total    = Customer::where('country', 'Egypt')->count();
$maxBal   = Customer::max('balance');
$minBal   = Customer::min('balance');
$avgBal   = Customer::avg('balance');
$totalBal = Customer::sum('balance');
```

#### Pagination

```php
// Paginate — 15 per page by default
$customers = Customer::paginate(15);

// Simple paginate — only prev/next links (more performant)
$customers = Customer::simplePaginate(15);

// In the Blade template
{{ $customers->links() }}
// Generates Bootstrap-compatible pagination links automatically
```

---

### UPDATE — Modifying Records

#### Find and Update

```php
$customer = Customer::find(5);
$customer->first_name = 'Sara';
$customer->email      = 'sara@example.com';
$customer->save();
// SQL: UPDATE customers SET first_name='Sara', email='sara@...' WHERE id = 5
// updated_at is set automatically
```

#### Mass Update with `update()`

```php
// Update one record via model
$customer = Customer::find(5);
$customer->update(['first_name' => 'Sara', 'balance' => 2000]);

// Update many records at once
Customer::where('country', 'Egypt')->update(['is_active' => false]);
// SQL: UPDATE customers SET is_active=0 WHERE country = 'Egypt'
// Note: model events do NOT fire with this approach
```

#### `increment()` and `decrement()`

```php
$customer = Customer::find(5);
$customer->increment('balance', 500);   // balance = balance + 500
$customer->decrement('balance', 200);   // balance = balance - 200

// Or without fetching first
Customer::where('id', 5)->increment('balance', 500);
```

Real situation: User makes a purchase — decrement wallet balance. User gets a refund — increment it.

---

### DELETE — Removing Records

#### Delete a Found Record

```php
$customer = Customer::find(5);
$customer->delete();
// SQL: DELETE FROM customers WHERE id = 5
```

#### Delete by ID Directly

```php
Customer::destroy(5);           // Delete by single ID
Customer::destroy([1, 5, 10]); // Delete multiple IDs
```

#### Delete via Query

```php
Customer::where('is_active', false)->delete();
// SQL: DELETE FROM customers WHERE is_active = 0
// Deletes ALL matching records
```

#### Soft Deletes — Mark as Deleted Without Actually Removing

Add `SoftDeletes` trait to the model and ensure the table has a `deleted_at` column:

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
}
```

Migration column:

```php
$table->softDeletes(); // Adds deleted_at TIMESTAMP NULL
```

```php
$customer = Customer::find(5);
$customer->delete();
// Does NOT delete from database
// Sets deleted_at = now()
// This customer is now invisible to all normal queries

// Normal queries automatically exclude soft-deleted records
$customers = Customer::all();           // Does NOT include deleted
$customer  = Customer::find(5);        // Returns null — not found

// Include soft-deleted records
$all = Customer::withTrashed()->get();
$deleted = Customer::onlyTrashed()->get();

// Permanently delete
$customer = Customer::withTrashed()->find(5);
$customer->forceDelete();

// Restore a soft-deleted record
$customer = Customer::withTrashed()->find(5);
$customer->restore();
```

Real situation: An e-commerce platform where products can be "deleted" from the admin panel but order history must still reference them. Soft deletes let you hide the product from the storefront while keeping it in the database for historical orders.

---

## Query Builder vs Eloquent ORM

Both query the same database, but in different ways.

### Query Builder — DB Facade

Writes SQL-like code in PHP. Works with table names as strings. Returns plain arrays or stdClass objects.

```php
use Illuminate\Support\Facades\DB;

// Select all
$customers = DB::table('customers')->get();
// Returns: Collection of stdClass objects

// With conditions
$customers = DB::table('customers')
    ->where('country', 'Egypt')
    ->where('is_active', 1)
    ->orderBy('first_name')
    ->get();

// Insert
DB::table('customers')->insert([
    'first_name' => 'Ahmed',
    'email'      => 'ahmed@example.com',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Update
DB::table('customers')
    ->where('id', 5)
    ->update(['first_name' => 'Sara']);

// Delete
DB::table('customers')->where('id', 5)->delete();

// Accessing data — stdClass
foreach ($customers as $customer) {
    echo $customer->first_name;  // Arrow on object
}
```

### Eloquent ORM

Works with model classes. Returns model instances. Has relationships, events, mutators, and all Eloquent features.

```php
// Select all
$customers = Customer::all();
// Returns: Collection of Customer model instances

// With conditions
$customers = Customer::where('country', 'Egypt')
    ->where('is_active', true)
    ->orderBy('first_name')
    ->get();

// Insert
Customer::create(['first_name' => 'Ahmed', 'email' => 'ahmed@example.com']);

// Update
Customer::where('id', 5)->update(['first_name' => 'Sara']);

// Delete
Customer::where('id', 5)->delete();

// Accessing data — model instance
foreach ($customers as $customer) {
    echo $customer->first_name;   // Property on model
    echo $customer->orders->count(); // Access relationships
}
```

### When to Use Which

| Situation | Use |
|---|---|
| Full CRUD with relationships, events, validation | Eloquent ORM |
| Complex SQL queries with multiple joins and subqueries | Query Builder |
| Bulk insert of thousands of rows fast | Query Builder `insert()` or Eloquent `insert()` |
| One-off database operations in a migration | Query Builder |
| Working with tables that have no model | Query Builder |
| Need model events (creating, updated, deleted) | Eloquent ORM |
| Need `$hidden`, `$casts`, mutators, accessors | Eloquent ORM |
| Need soft deletes | Eloquent ORM |
| Raw performance on large datasets | Query Builder |

> In practice: use Eloquent for most of your application. Use Query Builder when Eloquent's overhead is a problem or when the query is complex enough that SQL-style writing is clearer.

---

## Eloquent Relationships

Relationships define how tables connect to each other and let you load related data through PHP instead of writing JOIN queries.

### One-to-One — `hasOne` / `belongsTo`

One user has one profile. One profile belongs to one user.

```
users table:    id | name | email
profiles table: id | user_id | bio | avatar
```

```php
// User model
class User extends Model
{
    public function profile()
    {
        return $this->hasOne(Profile::class);
        // Laravel assumes profiles.user_id = users.id
    }
}

// Profile model
class Profile extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
        // Laravel assumes profiles.user_id references users.id
    }
}
```

Usage:

```php
$user = User::find(1);
$bio  = $user->profile->bio;        // Load profile via relationship

$profile = Profile::find(1);
$name    = $profile->user->name;    // Load user via relationship
```

Creating related record:

```php
$user = User::find(1);
$user->profile()->create([
    'bio'    => 'Laravel developer',
    'avatar' => 'profile.jpg',
]);
// Sets user_id = 1 automatically
```

---

### One-to-Many — `hasMany` / `belongsTo`

One customer has many orders. Each order belongs to one customer.

```
customers table: id | name | email
orders table:    id | customer_id | total | status
```

```php
// Customer model
class Customer extends Model
{
    public function orders()
    {
        return $this->hasMany(Order::class);
        // Laravel assumes orders.customer_id = customers.id
    }
}

// Order model
class Order extends Model
{
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
```

Usage:

```php
$customer = Customer::find(1);
$orders   = $customer->orders;           // Collection of all orders
$count    = $customer->orders()->count();

// Chaining conditions on the relationship
$pending  = $customer->orders()
    ->where('status', 'pending')
    ->orderBy('created_at', 'desc')
    ->get();

// Access customer from order
$order    = Order::find(5);
$name     = $order->customer->first_name;
```

Creating related record:

```php
$customer = Customer::find(1);

// Method 1 — via relationship (sets customer_id automatically)
$customer->orders()->create([
    'total'  => 250.00,
    'status' => 'pending',
]);

// Method 2 — manually
Order::create([
    'customer_id' => 1,
    'total'       => 250.00,
    'status'      => 'pending',
]);
```

Real situation: Show a customer's order history in their profile page.

---

### Many-to-Many — `belongsToMany`

A student can enroll in many courses. A course can have many students.

```
students table:         id | name
courses table:          id | title
student_course table:   student_id | course_id    (pivot table)
```

```php
// Student model
class Student extends Model
{
    public function courses()
    {
        return $this->belongsToMany(Course::class);
        // Pivot table: student_course
        // student_course.student_id, student_course.course_id
    }
}

// Course model
class Course extends Model
{
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
}
```

Usage:

```php
$student = Student::find(1);
$courses = $student->courses;          // All courses this student is enrolled in

$course   = Course::find(1);
$students = $course->students;         // All students in this course

// Attach — enroll a student in a course
$student->courses()->attach($courseId);
$student->courses()->attach([1, 2, 3]);   // Attach multiple

// Detach — remove enrollment
$student->courses()->detach($courseId);
$student->courses()->detach();            // Detach all

// Sync — replace all enrollments
$student->courses()->sync([1, 3, 5]);
// Removes courses not in the list, adds new ones
```

---

### Eager Loading — Solving the N+1 Problem

Without eager loading, Laravel runs a query for each related model — one query per row. With 100 customers it runs 101 queries.

```php
// N+1 problem — 1 query for customers + 1 query per customer for orders
$customers = Customer::all();
foreach ($customers as $customer) {
    echo $customer->orders->count(); // Each of these runs a separate query
}
// 100 customers = 101 queries total

// Eager loading — 2 queries total, regardless of how many customers
$customers = Customer::with('orders')->get();
// 1 query: SELECT * FROM customers
// 1 query: SELECT * FROM orders WHERE customer_id IN (1, 2, 3, ...)

foreach ($customers as $customer) {
    echo $customer->orders->count(); // No extra query — already loaded
}

// Multiple relationships
$customers = Customer::with(['orders', 'profile'])->get();

// Nested relationships
$customers = Customer::with('orders.items')->get();
// Loads customers → orders → order items in 3 queries
```

> The N+1 problem is the most common Laravel performance issue. Any time you access a relationship inside a loop, use eager loading with `with()`.

---

## Quick Reference

| Concept | Syntax | Notes |
|---|---|---|
| Run migrations | `php artisan migrate` | Creates all pending tables |
| Fresh migration | `php artisan migrate:fresh` | Drops all tables — dev only |
| Rollback | `php artisan migrate:rollback` | Undoes last batch |
| `create()` | `Model::create([...])` | Returns model instance + auto timestamps |
| `insert()` | `Model::insert([...])` | Returns bool — no timestamps, faster |
| `new` + `save()` | `$m = new Model; $m->field = x; $m->save()` | Step by step |
| `firstOrCreate()` | `Model::firstOrCreate([search], [create])` | Find or insert |
| `updateOrCreate()` | `Model::updateOrCreate([search], [values])` | Upsert |
| `find($id)` | `Model::find(5)` | By primary key — null if not found |
| `findOrFail($id)` | `Model::findOrFail(5)` | By primary key — 404 if not found |
| `first()` | `Model::where(...)->first()` | First match or null |
| `get()` | `Model::where(...)->get()` | All matches as Collection |
| `all()` | `Model::all()` | Every row |
| `count()` | `Model::count()` | Number of rows |
| `update()` | `$model->update([...])` | Save changed fields |
| `save()` | `$model->save()` | Save current model state |
| `delete()` | `$model->delete()` | Delete this record |
| `destroy($id)` | `Model::destroy(5)` | Delete by ID |
| Soft delete | `$model->delete()` + `SoftDeletes` trait | Sets deleted_at, hides from queries |
| `withTrashed()` | `Model::withTrashed()->get()` | Include soft-deleted records |
| `forceDelete()` | `$model->forceDelete()` | Permanently delete soft-deleted |
| `restore()` | `$model->restore()` | Undelete soft-deleted record |
| `increment()` | `$model->increment('col', 100)` | col = col + 100 |
| `paginate()` | `Model::paginate(15)` | 15 per page with full links |
| `$fillable` | `protected $fillable = [...]` | Columns allowed for mass assignment |
| `$hidden` | `protected $hidden = [...]` | Columns excluded from JSON |
| `$casts` | `protected $casts = [...]` | Auto-convert column types |
| Query Builder | `DB::table('name')->get()` | Returns stdClass, no model features |
| Eloquent ORM | `Model::get()` | Returns model instances, full features |
| `hasOne` | `return $this->hasOne(Model::class)` | One child record |
| `hasMany` | `return $this->hasMany(Model::class)` | Many child records |
| `belongsTo` | `return $this->belongsTo(Model::class)` | This model is the child |
| `belongsToMany` | `return $this->belongsToMany(Model::class)` | Many-to-many via pivot table |
| Eager loading | `Model::with('relation')->get()` | Load relationship in 1 extra query |
| Nested eager loading | `Model::with('orders.items')->get()` | Load nested relationships |
| N+1 problem | Accessing relationship inside loop without `with()` | 1 query per row — always use `with()` |
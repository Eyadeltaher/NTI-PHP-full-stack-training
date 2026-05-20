# Laravel — Routes

---

## What is a Route?

A route is a mapping between a URL and a piece of code. When a user visits a URL, Laravel looks through all defined routes, finds the one that matches, and runs the code attached to it.

All web routes are defined in `routes/web.php`.

---

## Basics of Routing

### The Six HTTP Methods

```php
Route::get('/path', function () {});        // Read — retrieve data
Route::post('/path', function () {});       // Create — submit data
Route::put('/path', function () {});        // Update — replace entire record
Route::patch('/path', function () {});      // Update — partial update
Route::delete('/path', function () {});     // Delete a record
Route::options('/path', function () {});    // Get information about a route
```

- `GET` — used to load pages, display data
- `POST` — used when submitting forms to create something
- `PUT` — update an entire record (send all fields)
- `PATCH` — update specific fields only
- `DELETE` — remove a record
- `OPTIONS` — rarely used directly, more relevant for APIs and CORS

### Returning a View

```php
Route::get('/', function () {
    return view('welcome');
    // Loads: resources/views/welcome.blade.php
});

Route::get('/about', function () {
    return view('about');
    // Loads: resources/views/about.blade.php
});
```

### View Route Shorthand

When a route only needs to return a view with no logic, use `Route::view()` — no controller or closure needed:

```php
Route::view('/about', 'about');
// Equivalent to: Route::get('/about', function() { return view('about'); });

// With data passed to the view
Route::view('/contact', 'contact', ['phone' => '+20 100 000 0000']);
```

### Redirect Route

```php
// Temporary redirect — status code 302 (default)
Route::redirect('/old-url', '/new-url');

// Permanent redirect — status code 301
Route::redirect('/old-url', '/new-url', 301);

// Permanent redirect using dedicated method
Route::permanentRedirect('/old-url', '/new-url');
```

> 302 means "temporarily moved" — search engines keep the old URL indexed. 301 means "permanently moved" — search engines transfer the ranking to the new URL. Use 301 when you are permanently changing a URL.

### Listening to Multiple Methods

```php
// Route responds to both GET and POST
Route::match(['get', 'post'], '/contact', function () {
    // runs for both GET and POST requests to /contact
});

// Route responds to all HTTP methods
Route::any('/endpoint', function () {
    // runs for any request method
});
```

---

## Route Required Parameters

Required parameters are parts of the URL that change — like an ID or a username. They are defined using curly braces `{name}`.

```php
// Single parameter
Route::get('/product/{id}', function ($id) {
    return "Product ID: " . $id;
});
```

Accessing `/product/1` → `Product ID: 1`
Accessing `/product/abc` → `Product ID: abc` (any value works unless restricted)

### Multiple Parameters

```php
Route::get('/{lang}/product/{id}/review/{reviewId}', function ($lang, $id, $reviewId) {
    return "Language: $lang | Product: $id | Review: $reviewId";
});
```

The variables in the function must be listed in the same order as in the URL. The names do not have to match, but the order does.

```
/en/product/5/review/12   → lang=en, id=5, reviewId=12
/ar/product/10/review/3   → lang=ar, id=10, reviewId=3
```

> Any string is accepted for each parameter as long as it does not contain a forward slash `/`. The slash is a special URL character — it separates URL segments.

---

## Route Optional Parameters

Optional parameters may or may not be present in the URL. They are defined with a question mark `?` and the function parameter must have a default value.

```php
Route::get('/product/{category?}', function ($category = null) {
    return "Category: " . $category;
});
```

```
/product/cars   → category = "cars"
/product        → category = null   (no error because default value is set)
```

> The default value is required. Without it, PHP will throw an error when the parameter is missing from the URL.

### Multiple Optional Parameters

```php
Route::get('/shop/{category?}/{subcategory?}', function ($category = null, $subcategory = null) {
    return "Category: $category | Sub: $subcategory";
});
```

---

## Route Parameter Validation — `where()`

By default, route parameters accept any value. The `where()` method restricts what a parameter can contain.

### Built-in Helpers

```php
// Only numeric values
Route::get('/product/{id}', function ($id) {
    return "Product: $id";
})->whereNumber('id');

// Only alphabetic characters (a-z, A-Z) — no numbers
Route::get('/user/{username}', function ($username) {
    return "User: $username";
})->whereAlpha('username');

// Only alphanumeric characters (letters and numbers)
Route::get('/profile/{username}', function ($username) {
    return "Profile: $username";
})->whereAlphaNumeric('username');

// Only UUID format
Route::get('/item/{id}', function ($id) {
    return $id;
})->whereUuid('id');
```

```
whereNumber:       /product/5     → works    /product/abc  → 404
whereAlpha:        /user/ahmed    → works    /user/ahmed99 → 404
whereAlphaNumeric: /profile/ahmed99 → works /profile/a@b  → 404
```

### Validating Multiple Parameters at Once

Pass an array to `where()`:

```php
Route::get('/{lang}/product/{id}', function ($lang, $id) {
    return "$lang | $id";
})->where([
    'lang' => '[a-zA-Z]{2}',   // Exactly 2 letters
    'id'   => '[0-9]+',        // One or more digits
]);
```

---

## Route Parameter Regex Validation

For full control, use custom regular expressions with `where()`.

```php
// Only lowercase letters
Route::get('/user/{username}', function ($username) {
    return $username;
})->where('username', '[a-z]+');

// Exactly 2 uppercase or lowercase letters
Route::get('/{lang}/product/{id}', function ($lang, $id) {
    return "$lang | $id";
})->where('lang', '[a-zA-Z]{2}')
  ->where('id', '[0-9]{4,}');
  // id must be at least 4 digits
```

### Regex Reference for Routes

| Pattern | Matches | Does not match |
|---|---|---|
| `[0-9]+` | `5`, `123` | `abc`, `12a` |
| `[a-z]+` | `ahmed`, `hello` | `Ahmed`, `123` |
| `[a-zA-Z]+` | `Ahmed`, `hello` | `ahmed1`, `123` |
| `[a-zA-Z0-9]+` | `ahmed1`, `Hello99` | `ahmed@`, `hello!` |
| `[a-zA-Z]{2}` | `en`, `AR` | `e`, `eng` |
| `[0-9]{4,}` | `1234`, `99999` | `123` (too short) |
| `en\|ar\|fr` | `en`, `ar`, `fr` | `de`, `es` |

### Allowing Slashes in a Parameter

By default, URL parameters cannot contain `/`. To allow it:

```php
Route::get('/search/{query}', function ($query) {
    return $query;
})->where('query', '.+');
// .+ means: any character (including /) at least once
```

```
/search/hello         → query = "hello"
/search/9/15          → query = "9/15"   (slash included)
/search/cats/food     → query = "cats/food"
```

---

## Named Routes

A named route is a route with an assigned label. Instead of hardcoding the URL in links and redirects, you reference the route by its name. If the URL changes, every link generated from the name updates automatically.

### Assigning a Name

```php
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/user/profile', function () {
    return view('profile');
})->name('profile');
```

### Generating URLs from Names

```php
$url = route('about');
// Returns: http://your-app.com/about

$url = route('profile');
// Returns: http://your-app.com/user/profile
```

In a Blade template:

```blade
<a href="{{ route('about') }}">About Us</a>
<a href="{{ route('profile') }}">My Profile</a>
```

### Why This Matters

```php
// Without named routes — URL is hardcoded everywhere
<a href="/about">About</a>   // in header
<a href="/about">About</a>   // in footer
<a href="/about">About</a>   // in sidebar

// You decide to change /about to /about-us
// You must find and update every single link manually

// With named routes — URL is generated from the name
<a href="{{ route('about') }}">About</a>   // in header
<a href="{{ route('about') }}">About</a>   // in footer
<a href="{{ route('about') }}">About</a>   // in sidebar

// You change the URL in ONE place — routes/web.php
Route::get('/about-us', function () { ... })->name('about');
// All three links update automatically — zero changes needed
```

### Redirect to a Named Route

```php
// Using redirect() with route()
return redirect(route('profile'));

// Using redirect()->route() — cleaner
return redirect()->route('profile');
```

---

## Named Routes with Parameters

When a named route has parameters, pass them as the second argument to `route()`.

```php
Route::get('/product/{id}', function ($id) {
    return "Product $id";
})->name('product.show');

Route::get('/{lang}/product/{id}', function ($lang, $id) {
    return "$lang | $id";
})->name('product.view');
```

Generating URLs with parameters:

```php
// Single parameter
$url = route('product.show', ['id' => 5]);
// Returns: /product/5

// Multiple parameters
$url = route('product.view', ['lang' => 'en', 'id' => 5]);
// Returns: /en/product/5
```

In Blade:

```blade
<a href="{{ route('product.show', ['id' => $product->id]) }}">View Product</a>
<a href="{{ route('product.view', ['lang' => 'en', 'id' => $product->id]) }}">View</a>
```

> Naming convention for routes: use dot notation — `resource.action`. For example: `customers.index`, `customers.show`, `customers.create`, `customers.store`, `customers.edit`, `customers.update`, `customers.destroy`. This is the same naming pattern Laravel uses automatically for resource routes.

---

## Route Groups

Route groups let you apply shared configuration — prefix, name prefix, middleware — to multiple routes at once without repeating yourself.

### URL Prefix

All routes in the group share the same URL prefix:

```php
Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        return 'Admin Users';
    });
    // URL: /admin/users

    Route::get('/dashboard', function () {
        return 'Admin Dashboard';
    });
    // URL: /admin/dashboard

    Route::get('/settings', function () {
        return 'Admin Settings';
    });
    // URL: /admin/settings
});
```

Without the group, you would have to repeat `admin/` in every route:
```php
Route::get('/admin/users', ...);
Route::get('/admin/dashboard', ...);
Route::get('/admin/settings', ...);
```

### Name Prefix

All routes in the group share the same name prefix:

```php
Route::name('admin.')->group(function () {
    Route::get('/users', function () {
        return 'Users';
    })->name('users');
    // Full name: admin.users

    Route::get('/dashboard', function () {
        return 'Dashboard';
    })->name('dashboard');
    // Full name: admin.dashboard
});

// Generate URLs
route('admin.users');       // /users
route('admin.dashboard');   // /dashboard
```

### URL Prefix and Name Prefix Together

```php
Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/users', function () {
        return 'Users';
    })->name('users');
    // URL:  /admin/users
    // Name: admin.users

    Route::get('/dashboard', function () {
        return 'Dashboard';
    })->name('dashboard');
    // URL:  /admin/dashboard
    // Name: admin.dashboard
});
```

### Middleware Group

```php
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        // Only logged-in users can access this
    });
    Route::get('/profile', function () {
        // Only logged-in users can access this
    });
});
```

> Middleware will be covered in detail separately. The key idea is: instead of applying `->middleware('auth')` to every single protected route, you wrap them all in a group once.

---

## Fallback Routes

A fallback route matches any URL that did not match any other defined route — the "catch all."

```php
Route::fallback(function () {
    return view('errors.404');
    // Or return a response:
    // return response()->view('errors.404', [], 404);
});
```

> The fallback route must be the last route defined in `web.php`. Laravel matches routes in order — if you define the fallback before other routes, it could catch requests that should go elsewhere.

Real situations:
- Show a custom branded 404 page instead of Laravel's default one
- Log 404 errors to a database for analysis
- Redirect users to the homepage with a "page not found" message

---

## Viewing Registered Routes with Artisan

```bash
# List all routes
php artisan route:list

# Include middleware details for each route
php artisan route:list -v

# Show only your custom routes (exclude Laravel's built-in vendor routes)
php artisan route:list --except-vendor

# Show only vendor routes
php artisan route:list --only-vendor

# Filter by path
php artisan route:list --path=admin
php artisan route:list --path=customers

# Filter by method
php artisan route:list --method=GET

# Combine filters
php artisan route:list --except-vendor --path=admin
```

Output shows: method, URI, name, action (controller@method), and middleware.

> Run `php artisan route:list` whenever you are not sure if a route is registered correctly, or when you need to see the exact URL a named resource route generates.

---

## Route Caching

In production, when your application has many routes, caching speeds up the routing process significantly.

```bash
# Cache all routes into a single file
php artisan route:cache

# Clear the route cache
php artisan route:clear
```

### Important Rules for Route Caching

Only routes that point to a controller can be cached. Routes that use a closure (anonymous function) cannot be cached:

```php
// Cannot be cached
Route::get('/about', function () {
    return view('about');
});

// Can be cached — points to a controller method
Route::get('/about', [PageController::class, 'about']);
```

> In development: never cache routes. Every time you add or change a route, you would need to re-run `php artisan route:cache` to see the change — which creates confusion. Only run `route:cache` as part of your production deployment process.

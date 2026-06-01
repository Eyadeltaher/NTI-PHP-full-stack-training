## 1. Creating the Main Layout File

We use Artisan to generate the main layout view.

```bash
php artisan make:view layouts.app
```

This creates `resources/views/layouts/app.blade.php`.

### Basic Layout Structure (`app.blade.php`)

We can build a standard HTML structure using Blade directives. Use `@yield('section_name')` to define placeholders that child views will fill.

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Car Selling Website</title>
</head>
<body>
    <header>
        Your Header
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        Your Footer
    </footer>
</body>
</html>
```

- **`@yield('title')`**: A placeholder for the page title.
- **`@yield('content')`**: A placeholder for the main page content.

## 2. Using the Layout in a Child View

In a child view (e.g., `index.blade.php`), extend the layout and define the sections.

```blade
{{-- resources/views/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Home Page')

@section('content')
    <h1>Homepage content goes here</h1>
    <p>This is the main content of the homepage.</p>
@endsection
```

- **`@extends('layouts.app')`**: Specifies the parent layout to use.
- **`@section('title', 'Home Page')`**: A shortcut to define a section with a single-line value.
- **`@section('content') ... @endsection`**: Defines a section with multi-line HTML content.

## 3. Outputting Dynamic Data in the Layout

### Application Language (`lang` attribute)

We can make the HTML `lang` attribute dynamic based on the application's configured locale. It's a best practice to replace underscores with dashes.

```html
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
```

- **`app()->getLocale()`**: Gets the current application locale (e.g., 'en', 'es_ES').
- **`str_replace('_', '-', ...)`**: Converts locale codes like 'es_ES' to 'es-ES', which is the standard for the `lang` attribute.

### Application Name (`<title>` and CSRF Token)

We can use the `config()` helper to fetch values from configuration files and the `csrf_token()` function for security.

```html
<head>
    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
```

- **`config('app.name', 'Laravel')`**: Retrieves the `name` value from `config/app.php`. The second parameter is a default fallback. This value is initially set in your `.env` file as `APP_NAME`.
- **`csrf_token()`**: Generates a CSRF token and outputs it. This is crucial for securing your application against cross-site request forgery attacks.

## 4. Creating and Using a Parent Layout (`clean.blade.php`)

Our application might have multiple distinct layouts. For example, pages like "Sign Up" and "Login" often have a simpler layout without the main site's header and footer.

We can create a base "clean" layout that both the main layout and the auth layout can extend.

### Create the Clean Layout

```bash
php artisan make:view layouts.clean
```

**`clean.blade.php` (Parent Layout)**
This file contains the absolute base HTML that every page shares: `<doctype>`, `<head>`, and the main scripts. It yields a section for its children.

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Common CSS, meta tags --}}
    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
</head>
<body>
    @yield('child_content')

    {{-- Common JS files --}}
</body>
</html>
```

### Refactor the Main Layout (`app.blade.php`)

Now, `app.blade.php` will extend `layouts.clean` instead of being the top-level layout.

```blade
{{-- resources/views/layouts/app.blade.php --}}
@extends('layouts.clean')

@section('child_content')
    <header>
        @include('layouts.partials.header')
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        Your Footer
    </footer>
@endsection
```

- **`@extends('layouts.clean')`**: The main layout now inherits from the clean layout.
- **`@section('child_content')`**: This fills the `@yield('child_content')` placeholder in the parent `clean` layout with the main site's header, main content area, and footer.

### Organizing with Partials

To keep layouts clean, extract large sections like headers into their own partial files.

```bash
# Create a partial for the header
php artisan make:view layouts.partials.header
```

Move the entire `<header>...</header>` HTML into this new file. Then, include it in your layout.

```blade
{{-- In app.blade.php --}}
@include('layouts.partials.header')
```

## 5. Creating the Signup and Login Pages

### Create Controllers and Views

```bash
# Create controllers
php artisan make:controller SignupController
php artisan make:controller LoginController

# Create views for the forms
php artisan make:view auth.signup
php artisan make:view auth.login
```

### Controller Logic

```php
// app/Http/Controllers/SignupController.php
public function create() {
    return view('auth.signup');
}
```

### Child Views Using the Clean Layout

Both `signup.blade.php` and `login.blade.php` will extend the `clean` layout directly, bypassing the header/footer.

```blade
{{-- resources/views/auth/signup.blade.php --}}
@extends('layouts.clean')

@section('title', 'Sign Up')
{{-- Optional: Pass a CSS class to the parent layout --}}
@section('child_content')
    <main class="signup-form">
        {{-- Paste the signup form HTML here --}}
    </main>
@endsection
```

### Defining Routes

```php
// routes/web.php
use App\Http\Controllers\SignupController;
use App\Http\Controllers\LoginController;

Route::get('/signup', [SignupController::class, 'create']);
Route::get('/login', [LoginController::class, 'create']);
```

## 6. Passing Data to Layouts (e.g., Dynamic CSS Class)

We can pass data from a child view to its parent layout.

**Goal:** Add a `page-signup` class to the `<body>` of `clean.blade.php` only for the signup page.

**Step 1: Pass data from child view**
```blade
{{-- resources/views/auth/signup.blade.php --}}
@extends('layouts.clean', ['css_class' => 'page-signup'])
```

**Step 2: Display data in parent layout**
```blade
{{-- resources/views/layouts/clean.blade.php --}}
<body @isset($css_class) class="{{ $css_class }}" @endisset>
```
- **`@isset($css_class)`**: This directive checks if the variable `$css_class` is set and is not `null`. If true, it executes the contained code, preventing errors or empty class attributes.

## 7. In-Depth Look at Directives

### `@section`, `@show`, and `@parent`

These directives allow for flexible content inheritance and appending.

**Example in `app.blade.php`:**
```blade
<footer>
    @section('footer_links')
        <a href="#">Link 1</a>
        <a href="#">Link 2</a>
    @show
</footer>
```
- **`@section...@show`**: This defines a section *and* immediately yields it. If a child view doesn't override this section, "Link 1" and "Link 2" are the defaults.
- **`@parent`**: In a child view, `@parent` lets you append to the parent's section content instead of overwriting it entirely.

**Child view usage (`index.blade.php`):**
```blade
@section('footer_links')
    @parent {{-- Outputs "Link 1 Link 2" --}}
    <a href="#">Link 3</a>
    <a href="#">Link 4</a>
@endsection
```
**Result in footer:** Link 1 Link 2 Link 3 Link 4

### `@hasSection` and `@sectionMissing`

These directives check if a section has been defined, allowing you to conditionally render HTML.

```blade
{{-- In app.blade.php --}}
@hasSection('footer_links')
    <footer>
        @yield('footer_links')
    </footer>
@else
    {{-- Optionally show a default footer or nothing --}}
@endif
```
- **`@hasSection('section_name')`**: Returns true if the section is defined.
- **`@sectionMissing('section_name')`**: The inverse of `@hasSection`.

### Form Attribute Directives

These are convenient shortcuts for conditionally adding boolean attributes to HTML form elements.

- **`@checked(boolean_expression)`**: Adds the `checked` attribute to a checkbox or radio input if the expression is true.
- **`@selected(boolean_expression)`**: Adds the `selected` attribute to an `<option>` tag.
- **`@disabled(boolean_expression)`**: Adds the `disabled` attribute.
- **`@readonly(boolean_expression)`**: Adds the `readonly` attribute.
- **`@required(boolean_expression)`**: Adds the `required` attribute.

**Example for a dropdown:**
```blade
<select name="year">
    @foreach($years as $year)
        <option value="{{ $year }}" @selected($year == date('Y'))>
            {{ $year }}
        </option>
    @endforeach
</select>
```
In this case, only the option matching the current year will have the `selected` attribute.

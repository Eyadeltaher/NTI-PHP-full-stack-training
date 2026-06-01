# Laravel — Views & Blade Templating

---

## What is a View?

A view is a file responsible for the presentation layer of your application — the HTML that gets sent to the browser. Views live in `resources/views/` and are completely separate from your controllers and models.

In Laravel, views use the **Blade** templating engine. Blade files have the extension `.blade.php` and allow you to mix HTML with clean PHP-like syntax using directives.

---

## Creating Views

### By Hand

Create a file directly in `resources/views/`:

```
resources/views/index.blade.php
resources/views/home/index.blade.php
resources/views/customers/show.blade.php
```

### With Artisan

```bash
# Create directly in resources/views/
php artisan make:view index

# Create inside a subfolder (dot = folder separator)
php artisan make:view home.index
php artisan make:view customers.show
php artisan make:view shared.alert
```

> All view names should be lowercase. The dot notation in Artisan mirrors the dot notation used when rendering the view — `home.index` creates `resources/views/home/index.blade.php` and is rendered with `view('home.index')`.

---

## Rendering Views

### From a Controller — `view()` Helper

```php
// Renders resources/views/index.blade.php
return view('index');

// Renders resources/views/home/index.blade.php
return view('home.index');

// Renders resources/views/customers/show.blade.php
return view('customers.show');
```

### Using the View Facade

```php
use Illuminate\Support\Facades\View;

// Equivalent to view('home.index')
return View::make('home.index');
```

### `View::first()` — Render the First Available View

Tries each view in the list and renders the first one that exists:

```php
return View::first(['index', 'home.index']);
// If resources/views/index.blade.php exists → render it
// Otherwise → render resources/views/home/index.blade.php
```

Real situation: A fallback system where you want a custom view if it exists, otherwise fall back to a default one.

### Checking if a View Exists

```php
if (view()->exists('home.index')) {
    return view('home.index');
} else {
    return "View does not exist";
}
```

---

## Passing Data to Views

### Method 1 — Associative Array as Second Argument

```php
return view('home.index', [
    'name'    => 'Ahmed',
    'surname' => 'Mohamed',
]);
```

### Method 2 — `compact()` Helper

`compact()` creates an array from variable names automatically:

```php
$name    = 'Ahmed';
$surname = 'Mohamed';

return view('home.index', compact('name', 'surname'));
// Same as: ['name' => $name, 'surname' => $surname]
```

### Method 3 — `with()` Chain

```php
return view('home.index')
    ->with('name', 'Ahmed')
    ->with('surname', 'Mohamed');
```

All three methods produce the same result inside the view — the variables are available as `$name` and `$surname`.

---

## Global Shared Data — Available in ALL Views

Sometimes you need a variable accessible in every view across the application — for example the current year, the logged-in user's name, or the site name. Define this in `AppServiceProvider`:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\View;

public function boot(): void
{
    View::share('year', date('Y'));
    View::share('siteName', config('app.name'));
}
```

After this, `$year` and `$siteName` are available in every `.blade.php` file without passing them from each controller.

---

## Blade Syntax — Displaying Data

### Echoing Variables — `{{ }}`

```blade
{{ $name }}
{{ $customer->first_name }}
{{ $user['email'] }}
```

> `{{ }}` automatically applies `htmlspecialchars()` — all output is escaped. If `$name` contains `<script>alert('xss')</script>`, it will display as text, never execute. This is the safe default.

### Raw/Unescaped Output — `{!! !!}`

```blade
{!! $htmlContent !!}
{!! $formattedText !!}
```

> Use `{!! !!}` only when you absolutely trust the content — for example HTML you generated yourself. Never use it with user-submitted data. It outputs exactly what is in the variable, tags and all.

### Using PHP Functions and Classes in Blade

Blade double curly braces can contain any PHP expression:

```blade
{{ date('Y') }}
{{ strtoupper($name . ' ' . $surname) }}
{{ Str::after('hello world', 'hello ') }}
{{ config('app.name') }}
{{ PHP_EOL }}
```

### The `Js` Class — Converting PHP Arrays to JavaScript

When you need a PHP variable available in JavaScript:

```blade
<script>
    const hobbies = {!! Js::from($hobbies) !!};
</script>
```

```php
// In the controller:
return view('home.index', [
    'hobbies' => ['tennis', 'fishing']
]);
```

The `Js::from()` method encodes the PHP array into a valid JSON value safe to embed in JavaScript. The result in the browser's source:

```html
<script>
    const hobbies = JSON.parse('["tennis","fishing"]');
</script>
```

---

## Escaping Blade Syntax

### Prevent a Single Expression from Being Processed — `@`

When using a frontend framework like Vue.js that also uses `{{ }}`, you need to tell Blade to ignore specific expressions:

```blade
@{{ name }}
{{-- Blade ignores this — outputs literally: {{ name }} --}}
{{-- Vue.js then processes it on the frontend --}}
```

### Prevent Multiple Expressions — `@verbatim`

When you have a large block of Vue.js or other frontend templates:

```blade
@verbatim
    <div>
        <p>{{ name }}</p>
        <p>{{ age }}</p>
        @if (isAdmin)
            <p>Admin Panel</p>
        @endif
    </div>
@endverbatim
```

Everything inside `@verbatim` and `@endverbatim` is output exactly as written — Blade does not process any of it.

### Escaping Directives — `@@`

If you need to literally print `@foreach` or `@if` as text:

```blade
@@foreach   {{-- Outputs: @foreach --}}
@@if        {{-- Outputs: @if --}}
```

---

## Comments

### HTML Comment — Visible in Page Source

```blade
<!-- This comment appears in the browser's view-source -->
```

### Blade Comment — Completely Hidden

```blade
{{-- This comment never appears in the HTML source --}}

{{--
    Multi-line
    blade comment
    also hidden from source
--}}
```

> Use Blade comments `{{-- --}}` for developer notes in templates. They are stripped out entirely — never sent to the browser. HTML comments `<!-- -->` are included in the page source and visible to anyone who views it.

---

## Blade Directives — Control Flow

All Blade directives start with `@`. They compile to plain PHP code.

### `@if`, `@elseif`, `@else`, `@endif`

```blade
@if (count($cars) > 1)
    <p>There are multiple cars</p>
@elseif (count($cars) === 1)
    <p>There is exactly one car</p>
@else
    <p>There are no cars</p>
@endif
```

### `@unless` — Opposite of `@if`

```blade
@unless ($user->isAdmin())
    <p>You do not have admin access</p>
@endunless
{{-- Same as: @if (!$user->isAdmin()) --}}
```

### `@isset` and `@empty`

```blade
@isset($cars)
    <p>Cars variable is defined and not null</p>
@endisset

@empty($cars)
    <p>Cars is empty, null, zero, or an empty string</p>
@endempty
```

### `@auth` and `@guest`

```blade
@auth
    <p>Welcome, {{ auth()->user()->name }}</p>
    <a href="/logout">Logout</a>
@endauth

@guest
    <a href="/login">Login</a>
    <a href="/register">Register</a>
@endguest
```

### `@switch`, `@case`, `@default`, `@endswitch`

```blade
@switch($country)
    @case('EG')
        <p>Egypt</p>
        @break
    @case('US')
        <p>United States</p>
        @break
    @default
        <p>Unknown Country</p>
@endswitch
```

---

## Blade Directives — Loops

### `@for` / `@endfor`

```blade
@for ($i = 1; $i <= 5; $i++)
    <p>{{ $i }}</p>
@endfor
```

### `@foreach` / `@endforeach`

```blade
@foreach ($customers as $customer)
    <p>{{ $customer->first_name }}</p>
@endforeach
```

### `@forelse` / `@empty` / `@endforelse`

The most useful loop directive — handles empty arrays gracefully:

```blade
@forelse ($customers as $customer)
    <p>{{ $customer->first_name }}</p>
@empty
    <p>No customers found</p>
@endforelse
```

Without `@forelse`, you would need a separate `@if (count($customers) > 0)` check. This combines both into one clean directive.

### `@while` / `@endwhile`

```blade
@while ($condition)
    <p>Running</p>
@endwhile
```

### `@continue` and `@break`

```blade
@foreach ($numbers as $n)
    @continue($n == 2)
    {{-- Skip when n equals 2 — equivalent to: @if($n==2) @continue @endif --}}

    <p>{{ $n }}</p>

    @break($n == 4)
    {{-- Stop when n equals 4 --}}
@endforeach
```

The shorthand with a condition inside the directive replaces three lines with one.

---

## The `$loop` Variable

Inside any `@foreach` or `@forelse`, Blade provides a special `$loop` variable with information about the current iteration.

```blade
@foreach ($customers as $customer)
    <p>
        Iteration: {{ $loop->iteration }}  {{-- Starts at 1 --}}
        Index:     {{ $loop->index }}      {{-- Starts at 0 --}}
        Total:     {{ $loop->count }}      {{-- Total items --}}
        Remaining: {{ $loop->remaining }}  {{-- Items left --}}

        @if ($loop->first) — First item @endif
        @if ($loop->last)  — Last item  @endif
        @if ($loop->even)  — Even iteration @endif
        @if ($loop->odd)   — Odd iteration  @endif
    </p>
@endforeach
```

### All `$loop` Properties

| Property | Type | Description |
|---|---|---|
| `$loop->index` | int | Current iteration index — starts at 0 |
| `$loop->iteration` | int | Current iteration number — starts at 1 |
| `$loop->remaining` | int | How many iterations are left |
| `$loop->count` | int | Total number of items in the array |
| `$loop->first` | bool | True on the first iteration |
| `$loop->last` | bool | True on the last iteration |
| `$loop->even` | bool | True on even iterations (2nd, 4th...) |
| `$loop->odd` | bool | True on odd iterations (1st, 3rd...) |
| `$loop->depth` | int | Nesting level — 1 for outer loop, 2 for inner |
| `$loop->parent` | object | The parent loop's `$loop` variable in nested loops |

### Real-World Uses of `$loop`

```blade
{{-- Add a divider between items but not after the last one --}}
@foreach ($items as $item)
    <div>{{ $item->name }}</div>
    @if (!$loop->last)
        <hr>
    @endif
@endforeach

{{-- Zebra striping --}}
@foreach ($rows as $row)
    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
        <td>{{ $row->name }}</td>
    </tr>
@endforeach

{{-- Numbered list --}}
@foreach ($steps as $step)
    <p>Step {{ $loop->iteration }}: {{ $step->title }}</p>
@endforeach
```

### Nested Loops — `$loop->parent`

```blade
@foreach ($categories as $category)
    {{-- $loop->depth = 1 here --}}
    <h3>{{ $category->name }}</h3>

    @foreach ($category->products as $product)
        {{-- $loop->depth = 2 here --}}
        {{-- $loop->parent refers to the outer loop's $loop --}}
        <p>
            Category iteration: {{ $loop->parent->iteration }}
            Product iteration: {{ $loop->iteration }}
            {{ $product->name }}
        </p>
    @endforeach
@endforeach
```

---

## Conditional Classes and Styles

### `@class` — Conditionally Add CSS Classes

```blade
<div @class([
    'base-card',           {{-- Always added --}}
    'georgia-style' => $country === 'GE',  {{-- Added only if true --}}
    'admin-style'   => $user->isAdmin(),   {{-- Added only if true --}}
])>
    Content
</div>
```

Result when country is 'GE':
```html
<div class="base-card georgia-style">
```

Result when country is 'UK':
```html
<div class="base-card">
```

### `@style` — Conditionally Add Inline Styles

```blade
<div @style([
    'color: green',              {{-- Always added --}}
    'background-color: cyan' => $country === 'GE',  {{-- Conditionally added --}}
    'font-weight: bold'      => $user->isAdmin(),
])>
    Content
</div>
```

---

## Including Sub-Views

### `@include` — Include a View

```blade
@include('shared.button')

{{-- With data passed to the included view --}}
@include('shared.button', ['text' => 'Submit', 'color' => 'blue'])
```

The included view has access to all variables from the parent view plus any extra variables you pass.

### `@includeIf` — Only Include if the View Exists

```blade
@includeIf('shared.search-form')
{{-- No error if the file does not exist — silently skipped --}}
```

### `@includeWhen` — Include Based on a Condition

```blade
@includeWhen($searchKeyword, 'shared.search-results', ['keyword' => $searchKeyword])
{{-- Only includes the view when $searchKeyword is truthy --}}
```

### `@includeUnless` — Include When Condition is False

```blade
@includeUnless($user->isGuest(), 'shared.user-menu')
{{-- Includes user-menu unless the user is a guest --}}
```

### `@includeFirst` — Include the First Available View

```blade
@includeFirst(['admin.button', 'shared.button'], ['text' => 'Click'])
{{-- Tries admin.button first, falls back to shared.button --}}
```

---

## Including Sub-Views in Loops

### Manual Approach — `@include` inside `@foreach`

```blade
@foreach ($cars as $car)
    @include('car.view', ['car' => $car])
@endforeach
```

### `@each` — One-Line Equivalent

```blade
@each('car.view', $cars, 'car')
{{-- Arguments: view name | array to iterate | variable name inside the view --}}

{{-- With fallback view for empty array --}}
@each('car.view', $cars, 'car', 'car.empty')
```

The `@each` directive iterates over `$cars`, renders `car.view` for each item (passing it as `$car`), and renders `car.empty` if the array is empty.

---

## Raw PHP in Blade

### Inline PHP Tags

```blade
<?php $count = count($items); ?>
```

### `@php` Directive — Cleaner Syntax

```blade
@php
    $count = count($items);
    $doubled = $count * 2;
@endphp

<p>Items: {{ $count }}</p>
```

### `@use` Directive — Import a Class

```blade
@use('App\Models\Customer')
@use('Illuminate\Support\Str')

{{-- Now you can use these classes directly --}}
{{ Str::upper($name) }}
```

---

## Quick Reference

| Syntax                                    | Purpose                                              |
| ----------------------------------------- | ---------------------------------------------------- |
| `{{ $var }}`                              | Echo escaped — safe for user data                    |
| `{!! $var !!}`                            | Echo raw — only for trusted HTML                     |
| `{{-- comment --}}`                       | Blade comment — never sent to browser                |
| `<!-- comment -->`                        | HTML comment — visible in page source                |
| `@{{ }}`                                  | Print curly braces literally — skip Blade processing |
| `@verbatim ... @endverbatim`              | Block of code Blade ignores entirely                 |
| `@@directive`                             | Print `@directive` literally                         |
| `@if / @elseif / @else / @endif`          | Conditionals                                         |
| `@unless / @endunless`                    | Opposite of `@if`                                    |
| `@isset / @endisset`                      | Check if variable is set                             |
| `@empty / @endempty`                      | Check if variable is empty                           |
| `@auth / @endauth`                        | Check if user is logged in                           |
| `@guest / @endguest`                      | Check if user is not logged in                       |
| `@switch / @case / @default / @endswitch` | Switch statement                                     |
| `@for / @endfor`                          | For loop                                             |
| `@foreach / @endforeach`                  | Foreach loop                                         |
| `@forelse / @empty / @endforelse`         | Foreach with empty fallback                          |
| `@while / @endwhile`                      | While loop                                           |
| `@continue($condition)`                   | Skip iteration when condition is true                |
| `@break($condition)`                      | Stop loop when condition is true                     |
| `$loop->iteration`                        | Current loop count — starts at 1                     |
| `$loop->index`                            | Current loop index — starts at 0                     |
| `$loop->first`                            | True on first iteration                              |
| `$loop->last`                             | True on last iteration                               |
| `$loop->count`                            | Total number of items                                |
| `$loop->depth`                            | Nesting level — 1 for outer, 2 for inner             |
| `$loop->parent`                           | Parent loop's `$loop` variable                       |
| `@class([...])`                           | Conditionally add CSS classes                        |
| `@style([...])`                           | Conditionally add inline styles                      |
| `@include('view', [data])`                | Include a sub-view                                   |
| `@includeIf`                              | Include only if view exists                          |
| `@includeWhen($cond, 'view')`             | Include when condition is true                       |
| `@includeUnless($cond, 'view')`           | Include when condition is false                      |
| `@includeFirst(['a', 'b'])`               | Include first view that exists                       |
| `@each('view', $arr, 'var')`              | Render a view for each item in an array              |
| `@php / @endphp`                          | Write raw PHP inside a Blade file                    |
| `@use('ClassName')`                       | Import a class into the Blade file                   |
| `View::share('key', $val)`                | Make a variable available in ALL views               |
| `view()->exists('name')`                  | Check if a view file exists                          |
| `View::first(['a', 'b'])`                 | Render first available view from list                |
| `Js::from($array)`                        | Convert PHP array to safe JavaScript value           |
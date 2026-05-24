<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;

Route::get('/', [CustomerController::class, 'index']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/expensive', [ProductController::class, 'expensiveProducts']);

Route::get('/customers', [CustomerController::class, 'index']);
Route::get('/customers/cairo', [CustomerController::class, 'cairoCustomers']);

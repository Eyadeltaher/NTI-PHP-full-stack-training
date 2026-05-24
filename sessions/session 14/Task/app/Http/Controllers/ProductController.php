<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function expensiveProducts()
    {
        $products = Product::where('price', '>', 100)->get();
        return view('products.expensive', compact('products'));
    }
}
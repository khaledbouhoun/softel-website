<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('sort_order')->get();

        return view('products.index', compact('products'));
    }

    public function show(string $locale, Product $product)
    {
        return view('products.show', compact('product'));
    }
}

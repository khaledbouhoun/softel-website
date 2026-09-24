<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::orderBy('sort_order')->get();

        return view('products.index', compact('products'));
    }

    public function show(string $locale, Product $product): View
    {
        $whatsappUrl = $product->getWhatsAppUrl($locale);

        return view('products.show', compact('product', 'whatsappUrl'));
    }
}

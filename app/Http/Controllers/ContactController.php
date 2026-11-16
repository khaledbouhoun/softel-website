<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        $products = Product::orderBy('sort_order')->get();
        $selectedProduct = $request->query('product')
            ? Product::where('slug', $request->query('product'))->first()
            : null;
        $source = $selectedProduct ? 'product_page' : 'contact_page';

        return view('contact', compact('products', 'selectedProduct', 'source'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'product_id' => 'nullable|exists:products,id',
            'source' => 'nullable|in:contact_page,product_page',
            'message' => 'required|string|max:2000',
        ]);

        $data['locale'] = app()->getLocale();
        $data['source'] ??= 'contact_page';

        Lead::create($data);

        return back()->with('success', __('site.contact_success'));
    }
}

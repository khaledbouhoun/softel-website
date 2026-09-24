<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('sort_order')->limit(4)->get();
        $products = Product::orderBy('sort_order')->get();
        $testimonials = Testimonial::orderBy('sort_order')->get();

        return view('home.index', compact('services', 'products', 'testimonials'));
    }

    public function about()
    {
        return view('about');
    }
}

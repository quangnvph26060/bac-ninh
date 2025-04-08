<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        // Category::fixTree();

        $products = Product::query()->select('id', 'name', 'sale_price', 'type', 'image', 'slug', 'discount_price', 'discount_start', 'discount_end', 'stock_status')->with('variants')->home()->active()->get();
        // dd($products);

        return view('frontend.pages.home', compact('products'));
    }
}

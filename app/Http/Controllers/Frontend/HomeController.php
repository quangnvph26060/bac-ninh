<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct(public CategoryService $categoryService) {}

    public function home()
    {
        // Category::fixTree();

        $products = Product::query()->select('id', 'name', 'slug', 'sale_price', 'type', 'image', 'slug', 'discount_price', 'discount_start', 'discount_end', 'stock_status', 'category_id')->with('category')->home()->active()->get();
        $categories = Category::query()->home()->with('products')->withCount('products')->get();

        return view('frontend.pages.home', compact('products', 'categories'));
    }
}

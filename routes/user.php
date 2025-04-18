<?php

use App\Http\Controllers\Frontend\App\DashboardController;
use App\Http\Controllers\Frontend\App\OrderController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use Illuminate\Support\Facades\Route;


Route::prefix('app')->name('app.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('get-products', 'getProducts')->name('get.products');
        Route::post('get-variant-price', 'getVariantPrice')->name('get-variant-price');
        Route::post('check-stock', 'checkStock')->name('check-stock');
        Route::get('create', 'create')->name('create');
        Route::get('filter', 'filter')->name('filter');
        Route::get('states/{country_id}', 'getStates')->name('get.states');
        Route::get('cities/{state_id}', 'getCities')->name('get.cities');
    });
});

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::controller(CartController::class)->name('carts.')->group(function () {
    Route::post('add-to-cart', 'addToCart')->name('add.to.cart');
});

Route::controller(ProductController::class)->name('products.')->group(function () {
    Route::post('select-attribute', 'selectAttribute')->name('select.attribute');
    Route::post('find-variant', 'findVariant')->name('find-variant');
    Route::get('{prefix}/{suffix?}', 'list')->name('list');
});

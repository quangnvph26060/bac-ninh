<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('list', [ProductController::class, 'list'])->name('product.list');
Route::get('detail', [ProductController::class, 'detail'])->name('product.detail');

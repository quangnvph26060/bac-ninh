<?php

use App\Http\Controllers\Frontend\App\BillController;
use App\Http\Controllers\Frontend\App\CouponController;
use App\Http\Controllers\Frontend\App\DashboardController;
use App\Http\Controllers\Frontend\App\OrderController;
use App\Http\Controllers\Frontend\Auth\AuthController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\App\ProfileController;
use App\Http\Controllers\Frontend\App\TransactionHistoryController;
use Illuminate\Support\Facades\Route;


Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('login', 'login')->name('login');
    Route::post('login', 'authenticate');
    Route::get('register', 'register')->name('register');
    Route::post('register', 'storeRegister');

    Route::get('forgot-password', 'forgotPassword')->name('forgot.password');
    Route::post('forgot-password', 'sendResetLinkEmail');
    Route::post('resend-otp', 'resendOtp')->name('resend.otp');
    Route::post('reset-password', 'resetPassword')->name('reset.password');

    Route::get('google', 'redirectToGoogle')->name('google.redirect');
    Route::get('auth/google/callback', 'handleGoogleCallback')->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('coupons', [CouponController::class, 'coupons'])->name('coupons.index');

    Route::get('transaction-history', [TransactionHistoryController::class, 'transactionHistory'])->name('transaction.history');

    Route::prefix('bills')
        ->controller(BillController::class)
        ->name('bills.')
        ->group(function () {
            Route::get('/', 'bill')->name('index');
            Route::post('/', 'process')->name('process');
            Route::post('paypal/process', 'processTransaction')->name('paypal.process');
            Route::get('paypal/success', 'successTransaction')->name('paypal.success');
            Route::get('paypal/cancel', 'cancelTransaction')->name('paypal.cancel');
        });

    Route::group(['controller' => ProfileController::class], function () {
        Route::get('profile', 'profile')->name('profile');
        Route::post('update', 'update')->name('profile.update');
        Route::post('change-password', 'changePassword')->name('change.password');
    });

    Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('show/{code}', 'show')->name('show');
        Route::post('cancel', 'orderCancel')->name('cancel');
        Route::post('payment/{code}', 'payment')->name('payment');
        Route::post('store-order', 'storeOrder')->name('store.order');
        Route::post('get-products', 'getProducts')->name('get.products');
        Route::post('get-variant-price', 'getVariantPrice')->name('get-variant-price');
        Route::post('check-stock', 'checkStock')->name('check-stock');
        Route::post('apply-coupon', 'applyCoupon')->name('apply.coupon');
        Route::get('create', 'create')->name('create');
        Route::get('filter', 'filter')->name('filter');
        Route::post('get-shipping-fee', 'getShippingFee')->name('get-shipping-fee');
    });
});

Route::get('/', [HomeController::class, 'home'])->name('home');

Route::controller(CartController::class)->name('carts.')->group(function () {
    Route::post('add-to-cart', 'addToCart')->name('add.to.cart');
});

Route::controller(ProductController::class)->name('products.')->group(function () {
    Route::get('all-product', 'all')->name('all');
    Route::get('collection/{slug}', 'collection')->name('collection');
    Route::get('product-category/{parent}/{children?}', 'category')->name('category');

    Route::post('select-attribute', 'selectAttribute')->name('select.attribute');
    Route::post('find-variant', 'findVariant')->name('find-variant');
    Route::get('{prefix}/{suffix?}', 'detail')->name('detail');
});

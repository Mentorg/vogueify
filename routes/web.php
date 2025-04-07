<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

Route::post('/login', [SessionController::class, 'store']);

Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/profile', function () {
        return Inertia::render('Profile/Profile');
    })->name('profile');

    Route::get('/show', function () {
        return Inertia::render('Profile/Show');
    })->name('show');

    Route::get('/orders', function () {
        return Inertia::render('Orders');
    })->name('orders');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

Route::controller(HomeController::class)->group(function() {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/categories', [HomeController::class, 'getCategories'])->name('categories');
    Route::get('/search', [HomeController::class, 'search'])->name('search');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index')->name('products.index');
    Route::get('/products/search', 'searchResults')->name('products.search');
    Route::get('/products/{product}', 'show')->name('product.show');
    Route::get('/product/create', 'create')->name('product.create');
    Route::post('/products', 'store')->name('product.store');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/dashboard', 'getWishlist')->name('dashboard');
});

<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Models\Country;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('auth.register');

Route::post('/login', [SessionController::class, 'store']);

Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/profile', function () {
        $countries = Country::all(['id', 'name', 'iso_code']);

        return Inertia::render('Profile/Profile', [
            'countries' => $countries
        ]);
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
    Route::get('/products/{product:slug}/{variation?}', 'show')->name('product.show');
    Route::get('/admin/product/create', 'create')->name('product.create');
    Route::post('/admin/products', 'store')->name('product.store');
    Route::get('/admin/products/{product:slug}/update', 'edit')->name('product.edit');
    Route::put('/admin/products/{product}', 'update')->name('product.update');
    Route::delete('/admin/products/{id}', 'destroy')->name('product.delete');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/dashboard', 'index')->name('dashboard');
    Route::delete('/admin/users/{user}', 'destroy')->name('user.destroy');
});

Route::controller(DashboardController::class)->group(function () {
    Route::get('/admin/overview', 'index')->name('admin.overview');
    Route::get('/admin/users', 'getUsers')->name('admin.users');
    Route::get('/admin/products', 'getProducts')->name('admin.products');
});

Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'index')->name('cart.index');
    Route::post('/cart', 'store')->name('cart.store');
    Route::delete('/cart/{id}', 'destroy')->name('cart.delete');
});

Route::controller(OrderController::class)->group(function() {
    Route::post('/orders', 'store')->name('order.store');
    Route::get('/order/{order}', 'show')->name('order.show');
    Route::get('/orders', 'getUserOrders')->name('order.userOrders');
    Route::put('/order/{order}/cancel', 'cancel')->name('order.cancel');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

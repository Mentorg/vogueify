<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WishlistController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::post('/register', [RegisteredUserController::class, 'store'])->name('user.register');

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('auth.register');

Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

Route::controller(HomeController::class)->group(function() {
    Route::get('/', 'index')->name('home');
    Route::get('/categories', 'getCategories')->name('categories');
    Route::get('/search', 'search')->name('search');
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index')->name('products.index');
    Route::get('/products/search', 'searchResults')->name('products.search');
    Route::get('/products/{product:slug}/{variation?}', 'show')->name('product.show');
    Route::middleware(['auth', 'verified'])->prefix('admin')->group(function() {
        Route::get('/product/create', 'create')->name('product.create');
        Route::post('/products', 'store')->name('product.store');
        Route::get('/products/{product:slug}/update', 'edit')->name('product.edit');
        Route::put('/products/{product}', 'update')->name('product.update');
        Route::delete('/products/{id}', 'destroy')->name('product.delete');
    });
});

Route::controller(UserController::class)->group(function () {
    Route::get('/dashboard', 'index')->middleware(['auth', 'verified'])->name('dashboard');
    Route::delete('/admin/users/{user}', 'destroy')->middleware(['auth', 'verified'])->name('user.destroy');
    Route::get('/profile', 'getProfile')->middleware(['auth'])->name('profile');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function() {
    Route::get('/overview', [DashboardController::class, 'index'])->name('admin.overview');
    Route::get('/users', [DashboardController::class, 'getUsers'])->name('admin.users');
    Route::get('/products', [DashboardController::class, 'getProducts'])->name('admin.products');
    Route::get('/orders', [DashboardController::class, 'getOrders'])->name('admin.orders');
    Route::get('/orders/{order}', [DashboardController::class, 'getOrder'])->name('admin.order');
    Route::get('/coupons', [DashboardController::class, 'getCoupons'])->name('admin.coupons');
});

Route::middleware(['auth', 'verified'])->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/{id}', [CartController::class, 'destroy'])->name('cart.delete');
});

Route::middleware(['auth', 'verified'])->prefix('orders')->group(function() {
    Route::post('/', [OrderController::class, 'store'])->name('order.store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/user/orders', [OrderController::class, 'getUserOrders'])->name('order.userOrders');
    Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/{order}/note', [OrderController::class, 'updateNote'])->name('orders.updateNote');
    Route::patch('/{order}/shipping-address', [OrderController::class, 'updateShippingAddress'])->name('orders.updateShippingAddress');
    Route::patch('/{order}/billing-address', [OrderController::class, 'updateBillingAddress'])->name('orders.updateBillingAddress');
    Route::post('/orders/{order}/resend-confirmation', [OrderController::class, 'resendConfirmation'])->name('orders.resendConfirmation');
    Route::patch('/order-items/{orderItem}/status', [OrderController::class, 'updateItemStatus'])->name('orderItems.updateStatus');
    Route::patch('/order-items/{orderItem}/shipping-date', [OrderController::class, 'updateItemShippingDate'])->name('orderItems.updateShippingDate');
    Route::post('/{order}/continue', [OrderController::class, 'continueOrder'])->name('order.continueOrder');
});

Route::middleware(['signed'])->get('/orders/{order}/confirm', [OrderController::class, 'confirm'])->name('order.confirm');

Route::middleware(['auth', 'verified'])->prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout');
    Route::get('/create-session', [CheckoutController::class, 'createSession'])->name('checkout.create');
    Route::get('/success', [CheckoutController::class, 'success'])->name('checkout.success');
});

Route::middleware(['auth', 'verified'])->prefix('wishlist')->group(function() {
    Route::get('/', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::post('/{productVariation}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/coupons/{coupon}', [CouponController::class, 'show'])->name('coupon.show');
    Route::post('/coupons', [CouponController::class, 'store'])->name('coupon.store');
    Route::put('/coupons/{coupon}', [CouponController::class, 'update'])->name('coupon.update');
    Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupon.delete');
    Route::post('/coupons/apply', [CouponController::class, 'apply'])->name('coupon.apply');
    Route::post('/coupons/remove', [CouponController::class, 'remove'])->name('coupon.remove');
    Route::patch('/coupons/{coupon}/status', [CouponController::class, 'updateStatus'])->name('coupon.updateStatus');
    Route::post('/coupons/{coupon}/notify-users', [CouponController::class, 'sendUserNotifications'])->name('coupon.sendUserNotifications');
});

Route::post('/webhook/stripe', [WebhookController::class, 'handle'])->name('webhook.stripe');

Route::get('/email/verify', function () {
    return Inertia::render('Auth/VerifyEmail');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

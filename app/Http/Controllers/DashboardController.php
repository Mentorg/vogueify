<?php

namespace App\Http\Controllers;

use App\Enums\AggregatedOrderStatus;
use App\Enums\OrderStatus;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CouponService;
use App\Services\HomeService;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    protected $productService;
    protected $homeService;
    protected $userService;
    protected $orderService;
    protected $couponService;

    public function __construct(
        ProductService $productService,
        HomeService $homeService,
        UserService $userService,
        OrderService $orderService,
        CouponService $couponService
    )
    {
        $this->productService = $productService;
        $this->homeService = $homeService;
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->couponService = $couponService;
    }

    public function index()
    {
        $this->authorize('viewAll', User::class);

        return Inertia::render('Admin/Overview', [
            'variations' => $this->productService->getProducts(request(), true),
            'categories' => $this->homeService->getCategories(),
            'users' => $this->userService->getUsers(true)
        ]);
    }

    public function getUsers()
    {
        $this->authorize('viewAll', User::class);

        return Inertia::render('Admin/Users', [
            'users' => $this->userService->getUsers(true)
        ]);
    }

    public function getProducts()
    {
        $this->authorize('viewAll', Product::class);

        return Inertia::render('Admin/Products', [
            'variations' => $this->productService->getProducts(request(), true),
            'categories' => $this->homeService->getCategories()
        ]);
    }

    public function getOrders()
    {
        $this->authorize('viewAll', Order::class);

        return Inertia::render('Admin/Orders', [
            'orders' => $this->orderService->getOrders(request(), true),
            'orderStatuses' => AggregatedOrderStatus::values(),
        ]);
    }

    public function getOrder(Order $order)
    {
        $this->authorize('viewInAdmin', $order);

        return Inertia::render('Admin/OrderDetails', [
            'order' => $this->orderService->getOrder($order),
            'orderStatuses' => OrderStatus::values(),
            'countries' => Country::all(['id', 'name', 'iso_code'])
        ]);
    }

    public function getCoupons()
    {
        $this->authorize('viewAll', Coupon::class);

        [$coupons, $categories, $products, $productVariations, $users] = $this->couponService->getCoupons();

        return Inertia::render('Admin/Coupons', [
            'coupons' => $coupons,
            'categories' => $categories,
            'products' => $products,
            'productVariations' => $productVariations,
            'users' => $users,
        ]);
    }
}

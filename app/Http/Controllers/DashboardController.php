<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Services\HomeService;
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

    public function __construct(ProductService $productService, HomeService $homeService, UserService $userService)
    {
        $this->productService = $productService;
        $this->homeService = $homeService;
        $this->userService = $userService;
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
}

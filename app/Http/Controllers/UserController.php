<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OrderService;
use App\Services\UserService;
use App\Services\WishlistService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    protected $userService;
    protected $orderService;
    protected $wishlistService;

    public function __construct(UserService $userService, OrderService $orderService, WishlistService $wishlistService)
    {
        $this->userService = $userService;
        $this->orderService = $orderService;
        $this->wishlistService = $wishlistService;
    }

    public function index(Request $request)
    {
        return Inertia::render('Dashboard', [
            'orders' => $this->orderService->getUserOrders($request),
            'wishlist' => $this->wishlistService->getWishlist($request),
        ]);
    }

    public function destroy(User $user)
    {
        $this->authorize('modify', $user);

        $this->userService->delete($user);

        return redirect()->route('admin.users');
    }

    public function getProfile() {
        return Inertia::render('Profile/Profile', [
            'countries' => $this->userService->getProfile()
        ]);
    }

    public function updateFirstTimeLogin(Request $request) {
        $this->userService->updateFirstTimeLogin($request->user());
        return redirect()->back()->with('success', 'First time login updated successfully.');
    }
}

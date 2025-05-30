<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function destroy(User $user)
    {
        $this->authorize('modify', $user);

        $user->delete();

        return redirect()->route('admin.users');
    }

    public function getWishlist(Request $request)
    {
        $wishlist = $request->user()->wishlist()->with(['productVariation.product'])->get();
        if (auth()->check()) {
            $wishlist = $request->user()->wishlist()->with(['productVariation.product'])->get();
        } else {
            abort(404);
        }

        return Inertia::render('Dashboard', [
            'wishlist' => $wishlist
        ]);
    }
}

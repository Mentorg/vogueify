<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Models\Coupon;
use App\Pricing\CouponService as PricingCouponService;
use App\Pricing\Exceptions\InvalidCouponException;
use App\Pricing\Validators\CouponValidator;
use App\Services\CouponService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CouponController extends Controller
{
    use AuthorizesRequests;

    protected $validator;
    protected $couponService;
    protected $service;

    public function __construct(CouponValidator $couponValidator, CouponService $couponService, PricingCouponService $service)
    {
        $this->validator = $couponValidator;
        $this->couponService = $couponService;
        $this->service = $service;
    }

    public function show(Coupon $coupon)
    {
        $this->authorize('view', Coupon::class);

        return Inertia::render('Admin/CouponDetails', [
            'coupon' => $this->couponService->getCoupon($coupon)
        ]);
    }

    public function store(StoreCouponRequest $request)
    {
        $this->authorize('create', Coupon::class);

        $this->couponService->create($request->validated());

        return redirect()->route('admin.coupons')->with('success', 'Coupon created successfully.');
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon)
    {
        $this->authorize('update', Coupon::class);

        $this->couponService->update($coupon, $request->validated());

        return redirect()->route('admin.coupons')->with('success', 'Coupon updated successfully.');
    }

    public function destroy($id)
    {
        $this->authorize('delete', Coupon::class);

        $this->couponService->delete($id);

        return redirect()->back();
    }

    public function apply(Request $request)
    {
        $data = $request->validate(['code' => 'required|string']);

        try {
            $discount = $this->couponService->validateAndApply($data['code'], Auth::user());

            return back()->with('success', "Coupon applied successfully! Discount: -{$discount}");
        }
        catch (InvalidCouponException $e) {
            return back()->withErrors(['coupon' => $e->getMessage()]);
        }
        catch (Exception $e) {
            return back()->withErrors(['coupon' => 'An unexpected error occurred.']);
        }
    }

    public function remove()
    {
        $this->couponService->remove();

        return redirect()->back()->with('success', 'Coupon removed successfully.');
    }

    public function updateStatus(Request $request, Coupon $coupon)
    {
        $this->authorize('updateStatus', Coupon::class);

        $request->validate([
            'status' => 'required|in:active,inactive,archived',
        ]);

        $this->couponService->updateStatus($coupon, $request->status);

        return redirect()->back();
    }

    public function sendUserNotifications(Coupon $coupon)
    {
        $this->authorize('sendUserNotifications', Coupon::class);

        $this->couponService->sendUserNotifications($coupon);

        return redirect()->back()->with('success', 'Users notified about the coupon successfully.');
    }
}

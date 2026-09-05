<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Models\UserSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * GET /api/v1/subscriptions/packages
     * List all active subscription packages.
     */
    public function packages(): JsonResponse
    {
        $packages = SubscriptionPackage::where('is_active', true)
            ->orderBy('price')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $packages,
        ]);
    }

    /**
     * GET /api/v1/subscriptions/my
     * Return the authenticated user's active subscription (if any).
     */
    public function mySubscription(Request $request): JsonResponse
    {
        $subscription = UserSubscription::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with('package')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => true,
                'data'    => null,
                'message' => 'No active subscription.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $subscription->id,
                'status'      => $subscription->status,
                'expires_at'  => $subscription->expires_at->toDateTimeString(),
                'days_left'   => (int) now()->diffInDays($subscription->expires_at, false),
                'package'     => $subscription->package,
            ],
        ]);
    }

    /**
     * POST /api/v1/subscriptions/subscribe
     * Subscribe to a package (mock payment).
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'package_id' => 'required|exists:subscription_packages,id',
            // 'payment_token' => 'required|string', // integrate real gateway here
        ]);

        $package = SubscriptionPackage::findOrFail($request->package_id);

        // Cancel any existing active subscription first
        UserSubscription::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        $subscription = UserSubscription::create([
            'user_id'    => $request->user()->id,
            'package_id' => $package->id,
            'expires_at' => now()->addDays($package->duration_days),
            'status'     => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to ' . $package->name,
            'data'    => [
                'id'         => $subscription->id,
                'status'     => $subscription->status,
                'expires_at' => $subscription->expires_at->toDateTimeString(),
                'package'    => $package,
            ],
        ]);
    }

    /**
     * POST /api/v1/subscriptions/cancel
     * Cancel the authenticated user's active subscription.
     */
    public function cancel(Request $request): JsonResponse
    {
        $subscription = UserSubscription::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription to cancel.',
            ], 404);
        }

        $subscription->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled. Access continues until ' . $subscription->expires_at->toDateString() . '.',
        ]);
    }
}

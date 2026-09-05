<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $packages = SubscriptionPackage::withCount('userSubscriptions')->latest()->paginate(20);
        return view('admin.learning.subscriptions.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.learning.subscriptions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:150',
            'price'         => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features'      => 'nullable|array',
            'features.*'    => 'nullable|string|max:200',
        ]);

        $features = array_values(array_filter($request->input('features', []), fn($f) => trim($f) !== ''));

        SubscriptionPackage::create([
            'name'          => $request->name,
            'price'         => $request->price,
            'duration_days' => $request->duration_days,
            'features_json' => $features,
            'is_active'     => $request->has('is_active'),
        ]);

        return redirect()->route('admin.learning.subscriptions.index')
            ->with('success', 'Subscription package created successfully.');
    }

    public function show(SubscriptionPackage $subscription)
    {
        $subscriptions = UserSubscription::where('package_id', $subscription->id)
            ->with('user:id,name,email,phone')
            ->latest()
            ->paginate(30);

        $activeCount  = UserSubscription::where('package_id', $subscription->id)->where('status', 'active')->count();
        $totalRevenue = UserSubscription::where('package_id', $subscription->id)->count() * $subscription->price;

        return view('admin.learning.subscriptions.show', compact('subscription', 'subscriptions', 'activeCount', 'totalRevenue'));
    }

    public function edit(SubscriptionPackage $subscription)
    {
        return view('admin.learning.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, SubscriptionPackage $subscription)
    {
        $request->validate([
            'name'          => 'required|string|max:150',
            'price'         => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features'      => 'nullable|array',
            'features.*'    => 'nullable|string|max:200',
        ]);

        $features = array_values(array_filter($request->input('features', []), fn($f) => trim($f) !== ''));

        $subscription->update([
            'name'          => $request->name,
            'price'         => $request->price,
            'duration_days' => $request->duration_days,
            'features_json' => $features,
            'is_active'     => $request->has('is_active'),
        ]);

        return redirect()->route('admin.learning.subscriptions.index')
            ->with('success', 'Subscription package updated.');
    }

    public function destroy(SubscriptionPackage $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'Subscription package deleted.');
    }
}

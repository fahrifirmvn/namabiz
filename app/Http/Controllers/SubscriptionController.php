<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function show()
    {
        return view('subscription.upgrade');
    }

    /**
     * Dummy checkout: instantly flips the user to premium.
     * Replace with a real payment gateway integration later.
     */
    public function checkout()
    {
        // subscription_tier/premium_expires_at are intentionally NOT mass-assignable
        // (see App\Models\User), so they are set explicitly here rather than via update().
        $user = Auth::user();
        $user->subscription_tier = 'premium';
        $user->premium_expires_at = now()->addYear();
        $user->save();

        return redirect()->route('discover.index')->with('status', 'You are now a Premium member!');
    }
}

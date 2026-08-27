<?php

namespace App\Support;

use App\Models\User;

class PremiumAccess
{
    public static function isPremium(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->subscription_tier === 'premium'
            && (! $user->premium_expires_at || $user->premium_expires_at->isFuture());
    }
}

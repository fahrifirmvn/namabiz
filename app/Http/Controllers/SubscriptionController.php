<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function show(): View
    {
        return view('subscription.upgrade');
    }

    public function checkout(): never
    {
        abort(403, 'Pembelian Premium melalui portal belum tersedia.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SavedSupplier;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class SavedSupplierController extends Controller
{
    public function toggle(Supplier $supplier)
    {
        $existing = SavedSupplier::where('user_id', Auth::id())
            ->where('supplier_id', $supplier->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            SavedSupplier::create([
                'user_id' => Auth::id(),
                'supplier_id' => $supplier->id,
            ]);
        }

        return back();
    }
}

<?php

namespace App\Http\Controllers;

use App\Livewire\ComparisonTable;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function index()
    {
        return view('analytics.compare');
    }

    public function downloadPdf(Request $request)
    {
        $supplierIds = array_slice(array_unique((array) $request->query('suppliers', [])), 0, ComparisonTable::MAX_SUPPLIERS);

        $suppliers = Supplier::with(['certifications.certification', 'documents'])
            ->whereIn('id', $supplierIds)
            ->get();

        $pdf = Pdf::loadView('analytics.compare-pdf', compact('suppliers'));

        return $pdf->download('supplier-comparison.pdf');
    }
}

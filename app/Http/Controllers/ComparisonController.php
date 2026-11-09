<?php

namespace App\Http\Controllers;

use App\Livewire\ComparisonTable;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ComparisonController extends Controller
{
    /**
     * Display the comparison interactive table view.
     */
    public function index()
    {
        return view('analytics.compare');
    }

    /**
     * Export comparison as a formal branded PDF report (Landscape A4).
     */
    public function downloadPdf(Request $request): Response
    {
        $rawSuppliers = $request->query('suppliers', []);
        if (is_string($rawSuppliers)) {
            $rawSuppliers = explode(',', $rawSuppliers);
        }

        $supplierIds = array_slice(array_filter(array_unique((array) $rawSuppliers)), 0, ComparisonTable::MAX_SUPPLIERS);

        if (empty($supplierIds)) {
            $sessionIds = session()->get('compared_suppliers', []);
            $supplierIds = ! empty($sessionIds)
                ? array_slice($sessionIds, 0, ComparisonTable::MAX_SUPPLIERS)
                : Supplier::pluck('id')->take(3)->toArray();
        }

        $suppliers = Supplier::with(['certifications.certification', 'documents', 'tags'])
            ->whereIn('id', $supplierIds)
            ->get();

        $pdf = Pdf::loadView('analytics.compare-pdf', [
            'suppliers' => $suppliers,
            'reportNumber' => 'NBZ-CMP-'.date('Ymd').'-'.strtoupper(substr(md5(implode('-', $supplierIds)), 0, 6)),
            'generatedAt' => now()->translatedFormat('d F Y, H:i').' WIB',
            'user' => auth()->user(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('namabiz-laporan-komparasi-pemasok.pdf');
    }
}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #E5E7EB; padding: 8px; text-align: left; }
        th { background: #F7F8FA; }
        .section { background: #F3F4F6; font-weight: bold; text-transform: uppercase; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Supplier Comparison Report</h1>
    <p>Generated {{ now()->format('d M Y') }}</p>

    <table>
        <tr>
            <th>Metric</th>
            @foreach($suppliers as $supplier)
                <th>{{ $supplier->name }}</th>
            @endforeach
        </tr>
        <tr class="section"><td colspan="{{ $suppliers->count() + 1 }}">Basic Information</td></tr>
        <tr>
            <td>Location</td>
            @foreach($suppliers as $supplier)
                <td>{{ $supplier->city }}, {{ $supplier->country }}</td>
            @endforeach
        </tr>
        <tr>
            <td>Founded</td>
            @foreach($suppliers as $supplier)
                <td>{{ $supplier->founded_year }}</td>
            @endforeach
        </tr>
        <tr>
            <td>Company Size</td>
            @foreach($suppliers as $supplier)
                <td>{{ $supplier->company_size }}</td>
            @endforeach
        </tr>
        <tr class="section"><td colspan="{{ $suppliers->count() + 1 }}">Operational Metrics</td></tr>
        <tr>
            <td>Production Capacity</td>
            @foreach($suppliers as $supplier)
                <td>{{ $supplier->production_capacity }}</td>
            @endforeach
        </tr>
        <tr>
            <td>MOQ (Minimum Order)</td>
            @foreach($suppliers as $supplier)
                <td>{{ $supplier->moq }}</td>
            @endforeach
        </tr>
        <tr>
            <td>Avg. Lead Time</td>
            @foreach($suppliers as $supplier)
                <td>{{ $supplier->avg_lead_time }}</td>
            @endforeach
        </tr>
        <tr class="section"><td colspan="{{ $suppliers->count() + 1 }}">Compliance &amp; Sustainability</td></tr>
        <tr>
            <td>Carbon Footprint Report</td>
            @foreach($suppliers as $supplier)
                <td>{{ $supplier->carbon_footprint_report ?? 'Not Disclosed' }}</td>
            @endforeach
        </tr>
    </table>
</body>
</html>

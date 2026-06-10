<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Tripsheets - {{ $party?->full_name ?? 'Party' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f4f4f4;
        }

        .header {
            margin-bottom: 8px;
        }

        .items-table {
            margin-top: 6px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Tripsheets for {{ $party?->full_name ?? 'Party' }}</h2>
        @if ($start && $end)
            <div>Period: {{ $start->format('Y-m-d') }} — {{ $end->format('Y-m-d') }}</div>
        @else
            <div>Generated: {{ now()->format('Y-m-d H:i') }}</div>
        @endif
    </div>

    @foreach ($items as $item)
        <table>
            <tr>
                <th style="width:30%">Date / Time</th>
                <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                <th style="width:20%">Challan No</th>
                <td>{{ $item->challan_number }}</td>
            </tr>
            <tr>
                <th>Party</th>
                <td>{{ $item->party?->full_name }}</td>
                <th>Vehicle</th>
                <td>{{ $item->vehicle?->reg_no ?? '-' }}</td>
            </tr>
            <tr>
                <th>Driver</th>
                <td>{{ $item->driver?->name ?? '-' }}</td>
                <th>Invoice</th>
                <td>{{ $item->invoice?->invoice_number ?? '-' }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity (CFT)</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($item->challan_items) && $item->challan_items->isNotEmpty())
                    @foreach ($item->challan_items as $ci)
                        <tr>
                            <td>{{ $ci->item?->material_name ?? $ci->item_id }}</td>
                            <td>{{ $ci->quantity_cft }}</td>
                            <td>{{ $ci->rate_at_sale }}</td>
                            <td>{{ $ci->amount }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">No items</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div style="page-break-after: always;"></div>
    @endforeach

</body>

</html>

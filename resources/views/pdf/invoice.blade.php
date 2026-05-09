<div style="font-family: DejaVu Sans, Arial, sans-serif;">
    <h1>Tax Invoice</h1>
    <p>Invoice #: {{ $record->invoice_number }}</p>
    <p>Date: {{ $record->created_at->format('d-m-Y') }}</p>
    <hr>
    <h3>Bill To: {{ $record->party->full_name }}</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f3f4f6;">
                <th style="border: 1px solid #ddd; padding: 8px;">Description</th>
                <th style="border: 1px solid #ddd; padding: 8px;">Quantity</th>
                <th style="border: 1px solid #ddd; padding: 8px;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">
                    Material Sale (via Challans: {{ $record->challans->pluck('challan_number')->implode(', ') }})
                </td>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ $record->challans->sum('quantity_cft') }} CFT</td>
                <td style="border: 1px solid #ddd; padding: 8px;">₹{{ number_format($record->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
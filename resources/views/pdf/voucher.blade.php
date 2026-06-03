<div style="font-family: DejaVu Sans, Arial, sans-serif; padding: 30px; border: 2px solid #eee;">
    <table style="width: 100%;">
        <tr>
            <td>
                <h1>{{ $record->type }}</h1>
            </td>
            <td style="text-align: right;"><strong>No:</strong> {{ $record->voucher_no }}</td>
        </tr>
    </table>
    <p>{{ $record->voucher_type === 'Received' ? 'Received with thanks from' : 'Paid to' }}
        <strong>{{ $record->party->full_name }}</strong></p>
    <p>The sum of <strong>₹{{ number_format($record->amount, 2) }}</strong></p>
    <p>Via <strong>{{ $record->payment_mode }}</strong></p>
    @if ($record->invoice)
        <p>Adjusted against Invoice: {{ $record->invoice->invoice_number }}</p>
    @endif
    <p style="font-style: italic;">Remarks: {{ $record->remarks ?? 'N/A' }}</p>
    <div style="margin-top: 40px; text-align: right;">
        <p>Date: {{ $record->voucher_date }}</p>
        <br><br>
        <p>Receiver's Signature</p>
    </div>
</div>

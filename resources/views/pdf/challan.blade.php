<style>
    .challan-box { border: 2px solid #000; padding: 15px; font-family: 'Helvetica', sans-serif; width: 100%; }
    .header { text-align: center; border-bottom: 1px solid #000; margin-bottom: 10px; }
    .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
    .label { font-weight: bold; text-transform: uppercase; font-size: 12px; color: #555; }
    .value { font-size: 16px; font-weight: bold; border-bottom: 1px dashed #ccc; }
    .big-value { font-size: 24px; border: 1px solid #000; padding: 5px 10px; display: inline-block; }
</style>
<div class="challan-box">
    <div class="header">
        <h1 style="margin:0;">CRUSHER TRIP SHEET</h1>
        <p>{{ config('app.name') }} | Site Location: {{ $record->party->city }}</p>
    </div>

    <div class="row">
        <div>
            <span class="label">Challan No:</span><br>
            <span class="value">#{{ $record->challan_number }}</span>
        </div>
        <div style="text-align: right;">
            <span class="label">Date & Time:</span><br>
            <span class="value">{{ $record->created_at->format('d-M-Y h:i A') }}</span>
        </div>
    </div>

    <div style="margin: 20px 0; background: #f9f9f9; padding: 10px;">
        <div class="row">
            <span class="label">Vehicle Number:</span>
            <span class="value">{{ $record->vehicle->vehicle_number }}</span>
        </div>
        <div class="row">
            <span class="label">Customer / Party:</span>
            <span class="value">{{ $record->party->full_name }}</span>
        </div>
        <div class="row">
            <span class="label">Material Type:</span>
            <span class="value">{{ $record->item->material_name }}</span>
        </div>
    </div>

    <div style="text-align: center; margin: 20px 0;">
        <span class="label">Net Quantity (CFT)</span><br>
        <div class="big-value">{{ $record->quantity_cft }} CFT</div>
    </div>

    <table style="width: 100%; margin-top: 40px;">
    <tr>
        <td style="width: 50%; text-align: center; vertical-align: top;">
            <p>___________________</p>
            <span class="label">Driver Signature</span>
        </td>

        <td style="width: 50%; text-align: center; vertical-align: top;">
            <p>___________________</p>
            <span class="label">Authorized Signatory</span>
        </td>
    </tr>
</table>
</div>
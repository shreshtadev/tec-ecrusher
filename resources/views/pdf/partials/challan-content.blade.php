<table style="width: 100%; border-collapse: collapse;">
    <tr>

        {{-- Left Section --}}
        <td style="vertical-align: top; text-align: left;">
            <div>
                <span style="margin: 0; font-size: 12px;">Shruthi Stone Crusher</span>
                <small>Nagarahalli Village, Chikkamagaluru - 577101</small>
            </div>

            <p style="margin-top: 4px; font-size: 11px;">
                <strong>Challan:</strong> <span style="color: red;">{{ $record->challan_number }}</span>
            </p>
        </td>

        {{-- Right Section --}}
        <td style="vertical-align: top; width: 140px; padding-left: 10px;">

            <div
                style="
                font-size: 11px;
                border-bottom: 1px dotted #000;
                padding-bottom: 2px;
                margin-bottom: 2px;
                text-align: left;
            ">
                <strong>Date:</strong>
                {{ $record->created_at->format('d-m-Y') }}
            </div>

            <div
                style="
                font-size: 11px;
                border-bottom: 1px dotted #000;
                padding-bottom: 2px;
                text-align: left;
            ">
                <strong>Time:</strong>
                {{ $record->created_at->format('H:i') }}
            </div>

        </td>

    </tr>
</table>

<table style="width: 100%; margin-top: 30px; font-size: 1rem; border-collapse: collapse;border-spacing: 0 10px;">
    <tr>
        <td style="padding: 2px 0;"><strong>Party:</strong></td>
        <td style="padding: 2px 0;">{{ $record->party->full_name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Vehicle:</strong></td>
        <td style="padding: 2px 0;">{{ $record->vehicle->vehicle_number }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Quantity:</strong></td>
        <td style="padding: 2px 0;">{{ $record->quantity_cft }} CFT</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Material:</strong></td>
        <td style="padding: 2px 0;">{{ $record->item->material_name }}</td>
    </tr>

    <tr>
        <td style="padding: 2px 0;"><strong>Payment:</strong></td>
        <td style="padding: 2px 0;">{{ $record->payment_mode }}</td>
    </tr>

</table>

<div style="height: 20mm;"></div>

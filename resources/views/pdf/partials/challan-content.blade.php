<div style="margin-top: 0;">
    <h3 style="margin: 0; font-size: 14px;">ECrusher</h3>
    <p style="font-size: 11px; margin: 0;">Challan: {{ $record->challan_number }}</p>
</div>

<table style="width: 100%; margin-top: 5px; font-size: 1rem; border-collapse: collapse;">
    <tr>
        <td style="padding: 2px 0;"><strong>Vehicle:</strong></td>
        <td style="padding: 2px 0;">{{ $record->vehicle->vehicle_number }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Party:</strong></td>
        <td style="padding: 2px 0;">{{ $record->party->name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Material:</strong></td>
        <td style="padding: 2px 0;">{{ $record->item->material_name }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Quantity:</strong></td>
        <td style="padding: 2px 0;">{{ $record->quantity_cft }} CFT</td>
    </tr>

    <tr>
        <td style="padding: 2px 0;"><strong>Vehicle:</strong></td>
        <td style="padding: 2px 0;">{{ $record->vehicle->vehicle_number }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Party:</strong></td>
        <td style="padding: 2px 0;">{{ $record->party->name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Material:</strong></td>
        <td style="padding: 2px 0;">{{ $record->item->material_name }}</td>
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
        <td style="padding: 2px 0;"><strong>Quantity:</strong></td>
        <td style="padding: 2px 0;">{{ $record->quantity_cft }} CFT</td>
    </tr>
</table>

<div style="height: 20mm;"></div>

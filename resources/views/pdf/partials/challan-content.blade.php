<table style="width: 100%; border-collapse: collapse;">
    <tr>

        {{-- Left Section --}}
        <td style="vertical-align: top; text-align: left;">
            <div>
                <span style="margin: 0; font-size: 12px;">{{ $record->company->name }}</span>
                <small>{{ $record->company->address }}</small>
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

<!-- Main Details Section -->
<table style="width: 100%; margin-top: 30px; font-size: 1rem; border-collapse: collapse; border-spacing: 0 10px;">
    <tr>
        <td style="padding: 2px 0; width: 50%;"><strong>Party:</strong></td>
        <td style="padding: 2px 0;">{{ $record->party->full_name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td style="padding: 2px 0;"><strong>Vehicle:</strong></td>
        <td style="padding: 2px 0;">{{ $record->vehicle->vehicle_number ?? 'N/A' }}</td>
    </tr>
    @if ($record->driver)
        <tr>
            <td style="padding: 2px 0;"><strong>Driver:</strong></td>
            <td style="padding: 2px 0;">{{ $record->driver->name ?? 'N/A' }}</td>
        </tr>
    @endif
    <tr>
        <td style="padding: 2px 0;"><strong>Payment:</strong></td>
        <td style="padding: 2px 0;">{{ $record->payment_mode ?? 'N/A' }}</td>
    </tr>
</table>

<!-- Items Section -->
@if ($record->challan_items && $record->challan_items->count() > 0)
    <table
        style="width: 100%; margin-top: 20px; font-size: 0.95rem; border-collapse: collapse; border: 1px solid #000;">
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th style="padding: 5px; text-align: left; border-right: 1px solid #000;"><strong>Material</strong></th>
                <th style="padding: 5px; text-align: center; border-right: 1px solid #000;"><strong>Unit</strong></th>
                <th style="padding: 5px; text-align: center;"><strong>Quantity</strong></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->challan_items as $challanItem)
                <tr style="border-bottom: 1px solid #000;">
                    <td style="padding: 5px; border-right: 1px solid #000;">
                        {{ $challanItem->item->material_name ?? 'N/A' }}</td>
                    <td style="padding: 5px; text-align: center; border-right: 1px solid #000;">
                        {{ $challanItem->item->unit ?? 'N/A' }}</td>
                    <td style="padding: 5px; text-align: center;">{{ number_format($challanItem->quantity_cft, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <!-- Fallback for single item if no challan_items -->
    @if ($record->item)
        <table
            style="width: 100%; margin-top: 20px; font-size: 0.95rem; border-collapse: collapse; border: 1px solid #000;">
            <tr style="border-bottom: 1px solid #000;">
                <td style="padding: 5px;"><strong>Material:</strong></td>
                <td style="padding: 5px;">NIL</td>
            </tr>
            <tr>
                <td style="padding: 5px;"><strong>Quantity:</strong></td>
                <td style="padding: 5px;">NIL
                </td>
            </tr>
        </table>
    @endif
@endif

<!-- Signature Section -->
<div style="margin-top: 30px; width: 100%;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td
                style="width: 50%; padding-top: 20px; border-top: 1px solid #000; text-align: center; font-size: 0.9rem;">
                Driver Sign.
            </td>
            <td
                style="width: 50%; padding-top: 20px; border-top: 1px solid #000; text-align: center; font-size: 0.9rem;">
                Officer Sign.
            </td>
        </tr>
    </table>
</div>

<div style="height: 10mm;"></div>

<style>
    @page {
        size: A4 landscape;
        margin: 10mm;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #2c2c2c;
        margin: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-bold {
        font-weight: bold;
    }

    .border {
        border: 1px solid #bdbdbd;
    }

    .section-header {
        background: #f4f4f4;
        font-weight: bold;
        padding: 8px;
        border-bottom: 1px solid #bdbdbd;
    }

    .company-header {
        border-bottom: 3px solid #2f2f2f;
        padding-bottom: 10px;
        margin-bottom: 12px;
    }

    .company-name {
        font-size: 24px;
        font-weight: bold;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }

    .company-details {
        font-size: 10px;
        line-height: 1.5;
    }

    .invoice-banner {
        margin-top: 10px;
        margin-bottom: 12px;
        border: 2px solid #333;
        padding: 8px;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 2px;
    }

    .info-table td {
        border: 1px solid #cfcfcf;
        padding: 8px;
        vertical-align: top;
    }

    .bill-title {
        font-size: 12px;
        font-weight: bold;
        margin-bottom: 6px;
    }

    .items-table {
        margin-top: 15px;
    }

    .items-table thead th {
        background: #efefef;
        border: 1px solid #999;
        padding: 8px;
        font-size: 11px;
        font-weight: bold;
    }

    .items-table tbody td {
        border: 1px solid #cfcfcf;
        padding: 7px;
    }

    .challan-row {
        background: #fafafa;
        font-weight: bold;
    }

    .summary-wrapper {
        margin-top: 12px;
    }

    .summary-table {
        width: 38%;
        margin-left: auto;
    }

    .summary-table td {
        border: 1px solid #cfcfcf;
        padding: 8px;
    }

    .grand-total {
        background: #f2f2f2;
        font-size: 14px;
        font-weight: bold;
    }

    .amount-words {
        margin-top: 14px;
        border: 1px solid #cfcfcf;
        padding: 10px;
    }

    .reference-box {
        margin-top: 10px;
        border: 1px solid #cfcfcf;
        padding: 10px;
    }

    .signature-section {
        margin-top: 45px;
    }

    .signature-table td {
        border: none;
        vertical-align: bottom;
    }

    .signature-line {
        margin-top: 50px;
        border-top: 1px solid #444;
        width: 220px;
        padding-top: 4px;
    }

    .footer-note {
        margin-top: 20px;
        font-size: 9px;
        text-align: center;
        color: #666;
    }
</style>

<div>

    {{-- COMPANY HEADER --}}
    <table class="company-header">
        <tr>

            {{-- Left: Logo --}}
            <td style="width:20%; border:none; vertical-align:middle;">

                @if ($record->company->logo)
                    @php
                        $logoPath = ltrim($record->company->logo, '/');
                        $logoPath = Storage::disk('local')->path($logoPath);
                    @endphp

                    <img src="{{ $logoPath }}" alt="{{ $record->company->name }}"
                        style="max-height:90px; max-width:180px;">
                @endif

            </td>

            {{-- Center: Company Details --}}
            <td style="width:60%; border:none; text-align:center; vertical-align:middle;">

                <div class="company-name">
                    {{ strtoupper($record->company->name) }}
                </div>

                <div class="company-details">
                    {{ $record->company->address }}
                    <br>

                    GSTIN:
                    {{ $record->company->gstin ?? 'N/A' }}

                    &nbsp;&nbsp;|&nbsp;&nbsp;

                    Phone:
                    {{ $record->company->phone ?? 'N/A' }}
                </div>

            </td>

            {{-- Right: Empty spacer --}}
            <td style="width:20%; border:none;">
                &nbsp;
            </td>

        </tr>
    </table>

    {{-- TITLE --}}
    <div class="invoice-banner">
        INVOICE
    </div>

    {{-- CUSTOMER + INVOICE DETAILS --}}
    <table class="info-table">

        <tr>

            <td width="55%">

                <div class="bill-title">
                    BILL TO
                </div>

                <strong>
                    {{ $record->party->full_name }}
                </strong>

                <br>

                {{ $record->party->address_line_1 }}

                <br>

                GSTIN:
                {{ $record->party->gst_number ?? 'N/A' }}

                <br>

                Mobile:
                {{ $record->party->contact_number ?? 'N/A' }}

            </td>

            <td width="45%">

                <table>

                    <tr>
                        <td width="45%">
                            <strong>Invoice Number</strong>
                        </td>

                        <td>
                            {{ $record->invoice_number }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Invoice Date</strong>
                        </td>

                        <td>
                            {{ $record->invoice_date->format('d-m-Y') }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>Payment Mode</strong>
                        </td>

                        <td>
                            {{ $record->payment_mode }}
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong>
                                @if (
                                    $record->challans->filter(function ($challan) {
                                            return $challan->challan_items->isNotEmpty();
                                        })->isNotEmpty())
                                    Total Challans
                                @elseif ($record->invoiceItems->isNotEmpty())
                                    Total Items
                                @else
                                    Total Challans
                                @endif
                            </strong>
                        </td>

                        <td>
                            @if (
                                $record->challans->filter(function ($challan) {
                                        return $challan->challan_items->isNotEmpty();
                                    })->isNotEmpty())
                                {{ $record->challans->count() }}
                            @elseif ($record->invoiceItems->isNotEmpty())
                                {{ $record->invoiceItems->count() }}
                            @else
                                0
                            @endif
                        </td>
                    </tr>

                </table>

            </td>

        </tr>

    </table>

    {{-- ITEMS --}}
    <table class="items-table">

        <thead>

            <tr>
                <th width="4%">#</th>
                <th width="10%">Challan No</th>
                <th width="8%">Date</th>
                <th width="22%">Material</th>
                <th width="10%">Vehicle</th>
                <th width="12%">Driver</th>
                <th width="10%">Quantity</th>
                <th width="10%">Rate</th>
                <th width="14%">Amount</th>
            </tr>

        </thead>

        <tbody>

            @php
                $rowNo = 1;
                $totalQty = 0;
                $hasChallanItems = $record->challans
                    ->filter(function ($challan) {
                        return $challan->challan_items->isNotEmpty();
                    })
                    ->isNotEmpty();
                $showInvoiceItems = !$hasChallanItems && $record->invoiceItems->isNotEmpty();
                $invoiceItemReferences = $record->invoiceItems
                    ->map(function ($invoiceItem) {
                        return $invoiceItem->item->material_name ?? 'Item #' . $invoiceItem->id;
                    })
                    ->implode(', ');
            @endphp

            @if ($hasChallanItems)
                @foreach ($record->challans as $challan)
                    @php
                        $challanTotal = $challan->challan_items->sum('amount');
                    @endphp

                    @foreach ($challan->challan_items as $item)
                        @php
                            $totalQty += $item->quantity_cft;
                        @endphp

                        <tr>

                            <td class="text-center">
                                {{ $rowNo++ }}
                            </td>

                            <td>
                                {{ $challan->challan_number }}
                            </td>

                            <td>
                                {{ $challan->invoice_date->format('d-m-Y') }}
                            </td>

                            <td>
                                {{ $item->item->material_name }}
                            </td>

                            <td>
                                {{ $challan->vehicle->vehicle_number ?? '-' }}
                            </td>

                            <td>
                                {{ $challan->driver->driver_name ?? '-' }}
                            </td>

                            <td class="text-right">
                                {{ number_format($item->quantity_cft, 2) }}
                            </td>

                            <td class="text-right">
                                ₹{{ number_format($item->rate_at_sale, 2) }}
                            </td>

                            <td class="text-right">
                                ₹{{ number_format($item->amount, 2) }}
                            </td>

                        </tr>
                    @endforeach

                    <tr class="challan-row">

                        <td colspan="8" class="text-right">
                            Challan Total :
                        </td>

                        <td class="text-right">
                            ₹{{ number_format($challanTotal, 2) }}
                        </td>

                    </tr>
                @endforeach
            @elseif ($showInvoiceItems)
                @foreach ($record->invoiceItems as $item)
                    @php
                        $totalQty += $item->quantity;
                    @endphp

                    <tr>

                        <td class="text-center">
                            {{ $rowNo++ }}
                        </td>

                        <td>
                            -
                        </td>

                        <td>
                            {{ $record->invoice_date->format('d-m-Y') }}
                        </td>

                        <td>
                            {{ $item->item->material_name ?? 'N/A' }}
                        </td>

                        <td>
                            -
                        </td>

                        <td>
                            -
                        </td>

                        <td class="text-right">
                            {{ number_format($item->quantity, 2) }}
                        </td>

                        <td class="text-right">
                            ₹{{ number_format($item->rate_at_sale, 2) }}
                        </td>

                        <td class="text-right">
                            ₹{{ number_format($item->amount, 2) }}
                        </td>

                    </tr>
                @endforeach

                <tr class="challan-row">

                    <td colspan="8" class="text-right">
                        Invoice Total :
                    </td>

                    <td class="text-right">
                        ₹{{ number_format($record->total_amount, 2) }}
                    </td>

                </tr>
            @else
                <tr>
                    <td colspan="9" class="text-center">
                        No invoice or challan details available.
                    </td>
                </tr>
            @endif

        </tbody>

    </table>

    {{-- TOTALS --}}
    <table style="width:100%; margin-top:12px; border-collapse:collapse;">
        <tr>

            {{-- LEFT SIDE --}}
            <td style="width:60%; vertical-align:top; padding-right:10px;">

                <div class="amount-words">
                    <strong>Amount in Words :</strong><br>

                    {{ ucfirst(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($record->total_amount)) }}
                    Rupees Only
                </div>

            </td>

            {{-- RIGHT SIDE --}}
            <td style="width:40%; vertical-align:top;">

                <table class="summary-table" style="width:100%; margin-left:0;">

                    <tr>
                        <td>Total Quantity</td>
                        <td class="text-right">
                            {{ number_format($totalQty, 2) }}
                        </td>
                    </tr>

                    <tr class="grand-total">
                        <td>Grand Total</td>
                        <td class="text-right">
                            ₹{{ number_format($record->total_amount, 2) }}
                        </td>
                    </tr>

                </table>

            </td>

        </tr>
    </table>

    {{-- SIGNATURES --}}
    <div class="signature-section">

        <table class="signature-table">

            <tr>

                <td width="50%">

                    <div class="signature-line">
                        Customer Signature
                    </div>

                </td>

                <td width="50%" align="right">

                    <div class="signature-line" style="margin-left:auto;">
                        Authorized Signatory
                    </div>

                    <div style="margin-top:6px;">
                        For {{ $record->company->name }}
                    </div>

                </td>

            </tr>

        </table>

    </div>

    <div class="footer-note">
        This is a computer-generated invoice and does not require a physical signature.
    </div>

</div>

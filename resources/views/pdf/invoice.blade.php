    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
        }

        .no-border {
            border: none !important;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .heading {
            font-size: 18px;
            font-weight: bold;
        }

        .sub-heading {
            font-size: 14px;
            font-weight: bold;
        }
    </style>
    <div style="font-family: DejaVu Sans, Arial, sans-serif;">
        <table>
            <tr>
                <td class="center no-border">
                    @if ($record->company->logo)
                        @php
                            $logoPath = ltrim($record->company->logo, '/');
                            $logoPath = Storage::disk('local')->path($logoPath);
                        @endphp

                        <img src="{{ $logoPath }}" alt="{{ $record->company->name }} Logo"
                            style="max-height: 80px; max-width: 180px; display: block; margin: 0 auto 10px;" />
                    @endif

                    <div class="heading">{{ $record->company->name }}</div>
                    <div>{{ $record->company->address }}</div>
                    <div>GSTIN: {{ $record->company->gstin ?? 'N/A' }}</div>
                    <div>Phone: {{ $record->company->phone ?? 'N/A' }}</div>
                </td>
            </tr>
        </table>

        <h2 class="center">INVOICE</h2>

        {{-- INVOICE DETAILS --}}
        <table>
            <tr>
                <td>
                    <strong>Invoice No:</strong>
                    {{ $record->invoice_number }}
                </td>

                <td>
                    <strong>Date:</strong>
                    {{ $record->created_at->format('d-m-Y') }}
                </td>
            </tr>

            <tr>
                <td>
                    <strong>Payment Mode:</strong>
                    {{ $record->payment_mode }}
                </td>

                <td>
                    <strong>Total Challans:</strong>
                    {{ $record->challans->count() }}
                </td>
            </tr>
        </table>

        <br>

        {{-- CUSTOMER DETAILS --}}
        <table>
            <tr>
                <td>
                    <strong>Bill To</strong><br>

                    {{ $record->party->full_name }}<br>

                    {{ $record->party->address_line_1 ?? '' }}<br>

                    GSTIN:
                    {{ $record->party->gst_number ?? 'N/A' }}<br>

                    Mobile:
                    {{ $record->party->contact_number ?? 'N/A' }}
                </td>
            </tr>
        </table>

        <br>

        {{-- MATERIAL DETAILS --}}
        <table>
            <thead>
                <tr>
                    <th>Sl</th>
                    <th>Challan No</th>
                    <th>Date</th>
                    <th>Material</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Quantity</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($record->challans as $index => $challan)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>{{ $challan->challan_number }}</td>

                        <td>{{ $challan->created_at->format('d-m-Y') }}</td>

                        <td>{{ $challan->item->material_name ?? '' }}</td>

                        <td>{{ $challan->vehicle->vehicle_number ?? '' }}</td>

                        <td>{{ $challan->driver->driver_name ?? '' }}</td>

                        <td class="right">
                            {{ number_format($challan->quantity_cft, 2) }}
                            {{ $challan->item->unit ?? 'CFT' }}
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <br>

        {{-- SUMMARY --}}
        <table>
            <tr>
                <td width="70%">

                    <strong>Challan References:</strong><br>

                    {{ $record->challans->pluck('challan_number')->implode(', ') }}

                </td>

                <td>

                    <table>
                        <tr>
                            <td>Total Quantity</td>
                            <td class="right">
                                {{ number_format($record->challans->sum('quantity_cft'), 2) }}
                            </td>
                        </tr>

                        <tr>
                            <td>Driver Bata</td>
                            <td class="right">
                                ₹{{ number_format($record->driver_bata, 2) }}
                            </td>
                        </tr>

                        <tr>
                            <td>Taxable Value</td>
                            <td class="right">
                                ₹{{ number_format($record->total_amount, 2) }}
                            </td>
                        </tr>

                        {{-- Future GST Support --}}
                        <tr>
                            <td>CGST</td>
                            <td class="right">₹0.00</td>
                        </tr>

                        <tr>
                            <td>SGST</td>
                            <td class="right">₹0.00</td>
                        </tr>

                        <tr>
                            <td><strong>Grand Total</strong></td>
                            <td class="right">
                                <strong>
                                    ₹{{ number_format($record->total_amount, 2) }}
                                </strong>
                            </td>
                        </tr>

                    </table>

                </td>
            </tr>
        </table>

        <br>

        {{-- AMOUNT IN WORDS --}}
        <table>
            <tr>
                <td>
                    <strong>Amount in Words:</strong>

                    {{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($record->total_amount) }}
                    Rupees Only
                </td>
            </tr>
        </table>

        <br><br>

        {{-- FOOTER --}}
        <table>
            <tr>
                <td width="50%">
                    Customer Signature
                </td>

                <td width="50%" class="right">
                    For {{ $record->company->name }}

                    <br><br><br>

                    Authorized Signatory
                </td>
            </tr>
        </table>
    </div>

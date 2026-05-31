<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .wrapper {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        .heading {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .sub-heading {
            font-size: 14px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td,
        .notes-table th,
        .notes-table td {
            border: 1px solid #444;
            padding: 8px;
            vertical-align: top;
        }

        .details-table th,
        .notes-table th {
            background: #f4f4f4;
            text-align: left;
            font-weight: bold;
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

        .notes {
            white-space: pre-wrap;
        }

        .signature-row td {
            border: none !important;
            padding-top: 30px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="center">
            <div class="heading">{{ config('app.name', 'Expense Report') }}</div>
            <div class="sub-heading">Expense Print Layout</div>
        </div>

        <table class="details-table">
            <tr>
                <th width="30%">Expense Date</th>
                <td>{{ $record->expenditure_date?->format('d-m-Y') ?? $record->created_at?->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Category</th>
                <td>{{ $record->category }}</td>
            </tr>
            <tr>
                <th>Reference No.</th>
                <td>{{ $record->reference_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Amount</th>
                <td>₹{{ number_format($record->amount, 2) }}</td>
            </tr>
            <tr>
                <th>Party / Paid To</th>
                <td>{{ $record->party->full_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Party Details</th>
                <td>{{ $record->party->address_line_1 . ' ' . ($record->party->address_line_2 ?? '') ?? 'N/A' }}</td>
            </tr>
        </table>

        <br>

        <table class="notes-table">
            <tr>
                <th>Notes</th>
                <td class="notes">{{ $record->notes ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $record->created_at?->format('d-m-Y H:i') ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $record->updated_at?->format('d-m-Y H:i') ?? 'N/A' }}</td>
            </tr>
        </table>

        <br>

        <table class="details-table">
            <tr>
                <th>Amount in Words</th>
                <td>{{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($record->amount) }} Rupees
                    Only</td>
            </tr>
        </table>

        <br><br>

        <table class="no-border signature-row">
            <tr>
                <td width="50%">Prepared By</td>
                <td width="50%" class="right">Authorized Signature</td>
            </tr>
        </table>
    </div>
</body>

</html>

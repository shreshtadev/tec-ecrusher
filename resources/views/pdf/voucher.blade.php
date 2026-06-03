<style>
    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
    }

    .voucher-container {
        padding: 20px;
        border: 1px solid #e6e6e6;
    }

    .voucher-header {
        width: 100%;
        margin-bottom: 12px;
    }

    .company-block {
        vertical-align: top;
    }

    .company-name {
        font-size: 16px;
        font-weight: 700;
    }

    .company-address {
        font-size: 11px;
        color: #333;
    }

    .voucher-meta {
        text-align: right;
        vertical-align: top;
    }

    .voucher-title {
        font-size: 20px;
        margin: 0 0 6px 0;
    }

    .details {
        width: 100%;
        margin-top: 10px;
        border-collapse: collapse;
    }

    .details td {
        padding: 6px 8px;
        vertical-align: top;
    }

    .amount-box {
        border: 1px solid #ddd;
        padding: 12px;
        background: #fafafa;
        text-align: right;
    }

    .amount-value {
        font-size: 20px;
        font-weight: 700;
    }

    .small-muted {
        font-size: 11px;
        color: #666;
    }

    .signatures {
        width: 100%;
        margin-top: 40px;
    }

    .signatures td {
        padding-top: 30px;
    }
</style>

@php $company = $record->invoice->company ?? null; @endphp

<div class="voucher-container">
    <table class="voucher-header">
        <tr>
            <td class="company-block" style="width:60%;">
                @if ($company && $company->logo)
                    @php
                        $logoPath = ltrim($company->logo, '/');
                        $logoPath = Storage::disk('local')->path($logoPath);
                    @endphp

                    <img src="{{ $logoPath }}" alt="{{ $company->name }} Logo"
                        style="max-height:72px; max-width:220px; display:block; margin-bottom:8px;" />
                @endif

                @if ($company)
                    <div class="company-name">{{ $company->name }}</div>
                    <div class="company-address">{{ $company->address }}</div>
                    <div class="small-muted">GSTIN: {{ $company->gstin ?? 'N/A' }} | Phone:
                        {{ $company->phone ?? 'N/A' }}</div>
                @endif
            </td>

            <td class="voucher-meta" style="width:40%;">
                <div class="voucher-title">{{ $record->type }}</div>
                <div><strong>Voucher No:</strong> {{ $record->voucher_no }}</div>
                <div><strong>Date:</strong> {{ $record->voucher_date }}</div>
                <div><strong>Mode:</strong> {{ $record->payment_mode ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <table class="details" style="border-top:1px solid #eee;">
        <tr>
            <td style="width:55%; border-right:1px solid #f0f0f0;">
                <strong>{{ $record->voucher_type === \App\Domains\Common\Enums\VoucherOpts::RECEIPT ? 'Received From' : 'Paid To' }}</strong>
                <div style="margin-top:6px;"><strong>{{ $record->party->full_name }}</strong></div>
                <div class="small-muted">{{ $record->party->address_line_1 ?? '' }}</div>
                <div class="small-muted">GSTIN: {{ $record->party->gst_number ?? 'N/A' }} | Mobile:
                    {{ $record->party->contact_number ?? 'N/A' }}</div>
            </td>

            <td style="width:45%;">
                <div class="amount-box">
                    <div class="small-muted">Amount</div>
                    <div class="amount-value">₹{{ number_format($record->amount, 2) }}</div>
                    <div class="small-muted" style="margin-top:6px;">
                        {{ \NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($record->amount) }}
                        Rupees Only</div>
                </div>

                @if ($record->invoice)
                    <div style="margin-top:10px;" class="small-muted">Adjusted against Invoice:
                        {{ $record->invoice->invoice_number }}</div>
                @endif
            </td>
        </tr>
    </table>

    @if ($record->remarks)
        <div style="margin-top:14px; font-style:italic;">Remarks: {{ $record->remarks }}</div>
    @endif

    <table class="signatures">
        <tr>
            <td style="width:50%; text-align:left;">
                <div>Prepared By</div>
                <div style="margin-top:40px;">__________________________</div>
            </td>
            <td style="width:50%; text-align:right;">
                <div>Authorized Signatory</div>
                <div style="margin-top:40px;">__________________________</div>
            </td>
        </tr>
    </table>
</div>

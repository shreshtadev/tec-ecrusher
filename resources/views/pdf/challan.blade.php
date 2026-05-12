<!DOCTYPE html>
<html>

<head>
    <style>
        /* CRITICAL: Remove all default paging margins */
        @page {
            margin: 0;
            size: A4 portrait;
        }

        body {
            margin: 0;
            padding: 0;
            width: 210mm;
            height: 297mm;
            font-family: sans-serif;
            font-size: 11px;
        }

        /* Fixed quadrant container */
        .slip-container {
            position: absolute;
            width: 100mm;
            height: 95mm;
            /* Fixed height to take up nearly half the vertical space */
            top: 5mm;
            box-sizing: border-box;
        }

        .office {
            left: 5mm;
        }

        .customer {
            left: 105mm;
        }

        .border-box {
            border: 1px solid #bcc;
            height: 100%;
            /* Fills the 140mm container */
            padding: 5mm;
            position: relative;
        }

        .copy-label {
            position: absolute;
            top: 2mm;
            right: 4mm;
            font-size: 8px;
            font-weight: bold;
            border: 0.5pt solid #bcc;
            padding: 1px 4px;
            text-transform: uppercase;
        }

        /* Forces signatures to the bottom of the 130mm box */
        .signature-wrap {
            position: absolute;
            bottom: 5mm;
            left: 5mm;
            right: 5mm;
        }
    </style>
</head>

<body>

    <div class="slip-container office">
        <div class="border-box">
            <div class="copy-label">Office Copy</div>

            <div style="margin-top: 5mm;">
                @include('pdf.partials.challan-content')
            </div>

            <div class="signature-wrap">
                @include('pdf.partials.challan-signature')
            </div>
        </div>
    </div>

    <div class="slip-container customer">
        <div class="border-box">
            <div class="copy-label">Customer Copy</div>

            <div style="margin-top: 5mm;">
                @include('pdf.partials.challan-content')
            </div>

            <div class="signature-wrap">
                @include('pdf.partials.challan-signature')
            </div>
        </div>
    </div>

</body>

</html>

<?php

namespace App\Domains\Shared\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use App\Domains\Accounting\Models\Voucher;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function challan(Challan $record)
    {
        return Pdf::loadView('pdf.challan', compact('record'))
            ->stream("Challan_{$record->challan_number}.pdf");
    }

    public function invoice(Invoice $record)
    {
        return Pdf::loadView('pdf.invoice', compact('record'))
            ->stream("Invoice_{$record->invoice_number}.pdf");
    }

    public function voucher(Voucher $record)
    {
        return Pdf::loadView('pdf.voucher', compact('record'))
            ->stream("Voucher_{$record->voucher_no}.pdf");
    }
}

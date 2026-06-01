<?php

namespace App\Domains\Shared\Controllers;

use App\Domains\Accounting\Models\Expense;
use App\Http\Controllers\Controller;
use App\Domains\Operations\Models\Challan;
use App\Domains\Operations\Models\Invoice;
use App\Domains\Accounting\Models\Voucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PrintController extends Controller
{
    public function challan(Challan $record)
    {
        return Pdf::loadView('pdf.challan', compact('record'))
            ->stream("Challan_{Str::slug($record->challan_number)}.pdf");
    }

    public function invoice(Invoice $record)
    {
        return Pdf::loadView('pdf.invoice', compact('record'))
            ->stream("Invoice_{Str::slug($record->invoice_number)}.pdf");
    }

    public function voucher(Voucher $record)
    {
        return Pdf::loadView('pdf.voucher', compact('record'))
            ->stream("Voucher_{Str::slug($record->voucher_no)}.pdf");
    }

    public function expense(Expense $record)
    {
        return Pdf::loadView('pdf.expense', compact('record'))
            ->stream("Expense_{Str::slug($record->expense_number)}.pdf");
    }
}

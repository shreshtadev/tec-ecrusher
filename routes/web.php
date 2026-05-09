<?php

use App\Domains\Shared\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/print/challan/{record}', [PrintController::class, 'challan'])->name('print.challan');
    Route::get('/print/invoice/{record}', [PrintController::class, 'invoice'])->name('print.invoice');
    Route::get('/print/voucher/{record}', [PrintController::class, 'voucher'])->name('print.voucher');
});

require __DIR__ . '/settings.php';

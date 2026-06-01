<?php

use App\Domains\Shared\Controllers\PrintController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::redirect('/login', '/admin/login', 301)->name('admin.login');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect("/dashboard", "/admin")->name('admin.dashboard');
    Route::get('/admin/logout', function () {
        // Log out the current user for the active Filament panel
        Filament::auth()->logout();

        // Invalidate the session and regenerate the token for security
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // Redirect to your desired location
        return redirect()->route('admin.login');
    })->name('admin.logout');

    Route::get('/print/challan/{record}', [PrintController::class, 'challan'])->name('print.challan');
    Route::get('/print/invoice/{record}', [PrintController::class, 'invoice'])->name('print.invoice');
    Route::get('/print/voucher/{record}', [PrintController::class, 'voucher'])->name('print.voucher');
    Route::get('/print/expense/{record}', [PrintController::class, 'expense'])->name('print.expense');
});

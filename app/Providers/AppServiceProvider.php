<?php

namespace App\Providers;

use App\Models\InvoiceAllocation;
use App\Models\InvoiceItem;
use App\Models\ProductionEntry;
use App\Models\StockIssue;
use App\Models\Voucher;
use App\Observers\InvoiceAllocationObserver;
use App\Observers\InvoiceItemObserver;
use App\Observers\ProductionEntryObserver;
use App\Observers\StockIssueObserver;
use App\Observers\VoucherObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        ProductionEntry::observe(ProductionEntryObserver::class);
        InvoiceItem::observe(InvoiceItemObserver::class);
        Voucher::observe(VoucherObserver::class);
        InvoiceAllocation::observe(InvoiceAllocationObserver::class);
        StockIssue::observe(StockIssueObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null,
        );
    }
}

<?php

namespace App\Filament\Widgets;

use App\Enums\VoucherOpts;
use App\Filament\Resources\Challans\ChallanResource;
use App\Filament\Resources\Companies\CompanyResource;
use App\Filament\Resources\Drivers\DriverResource;
use App\Filament\Resources\Expenses\ExpenseResource;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\Parties\PartyResource;
use App\Filament\Resources\ProductionEntries\ProductionEntryResource;
use App\Filament\Resources\Vehicles\VehicleResource;
use App\Filament\Resources\Vouchers\VoucherResource;
use App\Models\Challan;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Party;
use App\Models\ProductionEntry;
use App\Models\Vehicle;
use App\Models\Voucher;
use Filament\Widgets\Widget;

class WorkflowStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.workflow-status-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $companyExists = Company::exists();
        $partyExists = Party::exists();
        $itemExists = Item::exists();
        $driverExists = Driver::exists();
        $vehicleExists = Vehicle::exists();

        $mastersCompleted =
            $companyExists &&
            $partyExists &&
            $itemExists &&
            $driverExists &&
            $vehicleExists;

        $productionExists = ProductionEntry::exists();
        $challanExists = Challan::exists();
        $invoiceExists = Invoice::exists();

        return [
            'groups' => [

                [
                    'title' => 'Master Setup',
                    'steps' => [
                        $this->step(
                            'Companies',
                            'heroicon-m-building-office-2',
                            Company::count(),
                            $companyExists,
                            CompanyResource::getUrl(),
                        ),

                        $this->step(
                            'Parties',
                            'heroicon-m-users',
                            Party::count(),
                            $partyExists,
                            PartyResource::getUrl(),
                        ),

                        $this->step(
                            'Items',
                            'heroicon-m-cube',
                            Item::count(),
                            $itemExists,
                            ItemResource::getUrl(),
                        ),

                        $this->step(
                            'Drivers',
                            'heroicon-m-user',
                            Driver::count(),
                            $driverExists,
                            DriverResource::getUrl(),
                        ),

                        $this->step(
                            'Vehicles',
                            'heroicon-m-truck',
                            Vehicle::count(),
                            $vehicleExists,
                            VehicleResource::getUrl(),
                        ),
                    ],
                ],

                [
                    'title' => 'Operations',
                    'steps' => [
                        [
                            'title' => 'Production Entries',
                            'icon' => 'heroicon-m-cog-6-tooth',
                            'count' => ProductionEntry::count(),
                            'status' => ! $mastersCompleted
                                ? 'locked'
                                : ($productionExists ? 'completed' : 'current'),
                            'url' => ProductionEntryResource::getUrl(),
                        ],

                        [
                            'title' => 'Challans',
                            'icon' => 'heroicon-m-truck',
                            'count' => Challan::count(),
                            'status' => ! $mastersCompleted
                                ? 'locked'
                                : ($challanExists ? 'completed' : 'current'),
                            'url' => ChallanResource::getUrl(),
                        ],

                        [
                            'title' => 'Invoices',
                            'icon' => 'heroicon-m-document-text',
                            'count' => Invoice::count(),
                            'status' => ! $challanExists
                                ? 'locked'
                                : ($invoiceExists ? 'completed' : 'current'),
                            'url' => InvoiceResource::getUrl(),
                        ],
                    ],
                ],

                [
                    'title' => 'Accounting',
                    'steps' => [
                        [
                            'title' => 'Receipt Vouchers',
                            'icon' => 'heroicon-m-banknotes',
                            'count' => Voucher::query()
                                ->where('voucher_type', VoucherOpts::RECEIPT)
                                ->count(),
                            'status' => ! $invoiceExists
                                ? 'locked'
                                : 'current',
                            'url' => VoucherResource::getUrl(),
                        ],

                        [
                            'title' => 'Payment Vouchers',
                            'icon' => 'heroicon-m-credit-card',
                            'count' => Voucher::query()
                                ->where('voucher_type', VoucherOpts::PAYMENT)
                                ->count(),
                            'status' => 'completed',
                            'url' => VoucherResource::getUrl(),
                        ],

                        [
                            'title' => 'Expenses',
                            'icon' => 'heroicon-m-receipt-percent',
                            'count' => Expense::count(),
                            'status' => 'completed',
                            'url' => ExpenseResource::getUrl(),
                        ],
                    ],
                ],
            ],
        ];
    }

    private function step(
        string $title,
        string $icon,
        int $count,
        bool $exists,
        string $url,
    ): array {
        return [
            'title' => $title,
            'icon' => $icon,
            'count' => $count,
            'status' => $exists ? 'completed' : 'current',
            'url' => $url,
        ];
    }
}

<x-filament-widgets::widget class="fi-wi-table">
    <x-filament::section>
        <x-slot name="heading">
            Day Book
        </x-slot>

        @if($account)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-filament::section class="bg-gray-50 dark:bg-gray-900 border border-gray-150">
                    <div class="text-sm text-gray-500">Opening Balance</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        ₹ {{ number_format($balances['opening_balance'] ?? 0, 2) }}
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-gray-50 dark:bg-gray-900 border border-gray-150">
                    <div class="text-sm text-gray-500">Closing Balance</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        ₹ {{ number_format($balances['closing_balance'] ?? 0, 2) }}
                    </div>
                </x-filament::section>
            </div>
        @else
            <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-950 text-yellow-800 dark:text-yellow-200 rounded-lg text-sm">
                Please select an Account from the filters to view opening and closing balances.
            </div>
        @endif

        {{ $this->table }}
    </x-filament::section>
</x-filament-widgets::widget>

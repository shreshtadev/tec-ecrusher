<x-filament-panels::page>

    {{ $this->form }}

    @if (!empty($reportData))

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

            <x-filament::section>
                <div class="text-sm text-gray-500">
                    Invoice Count
                </div>

                <div class="text-3xl font-bold">
                    {{ $reportData['invoice_count'] }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">
                    Total Quantity (CFT)
                </div>

                <div class="text-3xl font-bold">
                    {{ number_format($reportData['total_qty'], 2) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">
                    Total Amount
                </div>

                <div class="text-3xl font-bold">
                    ₹ {{ number_format($reportData['total_amount'], 2) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">
                    Avg Invoice
                </div>

                <div class="text-3xl font-bold">
                    ₹ {{ number_format($reportData['average_invoice_value'] ?? 0, 2) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">
                    Largest Invoice
                </div>

                <div class="text-3xl font-bold">
                    ₹ {{ number_format($reportData['largest_invoice'] ?? 0, 2) }}
                </div>
            </x-filament::section>

        </div>

        <div class="mt-6">

            <x-filament::section>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">Date</th>
                                <th class="text-left p-2">Invoice No</th>
                                <th class="text-right p-2">Amount</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($reportData['invoices'] as $invoice)
                                <tr class="border-b">

                                    <td class="p-2">
                                        {{ $invoice->created_at?->format('d-m-Y') }}
                                    </td>

                                    <td class="p-2">
                                        {{ $invoice->invoice_number ?? $invoice->id }}
                                    </td>

                                    <td class="p-2 text-right">
                                        ₹ {{ number_format($invoice->total_amount, 2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="3" class="p-4 text-center text-gray-500">
                                        No invoices found
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </x-filament::section>
            <x-filament::section class="mt-6">

                <x-slot name="heading">
                    Item Wise Sales
                </x-slot>

                <table class="w-full">

                    <thead>
                        <tr>
                            <th class="text-left p-2">Item</th>
                            <th class="text-right p-2">Quantity</th>
                            <th class="text-right p-2">Amount</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($reportData['item_sales'] as $row)
                            <tr>

                                <td class="p-2">
                                    {{ $row->item->material_name }}
                                </td>

                                <td class="p-2 text-right">
                                    {{ number_format($row->total_qty, 2) }}
                                </td>

                                <td class="p-2 text-right">
                                    ₹ {{ number_format($row->total_amount, 2) }}
                                </td>

                            </tr>
                        @endforeach

                    </tbody>

                </table>

            </x-filament::section>

        </div>

    @endif

</x-filament-panels::page>

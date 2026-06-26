<x-filament-panels::page>

    {{ $this->form }}

    @if (!empty($reportData))

        {{-- Summary stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

            <x-filament::section>
                <div class="text-sm text-gray-500">Invoice Count</div>
                <div class="text-3xl font-bold">{{ $reportData['invoice_count'] }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Total Qty (CFT)</div>
                <div class="text-3xl font-bold">{{ number_format($reportData['total_qty'], 2) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Total Amount</div>
                <div class="text-3xl font-bold">₹ {{ number_format($reportData['total_amount'], 2) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Grand Total</div>
                <div class="text-3xl font-bold">₹ {{ number_format($reportData['grand_total'], 2) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Outstanding</div>
                <div class="text-3xl font-bold text-danger-600">₹
                    {{ number_format($reportData['outstanding_amount'], 2) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Avg Invoice</div>
                <div class="text-3xl font-bold">₹ {{ number_format($reportData['average_invoice_value'] ?? 0, 2) }}
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Largest Invoice</div>
                <div class="text-3xl font-bold">₹ {{ number_format($reportData['largest_invoice'] ?? 0, 2) }}</div>
            </x-filament::section>

        </div>

        <div class="mt-6">

            {{-- Invoices table --}}
            <x-filament::section>

                <x-slot name="heading">Invoices</x-slot>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">Invoice Date</th>
                                <th class="text-left p-2">Invoice No</th>
                                @if (blank($reportData['party_id']))
                                    <th class="text-left p-2">Party</th>
                                @endif
                                <th class="text-left p-2">Mode</th>
                                <th class="text-left p-2">Status</th>
                                <th class="text-right p-2">Amount</th>
                                <th class="text-right p-2">Grand Total</th>
                                <th class="text-right p-2">Outstanding</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($reportData['invoices'] as $invoice)
                                <tr class="border-b">

                                    <td class="p-2">
                                        {{ date('d-m-Y', strtotime($invoice->invoice_date)) ?? date('d-m-Y', strtotime($invoice->created_at)) }}
                                    </td>

                                    <td class="p-2 font-medium">
                                        {{ $invoice->invoice_number ?? $invoice->id }}
                                    </td>

                                    @if (blank($reportData['party_id']))
                                        <td class="p-2">{{ $invoice->party?->full_name }}</td>
                                    @endif

                                    <td class="p-2">{{ $invoice->payment_mode }}</td>

                                    <td class="p-2">
                                        @php
                                            $statusColor = match ($invoice->payment_status) {
                                                'paid' => 'text-success-600',
                                                'partial' => 'text-warning-600',
                                                default => 'text-danger-600',
                                            };
                                        @endphp
                                        <span class="font-medium {{ $statusColor }}">
                                            {{ ucfirst($invoice->payment_status) }}
                                        </span>
                                    </td>

                                    <td class="p-2 text-right">₹ {{ number_format($invoice->total_amount, 2) }}</td>

                                    <td class="p-2 text-right">₹ {{ number_format($invoice->grand_total, 2) }}</td>

                                    <td
                                        class="p-2 text-right {{ $invoice->outstanding_amount > 0 ? 'text-danger-600 font-medium' : '' }}">
                                        ₹ {{ number_format($invoice->outstanding_amount, 2) }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="p-4 text-center text-gray-500">
                                        No invoices found for the selected filters
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </x-filament::section>

            {{-- Item-wise sales --}}
            <x-filament::section class="mt-6">

                <x-slot name="heading">Item Wise Sales</x-slot>

                <table class="w-full text-sm">

                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-2">Item</th>
                            <th class="text-right p-2">Rate</th>
                            <th class="text-right p-2">Quantity (CFT)</th>
                            <th class="text-right p-2">Amount</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($reportData['item_sales'] as $row)
                            <tr class="border-b">
                                <td class="p-2">{{ $row->material_name }}</td>
                                <td class="p-2 text-right">₹ {{ number_format($row->price_per_unit, 2) }}</td>
                                <td class="p-2 text-right">{{ number_format($row->total_qty, 2) }}</td>
                                <td class="p-2 text-right">₹ {{ number_format($row->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500">No items found</td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </x-filament::section>

        </div>
    @else
        <div class="mt-6 text-center text-gray-400 py-12">
            Select filters above to load the report.
        </div>

    @endif

</x-filament-panels::page>

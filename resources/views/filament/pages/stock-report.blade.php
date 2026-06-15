<x-filament-panels::page>

    {{ $this->form }}

    @if (!empty($reportData))

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

            <x-filament::section>
                <div class="text-sm text-gray-500">Total Items</div>
                <div class="text-3xl font-bold">{{ $reportData['total_items'] }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Total Stock Value</div>
                <div class="text-3xl font-bold">₹ {{ number_format($reportData['total_value'] ?? 0, 2) }}</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500">Selected Filters</div>
                <div class="text-sm text-gray-700">
                    Warehouse:
                    {{ optional(\App\Models\Warehouse::find($data['warehouse_id'] ?? null))->name ?? 'All' }}<br>
                    Item: {{ optional(\App\Models\Item::find($data['item_id'] ?? null))->material_name ?? 'All' }}
                </div>
            </x-filament::section>

        </div>

        <div class="mt-6">

            <x-filament::section>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">Item</th>
                                <th class="text-left p-2">Warehouse</th>
                                <th class="text-right p-2">Available Qty</th>
                                <th class="text-right p-2">Unit Price</th>
                                <th class="text-right p-2">Stock Value</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($reportData['stock_levels'] as $row)
                                <tr class="border-b">

                                    <td class="p-2">{{ $row['item_name'] }}</td>

                                    <td class="p-2">{{ $row['warehouse'] }}</td>

                                    <td class="p-2 text-right">
                                        {{ number_format($row['total_qty'] ?? ($row['available_qty'] ?? 0), 2) }}</td>

                                    <td class="p-2 text-right">₹ {{ number_format($row['price_per_unit'] ?? 0, 2) }}
                                    </td>

                                    <td class="p-2 text-right">₹ {{ number_format($row['stock_value'] ?? 0, 2) }}</td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">No stock levels found</td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </x-filament::section>

            <x-filament::section class="mt-6">

                <x-slot name="heading">Stock Movements</x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">Date</th>
                                <th class="text-left p-2">Item</th>
                                <th class="text-left p-2">Warehouse</th>
                                <th class="text-left p-2">Type</th>
                                <th class="text-right p-2">Qty</th>
                                <th class="text-right p-2">Unit Cost</th>
                                <th class="text-right p-2">Total Cost</th>
                                <th class="text-left p-2">Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reportData['movements'] as $m)
                                <tr class="border-b">
                                    <td class="p-2">{{ $m['date'] }}</td>
                                    <td class="p-2">{{ $m['item'] }}</td>
                                    <td class="p-2">{{ $m['warehouse'] }}</td>
                                    <td class="p-2">{{ $m['movement_type'] }}</td>
                                    <td class="p-2 text-right">{{ number_format($m['quantity'], 2) }}</td>
                                    <td class="p-2 text-right">₹ {{ number_format($m['unit_cost'] ?? 0, 2) }}</td>
                                    <td class="p-2 text-right">₹ {{ number_format($m['total_cost'] ?? 0, 2) }}</td>
                                    <td class="p-2">{{ $m['reference'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="p-4 text-center text-gray-500">No movements found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </x-filament::section>

            @if (!empty($reportData['aging']) && $reportData['aging']->isNotEmpty())
                <x-filament::section class="mt-6">
                    <x-slot name="heading">Stock Aging</x-slot>

                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left p-2">Receipt Date</th>
                                <th class="text-right p-2">Quantity</th>
                                <th class="text-right p-2">Unit Cost</th>
                                <th class="text-right p-2">Total Cost</th>
                                <th class="text-right p-2">Age (days)</th>
                                <th class="text-left p-2">Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData['aging'] as $a)
                                <tr>
                                    <td class="p-2">{{ $a['receipt_date'] }}</td>
                                    <td class="p-2 text-right">{{ number_format($a['quantity'], 2) }}</td>
                                    <td class="p-2 text-right">₹ {{ number_format($a['unit_cost'] ?? 0, 2) }}</td>
                                    <td class="p-2 text-right">₹ {{ number_format($a['total_cost'] ?? 0, 2) }}</td>
                                    <td class="p-2 text-right">{{ $a['age_days'] }}</td>
                                    <td class="p-2">{{ $a['age_category'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-filament::section>
            @endif

            @if (!empty($reportData['costing']) && $reportData['costing']->isNotEmpty())
                <x-filament::section class="mt-6">
                    <x-slot name="heading">Item Costing</x-slot>

                    <table class="w-full text-sm">
                        <thead>
                            <tr>
                                <th class="text-left p-2">Item</th>
                                <th class="text-left p-2">Warehouse</th>
                                <th class="text-right p-2">Available Qty</th>
                                <th class="text-right p-2">FIFO Unit Cost</th>
                                <th class="text-right p-2">FIFO Total Cost</th>
                                <th class="text-right p-2">LIFO Unit Cost</th>
                                <th class="text-right p-2">LIFO Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportData['costing'] as $c)
                                <tr>
                                    <td class="p-2">{{ $c['item'] }}</td>
                                    <td class="p-2">{{ $c['warehouse'] }}</td>
                                    <td class="p-2 text-right">{{ number_format($c['available_qty'] ?? 0, 2) }}</td>
                                    <td class="p-2 text-right">₹ {{ number_format($c['fifo_unit_cost'] ?? 0, 2) }}</td>
                                    <td class="p-2 text-right">₹ {{ number_format($c['fifo_total_cost'] ?? 0, 2) }}
                                    </td>
                                    <td class="p-2 text-right">₹ {{ number_format($c['lifo_unit_cost'] ?? 0, 2) }}</td>
                                    <td class="p-2 text-right">₹ {{ number_format($c['lifo_total_cost'] ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-filament::section>
            @endif

        </div>

    @endif

</x-filament-panels::page>

<x-filament-widgets::widget>

    <div class="grid gap-6 lg:grid-cols-3">

        @foreach ($groups as $group)
            <x-filament::section :heading="$group['title']">

                <div class="space-y-3">

                    @foreach ($group['steps'] as $step)
                        <div class="flex items-center justify-between rounded-xl border p-3">

                            <div class="flex items-center gap-3">

                                <div>

                                    @if ($step['status'] === 'completed')
                                        <x-filament::icon icon="heroicon-m-check-circle"
                                            class="h-6 w-6 text-success-500" />
                                    @elseif($step['status'] === 'current')
                                        <x-filament::icon icon="heroicon-m-exclamation-circle"
                                            class="h-6 w-6 text-warning-500" />
                                    @else
                                        <x-filament::icon icon="heroicon-m-lock-closed" class="h-6 w-6 text-gray-400" />
                                    @endif

                                </div>

                                <div>

                                    <div class="flex items-center gap-2">

                                        <x-filament::icon :icon="$step['icon']" class="h-4 w-4" />

                                        <span class="font-medium">
                                            {{ $step['title'] }}
                                        </span>

                                    </div>

                                    <div class="text-xs text-gray-500">
                                        Count: {{ $step['count'] }}
                                    </div>

                                </div>

                            </div>

                            <x-filament::button tag="a" :href="$step['url']" size="xs">
                                Open
                            </x-filament::button>

                        </div>
                    @endforeach

                </div>

            </x-filament::section>
        @endforeach

    </div>

</x-filament-widgets::widget>

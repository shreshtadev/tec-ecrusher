<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ __('Welcome') }} - {{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-[#FDFDFC] dark:bg-zinc-950 text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <header class="w-full lg:max-w-4xl max-w-83.75 text-sm mb-6 not-has-[nav]:hidden">
        @if (Route::has('login'))
            <nav class="flex items-center justify-end gap-4">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('admin.login') }}"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                        Log in
                    </a>
                @endauth
            </nav>
        @endif
    </header>
    <div
        class="flex flex-col items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
        <!-- Hero Text Section -->
        <div class="text-center max-w-2xl mb-12">
            <h1 class="text-4xl lg:text-5xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
                Crush your overheads with <span class="text-blue-600 dark:text-blue-400">precision.</span>
            </h1>
            <p class="text-lg text-[#1b1b18]/70 dark:text-[#EDEDEC]/60 leading-relaxed">
                The complete ERP for stone crusher owners to manage daily production, automate GST billing, and track
                vehicle loads in real-time.
            </p>
        </div>

        <!-- Feature/Action Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full lg:max-w-4xl">
            <!-- Card 1: Daily Production & Loads -->
            <div
                class="group p-8 bg-white dark:bg-zinc-900 border border-[#19140015] dark:border-[#3E3E3A] rounded-xl hover:shadow-lg hover:shadow-blue-500/5 transition-all">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-6">
                    <!-- Truck/Logistics Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 dark:text-white">Load Tracking</h3>
                <p class="text-sm text-[#1b1b18]/60 dark:text-[#EDEDEC]/50 mb-4">Record weighbridge data, trip details,
                    and material types (20mm, 40mm, M-Sand) seamlessly.</p>
                <span class="text-sm font-medium text-blue-600 group-hover:underline">Manage Daily Loads &rarr;</span>
            </div>

            <!-- Card 2: Billing & GST -->
            <div
                class="group p-8 bg-white dark:bg-zinc-900 border border-[#19140015] dark:border-[#3E3E3A] rounded-xl hover:shadow-lg hover:shadow-emerald-500/5 transition-all">
                <div
                    class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center mb-6">
                    <!-- Receipt/Invoice Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2 dark:text-white">Automated Billing</h3>
                <p class="text-sm text-[#1b1b18]/60 dark:text-[#EDEDEC]/50 mb-4">Generate GST-compliant invoices and
                    track outstanding payments for credit customers.</p>
                <span class="text-sm font-medium text-emerald-600 group-hover:underline">Generate Invoices &rarr;</span>
            </div>
        </div>
    </div>

    <footer
        class="mt-auto w-full lg:max-w-4xl max-w-83.75 text-sm text-[#1b1b18]/70 dark:text-[#EDEDEC]/60 border-t border-[#19140015] dark:border-[#3E3E3A] pt-4">
        <p class="text-center">
            © {{ now()->format('Y') }} <a href="https://techsathya.in"
                class="text-blue-600 dark:text-blue-400 hover:underline">techsathya.in</a>. All rights reserved. Powered
            by <a href="https://shreshtasmg.in"
                class="text-blue-600 dark:text-blue-400 hover:underline">shreshtasmg.in</a>.
        </p>

    </footer>

    @if (Route::has('login'))
        <div class="h-14.5 hidden lg:block"></div>
    @endif
</body>

</html>

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

    <style>
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('/images/ssc6.jpg') center/cover fixed,
                url('/images/ssc6.jpg') center/cover fixed;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            z-index: -1;
            opacity: 0.25;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* background: linear-gradient(135deg, rgba(30, 27, 24, 0.121) 0%, rgba(24, 23, 23, 0.039) 50%, rgba(30, 27, 24, 0.027) 100%); */
            z-index: -1;
        }
    </style>
</head>

<body class="bg-[#FDFDFC] dark:bg-zinc-950 text-[#1b1b18] flex flex-col min-h-screen">
    <header
        class="w-full text-sm sticky top-0 z-50 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border-b border-[#19140015] dark:border-[#3E3E3A]">
        <div class="flex items-center justify-between gap-4 px-6 lg:px-8 py-4 max-w-6xl mx-auto w-full">
            <div class="text-lg font-semibold dark:text-[#EDEDEC]"><img src="/images/logos/SSC_LOGO.svg"
                    alt="TechSathya - SSC" class="h-10 inline">Shruthi Stone Crusher</div>
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a href="{{ route('admin.dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.logout') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                            Logout
                        </a>
                    @else
                        <a href="{{ route('admin.login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                            Log in
                        </a>
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <div class="flex-1 flex flex-col items-center justify-center w-full px-6 lg:px-8 py-12">
        <!-- Hero Section with Video Background -->
        <section class="w-full max-w-6xl mb-16">
            <div class="relative rounded-2xl overflow-hidden mb-12 h-96 lg:h-125 bg-zinc-900 dark:bg-zinc-950">
                <video id="loopingVideo"
                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-100"
                    autoplay muted playsinline>
                    <source src="/images/shruthistonecrusher.mp4" type="video/mp4">
                </video>
                <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent"></div>
            </div>

            <!-- Hero Text Section -->
            <div class="text-center max-w-3xl mx-auto mb-12">
                <h1 class="text-4xl lg:text-6xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC] mb-6">
                    Crush your overheads with <span class="text-blue-600 dark:text-blue-400">precision.</span>
                </h1>
                <p class="text-lg text-[#1b1b18]/70 dark:text-[#EDEDEC]/60 leading-relaxed mb-8">
                    The complete ERP for stone crusher owners to manage daily production, automate GST billing, and
                    track
                    vehicle loads in real-time.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('admin.dashboard') }}"
                                class="inline-block px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('admin.login') }}"
                                class="inline-block px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                Get Started
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </section>

        <!-- Feature/Action Grid with Images -->
        <section class="w-full max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <!-- Card 1: Daily Production & Loads -->
                <div
                    class="group overflow-hidden rounded-xl border border-[#19140015] dark:border-[#3E3E3A] hover:shadow-lg hover:shadow-blue-500/5 transition-all bg-white dark:bg-zinc-900">
                    <div
                        class="relative h-48 overflow-hidden bg-linear-to-br from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-800/20">
                        <img src="/images/ssc3.jpg" alt="Load Tracking"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-8">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Load Tracking</h3>
                        <p class="text-sm text-[#1b1b18]/60 dark:text-[#EDEDEC]/50 mb-4">Record weighbridge data, trip
                            details,
                            and material types (20mm, 40mm, M-Sand) seamlessly.</p>
                        <span class="text-sm font-medium text-blue-600 group-hover:underline">Manage Daily Loads
                            &rarr;</span>
                    </div>
                </div>

                <!-- Card 2: Billing & GST -->
                <div
                    class="group overflow-hidden rounded-xl border border-[#19140015] dark:border-[#3E3E3A] hover:shadow-lg hover:shadow-emerald-500/5 transition-all bg-white dark:bg-zinc-900">
                    <div
                        class="relative h-48 overflow-hidden bg-linear-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900/30 dark:to-emerald-800/20">
                        <img src="/images/ssc4.jpg" alt="Automated Billing"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-8">
                        <div
                            class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-emerald-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 dark:text-white">Automated Billing</h3>
                        <p class="text-sm text-[#1b1b18]/60 dark:text-[#EDEDEC]/50 mb-4">Generate GST-compliant invoices
                            and
                            track outstanding payments for credit customers.</p>
                        <span class="text-sm font-medium text-emerald-600 group-hover:underline">Generate Invoices
                            &rarr;</span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer
        class="w-full text-sm text-[#1b1b18]/70 dark:text-[#EDEDEC]/60 border-t border-[#19140015] dark:border-[#3E3E3A] mt-16 py-8">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">
            <p class="text-center">
                © {{ now()->format('Y') }} <a href="https://techsathya.in"
                    class="text-blue-600 dark:text-blue-400 hover:underline"><img
                        src="/images/logos/Tech_SathyA_Logo.svg" alt="TechSathya" class="h-10 inline">TechSathyA</a>.
                All rights reserved.
                Powered
                by <a href="https://shreshtasmg.in" class="text-blue-600 dark:text-blue-400 hover:underline">Shreshta
                    SMG</a>.
            </p>
        </div>
    </footer>
</body>
<script>
    const video = document.getElementById('loopingVideo');
    let playCount = 0;
    const maxPlays = 1;

    video.addEventListener('timeupdate', function() {
        // 1 second before the video ends, start easing out
        const fadePoint = video.duration - 1;

        if (video.currentTime >= fadePoint && !video.classList.contains('opacity-0')) {
            video.classList.remove('opacity-100');
            video.classList.add('opacity-0');
        }
    });

    video.addEventListener('ended', function() {
        playCount++;

        if (playCount >= maxPlays) {
            video.pause();
            video.currentTime = 0; // Reset to the first frame

            // Bring back opacity so the first frame is visible
            video.classList.remove('opacity-0');
            video.classList.add('opacity-100');
            return;
        }

        video.currentTime = 0;
        video.play();

        // Ease the video back in for the next loop
        video.classList.remove('opacity-0');
        video.classList.add('opacity-100');
    });
</script>

</html>

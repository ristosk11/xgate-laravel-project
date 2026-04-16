<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans text-zinc-900 antialiased selection:bg-indigo-500 selection:text-white">
        <div class="min-h-screen bg-zinc-950 text-white">
            <div class="relative min-h-screen">
                <!-- Ambient background -->
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
                    <div class="absolute -top-24 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/25 blur-3xl"></div>
                    <div class="absolute -bottom-24 -left-24 h-[520px] w-[520px] rounded-full bg-fuchsia-500/20 blur-3xl"></div>
                    <div class="absolute top-1/3 -right-24 h-[420px] w-[420px] rounded-full bg-cyan-500/15 blur-3xl"></div>
                </div>

                <div class="relative mx-auto flex min-h-screen max-w-6xl items-stretch">
                    <!-- Left brand/marketing panel -->
                    <aside class="hidden w-[44%] flex-col justify-between px-10 py-12 lg:flex">
                        <div class="flex items-center gap-3">
                            <a href="/" wire:navigate class="group inline-flex items-center gap-3 rounded-2xl px-3 py-2 transition-all duration-200 hover:bg-white/5">
                                <span class="text-lg font-semibold tracking-wide text-white/90">{{ config('app.name', 'Mini Social') }}</span>
                            </a>
                        </div>

                        <div class="max-w-sm">
                            <h1 class="text-4xl font-extrabold tracking-tight">Mini Social</h1>
                        </div>

                        <div class="text-xs text-white/50">© {{ date('Y') }} {{ config('app.name', 'Mini Social') }}. All rights reserved.</div>
                    </aside>

                    <!-- Right auth card -->
                    <main class="flex flex-1 items-center justify-center px-4 py-10 sm:px-8 lg:px-12">
                        <div class="w-full max-w-md">
                            <div class="mb-6 lg:hidden">
                                <a href="/" wire:navigate class="group inline-flex items-center gap-3 rounded-2xl px-3 py-2 transition-all duration-200 hover:bg-white/5">
                                    <span class="text-lg font-semibold tracking-wide text-white/90">{{ config('app.name', 'Mini Social') }}</span>
                                </a>
                            </div>

                            <div class="rounded-3xl bg-white/10 p-6 shadow-2xl shadow-black/30 ring-1 ring-white/15 backdrop-blur-xl sm:p-8">
                                {{ $slot }}
                            </div>

                            <div class="mt-6 text-center text-xs text-white/50">
                                By continuing, you agree to our <a href="#" class="text-white/70 underline decoration-white/30 underline-offset-4 transition hover:text-white">Terms</a>
                                and <a href="#" class="text-white/70 underline decoration-white/30 underline-offset-4 transition hover:text-white">Privacy Policy</a>.
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
        @livewireScripts
    </body>
</html>

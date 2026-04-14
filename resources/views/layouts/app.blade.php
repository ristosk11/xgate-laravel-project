<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-zinc-900 bg-zinc-50 h-full selection:bg-indigo-500 selection:text-white">
        <div class="min-h-screen">
            <!-- Subtle page texture -->
            <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10">
                <div class="absolute inset-0 bg-[radial-gradient(60rem_30rem_at_50%_-10%,rgba(99,102,241,0.12),transparent_60%),radial-gradient(40rem_20rem_at_10%_20%,rgba(236,72,153,0.08),transparent_55%)]"></div>
            </div>

            <div class="mx-auto flex w-full max-w-6xl items-stretch gap-0">
                <!-- Left Sidebar Navigation -->
                <header class="hidden sm:block w-[280px] shrink-0">
                    <div class="sticky top-0 h-screen border-r border-zinc-200/70 bg-white/70 backdrop-blur-xl">
                        <div class="h-full px-4 py-6">
                            <livewire:layout.navigation />
                        </div>
                    </div>
                </header>

                <!-- Main Content Area -->
                <main class="min-w-0 flex-1">
                    <!-- Mobile Navigation -->
                    <div class="sm:hidden sticky top-0 z-30 border-b border-zinc-200/70 bg-white/80 backdrop-blur-xl">
                        <livewire:layout.navigation />
                    </div>

                    <div class="mx-auto w-full max-w-2xl border-x border-zinc-200/70 bg-white/80 backdrop-blur-xl min-h-screen pb-24 sm:pb-10">
                        @if (isset($header))
                            <div class="sticky top-0 z-20 border-b border-zinc-200/70 bg-white/75 backdrop-blur-xl px-4 py-3 sm:px-6 sm:py-4">
                                {{ $header }}
                            </div>
                        @endif

                        <div>
                            {{ $slot }}
                        </div>
                    </div>
                </main>

                <!-- Right Sidebar (Trends/Suggestions) -->
                <aside class="hidden lg:block w-[340px] shrink-0">
                    <div class="sticky top-0 h-screen overflow-y-auto px-6 py-6">
                        <div class="space-y-4">
                            <div class="rounded-3xl bg-white/80 p-5 shadow-sm ring-1 ring-zinc-200/60 backdrop-blur-xl">
                                <h2 class="text-sm font-extrabold tracking-tight text-zinc-900">Trending</h2>
                                <div class="mt-4 space-y-2">
                                    <a href="#" class="block rounded-2xl px-3 py-2 transition-all duration-200 hover:bg-zinc-50 hover:ring-1 hover:ring-zinc-200/60">
                                        <p class="text-[11px] font-medium text-zinc-500">Programming · Trending</p>
                                        <p class="mt-0.5 text-sm font-bold text-zinc-900">#Laravel12</p>
                                        <p class="mt-0.5 text-[11px] text-zinc-500">2,431 posts</p>
                                    </a>
                                    <a href="#" class="block rounded-2xl px-3 py-2 transition-all duration-200 hover:bg-zinc-50 hover:ring-1 hover:ring-zinc-200/60">
                                        <p class="text-[11px] font-medium text-zinc-500">Design · Trending</p>
                                        <p class="mt-0.5 text-sm font-bold text-zinc-900">Tailwind CSS</p>
                                        <p class="mt-0.5 text-[11px] text-zinc-500">10.2K posts</p>
                                    </a>
                                    <a href="#" class="block rounded-2xl px-3 py-2 transition-all duration-200 hover:bg-zinc-50 hover:ring-1 hover:ring-zinc-200/60">
                                        <p class="text-[11px] font-medium text-zinc-500">Technology</p>
                                        <p class="mt-0.5 text-sm font-bold text-zinc-900">Livewire Volt</p>
                                        <p class="mt-0.5 text-[11px] text-zinc-500">1,123 posts</p>
                                    </a>
                                </div>
                            </div>

                            <div class="rounded-3xl bg-white/80 p-5 shadow-sm ring-1 ring-zinc-200/60 backdrop-blur-xl">
                                <h2 class="text-sm font-extrabold tracking-tight text-zinc-900">Who to follow</h2>
                                <div class="mt-4 space-y-3">
                                    @foreach ([['name' => 'Design Systems', 'handle' => '@design'], ['name' => 'Laravel Tips', 'handle' => '@laravel'], ['name' => 'Frontend Daily', 'handle' => '@frontend']] as $suggestion)
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-200 to-fuchsia-200 ring-1 ring-zinc-200/60"></div>
                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-bold text-zinc-900">{{ $suggestion['name'] }}</div>
                                                    <div class="truncate text-xs text-zinc-500">{{ $suggestion['handle'] }}</div>
                                                </div>
                                            </div>
                                            <button type="button" class="inline-flex items-center justify-center rounded-full bg-zinc-900 px-3 py-1.5 text-xs font-semibold text-white transition-all duration-200 hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 active:bg-zinc-950">Follow</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="px-2 text-xs text-zinc-500">
                                <div class="flex flex-wrap gap-x-3 gap-y-1">
                                    <a href="#" class="transition hover:text-zinc-700 hover:underline">Terms</a>
                                    <a href="#" class="transition hover:text-zinc-700 hover:underline">Privacy</a>
                                    <a href="#" class="transition hover:text-zinc-700 hover:underline">Cookies</a>
                                    <span>© {{ date('Y') }} {{ config('app.name', 'Mini Social') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
        @livewireScripts
    </body>
</html>

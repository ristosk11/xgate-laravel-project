<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-zinc-50 dark:bg-zinc-950">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="/favicon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-zinc-900 dark:text-zinc-100 bg-zinc-50 dark:bg-zinc-950 h-full selection:bg-indigo-500 selection:text-white" x-data>
        <div class="min-h-screen">
            <!-- Subtle page texture -->
            <div aria-hidden="true" class="pointer-events-none fixed inset-0 -z-10">
                <div class="absolute inset-0 bg-[radial-gradient(60rem_30rem_at_50%_-10%,rgba(99,102,241,0.12),transparent_60%),radial-gradient(40rem_20rem_at_10%_20%,rgba(236,72,153,0.08),transparent_55%)] dark:bg-[radial-gradient(60rem_30rem_at_50%_-10%,rgba(99,102,241,0.15),transparent_60%),radial-gradient(40rem_20rem_at_10%_20%,rgba(236,72,153,0.1),transparent_55%)]"></div>
            </div>

            <div class="mx-auto flex w-full max-w-6xl items-stretch gap-0">
                <!-- Left Sidebar Navigation -->
                <header class="hidden sm:block w-[280px] shrink-0">
                    <div class="sticky top-0 h-screen border-r border-zinc-200/70 dark:border-zinc-800/70 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-xl">
                        <div class="h-full px-4 py-6">
                            <livewire:layout.navigation />
                        </div>
                    </div>
                </header>

                <!-- Main Content Area -->
                <main class="min-w-0 flex-1">
                    <!-- Mobile Navigation -->
                    <div class="sm:hidden sticky top-0 z-30 border-b border-zinc-200/70 dark:border-zinc-800/70 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl">
                        <livewire:layout.navigation />
                    </div>

                    <div class="mx-auto w-full max-w-2xl border-x border-zinc-200/70 dark:border-zinc-800/70 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl min-h-screen pb-24 sm:pb-10">
                        @if (isset($header))
                            <div class="sticky top-0 z-20 border-b border-zinc-200/70 dark:border-zinc-800/70 bg-white/75 dark:bg-zinc-900/75 backdrop-blur-xl px-4 py-3 sm:px-6 sm:py-4">
                                {{ $header }}
                            </div>
                        @endif

                        <div>
                            {{ $slot }}
                        </div>
                    </div>
                </main>

                <!-- Right Sidebar (Suggestions) -->
                <aside class="hidden lg:block w-[340px] shrink-0">
                    <div class="sticky top-0 h-screen overflow-y-auto px-6 py-6">
                        <div class="space-y-4">
                            <livewire:components.profile-search />

                            <div class="rounded-3xl bg-white/80 dark:bg-zinc-900/80 p-5 shadow-sm ring-1 ring-zinc-200/60 dark:ring-zinc-800/60 backdrop-blur-xl">
                                <h2 class="text-sm font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">Who to follow</h2>
                                <div class="mt-4 space-y-3">
                                    @php
                                        $orderedNames = ['Design', 'Frontend', 'Laravel'];
                                        $suggestedUsers = \App\Models\User::query()
                                            ->whereIn('name', $orderedNames)
                                            ->with('profile')
                                            ->get()
                                            ->sortBy(fn ($u) => array_search($u->name, $orderedNames))
                                            ->values();
                                    @endphp

                                    @foreach ($suggestedUsers as $user)
                                        @php($handle = '@' . strtolower(str_replace(' ', '', $user->name)))
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex min-w-0 items-center gap-3">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-200 to-fuchsia-200 dark:from-indigo-800 dark:to-fuchsia-800 ring-1 ring-zinc-200/60 dark:ring-zinc-700/60 overflow-hidden">
                                                    @if($user->profile && $user->profile->avatar_url)
                                                        <img src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="truncate text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                                                    <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $handle }}</div>
                                                </div>
                                            </div>
                                            <form method="POST" action="{{ route('profile.follow', ['user' => $user->id]) }}" data-follow-form>
                                                @csrf
                                                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-zinc-900 dark:bg-white px-3 py-1.5 text-xs font-semibold text-white dark:text-zinc-900 transition-all duration-200 hover:bg-zinc-800 dark:hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 active:bg-zinc-950 dark:active:bg-zinc-200">Follow</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="px-2 text-xs text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-wrap gap-x-3 gap-y-1">
                                    <a href="#" class="transition hover:text-zinc-700 dark:hover:text-zinc-300 hover:underline">Terms</a>
                                    <a href="#" class="transition hover:text-zinc-700 dark:hover:text-zinc-300 hover:underline">Privacy</a>
                                    <a href="#" class="transition hover:text-zinc-700 dark:hover:text-zinc-300 hover:underline">Cookies</a>
                                    <span>© {{ date('Y') }} {{ config('app.name', 'Mini Social') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Global toast -->
        <div
            x-data="{ show: false, message: '', type: 'success', timer: null }"
            x-on:toast.window="
                message = $event.detail?.message ?? '';
                type = $event.detail?.type ?? 'success';
                show = true;
                clearTimeout(timer);
                timer = setTimeout(() => show = false, 2600);
            "
            x-show="show"
            x-transition.opacity.duration.150ms
            style="display: none;"
            class="fixed bottom-4 left-1/2 z-[100] w-[calc(100%-2rem)] max-w-sm -translate-x-1/2 rounded-2xl px-4 py-3 text-sm font-semibold shadow-lg ring-1"
            :class="type === 'error'
                ? 'bg-red-600 text-white ring-red-600/20'
                : 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 ring-black/10 dark:ring-white/10'"
            role="status"
            aria-live="polite"
        >
            <span x-text="message"></span>
        </div>

        <script>
            // Dark mode initialization - runs immediately
            (function() {
                const isDark = localStorage.getItem('darkMode') === 'true' || 
                    (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        @livewireScripts

        <script>
            document.addEventListener('click', async (e) => {
                const button = e.target.closest('[data-share-url]');
                if (!button) return;

                e.preventDefault();
                e.stopPropagation();

                const url = button.getAttribute('data-share-url');
                if (!url) return;

                try {
                    if (navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(url);
                    } else {
                        const input = document.createElement('input');
                        input.value = url;
                        input.setAttribute('readonly', 'readonly');
                        input.style.position = 'absolute';
                        input.style.left = '-9999px';
                        document.body.appendChild(input);
                        input.select();
                        document.execCommand('copy');
                        document.body.removeChild(input);
                    }

                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Link copied', type: 'success' } }));
                } catch (err) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Could not copy link', type: 'error' } }));
                }
            }, true);

            document.addEventListener('submit', async (e) => {
                const form = e.target.closest('[data-follow-form]');
                if (!form) return;

                e.preventDefault();

                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = '...';

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message || 'Could not follow', type: 'error' } }));
                        btn.textContent = originalText;
                        btn.disabled = false;
                        return;
                    }

                    btn.textContent = data.following ? 'Following' : 'Follow';
                    btn.disabled = false;

                    if (data.following) {
                        btn.classList.remove('bg-zinc-900', 'dark:bg-white', 'hover:bg-zinc-800', 'dark:hover:bg-zinc-100', 'active:bg-zinc-950', 'dark:active:bg-zinc-200');
                        btn.classList.add('bg-zinc-200', 'dark:bg-zinc-700', 'text-zinc-700', 'dark:text-zinc-300', 'hover:bg-zinc-300', 'dark:hover:bg-zinc-600');
                    } else {
                        btn.classList.add('bg-zinc-900', 'dark:bg-white', 'hover:bg-zinc-800', 'dark:hover:bg-zinc-100', 'active:bg-zinc-950', 'dark:active:bg-zinc-200');
                        btn.classList.remove('bg-zinc-200', 'dark:bg-zinc-700', 'text-zinc-700', 'dark:text-zinc-300', 'hover:bg-zinc-300', 'dark:hover:bg-zinc-600');
                    }

                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.following ? 'Followed!' : 'Unfollowed', type: 'success' } }));
                } catch (err) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Could not follow', type: 'error' } }));
                    btn.textContent = originalText;
                    btn.disabled = false;
                }
            }, true);
        </script>
    </body>
</html>

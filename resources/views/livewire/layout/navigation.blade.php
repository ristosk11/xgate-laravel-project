<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav x-data="{ open: false, searchOpen: false }" class="relative h-full flex flex-col justify-between">
    <div class="hidden sm:flex flex-col h-full">
        <div class="flex flex-col gap-7">
            <div class="px-2">
                <a href="{{ route('feed.index') }}" wire:navigate class="group inline-flex items-center gap-3 rounded-2xl px-3 py-2 transition-all duration-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/70">
                    <span class="text-lg font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">{{ config('app.name', 'Mini Social') }}</span>
                </a>
            </div>

            <div class="flex flex-col gap-1">
                <a
                    href="{{ route('feed.index') }}"
                    wire:navigate
                    class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 transition-all duration-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/70 hover:ring-1 hover:ring-zinc-200/70 dark:hover:ring-zinc-700/70 {{ request()->routeIs('feed.*') ? 'bg-zinc-100/80 dark:bg-zinc-800/80 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70' : '' }}"
                >
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl transition-all duration-200 {{ request()->routeIs('feed.*') ? 'bg-white dark:bg-zinc-700 shadow-sm ring-1 ring-zinc-200/70 dark:ring-zinc-600/70' : 'bg-transparent group-hover:bg-white dark:group-hover:bg-zinc-700 group-hover:shadow-sm group-hover:ring-1 group-hover:ring-zinc-200/70 dark:group-hover:ring-zinc-600/70' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('feed.*') ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-zinc-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </span>
                    <span class="text-[15px] font-semibold tracking-tight {{ request()->routeIs('feed.*') ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-zinc-100' }}">Feed</span>
                </a>

                <a
                    href="{{ route('profile.show', ['id' => auth()->id()]) }}"
                    wire:navigate
                    class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 transition-all duration-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/70 hover:ring-1 hover:ring-zinc-200/70 dark:hover:ring-zinc-700/70 {{ request()->routeIs('profile.*') ? 'bg-zinc-100/80 dark:bg-zinc-800/80 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70' : '' }}"
                >
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-white dark:bg-zinc-700 shadow-sm ring-1 ring-zinc-200/70 dark:ring-zinc-600/70' : 'bg-transparent group-hover:bg-white dark:group-hover:bg-zinc-700 group-hover:shadow-sm group-hover:ring-1 group-hover:ring-zinc-200/70 dark:group-hover:ring-zinc-600/70' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('profile.*') ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-zinc-100' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    <span class="text-[15px] font-semibold tracking-tight {{ request()->routeIs('profile.*') ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-zinc-100' }}">Profile</span>
                </a>
            </div>

            <div class="mt-2 px-2">
                <a href="{{ route('posts.create') }}" wire:navigate class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-zinc-900 dark:bg-white px-4 py-3 text-sm font-semibold text-white dark:text-zinc-900 shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-zinc-800 dark:hover:bg-zinc-100 hover:shadow-md active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                    <svg class="h-5 w-5 text-white/90 dark:text-zinc-900/90 transition group-hover:text-white dark:group-hover:text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Post
                </a>
            </div>

            <div class="mt-2 px-2">
                <button
                    type="button"
                    @click="$store.darkMode.toggle()"
                    class="group flex w-full items-center gap-3 rounded-2xl px-3 py-2.5 transition-all duration-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/70 hover:ring-1 hover:ring-zinc-200/70 dark:hover:ring-zinc-700/70"
                >
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl transition-all duration-200 bg-transparent group-hover:bg-white dark:group-hover:bg-zinc-800 group-hover:shadow-sm group-hover:ring-1 group-hover:ring-zinc-200/70 dark:group-hover:ring-zinc-700/70">
                        <svg x-show="!$store.darkMode.on" class="h-5 w-5 text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <svg x-show="$store.darkMode.on" x-cloak class="h-5 w-5 text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </span>
                    <span class="text-[15px] font-semibold tracking-tight text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white" x-text="$store.darkMode.on ? 'Light Mode' : 'Dark Mode'"></span>
                </button>
            </div>
        </div>

        <div class="mt-auto mb-4">
            <x-dropdown align="top" width="48">
                <x-slot name="trigger">
                    <button class="group flex w-full items-center gap-3 rounded-2xl p-3 text-left transition-all duration-200 hover:bg-zinc-100/70 dark:hover:bg-zinc-800/70 hover:ring-1 hover:ring-zinc-200/70 dark:hover:ring-zinc-700/70">
                        <div class="h-10 w-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                            @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                                <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-lg">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate" x-data="{ name: @js(auth()->user()->name) }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ auth()->user()->username ?? '@'.strtolower(str_replace(' ', '', auth()->user()->name)) }}</p>
                        </div>
                        <svg class="w-5 h-5 text-zinc-500 dark:text-zinc-400 transition group-hover:text-zinc-700 dark:group-hover:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.show', ['id' => auth()->id()])" wire:navigate>
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <button wire:click="logout" class="w-full text-start">
                        <x-dropdown-link>
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </button>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    <div class="sm:hidden flex justify-between items-center h-14 px-4 w-full">
        <button @click="open = ! open" class="w-9 h-9 rounded-full bg-zinc-200 dark:bg-zinc-700 overflow-hidden ring-1 ring-black/5 dark:ring-white/10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            @endif
        </button>

        <a href="{{ route('feed.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-2xl px-2 py-1 transition-all duration-200 hover:bg-white/60 dark:hover:bg-zinc-800/60 hover:ring-1 hover:ring-zinc-200/70 dark:hover:ring-zinc-700/70">
            <span class="text-base font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">{{ config('app.name', 'Mini Social') }}</span>
        </a>

        <button @click="searchOpen = !searchOpen" class="w-9 h-9 flex items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile search dropdown -->
    <div x-show="searchOpen" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.away="searchOpen = false"
         class="sm:hidden absolute top-full left-0 right-0 bg-white dark:bg-zinc-900 border-b border-zinc-200/70 dark:border-zinc-800/70 p-3 shadow-lg z-40"
         style="display: none;">
        <livewire:components.profile-search />
    </div>

    <!-- Mobile sidebar overlay -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-[100] bg-black/50 sm:hidden" @click="open = false" style="display: none;"></div>

    <!-- Mobile sidebar drawer -->
    <div x-show="open"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="-translate-x-full"
          x-transition:enter-end="translate-x-0"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="translate-x-0"
          x-transition:leave-end="-translate-x-full"
          class="fixed inset-y-0 left-0 z-[101] w-80 bg-white dark:bg-zinc-900 shadow-2xl flex flex-col sm:hidden ring-1 ring-zinc-200/70 dark:ring-zinc-700/70"
          style="display: none;">

        <div class="p-4 border-b border-zinc-200/70 dark:border-zinc-700/70 flex justify-between items-center">
            <h2 class="font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">Account</h2>
            <button @click="open = false" class="p-2 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-2xl transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-4">
            <div class="w-12 h-12 rounded-full bg-zinc-200 dark:bg-zinc-700 overflow-hidden mb-3 ring-1 ring-black/5 dark:ring-white/10">
                @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                    <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-xl">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="font-extrabold text-lg tracking-tight text-zinc-900 dark:text-zinc-100" x-data="{ name: @js(auth()->user()->name) }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
            <div class="text-sm text-zinc-500 dark:text-zinc-400 mb-6">{{ auth()->user()->username ?? '@'.strtolower(str_replace(' ', '', auth()->user()->name)) }}</div>

            <div class="flex flex-col gap-2 mb-8">
                <a href="{{ route('feed.index') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-2 text-zinc-900 dark:text-zinc-100 font-semibold transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white dark:bg-zinc-700 ring-1 ring-zinc-200/70 dark:ring-zinc-600/70 shadow-sm">
                        <svg class="h-5 w-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </span>
                    Feed
                </a>

                <a href="{{ route('profile.show', ['id' => auth()->id()]) }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-2 text-zinc-900 dark:text-zinc-100 font-semibold transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white dark:bg-zinc-700 ring-1 ring-zinc-200/70 dark:ring-zinc-600/70 shadow-sm">
                        <svg class="h-5 w-5 text-zinc-700 dark:text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    Profile
                </a>
            </div>

            <div class="border-t border-zinc-200/70 dark:border-zinc-700/70 pt-4">
                <button wire:click="logout" class="text-left w-full font-semibold text-zinc-900 dark:text-zinc-100 py-2 rounded-2xl px-3 transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    Log out
                </button>
            </div>
        </div>
    </div>
</nav>

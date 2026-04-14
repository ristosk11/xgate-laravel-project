<?php

use App\Livewire\Actions\Logout;

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<nav x-data="{ open: false }" class="h-full flex flex-col justify-between">
    <!-- Desktop Sidebar (Hidden on mobile) -->
    <div class="hidden sm:flex flex-col h-full">
        <div class="flex flex-col gap-7">
            <!-- Logo -->
            <div class="px-2">
                <a href="{{ route('feed.index') }}" wire:navigate class="group inline-flex items-center gap-3 rounded-2xl px-3 py-2 transition-all duration-200 hover:bg-zinc-100/70">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 shadow-sm ring-1 ring-black/5 transition-all duration-200 group-hover:shadow-md">
                        <x-application-logo class="block h-6 w-6 fill-current text-white" />
                    </span>
                    <span class="text-sm font-extrabold tracking-tight text-zinc-900">{{ config('app.name', 'Mini Social') }}</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex flex-col gap-1">
                <a
                    href="{{ route('feed.index') }}"
                    wire:navigate
                    class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 transition-all duration-200 hover:bg-zinc-100/70 hover:ring-1 hover:ring-zinc-200/70 {{ request()->routeIs('feed.*') ? 'bg-zinc-100/80 ring-1 ring-zinc-200/70' : '' }}"
                >
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl transition-all duration-200 {{ request()->routeIs('feed.*') ? 'bg-white shadow-sm ring-1 ring-zinc-200/70' : 'bg-transparent group-hover:bg-white group-hover:shadow-sm group-hover:ring-1 group-hover:ring-zinc-200/70' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('feed.*') ? 'text-zinc-900' : 'text-zinc-600 group-hover:text-zinc-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </span>
                    <span class="text-[15px] font-semibold tracking-tight {{ request()->routeIs('feed.*') ? 'text-zinc-900' : 'text-zinc-700 group-hover:text-zinc-900' }}">Feed</span>
                </a>
                
                <a
                    href="{{ route('profile.show', ['id' => auth()->id()]) }}"
                    wire:navigate
                    class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 transition-all duration-200 hover:bg-zinc-100/70 hover:ring-1 hover:ring-zinc-200/70 {{ request()->routeIs('profile.*') ? 'bg-zinc-100/80 ring-1 ring-zinc-200/70' : '' }}"
                >
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-white shadow-sm ring-1 ring-zinc-200/70' : 'bg-transparent group-hover:bg-white group-hover:shadow-sm group-hover:ring-1 group-hover:ring-zinc-200/70' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('profile.*') ? 'text-zinc-900' : 'text-zinc-600 group-hover:text-zinc-900' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    <span class="text-[15px] font-semibold tracking-tight {{ request()->routeIs('profile.*') ? 'text-zinc-900' : 'text-zinc-700 group-hover:text-zinc-900' }}">Profile</span>
                </a>
            </div>

            <!-- Post Button -->
            <div class="mt-2 px-2">
                <a href="{{ route('posts.create') }}" wire:navigate class="group flex w-full items-center justify-center gap-2 rounded-2xl bg-zinc-900 px-4 py-3 text-sm font-semibold text-white shadow-sm ring-1 ring-black/5 transition-all duration-200 hover:-translate-y-0.5 hover:bg-zinc-800 hover:shadow-md active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                    <svg class="h-5 w-5 text-white/90 transition group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Post
                </a>
            </div>
        </div>

        <!-- User Menu (Bottom) -->
        <div class="mt-auto mb-4">
            <x-dropdown align="top" width="48">
                <x-slot name="trigger">
                    <button class="group flex w-full items-center gap-3 rounded-2xl p-3 text-left transition-all duration-200 hover:bg-zinc-100/70 hover:ring-1 hover:ring-zinc-200/70">
                        <div class="h-10 w-10 rounded-full bg-zinc-200 flex-shrink-0 overflow-hidden ring-1 ring-black/5">
                            @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                                <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-700 font-bold text-lg">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-zinc-900 truncate" x-data="{ name: @js(auth()->user()->name) }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></p>
                            <p class="text-xs text-zinc-500 truncate">{{ auth()->user()->username ?? '@'.strtolower(str_replace(' ', '', auth()->user()->name)) }}</p>
                        </div>
                        <svg class="w-5 h-5 text-zinc-500 transition group-hover:text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
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

    <!-- Mobile Top Navigation (Visible only on mobile) -->
    <div class="sm:hidden flex justify-between items-center h-14 px-4 w-full">
        <!-- User Avatar (Triggers sidebar/menu) -->
        <button @click="open = ! open" class="w-9 h-9 rounded-full bg-zinc-200 overflow-hidden ring-1 ring-black/5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-700 font-bold text-sm">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            @endif
        </button>

        <!-- Logo -->
        <a href="{{ route('feed.index') }}" wire:navigate class="inline-flex items-center gap-2 rounded-2xl px-2 py-1 transition-all duration-200 hover:bg-white/60 hover:ring-1 hover:ring-zinc-200/70">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 shadow-sm ring-1 ring-black/5">
                <x-application-logo class="block h-5 w-5 fill-current text-white" />
            </span>
        </a>

        <!-- Empty div for flex balance -->
        <div class="w-8"></div>
    </div>

    <!-- Mobile Full Screen Menu Overlay -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-40 bg-black/50 sm:hidden" @click="open = false" style="display: none;"></div>
    
    <!-- Mobile Slide-out Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 w-80 bg-white/90 shadow-2xl backdrop-blur-xl flex flex-col sm:hidden ring-1 ring-zinc-200/70" 
         style="display: none;">
        
        <div class="p-4 border-b border-zinc-200/70 flex justify-between items-center">
            <h2 class="font-extrabold tracking-tight text-zinc-900">Account</h2>
            <button @click="open = false" class="p-2 text-zinc-600 hover:bg-zinc-100 rounded-2xl transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-4">
            <div class="w-12 h-12 rounded-full bg-zinc-200 overflow-hidden mb-3 ring-1 ring-black/5">
                @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                    <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-700 font-bold text-xl">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="font-extrabold text-lg tracking-tight text-zinc-900" x-data="{ name: @js(auth()->user()->name) }" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
            <div class="text-sm text-zinc-500 mb-6">{{ auth()->user()->username ?? '@'.strtolower(str_replace(' ', '', auth()->user()->name)) }}</div>

            <div class="flex flex-col gap-2 mb-8">
                <a href="{{ route('feed.index') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-2 text-zinc-900 font-semibold transition-all duration-200 hover:bg-zinc-100">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white ring-1 ring-zinc-200/70 shadow-sm">
                        <svg class="h-5 w-5 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </span>
                    Feed
                </a>

                <a href="{{ route('profile.show', ['id' => auth()->id()]) }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-3 py-2 text-zinc-900 font-semibold transition-all duration-200 hover:bg-zinc-100">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-white ring-1 ring-zinc-200/70 shadow-sm">
                        <svg class="h-5 w-5 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                    Profile
                </a>
            </div>

            <div class="border-t border-zinc-200/70 pt-4">
                <button wire:click="logout" class="text-left w-full font-semibold text-zinc-900 py-2 rounded-2xl px-3 transition-all duration-200 hover:bg-zinc-100">
                    Log out
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation (Floating action button for create post) -->
    <div class="sm:hidden fixed bottom-6 right-4 z-30">
        <a href="{{ route('posts.create') }}" wire:navigate class="flex items-center justify-center w-14 h-14 bg-zinc-900 text-white rounded-full shadow-lg ring-1 ring-black/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-zinc-800 hover:shadow-xl active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        </a>
    </div>
</nav>

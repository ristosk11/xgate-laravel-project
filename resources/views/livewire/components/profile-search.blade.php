<?php

use App\Models\User;
use Illuminate\Support\Collection;

use function Livewire\Volt\state;
use function Livewire\Volt\computed;

state([
    'query' => '',
]);

$results = computed(function (): Collection {
    $query = $this->query;

    if (strlen($query) < 2) {
        return collect();
    }

    $currentUserId = auth()->id();

    return User::query()
        ->where('id', '!=', $currentUserId)
        ->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })
        ->limit(5)
        ->get();
});

?>

<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <div class="relative">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-4 w-4 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <input
            type="search"
            wire:model.live.debounce.300ms="query"
            @focus="open = true"
            @input="open = true"
            placeholder="Search profiles..."
            class="w-full rounded-2xl border-0 bg-zinc-100 dark:bg-zinc-800 py-2.5 pl-10 pr-4 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-500 dark:placeholder:text-zinc-400 ring-1 ring-zinc-200/60 dark:ring-zinc-700/60 transition-all duration-200 focus:bg-white dark:focus:bg-zinc-900 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
        >
    </div>

    @if($this->results->isNotEmpty())
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute left-0 right-0 z-50 mt-2 rounded-2xl bg-white dark:bg-zinc-800 p-2 shadow-lg ring-1 ring-zinc-200/60 dark:ring-zinc-700/60"
        >
            @foreach($this->results as $user)
                @php($handle = '@' . strtolower(str_replace(' ', '', $user->name)))
                <a
                    href="{{ route('profile.show', ['id' => $user->id]) }}"
                    wire:navigate
                    @click="open = false"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 transition-all duration-150 hover:bg-zinc-50 dark:hover:bg-zinc-700"
                >
                    <div class="h-9 w-9 flex-shrink-0 rounded-full bg-gradient-to-br from-indigo-200 to-fuchsia-200 dark:from-indigo-800 dark:to-fuchsia-800 ring-1 ring-zinc-200/60 dark:ring-zinc-700/60 overflow-hidden">
                        @if($user->profile && $user->profile->avatar_url)
                            <img src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center text-sm font-bold text-indigo-700 dark:text-indigo-300">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                        <div class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $handle }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @elseif(strlen($this->query) >= 2)
        <div
            x-show="open"
            x-transition
            class="absolute left-0 right-0 z-50 mt-2 rounded-2xl bg-white dark:bg-zinc-800 p-4 shadow-lg ring-1 ring-zinc-200/60 dark:ring-zinc-700/60 text-center"
        >
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No profiles found</p>
        </div>
    @endif
</div>

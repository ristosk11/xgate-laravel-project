<?php

use App\Domain\Content\Services\FeedService;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\computed;
use function Livewire\Volt\layout;
use function Livewire\Volt\state;

layout('layouts.app');

state([
    'perPage' => 15,
]);

$feed = computed(function () {
    $service = app(FeedService::class);

    return $service->getFeed(Auth::user(), $this->perPage);
});

?>

<div class="w-full">
    <!-- Header for Mobile, Hidden on Desktop as Layout handles it -->
    <div class="sm:hidden px-4 py-3 border-b border-zinc-200/70 dark:border-zinc-800/70 bg-white/75 dark:bg-zinc-900/75 backdrop-blur-xl sticky top-14 z-10">
        <h1 class="text-[17px] font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">Feed</h1>
    </div>

    <!-- Create Post Prompt -->
    <div class="hidden sm:block border-b border-zinc-200/60 dark:border-zinc-800/60 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl">
        <div class="p-4 sm:p-6">
            <div class="flex gap-4">
                <div class="w-12 h-12 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-sm">
                @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                    <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-xl">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
                <div class="flex-1">
                    <a href="{{ route('posts.create') }}" wire:navigate class="block w-full">
                        <div class="rounded-3xl bg-zinc-50 dark:bg-zinc-800 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70 px-4 py-4 transition-all duration-200 hover:bg-white dark:hover:bg-zinc-700 hover:ring-2 hover:ring-indigo-500/20">
                            <div class="text-[15px] text-zinc-500 dark:text-zinc-400">What's happening?</div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-2 text-indigo-600/80 dark:text-indigo-400/80">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-white dark:bg-zinc-800 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </span>
                                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400">Add media</span>
                                </div>
                                <span class="inline-flex items-center justify-center rounded-full bg-zinc-900 dark:bg-white px-4 py-2 text-xs font-semibold text-white dark:text-zinc-900 shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all duration-200 hover:bg-zinc-800 dark:hover:bg-zinc-100">Post</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
        @forelse ($this->feed as $post)
            <article 
                class="transition-all duration-200 cursor-pointer relative hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50"
                @click="if ($event.target.closest('a, button, [x-data]') === null) { window.Livewire.navigate('{{ route('posts.show', $post->id) }}') }"
            >
                @include('livewire.components.post-card', ['post' => $post])
            </article>
        @empty
            <div class="p-10 text-center text-zinc-500 dark:text-zinc-400">
                <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70">
                    <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-lg font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100 mb-1">No posts yet</h3>
                <p>Follow some users or create your first post!</p>
                <a href="{{ route('posts.create') }}" wire:navigate class="inline-flex mt-5 items-center justify-center rounded-full bg-zinc-900 dark:bg-white px-5 py-2.5 text-sm font-semibold text-white dark:text-zinc-900 shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-zinc-800 dark:hover:bg-zinc-100 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    Create Post
                </a>
            </div>
        @endforelse
    </div>

    <div class="p-4 border-t border-zinc-200/60 dark:border-zinc-800/60 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl">
        {{ $this->feed->links() }}
    </div>
</div>

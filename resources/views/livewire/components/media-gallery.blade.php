<?php

use Illuminate\Support\Facades\Storage;

?>

<div>
    @php
        $media = $post->media->filter(function ($item) {
            $path = $item->file_path;

            return Storage::disk('public')->exists($path);
        })->values();

        $mediaCount = $media->count();
        $mediaItems = $media->map(function ($item) {
            $path = $item->file_path;

            return [
                'url' => '/storage/'.$path,
                'type' => $item->type->value,
                'alt' => $item->alt_text,
            ];
        })->values();
    @endphp

    @if ($mediaCount > 0)
        <div
            x-data="mediaGallery({{ Js::from($mediaItems) }})"
            @keydown.escape.window="close()"
            @keydown.arrow-right.window="open && next()"
            @keydown.arrow-left.window="open && prev()"
        >
            <div class="grid gap-0.5 overflow-hidden rounded-2xl {{ $mediaCount === 1 ? 'grid-cols-1' : ($mediaCount === 2 ? 'grid-cols-2' : 'grid-cols-2') }}">
                @foreach ($media as $index => $item)
                    <div
                        @click="show({{ $index }})"
                        class="group relative aspect-[4/3] w-full bg-zinc-200 dark:bg-zinc-800 cursor-pointer {{ $mediaCount >= 3 && $index === 0 ? 'col-span-2' : '' }} overflow-hidden"
                    >
                        @if ($item->type->value === 'video')
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30 z-10 transition-all duration-300 group-hover:bg-black/20">
                                <div class="w-14 h-14 bg-white/90 rounded-full flex items-center justify-center backdrop-blur-sm ring-1 ring-white/40 shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:bg-white">
                                    <svg class="w-7 h-7 text-zinc-900 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                            <video class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105">
                                <source src="/storage/{{ $item->file_path }}">
                            </video>
                        @else
                            <img
                                src="/storage/{{ $item->file_path }}"
                                alt="{{ $item->alt_text }}"
                                class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        @endif

                        <div class="absolute bottom-3 right-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-1 group-hover:translate-y-0 pointer-events-none">
                            <div class="p-2 rounded-full bg-black/50 text-white backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <template x-teleport="body">
                <div
                    x-cloak
                    x-show="open"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black"
                    style="display: none;"
                    @click.self="close()"
                >
                    <div class="absolute inset-0 bg-gradient-to-b from-black via-zinc-950 to-black"></div>

                    <button
                        type="button"
                        @click="close()"
                        class="absolute top-4 right-4 z-50 p-3 rounded-full bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20 hover:ring-white/30 transition-all duration-200 backdrop-blur-md group"
                    >
                        <svg class="w-6 h-6 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <div class="absolute top-4 left-4 z-50 flex items-center gap-3">
                        <span class="text-sm font-medium text-white/80 bg-white/10 px-4 py-2 rounded-full backdrop-blur-md ring-1 ring-white/10" x-text="`${index + 1} / ${items.length}`"></span>

                        <button
                            type="button"
                            @click="toggleZoom()"
                            class="p-2 rounded-full bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200 backdrop-blur-md"
                            :class="zoom > 1 ? 'bg-white/20 ring-white/30' : ''"
                        >
                            <svg x-show="zoom === 1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                            <svg x-show="zoom > 1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path>
                            </svg>
                        </button>
                    </div>

                    <div
                        class="relative w-full h-full flex items-center justify-center p-4 sm:p-8 md:p-16"
                        x-ref="viewer"
                        @touchstart.passive="touchStartX = $event.touches[0].clientX"
                        @touchend.passive="
                            const diff = $event.changedTouches[0].clientX - touchStartX;
                            if (Math.abs(diff) > 50) {
                                diff > 0 ? prev() : next();
                            }
                        "
                    >
                        <template x-if="items[index]?.type === 'video'">
                            <video
                                controls
                                autoplay
                                playsinline
                                class="max-h-full max-w-full rounded-xl shadow-2xl ring-1 ring-white/10 transition-all duration-300"
                                :class="loading ? 'opacity-0 scale-95' : 'opacity-100 scale-100'"
                            >
                                <source :src="items[index]?.url">
                            </video>
                        </template>

                        <template x-if="items[index]?.type !== 'video'">
                            <div class="relative max-h-full max-w-full overflow-hidden" :class="zoom > 1 ? 'cursor-move' : 'cursor-zoom-in'" @click="toggleZoom()">
                                <img
                                    :src="items[index]?.url"
                                    :alt="items[index]?.alt ?? ''"
                                    class="max-h-[85vh] max-w-full object-contain rounded-xl shadow-2xl ring-1 ring-white/10 transition-all duration-500 ease-out"
                                    :class="loading ? 'opacity-0 scale-95' : 'opacity-100 scale-100'"
                                    :style="`transform: scale(${zoom})`"
                                >
                            </div>
                        </template>

                        <template x-if="items.length > 1">
                            <button
                                type="button"
                                @click.stop="prev()"
                                class="absolute left-4 sm:left-8 top-1/2 -translate-y-1/2 p-4 rounded-full bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/25 hover:ring-white/40 hover:scale-110 transition-all duration-200 backdrop-blur-md group"
                            >
                                <svg class="w-6 h-6 transition-transform duration-200 group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                        </template>

                        <template x-if="items.length > 1">
                            <button
                                type="button"
                                @click.stop="next()"
                                class="absolute right-4 sm:right-8 top-1/2 -translate-y-1/2 p-4 rounded-full bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/25 hover:ring-white/40 hover:scale-110 transition-all duration-200 backdrop-blur-md group"
                            >
                                <svg class="w-6 h-6 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </template>
                    </div>

                    <template x-if="items.length > 1">
                        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-50">
                            <div class="flex items-center gap-2 px-4 py-3 rounded-full bg-white/10 backdrop-blur-md ring-1 ring-white/10">
                                <template x-for="(item, i) in items" :key="i">
                                    <button
                                        type="button"
                                        @click="index = i; zoom = 1;"
                                        class="w-2 h-2 rounded-full transition-all duration-300"
                                        :class="i === index ? 'bg-white w-6' : 'bg-white/40 hover:bg-white/60'"
                                    ></button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="absolute bottom-6 right-6 z-50 hidden sm:block">
                        <div class="text-xs text-white/50 bg-white/5 px-3 py-2 rounded-lg backdrop-blur-sm">
                            <span class="inline-flex items-center gap-1">
                                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-white/70">←</kbd>
                                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-white/70">→</kbd>
                                navigate
                            </span>
                            <span class="mx-2 text-white/30">·</span>
                            <span class="inline-flex items-center gap-1">
                                <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-white/70">esc</kbd>
                                close
                            </span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    @endif
</div>

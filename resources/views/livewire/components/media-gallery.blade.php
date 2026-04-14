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
                'url' => Storage::disk('public')->url($path),
                'type' => $item->type->value,
                'alt' => $item->alt_text,
            ];
        })->values();
    @endphp

    @if ($mediaCount > 0)
        <div
            x-data='{
                open: false,
                index: 0,
                items: @js($mediaItems),
                show(i) { this.index = i; this.open = true; },
                next() { this.index = (this.index + 1) % this.items.length; },
                prev() { this.index = (this.index - 1 + this.items.length) % this.items.length; }
            }'
        >
            <div class="grid gap-0 overflow-hidden rounded-2xl bg-zinc-300 {{ $mediaCount === 1 ? 'grid-cols-1' : ($mediaCount === 2 ? 'grid-cols-2' : 'grid-cols-2') }}">
                @foreach ($media as $index => $item)
                    <div @click="show({{ $index }})" class="group relative aspect-square w-full bg-zinc-300 cursor-pointer {{ $mediaCount >= 3 && $index === 0 ? 'col-span-2' : '' }} overflow-hidden">
                        @if ($item->type->value === 'video')
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 z-10 transition-all duration-200 group-hover:bg-black/10">
                                <div class="w-12 h-12 bg-white/85 rounded-full flex items-center justify-center backdrop-blur-sm ring-1 ring-white/30 shadow-sm transition-all duration-200 group-hover:scale-105">
                                    <svg class="w-6 h-6 text-gray-900 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                            <video class="w-full h-full object-cover transition-all duration-200 group-hover:scale-[1.02]">
                                <source src="{{ Storage::disk('public')->url($item->file_path) }}">
                            </video>
                        @else
                            <img src="{{ Storage::disk('public')->url($item->file_path) }}" alt="{{ $item->alt_text }}" class="w-full h-full object-cover transition-all duration-200 group-hover:scale-[1.02] group-hover:opacity-95">
                        @endif
                    </div>
                @endforeach
            </div>

            <div
                x-cloak
                x-show="open"
                @keydown.escape.window="open = false"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm"
                style="display: none;"
            >
                <div class="w-full max-w-5xl flex flex-col h-full justify-center space-y-4">
                    <div class="absolute top-4 right-4 z-50">
                        <button type="button" @click="open = false" class="p-3 rounded-full bg-white/10 text-white ring-1 ring-white/15 hover:bg-white/20 transition-all duration-200 backdrop-blur-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="relative flex-1 flex items-center justify-center overflow-hidden">
                        <template x-if="items[index]?.type === 'video'">
                            <video controls autoplay class="max-h-full max-w-full rounded-2xl shadow-2xl ring-1 ring-white/10">
                                <source :src="items[index]?.url">
                            </video>
                        </template>

                        <template x-if="items[index]?.type !== 'video'">
                            <img :src="items[index]?.url" :alt="items[index]?.alt ?? ''" class="max-h-full max-w-full rounded-2xl shadow-2xl ring-1 ring-white/10 object-contain">
                        </template>
                        
                        <button type="button" @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/10 text-white ring-1 ring-white/15 hover:bg-white/20 transition-all duration-200 backdrop-blur-md hidden sm:block">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        
                        <button type="button" @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 p-3 rounded-full bg-white/10 text-white ring-1 ring-white/15 hover:bg-white/20 transition-all duration-200 backdrop-blur-md hidden sm:block">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>

                    <div class="text-center">
                        <span class="text-sm font-medium text-white/70 bg-black/50 px-3 py-1 rounded-full backdrop-blur-md" x-text="`${index + 1} / ${items.length}`"></span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

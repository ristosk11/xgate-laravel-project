<?php

use App\Domain\Content\Actions\CreatePostAction;
use App\Domain\Content\DTOs\CreatePostDTO;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\layout;
use function Livewire\Volt\state;
use function Livewire\Volt\uses;

layout('layouts.app');

uses([\Livewire\WithFileUploads::class]);

state([
    'content' => '',
    'media' => [],
]);

$removeMedia = function ($index) {
    $mediaArray = $this->media;
    unset($mediaArray[$index]);
    $this->media = array_values($mediaArray);
};

$store = function () {
    $validated = $this->validate([
        'content' => ['nullable', 'string'],
        'media' => ['array'],
        'media.*' => [
            'file',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! $value instanceof UploadedFile) {
                    $fail('Invalid media file.');

                    return;
                }

                $mimeType = $value->getMimeType() ?? '';
                $size = $value->getSize() ?? 0;

                $imageMimes = ['image/jpeg', 'image/png', 'image/webp'];
                $videoMimes = ['video/mp4', 'video/webm'];

                if (in_array($mimeType, $imageMimes, true)) {
                    if ($size > (5 * 1024 * 1024)) {
                        $fail('Image files must be 5MB or less.');
                    }

                    return;
                }

                if (in_array($mimeType, $videoMimes, true)) {
                    if ($size > (50 * 1024 * 1024)) {
                        $fail('Video files must be 50MB or less.');
                    }

                    return;
                }

                $fail('Only jpg, png, webp, mp4, and webm files are allowed.');
            },
        ],
    ]);

    $dto = new CreatePostDTO(
        content: $validated['content'] ?? null,
        media: $validated['media'] ?? [],
    );

    app(CreatePostAction::class)->execute(Auth::user(), $dto);

    $this->redirect(route('feed.index', absolute: false), navigate: true);
};

?>

<div class="w-full" x-data="{ charCount: 0, maxChars: 500 }" x-init="charCount = $refs.textarea?.value?.length || 0">
    <div class="sticky top-0 z-20 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border-b border-zinc-200/70 dark:border-zinc-800/70">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('feed.index') }}" wire:navigate class="p-2 -ml-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-200 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
                <h1 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">New Post</h1>
            </div>
            <button 
                form="post-form"
                type="submit" 
                class="rounded-full bg-indigo-600 hover:bg-indigo-500 px-5 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-indigo-600 transition-all duration-200 hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-600 disabled:hover:shadow-sm"
                :disabled="charCount === 0 && {{ count($media) }} === 0"
            >
                Post
            </button>
        </div>
    </div>

    <form wire:submit.prevent="store" id="post-form" class="px-4 sm:px-6">
        <div class="py-4 flex gap-4">
            <div class="flex-shrink-0">
                <div class="w-11 h-11 rounded-full overflow-hidden ring-2 ring-white dark:ring-zinc-800 shadow-sm">
                    @if(auth()->user()->profile?->avatar_url)
                        <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-lg">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="relative">
                    <textarea 
                        id="content" 
                        wire:model="content" 
                        x-ref="textarea"
                        @input="charCount = $event.target.value.length"
                        rows="4" 
                        maxlength="500"
                        class="block w-full border-none bg-transparent focus:ring-0 resize-none text-lg leading-relaxed p-0 placeholder-zinc-400 dark:placeholder-zinc-500 text-zinc-900 dark:text-zinc-100 focus:outline-none" 
                        placeholder="What's on your mind?"
                        autofocus
                    ></textarea>
                </div>
                <x-input-error :messages="$errors->get('content')" class="mt-2" />

                @if ($media && count($media) > 0)
                    <div class="mt-4 grid gap-2 {{ count($media) === 1 ? 'grid-cols-1' : 'grid-cols-2' }}">
                        @foreach ($media as $index => $file)
                            @php
                                $isImage = str_starts_with($file->getMimeType(), 'image/');
                                $isVideo = str_starts_with($file->getMimeType(), 'video/');
                            @endphp
                            <div class="relative group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 {{ count($media) === 1 ? 'aspect-video' : 'aspect-square' }} ring-1 ring-zinc-200/50 dark:ring-zinc-700/50">
                                @if($isImage)
                                    <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                                @elseif($isVideo)
                                    <video src="{{ $file->temporaryUrl() }}" class="w-full h-full object-cover" muted></video>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-black/60 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center px-4">
                                            <svg class="w-10 h-10 mx-auto text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <span class="mt-2 block text-xs text-zinc-500 dark:text-zinc-400 truncate">{{ $file->getClientOriginalName() }}</span>
                                        </div>
                                    </div>
                                @endif
                                <button 
                                    type="button"
                                    wire:click="removeMedia({{ $index }})"
                                    class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/70 hover:bg-black/90 flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('media.*')" class="mt-2 text-sm" />
                @endif
            </div>
        </div>

        <div class="sticky bottom-0 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border-t border-zinc-200/70 dark:border-zinc-800/70 -mx-4 sm:-mx-6 px-4 sm:px-6 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                    <label for="media" class="cursor-pointer p-2.5 rounded-full text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all duration-200" title="Add photos or videos">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path></svg>
                        <input id="media" type="file" wire:model.live="media" multiple accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" class="hidden" />
                    </label>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500 ml-2">JPG, PNG, WebP, MP4, WebM</span>
                </div>

                <div class="flex items-center gap-3">
                    <div 
                        class="text-xs font-medium transition-colors duration-200"
                        :class="{
                            'text-zinc-400 dark:text-zinc-500': charCount < maxChars * 0.8,
                            'text-amber-500': charCount >= maxChars * 0.8 && charCount < maxChars,
                            'text-red-500': charCount >= maxChars
                        }"
                    >
                        <span x-text="charCount"></span>/<span x-text="maxChars"></span>
                    </div>
                    <div class="w-6 h-6 relative" x-show="charCount > 0">
                        <svg class="w-6 h-6 -rotate-90" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" class="text-zinc-200 dark:text-zinc-700" stroke-width="2"></circle>
                            <circle 
                                cx="12" cy="12" r="10" fill="none" stroke="currentColor" 
                                stroke-width="2" 
                                stroke-linecap="round"
                                :stroke-dasharray="2 * Math.PI * 10"
                                :stroke-dashoffset="2 * Math.PI * 10 * (1 - charCount / maxChars)"
                                :class="{
                                    'text-indigo-500': charCount < maxChars * 0.8,
                                    'text-amber-500': charCount >= maxChars * 0.8 && charCount < maxChars,
                                    'text-red-500': charCount >= maxChars
                                }"
                            ></circle>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div wire:loading wire:target="media" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-zinc-800 rounded-2xl p-6 shadow-2xl flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-zinc-900 dark:text-zinc-100 font-medium">Uploading media...</span>
        </div>
    </div>
</div>

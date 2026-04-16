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

<div class="max-w-2xl w-full bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Header -->
    <div class="sticky top-0 z-20 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md px-4 py-3 flex items-center gap-6 border-b border-zinc-200 dark:border-zinc-800">
        <a href="{{ route('feed.index') }}" wire:navigate class="p-2 -ml-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Create Post</h1>
    </div>

    <div class="px-4 py-6 sm:px-6">
        <div class="flex gap-4">
            <div class="w-12 h-12 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden">
                @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                    <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-xl">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <form wire:submit.prevent="store" class="flex-1 pb-10">
                <div>
                    <textarea 
                        id="content" 
                        wire:model="content" 
                        rows="5" 
                        class="block w-full border-none bg-transparent focus:ring-0 resize-none text-xl p-0 placeholder-zinc-500 dark:placeholder-zinc-400 text-zinc-900 dark:text-zinc-100" 
                        placeholder="What is happening?!"
                        autofocus
                    ></textarea>
                    <x-input-error :messages="$errors->get('content')" class="mt-2" />
                </div>

                @if ($media)
                    <div class="mt-4 grid gap-2 {{ count($media) === 1 ? 'grid-cols-1' : 'grid-cols-2' }}">
                        @foreach ($media as $index => $file)
                            <div class="relative rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 aspect-video">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/5 backdrop-blur-sm">
                                    <span class="text-white font-bold text-sm bg-black/50 px-3 py-1 rounded-full">{{ $file->getClientOriginalName() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6 border-t border-zinc-100 dark:border-zinc-800 pt-4 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <label for="media" class="cursor-pointer p-2 rounded-full text-indigo-500 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition" title="Add Media">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <input id="media" type="file" wire:model.live="media" multiple accept="image/jpeg,image/png,image/webp,video/mp4,video/webm" class="hidden" />
                        </label>
                        <x-input-error :messages="$errors->get('media.*')" class="text-xs" />
                    </div>

                    <button type="submit" class="rounded-full bg-indigo-600 px-6 py-2 text-white font-bold hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

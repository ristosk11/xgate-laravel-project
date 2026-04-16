<?php

use App\Domain\IdentityAndAccess\Actions\UpdateProfileAction;
use App\Domain\IdentityAndAccess\DTOs\UpdateProfileDTO;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\layout;
use function Livewire\Volt\state;

layout('layouts.app');

state([
    'bio' => fn () => Auth::user()->profile?->bio,
    'location' => fn () => Auth::user()->profile?->location,
    'website' => fn () => Auth::user()->profile?->website,
    'avatar' => null,
    'cover_image' => null,
]);

$save = function () {
    $validated = $this->validate([
        'bio' => ['nullable', 'string'],
        'location' => ['nullable', 'string', 'max:255'],
        'website' => ['nullable', 'url', 'max:255'],
        'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
    ]);

    $avatarPath = isset($validated['avatar']) && $validated['avatar'] !== null
        ? $validated['avatar']->store('avatars', 'public')
        : null;

    $coverPath = isset($validated['cover_image']) && $validated['cover_image'] !== null
        ? $validated['cover_image']->store('covers', 'public')
        : null;

    $dto = new UpdateProfileDTO(
        bio: $validated['bio'] ?? null,
        location: $validated['location'] ?? null,
        website: $validated['website'] ?? null,
        avatarPath: $avatarPath,
        coverImagePath: $coverPath,
    );

    app(UpdateProfileAction::class)->execute(Auth::user(), $dto);

    $this->redirect(route('profile.show', ['id' => Auth::id()], absolute: false), navigate: true);
};

?>

<div class="max-w-2xl w-full bg-white dark:bg-zinc-900 min-h-screen">
    <!-- Header -->
    <div class="sticky top-0 z-20 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md px-4 py-3 flex justify-between items-center border-b border-zinc-200 dark:border-zinc-800">
        <div class="flex items-center gap-6">
            <a href="{{ route('profile.show', ['id' => auth()->id()]) }}" wire:navigate class="p-2 -ml-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h1 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">Edit Profile</h1>
        </div>
        <button type="submit" form="editProfileForm" class="rounded-full bg-zinc-900 dark:bg-white px-4 py-1.5 text-sm font-bold text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition">
            Save
        </button>
    </div>

    <!-- Form -->
    <form id="editProfileForm" wire:submit.prevent="save" class="relative pb-10">
        
        <!-- Cover Image Input area -->
        <div class="relative h-32 sm:h-48 bg-zinc-200 dark:bg-zinc-800 w-full group">
            @if($cover_image)
                <img src="{{ $cover_image->temporaryUrl() }}" class="w-full h-full object-cover">
            @elseif(auth()->user()->profile && auth()->user()->profile->cover_image_url)
                <img src="{{ auth()->user()->profile->cover_image_url }}" alt="Cover" class="w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-100 dark:from-indigo-900/50 to-purple-100 dark:to-purple-900/50"></div>
            @endif
            
            <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                <label class="cursor-pointer p-3 bg-black/50 hover:bg-black/60 rounded-full text-white backdrop-blur-sm transition">
                    <span class="sr-only">Cover image</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <input wire:model="cover_image" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" />
                </label>
            </div>
            <x-input-error :messages="$errors->get('cover_image')" class="absolute bottom-2 left-4 text-white drop-shadow-md text-sm" />
        </div>

        <div class="px-4 relative mb-6">
            <!-- Avatar Input area -->
            <div class="relative -mt-12 sm:-mt-16 w-24 h-24 sm:w-32 sm:h-32 rounded-full ring-4 ring-white dark:ring-zinc-900 bg-zinc-200 dark:bg-zinc-800 overflow-hidden flex-shrink-0 group">
                @if($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                @elseif(auth()->user()->profile && auth()->user()->profile->avatar_url)
                    <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-4xl">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif

                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                    <label class="cursor-pointer p-2 bg-black/50 hover:bg-black/60 rounded-full text-white backdrop-blur-sm transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <input wire:model="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" />
                    </label>
                </div>
            </div>
            <x-input-error :messages="$errors->get('avatar')" class="mt-1" />
        </div>

        <!-- Input Fields -->
        <div class="px-4 space-y-6">
            <div class="relative">
                <label class="absolute text-sm text-zinc-500 dark:text-zinc-400 top-2 left-3 transition-all">Bio</label>
                <textarea wire:model="bio" rows="4" class="block w-full rounded-md border-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-600 pt-7 pb-2 px-3 text-[15px] resize-none transition" placeholder="Add a bio"></textarea>
                <x-input-error :messages="$errors->get('bio')" class="mt-1" />
            </div>

            <div class="relative">
                <label class="absolute text-sm text-zinc-500 dark:text-zinc-400 top-2 left-3 transition-all">Location</label>
                <input wire:model="location" type="text" class="block w-full rounded-md border-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-600 pt-7 pb-2 px-3 text-[15px] transition" placeholder="Add location" />
                <x-input-error :messages="$errors->get('location')" class="mt-1" />
            </div>

            <div class="relative">
                <label class="absolute text-sm text-zinc-500 dark:text-zinc-400 top-2 left-3 transition-all">Website</label>
                <input wire:model="website" type="url" class="block w-full rounded-md border-zinc-200 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-600 pt-7 pb-2 px-3 text-[15px] transition" placeholder="Add website" />
                <x-input-error :messages="$errors->get('website')" class="mt-1" />
            </div>
        </div>
    </form>
</div>

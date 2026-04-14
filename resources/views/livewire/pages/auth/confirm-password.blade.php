<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state(['password' => '']);

rules(['password' => ['required', 'string']]);

$confirmPassword = function () {
    $this->validate();

    if (! Auth::guard('web')->validate([
        'email' => Auth::user()->email,
        'password' => $this->password,
    ])) {
        throw ValidationException::withMessages([
            'password' => __('auth.password'),
        ]);
    }

    session(['auth.password_confirmed_at' => time()]);

    $this->redirectIntended(default: route('feed.index', absolute: false), navigate: true);
};

?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-white">Confirm it’s you</h1>
        <p class="mt-1 text-sm text-white/65">{{ __('Please confirm your password to continue.') }}</p>
    </div>

    <form wire:submit.prevent="confirmPassword">
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-white/80" />

            <x-text-input
                wire:model="password"
                id="password"
                class="mt-2 block w-full rounded-2xl border-white/10 bg-white/10 text-white placeholder:text-white/35 shadow-sm ring-1 ring-white/10 backdrop-blur-md transition-all duration-200 focus:border-white/20 focus:bg-white/15 focus:ring-2 focus:ring-indigo-400/50"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-200" />
        </div>

        <div class="mt-6 flex justify-end">
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-zinc-950 shadow-sm ring-1 ring-white/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-white/90 hover:shadow-lg hover:shadow-black/20 focus:outline-none focus:ring-2 focus:ring-indigo-400/60 active:translate-y-0"
            >
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</div>

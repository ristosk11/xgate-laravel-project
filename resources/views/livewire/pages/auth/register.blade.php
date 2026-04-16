<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
]);

$register = function () {
    $validated = $this->validate();

    $validated['password'] = Hash::make($validated['password']);

    event(new Registered($user = User::create($validated)));

    Auth::login($user);

    $this->redirect(route('feed.index', absolute: false), navigate: true);
};

?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-white">Create your account</h1>
        <p class="mt-1 text-sm text-white/65">Join the conversation in under a minute.</p>
    </div>

    <form wire:submit.prevent="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="!text-white/80" />
            <input
                wire:model="name"
                id="name"
                class="mt-2 block w-full rounded-2xl border-white/10 bg-white/10 text-white placeholder:text-white/35 shadow-sm ring-1 ring-white/10 backdrop-blur-md transition-all duration-200 focus:border-white/20 focus:bg-white/15 focus:ring-2 focus:ring-indigo-400/50 focus:outline-none"
                type="text"
                name="name"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-200" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="!text-white/80" />
            <input
                wire:model="email"
                id="email"
                class="mt-2 block w-full rounded-2xl border-white/10 bg-white/10 text-white placeholder:text-white/35 shadow-sm ring-1 ring-white/10 backdrop-blur-md transition-all duration-200 focus:border-white/20 focus:bg-white/15 focus:ring-2 focus:ring-indigo-400/50 focus:outline-none"
                type="email"
                name="email"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-200" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="!text-white/80" />

            <input
                wire:model="password"
                id="password"
                class="mt-2 block w-full rounded-2xl border-white/10 bg-white/10 text-white placeholder:text-white/35 shadow-sm ring-1 ring-white/10 backdrop-blur-md transition-all duration-200 focus:border-white/20 focus:bg-white/15 focus:ring-2 focus:ring-indigo-400/50 focus:outline-none"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-200" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="!text-white/80" />

            <input
                wire:model="password_confirmation"
                id="password_confirmation"
                class="mt-2 block w-full rounded-2xl border-white/10 bg-white/10 text-white placeholder:text-white/35 shadow-sm ring-1 ring-white/10 backdrop-blur-md transition-all duration-200 focus:border-white/20 focus:bg-white/15 focus:ring-2 focus:ring-indigo-400/50 focus:outline-none"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-200" />
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-zinc-950 shadow-sm ring-1 ring-white/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-white/90 hover:shadow-lg hover:shadow-black/20 focus:outline-none focus:ring-2 focus:ring-indigo-400/60 active:translate-y-0"
            >
                {{ __('Register') }}
            </button>
        </div>

        <div class="mt-6 border-t border-white/10 pt-5">
            <p class="text-sm text-white/60">
                Already registered?
                <a href="{{ route('login') }}" wire:navigate class="font-semibold text-white underline decoration-white/25 underline-offset-4 transition hover:text-white/90">Sign in</a>
            </p>
        </div>
    </form>
</div>

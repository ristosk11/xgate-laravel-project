<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state('token')->locked();

state([
    'email' => fn () => request()->string('email')->value(),
    'password' => '',
    'password_confirmation' => ''
]);

rules([
    'token' => ['required'],
    'email' => ['required', 'string', 'email'],
    'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
]);

$resetPassword = function () {
    $this->validate();

    // Here we will attempt to reset the user's password. If it is successful we
    // will update the password on an actual user model and persist it to the
    // database. Otherwise we will parse the error and return the response.
    $status = Password::reset(
        $this->only('email', 'password', 'password_confirmation', 'token'),
        function ($user) {
            $user->forceFill([
                'password' => Hash::make($this->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    // If the password was successfully reset, we will redirect the user back to
    // the application's home authenticated view. If there is an error we can
    // redirect them back to where they came from with their error message.
    if ($status != Password::PASSWORD_RESET) {
        $this->addError('email', __($status));

        return;
    }

    Session::flash('status', __($status));

    $this->redirectRoute('login', navigate: true);
};

?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-white">Choose a new password</h1>
        <p class="mt-1 text-sm text-white/65">Make it strong—this keeps your account safe.</p>
    </div>

    <form wire:submit.prevent="resetPassword">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-white/80" />
            <x-text-input
                wire:model="email"
                id="email"
                class="mt-2 block w-full rounded-2xl border-white/10 bg-white/10 text-white placeholder:text-white/35 shadow-sm ring-1 ring-white/10 backdrop-blur-md transition-all duration-200 focus:border-white/20 focus:bg-white/15 focus:ring-2 focus:ring-indigo-400/50"
                type="email"
                name="email"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-200" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-white/80" />
            <x-text-input
                wire:model="password"
                id="password"
                class="mt-2 block w-full rounded-2xl border-white/10 bg-white/10 text-white placeholder:text-white/35 shadow-sm ring-1 ring-white/10 backdrop-blur-md transition-all duration-200 focus:border-white/20 focus:bg-white/15 focus:ring-2 focus:ring-indigo-400/50"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-200" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-white/80" />

            <x-text-input
                wire:model="password_confirmation"
                id="password_confirmation"
                class="mt-2 block w-full rounded-2xl border-white/10 bg-white/10 text-white placeholder:text-white/35 shadow-sm ring-1 ring-white/10 backdrop-blur-md transition-all duration-200 focus:border-white/20 focus:bg-white/15 focus:ring-2 focus:ring-indigo-400/50"
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
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</div>

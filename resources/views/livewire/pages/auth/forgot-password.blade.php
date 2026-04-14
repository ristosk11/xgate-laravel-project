<?php

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;
use function Livewire\Volt\rules;
use function Livewire\Volt\state;

layout('layouts.guest');

state(['email' => '']);

rules(['email' => ['required', 'string', 'email']]);

$sendPasswordResetLink = function () {
    $this->validate();

    // We will send the password reset link to this user. Once we have attempted
    // to send the link, we will examine the response then see the message we
    // need to show to the user. Finally, we'll send out a proper response.
    $status = Password::sendResetLink(
        $this->only('email')
    );

    if ($status != Password::RESET_LINK_SENT) {
        $this->addError('email', __($status));

        return;
    }

    $this->reset('email');

    Session::flash('status', __($status));
};

?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-white">Reset your password</h1>
        <p class="mt-1 text-sm text-white/65">
            {{ __('Forgot your password? No problem. Enter your email and we’ll send a reset link.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 rounded-2xl bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 ring-1 ring-emerald-400/20" :status="session('status')" />

    <form wire:submit.prevent="sendPasswordResetLink">
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
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-200" />
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button
                type="submit"
                class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-zinc-950 shadow-sm ring-1 ring-white/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-white/90 hover:shadow-lg hover:shadow-black/20 focus:outline-none focus:ring-2 focus:ring-indigo-400/60 active:translate-y-0"
            >
                {{ __('Email Password Reset Link') }}
            </button>
        </div>

        <div class="mt-6 border-t border-white/10 pt-5">
            <p class="text-sm text-white/60">
                Remembered it?
                <a href="{{ route('login') }}" wire:navigate class="font-semibold text-white underline decoration-white/25 underline-offset-4 transition hover:text-white/90">Back to sign in</a>
            </p>
        </div>
    </form>
</div>

<?php

use App\Livewire\Actions\Logout;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\layout;

layout('layouts.guest');

$sendVerification = function () {
    $user = Auth::user();

    if ($user instanceof MustVerifyEmail && method_exists($user, 'hasVerifiedEmail') && $user->hasVerifiedEmail()) {
    $this->redirectIntended(default: route('feed.index', absolute: false), navigate: true);

        return;
    }

    if ($user instanceof MustVerifyEmail && method_exists($user, 'sendEmailVerificationNotification')) {
        $user->sendEmailVerificationNotification();
    }

    Session::flash('status', 'verification-link-sent');
};

$logout = function (Logout $logout) {
    $logout();

    $this->redirect('/', navigate: true);
};

?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-white">Verify your email</h1>
        <p class="mt-1 text-sm text-white/65">
            {{ __('Thanks for signing up! Check your inbox for a verification link. If you didn\'t receive it, we can send another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-2xl bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-200 ring-1 ring-emerald-400/20">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col gap-3">
        <button
            type="button"
            wire:click="sendVerification"
            class="inline-flex w-full items-center justify-center rounded-2xl bg-white px-5 py-2.5 text-sm font-semibold text-zinc-950 shadow-sm ring-1 ring-white/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-white/90 hover:shadow-lg hover:shadow-black/20 focus:outline-none focus:ring-2 focus:ring-indigo-400/60 active:translate-y-0"
        >
            {{ __('Resend Verification Email') }}
        </button>

        <button
            wire:click="logout"
            type="submit"
            class="inline-flex w-full items-center justify-center rounded-2xl bg-white/10 px-5 py-2.5 text-sm font-semibold text-white ring-1 ring-white/15 backdrop-blur-md transition-all duration-200 hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-indigo-400/50"
        >
            {{ __('Log Out') }}
        </button>
    </div>
</div>

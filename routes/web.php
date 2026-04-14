<?php

use App\Http\Controllers\FollowController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('feed.index');
});

Route::get('dashboard', function () {
    return redirect()->route('feed.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::redirect('profile', '/profile/edit')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Volt::route('feed', 'pages.feed.index')->name('feed.index');

    Volt::route('posts/create', 'pages.posts.create')->name('posts.create');
    Volt::route('posts/{id}', 'pages.posts.show')->name('posts.show');

    Volt::route('profiles/{id}', 'pages.profile.show')->name('profile.show');
    Volt::route('profile/edit', 'pages.profile.edit')->name('profile.edit');

    Route::post('profiles/{user}/follow', FollowController::class)
        ->name('profile.follow');
});

require __DIR__.'/auth.php';

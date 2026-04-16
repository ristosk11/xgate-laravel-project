<?php

use App\Domain\Content\Actions\DeletePostAction;
use App\Domain\Content\Models\Post;
use App\Domain\Engagement\Models\Comment;
use App\Domain\IdentityAndAccess\Services\FollowService;
use App\Domain\IdentityAndAccess\Actions\ToggleFollowAction;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use function Livewire\Volt\computed;
use function Livewire\Volt\layout;
use function Livewire\Volt\on;
use function Livewire\Volt\state;

layout('layouts.app');

state([
    'user' => fn ($id) => User::query()
        ->with(['profile'])
        ->withCount(['followsAsFollowing as followers_count', 'followsAsFollower as following_count'])
        ->findOrFail($id),
    'activeTab' => 'posts',
]);

$posts = computed(fn () => $this->user->posts()->with(['media', 'reactions'])->latest()->paginate(15));

$replies = computed(fn () => Comment::query()
    ->where('user_id', $this->user->id)
    ->with(['post.author.profile', 'post.media'])
    ->latest()
    ->paginate(15));

$mediaPosts = computed(fn () => $this->user->posts()
    ->whereHas('media')
    ->with(['media', 'reactions'])
    ->latest()
    ->paginate(15));

$followers = computed(fn () => app(FollowService::class)->followers($this->user));
$following = computed(fn () => app(FollowService::class)->following($this->user));

$isFollowing = computed(function () {
    $authId = Auth::id();

    if ($authId === null || $authId === $this->user->id) {
        return false;
    }

    $authUser = Auth::user();

    if (! ($authUser instanceof User)) {
        return false;
    }

    return $authUser
        ->followsAsFollower()
        ->where('following_id', $this->user->id)
        ->exists();
});

$toggleFollow = function () {
    $authId = Auth::id();

    if ($authId === null || $authId === $this->user->id) {
        return;
    }

    try {
        app(ToggleFollowAction::class)->execute(Auth::user(), $this->user);
    } catch (ValidationException $exception) {
        $this->addError('follow', $exception->getMessage());

        return;
    }

    $this->user = User::query()
        ->with(['profile'])
        ->withCount(['followsAsFollowing as followers_count', 'followsAsFollower as following_count'])
        ->findOrFail($this->user->id);
};

$setTab = function (string $tab) {
    $this->activeTab = $tab;
};

on(['delete-post' => function (string $id) {
    $post = Post::query()->find($id);

    if (! $post || $post->user_id !== Auth::id()) {
        return;
    }

    app(DeletePostAction::class)->execute($post);

    unset($this->posts);
    unset($this->mediaPosts);
}]);

?>

<div class="w-full min-h-screen">
    <div class="sticky top-0 z-20 bg-white/75 dark:bg-zinc-900/75 backdrop-blur-xl px-4 py-3 flex items-center gap-4 border-b border-zinc-200/70 dark:border-zinc-800/70">
        <a href="{{ route('feed.index') }}" wire:navigate class="p-2 -ml-2 rounded-full transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 active:bg-zinc-200/60 dark:active:bg-zinc-700/60">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h1 class="text-[17px] font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100 leading-tight">{{ $user->name }}</h1>
            <p class="text-[12px] text-zinc-500 dark:text-zinc-400">{{ $this->posts->total() }} posts</p>
        </div>
    </div>

    <div class="relative border-b border-zinc-200/70 dark:border-zinc-800/70 pb-4">
        <div class="h-36 sm:h-56 bg-zinc-200 dark:bg-zinc-800 w-full relative overflow-hidden">
            @if($user->profile?->cover_image_url)
                <img src="{{ $user->profile->cover_image_url }}" alt="Cover" class="w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-[radial-gradient(50rem_20rem_at_20%_20%,rgba(99,102,241,0.35),transparent_60%),radial-gradient(40rem_20rem_at_80%_30%,rgba(236,72,153,0.25),transparent_55%),linear-gradient(to_right,rgba(250,250,250,1),rgba(244,244,245,1))] dark:bg-[radial-gradient(50rem_20rem_at_20%_20%,rgba(99,102,241,0.25),transparent_60%),radial-gradient(40rem_20rem_at_80%_30%,rgba(236,72,153,0.15),transparent_55%),linear-gradient(to_right,rgba(24,24,27,1),rgba(39,39,42,1))]"></div>
            @endif
            <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-white/35 dark:from-zinc-900/35 via-white/0 dark:via-zinc-900/0 to-white/0 dark:to-zinc-900/0"></div>
        </div>

        <div class="px-4 relative">
            <div class="flex justify-between items-end mb-4">
                <div class="relative -mt-12 sm:-mt-16 w-24 h-24 sm:w-32 sm:h-32 rounded-full ring-4 ring-white dark:ring-zinc-900 bg-zinc-200 dark:bg-zinc-700 overflow-hidden flex-shrink-0 shadow-sm ring-offset-0">
                    @if($user->profile?->avatar_url)
                        <img src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-4xl">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div>
                    @if (Auth::id() === $user->id)
                        <a href="{{ route('profile.edit') }}" wire:navigate class="inline-flex rounded-full border border-zinc-300/80 dark:border-zinc-600/80 bg-white dark:bg-zinc-800 px-4 sm:px-5 py-2 text-[13px] font-semibold text-zinc-900 dark:text-zinc-100 shadow-sm ring-1 ring-black/5 dark:ring-white/5 transition-all duration-200 hover:-translate-y-0.5 hover:bg-zinc-50 dark:hover:bg-zinc-700 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                            Edit profile
                        </a>
                    @else
                        <button
                            type="button"
                            wire:click="toggleFollow"
                            class="inline-flex rounded-full px-4 sm:px-5 py-2 text-[13px] font-semibold transition-all duration-200 shadow-sm ring-1 ring-black/5 dark:ring-white/5 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 {{ $this->isFollowing ? 'border border-zinc-300/80 dark:border-zinc-600/80 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-700 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-800' : 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100' }}"
                        >
                            {{ $this->isFollowing ? 'Following' : 'Follow' }}
                        </button>
                        <x-input-error :messages="$errors->get('follow')" class="mt-2 text-xs" />
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <h2 class="text-xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $user->name }}</h2>
                <p class="text-[15px] text-zinc-500 dark:text-zinc-400">{{ $user->username ?? '@'.strtolower(str_replace(' ', '', $user->name)) }}</p>
            </div>

            @if($user->profile?->bio)
                <p class="text-[15px] text-zinc-900 dark:text-zinc-100 mb-3 whitespace-pre-line leading-relaxed">{{ $user->profile->bio }}</p>
            @endif

            <div class="flex flex-wrap gap-x-4 gap-y-2 mb-3 text-[14px] text-zinc-500 dark:text-zinc-400">
                @if($user->profile?->location)
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>{{ $user->profile->location }}</span>
                    </div>
                @endif
                @if($user->profile?->website)
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        <a href="{{ $user->profile->website }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 dark:text-indigo-400 hover:underline decoration-indigo-200 dark:decoration-indigo-700 underline-offset-4 truncate max-w-[200px]">{{ str_replace(['http://', 'https://'], '', $user->profile->website) }}</a>
                    </div>
                @endif
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Joined {{ $user->created_at->format('F Y') }}</span>
                </div>
            </div>

            <div class="flex gap-4 text-[14px]">
                <div x-data="{ showFollowing: false }" class="relative">
                    <button @click="showFollowing = !showFollowing" class="rounded-full px-3 py-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-200 cursor-pointer flex gap-1">
                        <strong class="text-zinc-900 dark:text-zinc-100">{{ $user->following_count }}</strong>
                        <span class="text-zinc-500 dark:text-zinc-400">Following</span>
                    </button>

                    <div x-show="showFollowing" @click.away="showFollowing = false" style="display: none;" class="absolute left-0 mt-2 w-72 bg-white/90 dark:bg-zinc-800/90 rounded-2xl shadow-xl border border-zinc-200/70 dark:border-zinc-700/70 z-30 overflow-hidden ring-1 ring-black/5 dark:ring-white/5 backdrop-blur-xl">
                        <div class="p-3 border-b border-zinc-200/70 dark:border-zinc-700/70 font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">Following</div>
                        <div class="max-h-64 overflow-y-auto p-2">
                            @forelse ($this->following as $followed)
                                <a href="{{ route('profile.show', ['id' => $followed->id]) }}" wire:navigate class="block p-2 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-xl truncate transition-all duration-200">
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $followed->name }}</span>
                                </a>
                            @empty
                                <div class="p-4 text-center text-sm text-zinc-500 dark:text-zinc-400">Not following anyone yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div x-data="{ showFollowers: false }" class="relative">
                    <button @click="showFollowers = !showFollowers" class="rounded-full px-3 py-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-all duration-200 cursor-pointer flex gap-1">
                        <strong class="text-zinc-900 dark:text-zinc-100">{{ $user->followers_count }}</strong>
                        <span class="text-zinc-500 dark:text-zinc-400">Followers</span>
                    </button>

                    <div x-show="showFollowers" @click.away="showFollowers = false" style="display: none;" class="absolute left-0 mt-2 w-72 bg-white/90 dark:bg-zinc-800/90 rounded-2xl shadow-xl border border-zinc-200/70 dark:border-zinc-700/70 z-30 overflow-hidden ring-1 ring-black/5 dark:ring-white/5 backdrop-blur-xl">
                        <div class="p-3 border-b border-zinc-200/70 dark:border-zinc-700/70 font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">Followers</div>
                        <div class="max-h-64 overflow-y-auto p-2">
                            @forelse ($this->followers as $follower)
                                <a href="{{ route('profile.show', ['id' => $follower->id]) }}" wire:navigate class="block p-2 hover:bg-zinc-50 dark:hover:bg-zinc-700 rounded-xl truncate transition-all duration-200">
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $follower->name }}</span>
                                </a>
                            @empty
                                <div class="p-4 text-center text-sm text-zinc-500 dark:text-zinc-400">No followers yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex border-b border-zinc-200/70 dark:border-zinc-800/70 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl">
        <button wire:click="setTab('posts')" class="flex-1 text-center hover:bg-zinc-50/80 dark:hover:bg-zinc-800/80 transition-all duration-200 cursor-pointer text-[14px] relative focus:outline-none {{ $activeTab === 'posts' ? 'font-semibold' : 'font-medium text-zinc-500 dark:text-zinc-400' }}">
            <div class="py-4 inline-block relative {{ $activeTab === 'posts' ? 'text-zinc-900 dark:text-zinc-100' : '' }}">
                Posts
                @if($activeTab === 'posts')
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-zinc-900 dark:bg-white rounded-full"></div>
                @endif
            </div>
        </button>
        <button wire:click="setTab('replies')" class="flex-1 text-center hover:bg-zinc-50/80 dark:hover:bg-zinc-800/80 transition-all duration-200 cursor-pointer text-[14px] relative focus:outline-none {{ $activeTab === 'replies' ? 'font-semibold' : 'font-medium text-zinc-500 dark:text-zinc-400' }}">
            <div class="py-4 inline-block relative {{ $activeTab === 'replies' ? 'text-zinc-900 dark:text-zinc-100' : '' }}">
                Replies
                @if($activeTab === 'replies')
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-zinc-900 dark:bg-white rounded-full"></div>
                @endif
            </div>
        </button>
        <button wire:click="setTab('media')" class="flex-1 text-center hover:bg-zinc-50/80 dark:hover:bg-zinc-800/80 transition-all duration-200 cursor-pointer text-[14px] relative focus:outline-none {{ $activeTab === 'media' ? 'font-semibold' : 'font-medium text-zinc-500 dark:text-zinc-400' }}">
            <div class="py-4 inline-block relative {{ $activeTab === 'media' ? 'text-zinc-900 dark:text-zinc-100' : '' }}">
                Media
                @if($activeTab === 'media')
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-zinc-900 dark:bg-white rounded-full"></div>
                @endif
            </div>
        </button>
    </div>

    {{-- Posts Tab --}}
    @if($activeTab === 'posts')
        <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
            @forelse ($this->posts as $post)
                <article 
                    class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-all duration-200 cursor-pointer relative"
                    @click="if ($event.target.closest('a, button, [x-data]') === null) { window.Livewire.navigate('{{ route('posts.show', $post->id) }}') }"
                >
                    @include('livewire.components.post-card', ['post' => $post])
                </article>
            @empty
                <div class="p-10 text-center text-zinc-500 dark:text-zinc-400">
                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70">
                        <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="text-lg font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100 mb-1">No posts yet</h3>
                    <p>When {{ $user->name }} posts, they'll show up here.</p>
                </div>
            @endforelse
        </div>

        @if($this->posts->hasPages())
            <div class="p-4 border-t border-zinc-200/60 dark:border-zinc-800/60 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl">
                {{ $this->posts->links() }}
            </div>
        @endif
    @endif

    {{-- Replies Tab --}}
    @if($activeTab === 'replies')
        <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
            @forelse ($this->replies as $comment)
                <div class="px-4 py-4 sm:px-6 hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-all duration-200">
                    {{-- Context: what post this was a reply to --}}
                    <div class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400 mb-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        <span>Replying to</span>
                        <a href="{{ route('profile.show', ['id' => $comment->post->author->id]) }}" wire:navigate class="font-semibold text-zinc-700 dark:text-zinc-300 hover:underline">{{ $comment->post->author->name }}</a>
                    </div>
                    
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
                            @if($user->profile?->avatar_url)
                                <img src="{{ $user->profile->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-lg">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ $user->name }}</span>
                                <span class="text-zinc-400 dark:text-zinc-500">·</span>
                                <a href="{{ route('posts.show', ['id' => $comment->post->id]) }}" wire:navigate class="text-zinc-500 dark:text-zinc-400 hover:underline">{{ $comment->created_at->diffForHumans(null, true, true) }}</a>
                            </div>
                            <p class="text-zinc-900 dark:text-zinc-100 text-[15px] mt-1 whitespace-pre-line">{{ $comment->content }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-zinc-500 dark:text-zinc-400">
                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70">
                        <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100 mb-1">No replies yet</h3>
                    <p>When {{ $user->name }} replies to posts, they'll show up here.</p>
                </div>
            @endforelse
        </div>

        @if($this->replies->hasPages())
            <div class="p-4 border-t border-zinc-200/60 dark:border-zinc-800/60 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl">
                {{ $this->replies->links() }}
            </div>
        @endif
    @endif

    {{-- Media Tab --}}
    @if($activeTab === 'media')
        <div class="divide-y divide-zinc-200/60 dark:divide-zinc-800/60">
            @forelse ($this->mediaPosts as $post)
                <article 
                    class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/50 transition-all duration-200 cursor-pointer relative"
                    @click="if ($event.target.closest('a, button, [x-data]') === null) { window.Livewire.navigate('{{ route('posts.show', $post->id) }}') }"
                >
                    @include('livewire.components.post-card', ['post' => $post])
                </article>
            @empty
                <div class="p-10 text-center text-zinc-500 dark:text-zinc-400">
                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70">
                        <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100 mb-1">No media yet</h3>
                    <p>When {{ $user->name }} posts photos or videos, they'll show up here.</p>
                </div>
            @endforelse
        </div>

        @if($this->mediaPosts->hasPages())
            <div class="p-4 border-t border-zinc-200/60 dark:border-zinc-800/60 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-xl">
                {{ $this->mediaPosts->links() }}
            </div>
        @endif
    @endif
</div>

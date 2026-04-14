<?php

use App\Domain\Content\Models\Post;
use App\Domain\Content\Actions\UpdatePostAction;
use App\Domain\Content\Actions\DeletePostAction;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\layout;
use function Livewire\Volt\state;

layout('layouts.app');

state([
    'post' => fn ($id) => Post::query()
        ->with([
            'author.profile',
            'media',
            'reactions',
            'comments.author.profile',
            'comments.reactions',
            'comments.replies.author.profile',
            'comments.replies.reactions',
        ])
        ->findOrFail($id),
    'editingPost' => false,
    'postContent' => fn () => null,
]);

$refreshPost = function (): void {
    $this->post = Post::query()
        ->with([
            'author.profile',
            'media',
            'reactions',
            'comments.author.profile',
            'comments.reactions',
            'comments.replies.author.profile',
            'comments.replies.reactions',
        ])
        ->findOrFail($this->post->id);
};

$startPostEdit = function () {
    if (Auth::id() !== $this->post->user_id) {
        return;
    }

    $this->postContent = $this->post->content;
    $this->editingPost = true;
};

$cancelPostEdit = function () {
    $this->editingPost = false;
    $this->postContent = $this->post->content;
};

$updatePost = function () {
    if (Auth::id() !== $this->post->user_id) {
        return;
    }

    $validated = $this->validate([
        'postContent' => ['nullable', 'string'],
    ]);

    app(UpdatePostAction::class)->execute($this->post, $validated['postContent'] ?? null);

    $this->refreshPost();

    $this->editingPost = false;
};

$deletePost = function () {
    if (Auth::id() !== $this->post->user_id) {
        return;
    }

    app(DeletePostAction::class)->execute($this->post);

    $this->redirect(route('feed.index', absolute: false), navigate: true);
};

?>

<div class="max-w-2xl w-full bg-white min-h-screen">
    <!-- Header -->
    <div class="sticky top-0 z-20 bg-white/80 backdrop-blur-md px-4 py-3 flex items-center gap-6 border-b border-gray-200">
        <a href="{{ route('feed.index') }}" wire:navigate class="p-2 -ml-2 rounded-full hover:bg-gray-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Post</h1>
    </div>

    <!-- Post Details -->
    <div class="px-4 py-4 sm:px-6">
        <div class="flex gap-3 mb-4">
            <!-- Avatar -->
            <div class="w-12 h-12 rounded-full bg-gray-200 flex-shrink-0 overflow-hidden">
                @if($post->author->profile && $post->author->profile->avatar_url)
                    <img src="{{ $post->author->profile->avatar_url }}" alt="{{ $post->author->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-700 font-bold text-xl">
                        {{ substr($post->author->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <!-- Author Info -->
            <div class="flex-1 flex justify-between items-start">
                <div class="flex flex-col">
                    <a href="{{ route('profile.show', ['id' => $post->author->id]) }}" wire:navigate class="font-bold text-gray-900 text-[15px] hover:underline">
                        {{ $post->author->name }}
                    </a>
                    <span class="text-gray-500 text-[15px] leading-tight">
                        {{ $post->author->username ?? '@'.strtolower(str_replace(' ', '', $post->author->name)) }}
                    </span>
                </div>
                
                @if (Auth::id() === $post->user_id)
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"></path></svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-10 py-2">
                            <button wire:click="startPostEdit" @click="open = false" class="w-full text-left px-4 py-2 hover:bg-gray-50 font-bold text-gray-700 text-sm">
                                Edit Post
                            </button>
                            <button wire:click="deletePost" wire:confirm="Delete this post?" class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 font-bold text-sm">
                                Delete Post
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($editingPost)
            <form wire:submit.prevent="updatePost" class="space-y-3 mb-4">
                <textarea wire:model="postContent" rows="4" class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 transition p-3 resize-none text-[17px]"></textarea>
                <x-input-error :messages="$errors->get('postContent')" class="mt-1" />
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="cancelPostEdit" class="rounded-full bg-gray-100 hover:bg-gray-200 px-5 py-1.5 text-sm font-bold text-gray-700 transition">Cancel</button>
                    <button type="submit" class="rounded-full bg-indigo-600 hover:bg-indigo-700 px-5 py-1.5 text-sm font-bold text-white transition">Save</button>
                </div>
            </form>
        @else
            @if (! empty($post->content))
                <p class="text-gray-900 text-[17px] whitespace-pre-line leading-normal mb-4">{{ $post->content }}</p>
            @endif
        @endif

        <div class="mb-4 rounded-2xl overflow-hidden border border-gray-100 bg-gray-50">
            @include('livewire.components.media-gallery', ['post' => $post])
        </div>

        <div class="flex gap-2 text-gray-500 text-[15px] border-b border-gray-100 pb-4 mb-4">
            <span class="hover:underline cursor-pointer">{{ $post->created_at->format('g:i A') }}</span>
            <span>·</span>
            <span class="hover:underline cursor-pointer">{{ $post->created_at->format('M j, Y') }}</span>
        </div>

        <!-- Post Actions -->
        <div class="flex items-center justify-around text-gray-500 border-b border-gray-100 pb-3 mb-2">
            <div class="flex items-center gap-2 group transition">
                <div class="p-2 rounded-full group-hover:bg-indigo-50 group-hover:text-indigo-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <span class="text-[15px] font-medium group-hover:text-indigo-600 transition">
                    {{ $post->comments_count ?? $post->comments()->count() }}
                </span>
            </div>

            <div class="scale-110">
                <livewire:components.reaction-bar :reactable="$post" :key="'reaction-bar-post-show-'.$post->id" />
            </div>
            
            <button class="flex items-center gap-2 group transition p-2 rounded-full hover:bg-indigo-50 hover:text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
            </button>
        </div>
    </div>

    <div class="border-t-[10px] border-gray-50">
        <livewire:components.comment-thread :post="$post" :key="'comment-thread-'.$post->id" />
    </div>
</div>

<?php

use App\Domain\Engagement\Actions\CreateCommentAction;
use App\Domain\Engagement\Actions\DeleteCommentAction;
use App\Domain\Engagement\Actions\UpdateCommentAction;
use App\Domain\Engagement\DTOs\CreateCommentDTO;
use App\Domain\Engagement\Models\Comment;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\state;

state([
    'post' => null,
    'content' => '',
    'reply_to' => null,
    'editing_comment_id' => null,
    'editing_content' => '',
]);

$refreshComments = function (): void {
    if (! $this->post) {
        return;
    }

    $this->post->unsetRelation('comments');

    $this->post->load([
        'comments.replies.author.profile',
        'comments.author.profile',
        'comments.reactions',
        'comments.replies.reactions',
    ]);
};

$submit = function () {
    if (! $this->post) {
        return;
    }

    $validated = $this->validate([
        'content' => ['required', 'string'],
        'reply_to' => ['nullable', 'string'],
    ]);

    app(CreateCommentAction::class)->execute(
        Auth::user(),
        $this->post,
        new CreateCommentDTO(
            content: $validated['content'],
            parentCommentId: $validated['reply_to'] ?? null,
        )
    );

    $this->content = '';
    $this->reply_to = null;

    $this->refreshComments();
};

$startEdit = function (string $commentId) {
    $comment = Comment::query()->findOrFail($commentId);

    if ($comment->user_id !== Auth::id()) {
        return;
    }

    $this->editing_comment_id = $comment->id;
    $this->editing_content = $comment->content;
};

$cancelEdit = function () {
    $this->editing_comment_id = null;
    $this->editing_content = '';
};

$saveEdit = function () {
    $validated = $this->validate([
        'editing_comment_id' => ['required', 'string'],
        'editing_content' => ['required', 'string'],
    ]);

    $comment = Comment::query()->findOrFail($validated['editing_comment_id']);

    if ($comment->user_id !== Auth::id()) {
        return;
    }

    app(UpdateCommentAction::class)->execute($comment, $validated['editing_content']);

    $this->editing_comment_id = null;
    $this->editing_content = '';

    $this->refreshComments();
};

$deleteComment = function (string $commentId) {
    $comment = Comment::query()->findOrFail($commentId);

    if ($comment->user_id !== Auth::id()) {
        return;
    }

    app(DeleteCommentAction::class)->execute($comment);

    if ($this->editing_comment_id === $commentId) {
        $this->editing_comment_id = null;
        $this->editing_content = '';
    }

    $this->refreshComments();
};

$replyToReply = function (string $replyId) {
    $reply = Comment::query()->with('author')->findOrFail($replyId);
    $parentId = $reply->parent_comment_id ?? $replyId;

    $this->reply_to = $parentId;
    $this->content = '@' . $reply->author->name . ' ';
};

?>

<div class="px-4 py-6 sm:px-6">
    <h2 class="text-base font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100 mb-5">Comments</h2>

    <!-- Write a comment form -->
    <div class="flex gap-3 mb-8">
        <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-sm">
            @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-lg">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            @endif
        </div>
        <div class="flex-1">
            <form wire:submit.prevent="submit">
                <div class="rounded-3xl bg-zinc-50 dark:bg-zinc-800 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70 focus-within:bg-white dark:focus-within:bg-zinc-900 focus-within:ring-2 focus-within:ring-indigo-500/30 transition-all duration-200">
                    <textarea wire:model="content" rows="2" class="block w-full rounded-3xl border-0 bg-transparent p-3 text-[15px] text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 dark:placeholder:text-zinc-500 focus:outline-none resize-none" placeholder="Post your reply..."></textarea>
                </div>
                <x-input-error :messages="$errors->get('content')" class="mt-1" />
                <div class="flex justify-end mt-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-zinc-900 dark:bg-white px-5 py-2 text-sm font-semibold text-white dark:text-zinc-900 shadow-sm ring-1 ring-black/5 dark:ring-white/10 transition-all duration-200 hover:-translate-y-0.5 hover:bg-zinc-800 dark:hover:bg-zinc-100 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">Reply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Comments List -->
    <div class="space-y-6">
        @forelse (($this->post?->comments?->whereNull('parent_comment_id') ?? collect()) as $comment)
            <div class="flex gap-3">
                <!-- Comment Avatar -->
                <div class="w-10 h-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-sm">
                    @if($comment->author->profile && $comment->author->profile->avatar_url)
                        <img src="{{ $comment->author->profile->avatar_url }}" alt="{{ $comment->author->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-zinc-300 dark:bg-zinc-600 text-zinc-700 dark:text-zinc-300 font-bold text-lg">
                            {{ substr($comment->author->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $comment->author->name }}</span>
                        <span class="text-zinc-400 dark:text-zinc-500 text-sm">·</span>
                        <span class="text-zinc-500 dark:text-zinc-400 text-sm hover:underline decoration-zinc-300 dark:decoration-zinc-600 underline-offset-4 cursor-pointer">{{ $comment->created_at->diffForHumans(null, true, true) }}</span>
                    </div>

                    <div class="text-zinc-900 dark:text-zinc-100 text-[15px] mt-0.5 whitespace-pre-line leading-relaxed">{{ $comment->content }}</div>

                    <div class="flex items-center gap-4 mt-2">
                        <livewire:components.reaction-bar :reactable="$comment" :key="'reaction-bar-comment-'.$comment->id" />

                        <button type="button" wire:click="$set('reply_to', '{{ $comment->id }}')" class="flex items-center gap-1.5 text-zinc-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-200 group text-sm">
                            <div class="p-1.5 rounded-full transition-all duration-200 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 group-active:bg-indigo-100 dark:group-active:bg-indigo-900/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            </div>
                            <span class="font-medium">Reply</span>
                        </button>

                        @if (Auth::id() === $comment->user_id)
                            <div class="flex items-center gap-2 ml-auto">
                                <button type="button" wire:click="startEdit('{{ $comment->id }}')" class="rounded-full px-3 py-1 text-xs font-semibold text-zinc-600 dark:text-zinc-400 transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-zinc-100">Edit</button>
                                <button type="button" wire:click="deleteComment('{{ $comment->id }}')" wire:confirm="Delete this comment?" class="rounded-full px-3 py-1 text-xs font-semibold text-red-500 dark:text-red-400 transition-all duration-200 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-700 dark:hover:text-red-300">Delete</button>
                            </div>
                        @endif
                    </div>

                    <!-- Edit Form -->
                    @if ($editing_comment_id === $comment->id)
                        <form wire:submit.prevent="saveEdit" class="mt-3 mb-2 space-y-2">
                            <div class="rounded-2xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70 focus-within:ring-2 focus-within:ring-indigo-500/30 transition-all duration-200">
                                <textarea wire:model="editing_content" rows="2" class="block w-full rounded-2xl border-0 bg-transparent p-3 text-sm text-zinc-900 dark:text-zinc-100 focus:outline-none resize-none"></textarea>
                            </div>
                            <x-input-error :messages="$errors->get('editing_content')" class="mt-1" />
                            <div class="flex gap-2 justify-end">
                                <button type="button" wire:click="cancelEdit" class="rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 transition-all duration-200">Cancel</button>
                                <button type="submit" class="rounded-full bg-zinc-900 dark:bg-white hover:bg-zinc-800 dark:hover:bg-zinc-100 px-4 py-2 text-xs font-semibold text-white dark:text-zinc-900 transition-all duration-200 shadow-sm ring-1 ring-black/5 dark:ring-white/10">Save</button>
                            </div>
                        </form>
                    @endif

                    <!-- Reply Input Form -->
                    @if ($reply_to === $comment->id)
                        <div class="mt-3 flex gap-2">
                            <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-sm">
                                @if(auth()->user()->profile && auth()->user()->profile->avatar_url)
                                    <img src="{{ auth()->user()->profile->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <form wire:submit.prevent="submit" class="flex-1">
                                <div class="rounded-2xl bg-zinc-50 dark:bg-zinc-800 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70 focus-within:bg-white dark:focus-within:bg-zinc-900 focus-within:ring-2 focus-within:ring-indigo-500/30 transition-all duration-200">
                                    <textarea wire:model="content" rows="2" class="block w-full rounded-2xl border-0 bg-transparent p-3 text-sm text-zinc-900 dark:text-zinc-100 placeholder:text-zinc-400 dark:placeholder:text-zinc-500 focus:outline-none resize-none" placeholder="Post a reply..."></textarea>
                                </div>
                                <div class="flex justify-end gap-2 mt-2">
                                    <button type="button" wire:click="$set('reply_to', null)" class="rounded-full bg-zinc-100 dark:bg-zinc-800 px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all duration-200">Cancel</button>
                                    <button type="submit" class="rounded-full bg-zinc-900 dark:bg-white px-4 py-2 text-xs font-semibold text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all duration-200 shadow-sm ring-1 ring-black/5 dark:ring-white/10">Reply</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Replies -->
                    @if ($comment->replies->count() > 0)
                        <div x-data="{ expanded: true }" class="mt-2">
                            <button
                                type="button"
                                @click="expanded = !expanded"
                                class="text-[13px] font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-all duration-200 flex items-center gap-1"
                            >
                                <span x-text="expanded ? 'Hide replies' : 'Show replies'"></span>
                                <span>({{ $comment->replies->count() }})</span>
                            </button>

                            <div x-show="expanded" class="mt-4 space-y-4 border-l border-zinc-200/70 dark:border-zinc-700/70 pl-4">
                                @foreach ($comment->replies as $reply)
                                    <div class="flex gap-2">
                                        <div class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex-shrink-0 overflow-hidden ring-1 ring-black/5 dark:ring-white/10 shadow-sm">
                                            @if($reply->author->profile && $reply->author->profile->avatar_url)
                                                <img src="{{ $reply->author->profile->avatar_url }}" alt="{{ $reply->author->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-zinc-300 dark:bg-zinc-600 text-zinc-700 dark:text-zinc-300 font-bold text-sm">
                                                    {{ substr($reply->author->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-zinc-900 dark:text-zinc-100 text-[13px]">{{ $reply->author->name }}</span>
                                                <span class="text-zinc-400 dark:text-zinc-500 text-[13px]">·</span>
                                                <span class="text-zinc-500 dark:text-zinc-400 text-[13px] hover:underline decoration-zinc-300 dark:decoration-zinc-600 underline-offset-4 cursor-pointer">{{ $reply->created_at->diffForHumans(null, true, true) }}</span>
                                            </div>
                                            <div class="text-zinc-900 dark:text-zinc-100 text-sm mt-0.5 leading-relaxed">{{ $reply->content }}</div>

                                            <div class="flex items-center gap-4 mt-1">
                                                <livewire:components.reaction-bar :reactable="$reply" :key="'reaction-bar-reply-'.$reply->id" />

                                                <button type="button" wire:click="replyToReply('{{ $reply->id }}')" class="flex items-center gap-1 text-zinc-500 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-200 group text-[13px]">
                                                    <div class="p-1 rounded-full transition-all duration-200 group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/30 group-active:bg-indigo-100 dark:group-active:bg-indigo-900/50">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                                    </div>
                                                    <span class="font-medium">Reply</span>
                                                </button>

                                                @if (Auth::id() === $reply->user_id)
                                                    <div class="flex items-center gap-2 ml-auto">
                                                        <button type="button" wire:click="startEdit('{{ $reply->id }}')" class="rounded-full px-2.5 py-1 text-[11px] font-semibold text-zinc-600 dark:text-zinc-400 transition-all duration-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-zinc-100">Edit</button>
                                                        <button type="button" wire:click="deleteComment('{{ $reply->id }}')" wire:confirm="Delete this reply?" class="rounded-full px-2.5 py-1 text-[11px] font-semibold text-red-500 dark:text-red-400 transition-all duration-200 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-700 dark:hover:text-red-300">Delete</button>
                                                    </div>
                                                @endif
                                            </div>

                                            @if ($editing_comment_id === $reply->id)
                                                <form wire:submit.prevent="saveEdit" class="mt-2 space-y-2">
                                                    <div class="rounded-2xl bg-white dark:bg-zinc-900 ring-1 ring-zinc-200/70 dark:ring-zinc-700/70 focus-within:ring-2 focus-within:ring-indigo-500/30 transition-all duration-200">
                                                        <textarea wire:model="editing_content" rows="2" class="block w-full rounded-2xl border-0 bg-transparent text-sm p-3 text-zinc-900 dark:text-zinc-100 focus:outline-none resize-none"></textarea>
                                                    </div>
                                                    <x-input-error :messages="$errors->get('editing_content')" class="mt-1" />
                                                    <div class="flex gap-2 justify-end">
                                                        <button type="button" wire:click="cancelEdit" class="rounded-full bg-zinc-100 dark:bg-zinc-800 px-4 py-2 text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-all duration-200">Cancel</button>
                                                        <button type="submit" class="rounded-full bg-zinc-900 dark:bg-white px-4 py-2 text-xs font-semibold text-white dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all duration-200 shadow-sm ring-1 ring-black/5 dark:ring-white/10">Save</button>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-10 text-center text-zinc-500 dark:text-zinc-400">
                No comments yet. Be the first to share your thoughts!
            </div>
        @endforelse
    </div>
</div>

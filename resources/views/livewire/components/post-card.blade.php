<article class="px-4 py-4 sm:px-6 cursor-pointer transition-all duration-200 border-b border-zinc-200/60 last:border-b-0 hover:bg-zinc-50/70">
    <div class="flex gap-3 sm:gap-4">
        <!-- Avatar -->
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-zinc-200 flex-shrink-0 overflow-hidden ring-1 ring-black/5 shadow-sm">
            @if($post->author->profile && $post->author->profile->avatar_url)
                <img src="{{ $post->author->profile->avatar_url }}" alt="{{ $post->author->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center bg-indigo-100 text-indigo-700 font-bold text-lg">
                    {{ substr($post->author->name, 0, 1) }}
                </div>
            @endif
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <!-- Header -->
            <header class="flex items-start justify-between mb-1.5">
                <div class="flex flex-wrap items-center gap-x-2 text-sm sm:text-[15px]">
                    <a href="{{ route('profile.show', ['id' => $post->author->id]) }}" class="font-bold text-zinc-900 hover:underline truncate max-w-full decoration-zinc-300 underline-offset-4" wire:navigate @click.stop>
                        {{ $post->author->name }}
                    </a>
                    <span class="text-zinc-500 truncate max-w-full hidden sm:inline">
                        {{ $post->author->username ?? '@'.strtolower(str_replace(' ', '', $post->author->name)) }}
                    </span>
                    <span class="text-zinc-400">·</span>
                    <a href="{{ route('posts.show', ['id' => $post->id]) }}" class="text-zinc-500 hover:underline whitespace-nowrap decoration-zinc-300 underline-offset-4" wire:navigate @click.stop>
                        {{ $post->created_at->diffForHumans(null, true, true) }}
                    </a>
                </div>
                
                <!-- Options / Open -->
                <a href="{{ route('posts.show', ['id' => $post->id]) }}" class="text-zinc-400 transition-all duration-200 p-2 -mr-2 rounded-full hover:bg-zinc-100 hover:text-indigo-600 active:bg-zinc-200/60" wire:navigate @click.stop title="View post">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </header>

            <!-- Text Content -->
            @if (! empty($post->content))
                <p class="text-zinc-900 text-[15px] sm:text-[15px] whitespace-pre-line leading-relaxed mb-3">{{ $post->content }}</p>
            @endif

            <!-- Media -->
            @if($post->media->count() > 0)
                <div class="mb-3 rounded-3xl overflow-hidden" @click.stop>
                    @include('livewire.components.media-gallery', ['post' => $post])
                </div>
            @endif

            <!-- Action Bar (Reactions & Comments) -->
            <div class="flex items-center justify-between text-zinc-500 max-w-md mt-1" @click.stop>
                <a href="{{ route('posts.show', ['id' => $post->id]) }}" wire:navigate class="flex items-center gap-2 group transition-all duration-200">
                    <div class="p-2 rounded-full transition-all duration-200 group-hover:bg-indigo-50 group-hover:text-indigo-600 group-active:bg-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <span class="text-sm font-medium transition-all duration-200 group-hover:text-indigo-600">
                        {{ $post->comments_count ?? $post->comments()->count() }}
                    </span>
                </a>

                <div class="flex-1">
                    <livewire:components.reaction-bar :reactable="$post" :key="'reaction-bar-post-card-'.$post->id" />
                </div>
                
                <button class="flex items-center gap-2 group transition-all duration-200 p-2 rounded-full hover:bg-indigo-50 hover:text-indigo-600 active:bg-indigo-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                </button>
            </div>
        </div>
    </div>
</article>

<?php

use App\Domain\Engagement\Actions\ToggleReactionAction;
use App\Domain\Engagement\Enums\ReactionType;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\computed;
use function Livewire\Volt\state;

state([
    'reactable' => null,
]);

$counts = computed(function () {
    if (! $this->reactable || ! method_exists($this->reactable, 'reactions')) {
        return collect();
    }

    return $this->reactable->reactions()
        ->selectRaw('type, COUNT(*) as count')
        ->groupBy('type')
        ->pluck('count', 'type');
});

$currentReaction = computed(function () {
    if (! $this->reactable || ! method_exists($this->reactable, 'reactions')) {
        return null;
    }

    $userId = Auth::id();

    if ($userId === null) {
        return null;
    }

    return $this->reactable->reactions()
        ->where('user_id', $userId)
        ->value('type');
});

$toggle = function (string $type) {
    if (! Auth::check()) {
        return;
    }

    if (! $this->reactable || ! method_exists($this->reactable, 'reactions')) {
        return;
    }

    app(ToggleReactionAction::class)->execute(
        Auth::user(),
        $this->reactable,
        ReactionType::from($type)
    );

    unset($this->counts, $this->currentReaction);
};

?>

<div>
@auth
<div class="flex items-center gap-2" @click.stop>
    <div x-data="{ open: false }" class="relative">
        <button
            type="button"
            @click="open = !open"
            class="group flex items-center gap-1.5 rounded-full px-2 py-1.5 text-sm font-medium ring-1 ring-transparent transition-all duration-200 {{ $this->currentReaction ? 'text-pink-600 bg-pink-50/60 ring-pink-200/60 hover:bg-pink-50' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-800 hover:ring-zinc-200/60' }}"
        >
            <div class="p-1 rounded-full transition-all duration-200 {{ $this->currentReaction ? 'bg-pink-50' : 'group-hover:bg-zinc-200/40' }}">
                <svg class="w-5 h-5 {{ $this->currentReaction ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </div>
            
            <span class="transition-all duration-200 {{ $this->currentReaction ? '' : 'group-hover:text-zinc-800' }}">
                {{ collect($this->counts)->sum() > 0 ? collect($this->counts)->sum() : '' }}
            </span>
        </button>

        <div
            x-cloak
            x-show="open"
            @click.outside="open = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-20 mt-2 w-56 rounded-2xl border border-zinc-200/70 bg-white/90 p-2 shadow-xl shadow-black/10 ring-1 ring-black/5 backdrop-blur-xl"
            style="display: none;"
        >
            <div class="flex flex-col gap-1">
                @foreach (['like', 'love', 'laugh', 'wow', 'sad', 'angry'] as $type)
                    <button
                        type="button"
                        wire:click="toggle('{{ $type }}')"
                        @click="open = false"
                        class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-sm font-medium transition-all duration-200 {{ $this->currentReaction === $type ? 'bg-pink-50 text-pink-700 ring-1 ring-pink-200/70' : 'text-zinc-700 hover:bg-zinc-100 hover:ring-1 hover:ring-zinc-200/70' }}"
                    >
                        <span class="flex items-center gap-2">
                            @if($type === 'like') 👍 
                            @elseif($type === 'love') ❤️ 
                            @elseif($type === 'laugh') 😂 
                            @elseif($type === 'wow') 😮 
                            @elseif($type === 'sad') 😢 
                            @elseif($type === 'angry') 😡 
                            @endif
                            {{ ucfirst($type) }}
                        </span>
                        @if(($this->counts[$type] ?? 0) > 0)
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600 ring-1 ring-zinc-200/60">{{ $this->counts[$type] }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endauth
</div>

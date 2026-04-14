<?php

namespace App\Domain\Content\Models;

use App\Domain\Engagement\Models\Comment;
use App\Domain\Engagement\Models\Reaction;
use App\Models\User;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'user_id',
        'content',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class)->orderBy('display_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function scopeForFeed(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount('reactions')->orderByDesc('reactions_count');
    }

    protected static function newFactory(): PostFactory
    {
        return PostFactory::new();
    }
}

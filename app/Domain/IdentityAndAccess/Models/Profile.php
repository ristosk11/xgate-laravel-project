<?php

namespace App\Domain\IdentityAndAccess\Models;

use App\Models\User;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'user_id',
        'bio',
        'avatar_path',
        'cover_image_path',
        'location',
        'website',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path === null) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image_path === null) {
            return null;
        }

        return Storage::disk('public')->url($this->cover_image_path);
    }

    protected static function newFactory(): ProfileFactory
    {
        return ProfileFactory::new();
    }
}

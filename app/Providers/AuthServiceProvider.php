<?php

namespace App\Providers;

use App\Domain\Content\Models\Post;
use App\Domain\Content\Policies\PostPolicy;
use App\Domain\IdentityAndAccess\Models\Profile;
use App\Domain\IdentityAndAccess\Policies\ProfilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Profile::class => ProfilePolicy::class,
        Post::class => PostPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

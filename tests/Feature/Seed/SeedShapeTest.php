<?php

namespace Tests\Feature\Seed;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class SeedShapeTest extends TestCase
{
    public function test_reseed_creates_exactly_three_example_users_and_each_has_at_least_two_posts(): void
    {
        Artisan::call('migrate:fresh', ['--seed' => true]);

        $requiredExampleHandles = ['@design', '@frontend', '@laravel'];

        $exampleUsers = User::query()
            ->get()
            ->filter(function (User $user) use ($requiredExampleHandles): bool {
                $handle = $this->userHandle($user);

                return in_array($handle, $requiredExampleHandles, true);
            })
            ->values();

        $this->assertCount(
            3,
            $exampleUsers,
            'Expected exactly 3 seeded example users (@design/@frontend/@laravel).'
        );

        $actualHandles = $exampleUsers
            ->map(fn (User $user): string => $this->userHandle($user))
            ->sort()
            ->values()
            ->all();

        $this->assertSame(collect($requiredExampleHandles)->sort()->values()->all(), $actualHandles);

        foreach ($exampleUsers as $user) {
            $this->assertGreaterThanOrEqual(
                2,
                $user->posts()->count(),
                sprintf('Expected %s to have at least 2 posts.', $this->userHandle($user))
            );
        }
    }

    private function userHandle(User $user): string
    {
        $username = $user->getAttribute('username');
        if (is_string($username) && $username !== '') {
            return $username;
        }

        return '@'.Str::of((string) $user->name)->lower()->replace(' ', '')->toString();
    }
}

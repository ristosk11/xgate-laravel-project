## 2026-04-15 Task: initialization
- Plan requires strict TDD per slice.
- Seed must be reset to baseline + exactly @design/@frontend/@laravel.
- Media assets must be real files on disk (avoid console spam).

## 2026-04-15 Task: hashtag inventory + failing UI assertions
- Tests are PHPUnit Feature tests (class-based) per `phpunit.xml` Feature suite conventions.
- Feed accessed via `GET /feed` (matches existing test patterns after `actingAs()`).
- Assertion is scoped to the literal `#Laravel12` to avoid false positives from hex color strings like `#FF2D20` in templates.

## 2026-04-15 Task: seeder reset design + failing seed-shape test
- Seed baseline currently creates 12 random users + a test user, and random 2–4 posts per user (DatabaseSeeder).
- Seed shape contract test already exists at `tests/Feature/Seed/SeedShapeTest.php` and is RED right now (0 matching handles) because seeded users do not include @design/@frontend/@laravel.
- Handle logic in the test: use `username` attribute if present; else derive from name: `'@' + lowercase(name with spaces removed)`.

## 2026-04-15 Task: share behavior contract tests
- Share buttons exist in both `resources/views/livewire/components/post-card.blade.php` and `resources/views/livewire/pages/posts/show.blade.php` but have no contract (no `data-*` attribute, no `wire:click`) yet.
- Chosen server-side contract for TDD: share button should expose `data-share-url="{route('posts.show', id)}"` so frontend JS can copy URL + show toast.

## 2026-04-15 Task: seed reset shape (RED)
- `users` table has no `username`/`handle` column in migrations; display logic falls back to `@` + lowercased `name` with spaces removed.
- `SeedShapeTest` identifies example users by that handle rule (using `$user->getAttribute('username')` if present, else fallback) and asserts only `@design/@frontend/@laravel` are present as example users.

## 2026-04-15 Task: share contract test (RED)
- Chosen server-side contract: a share control must expose `data-share-url="<canonical post URL>"` on both the feed post card and the post show page.
- Canonical URL is defined as `route('posts.show', ['id' => $post->id])` (stable, backend-owned), enabling frontend clipboard copy + success toast without asserting brittle classes/JS.

## 2026-04-15 Task: sidebar follow behavior test (RED)
- Chosen server-side contract for the right sidebar suggestions: the feed page HTML must include exactly the three example handles `@design/@frontend/@laravel` and each suggestion must have a Follow control wired to `POST` the follow endpoint `route('profile.follow', ['user' => <id>])` (asserted as a `<form method="POST" action="...">` per example user).
- Handles for fixtures are ensured by creating users with `name` values `Design/Frontend/Laravel`, which the existing handle derivation maps to `@design/@frontend/@laravel` when no `username` attribute exists.

## 2026-04-15 Task: remove hashtag UI labels (T5)
- Updated `resources/views/layouts/app.blade.php` Trending item label from `#Laravel12` to plain text `Laravel12` to avoid user-facing hashtag UI while preserving the existing layout.

## 2026-04-15 Task: minimal deterministic reseed (T6)
- Replaced `DatabaseSeeder` with a deterministic seed: exactly 3 users named `Design/Frontend/Laravel` (emails `design/frontend/laravel@example.test`) and exactly 2 posts per user with fixed content.
- Removed prior random/demo seeding (12 users + follows/comments/reactions/media) and stopped calling TestUserSeeder.
- Verified `SeedShapeTest` passes after `migrate:fresh --seed`.

## 2026-04-15 Task: deterministic seed fixtures (T6)
- Replaced the previous random seeding (12 users + random posts/media/comments/reactions/follows) with a minimal deterministic `ExampleUsersSeeder` called from `DatabaseSeeder`.
- Seed now creates exactly three example users by name (`Design`, `Frontend`, `Laravel`) so handle derivation produces `@design/@frontend/@laravel`, each with exactly 2 deterministic posts.

## 2026-04-15 Task: share copy-link + toast (T7)
- Implemented a minimal global toast in `layouts/app.blade.php` using Alpine: it listens for `toast` window events and auto-dismisses after ~2.6s.
- Share buttons expose `data-share-url="{{ route('posts.show', ['id' => $post->id]) }}"`; a small click delegate copies that URL to clipboard (with an execCommand fallback) and dispatches success/error toast.

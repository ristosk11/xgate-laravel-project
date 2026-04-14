# Project Context — Mini Social Media Platform

## Quick Summary
- Build a mini social media app with profiles, posts (image/video), reactions, comments, and follow/unfollow.
- Core stack: Laravel 12, Livewire Volt, Tailwind 4, Alpine.js, MySQL, PHP 8.2+, Pest/PHPUnit.
- Architecture style: DDD with three bounded contexts:
  - `IdentityAndAccess` (users, profiles, follows)
  - `Content` (posts, media, feed)
  - `Engagement` (reactions, comments)

## Critical Domain Rules
- Reactions are **polymorphic** (`reactable_type`, `reactable_id`) and work for both posts and comments.
- One reaction per user per reactable item (unique on `user_id + reactable_type + reactable_id`).
- Reaction toggle behavior:
  - no existing reaction → create
  - same type → delete (toggle off)
  - different type → update
- Comments allow one-level replies via `parent_comment_id`.
- Prevent self-follow; `follows` has unique `follower_id + following_id`.

## Feed Rules
- Main feed: posts from followed users, newest first.
- Include author/profile, media, grouped reaction counts, and comment count.
- Fallback discover feed: recent popular posts when user follows nobody.
- Paginate at ~15 posts per page.

## Media Rules
- Post media supports images and videos.
- Image validation: `jpg/png/webp`, max 5MB each.
- Video validation: `mp4/webm`, max 50MB each.
- Store files via Laravel `Storage` on `public` disk; persist metadata in `post_media`.

## Engineering Conventions
- Thin controllers; business logic in Actions/Services; use DTOs (no raw Request into Actions).
- Use enums for typed values (e.g., `ReactionType`, `MediaType`) instead of raw strings.
- Use ULIDs and constrained foreign keys with cascade behavior.
- Keep model relationships/scopes in models; complex queries in services.

## Minimum Test Coverage Targets
- Reaction toggle (create/change/remove + grouped counts)
- Nested comment creation (one-level replies)
- Feed shows only followed users’ posts
- Media validation rejects oversized/invalid files
- Self-follow prevention

## Suggested Delivery Order
1. Migrations + models/factories/seeders
2. Actions/DTOs/services for profiles, posts/media, feed, reactions, comments, follows
3. Volt UI components and routes
4. Feature + unit tests
5. Final verification (tests, lint/style, app sanity checks)

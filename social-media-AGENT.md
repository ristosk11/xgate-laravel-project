# AGENT.md — Mini Social Media Platform

> Drop this file in your project root. Your AI coding agent (Claude Code, Cursor, Copilot, etc.) will use it to understand your project's architecture, conventions, and rules — so every suggestion it makes already follows your standards.

---

## Project Overview

**Name:** Mini Social Media Platform
**Stack:** Laravel 12.x, Livewire 3 (Volt), Tailwind CSS 4.x, Alpine.js 3.x, MySQL
**Architecture:** Domain-Driven Design (DDD)
**PHP Version:** 8.2+
**Testing:** Pest / PHPUnit

---

## Domain Map

This app has four bounded contexts (business domains):

| Domain | Responsibility | Key Models |
|---|---|---|
| **IdentityAndAccess** | Users, authentication, profiles, follow system | `User`, `Profile`, `Follow` |
| **Content** | Posts, media uploads, feed generation | `Post`, `PostMedia` |
| **Engagement** | Reactions (polymorphic), comments, nested replies | `Reaction`, `Comment` |

### Key Relationships

```
User ──has one──▶ Profile (bio, avatar, cover image)
User ──has many──▶ Post
Post ──has many──▶ PostMedia (images, videos)
Post ──has many──▶ Comment
Comment ──belongs to──▶ Post
Comment ──belongs to──▶ Comment (parent, one level only)
Post ──morph many──▶ Reaction (polymorphic)
Comment ──morph many──▶ Reaction (polymorphic)
User ──has many──▶ Follow (as follower)
User ──has many──▶ Follow (as following)
```

### The Polymorphic Pattern

This is the most important design decision in the project. Reactions use a **polymorphic** relationship so one `reactions` table handles both posts and comments:

```
reactions table:
- id (ulid)
- user_id (foreign key)
- reactable_type ("Post" or "Comment")
- reactable_id (ulid of the post or comment)
- type (enum: like, love, laugh, wow, sad, angry)
- unique constraint on [user_id, reactable_type, reactable_id]
```

In Laravel, this is implemented with `morphMany` on Post/Comment and `morphTo` on Reaction:

```php
// Post model & Comment model both have:
public function reactions(): MorphMany
{
    return $this->morphMany(Reaction::class, 'reactable');
}

// Reaction model has:
public function reactable(): MorphTo
{
    return $this->morphTo();
}
```

---

## Directory Structure

```
app/
├── Domain/
│   ├── IdentityAndAccess/
│   │   ├── Actions/              # RegisterUserAction, UpdateProfileAction, ToggleFollowAction
│   │   ├── DTOs/                 # UpdateProfileDTO
│   │   ├── Models/               # User, Profile, Follow
│   │   ├── Policies/
│   │   └── Services/             # FollowService
│   ├── Content/
│   │   ├── Actions/              # CreatePostAction, DeletePostAction
│   │   ├── DTOs/                 # CreatePostDTO
│   │   ├── Enums/                # MediaType (image, video)
│   │   ├── Models/               # Post, PostMedia
│   │   ├── Policies/             # PostPolicy
│   │   └── Services/             # MediaUploadService, FeedService
│   └── Engagement/
│       ├── Actions/              # ToggleReactionAction, CreateCommentAction
│       ├── DTOs/                 # CreateCommentDTO
│       ├── Enums/                # ReactionType (like, love, laugh, wow, sad, angry)
│       ├── Models/               # Reaction, Comment
│       └── Services/             # ReactionCountService
├── Http/
│   ├── Controllers/
│   └── Middleware/
resources/
├── views/
│   ├── livewire/
│   │   ├── feed/                 # index (main feed), discover (fallback)
│   │   ├── posts/                # create, show (with comments)
│   │   ├── profile/              # show, edit
│   │   └── components/           # post-card, reaction-bar, comment-thread, media-gallery
│   ├── layouts/
│   └── components/
storage/
├── app/public/
│   ├── avatars/
│   ├── covers/
│   └── posts/                    # Post media uploads
database/
├── migrations/
├── seeders/
└── factories/
tests/
├── Feature/
└── Unit/
```

---

## Conventions & Rules

### Naming

- **Models:** Singular PascalCase → `Post`, `PostMedia`, `Reaction`, `Comment`
- **Tables:** Plural snake_case → `posts`, `post_media`, `reactions`, `comments`, `follows`
- **Actions:** Verb + Noun + "Action" → `CreatePostAction`, `ToggleReactionAction`, `ToggleFollowAction`
- **DTOs:** Noun + "DTO" → `CreatePostDTO`, `CreateCommentDTO`
- **Enums:** Noun → `ReactionType`, `MediaType`
- **Services:** Noun + "Service" → `MediaUploadService`, `FeedService`

### Architecture Rules

1. **Controllers are thin.** Validate, call an Action, return a response.
2. **Actions do one thing.** `CreatePostAction` creates the post and associates media. `ToggleReactionAction` handles the create/update/delete toggle logic.
3. **Services handle complex queries.** `FeedService` builds the feed query with eager loading. `MediaUploadService` handles validation, storage, and metadata.
4. **Models own relationships and scopes.** `Post::scopeForFeed($userId)`, `Post::scopePopular()`.
5. **DTOs carry data.** Never pass raw requests into Actions.
6. **Enums for types.** `ReactionType`, `MediaType` — never raw strings.

### Database

- **Primary keys:** ULIDs → `$table->ulid('id')->primary()`
- **Foreign keys:** Always constrained → `$table->foreignUlid('user_id')->constrained()->cascadeOnDelete()`
- **Polymorphic columns:** `reactable_type` (string) + `reactable_id` (ulid). Add a composite index on `[reactable_type, reactable_id]`.
- **Unique constraints:** `reactions` table has a unique constraint on `[user_id, reactable_type, reactable_id]` — one reaction per user per entity.
- **Cascade deletes:** When a post is deleted, cascade to `post_media`, `reactions`, and `comments`. When a comment is deleted, cascade to its `reactions` and child `comments`.
- **Indexes:** On `user_id`, `post_id`, `parent_comment_id`, `reactable_type + reactable_id`, `created_at` (for feed ordering).

### Business Rules

**Posts & Media:**
- A post can have zero or more media attachments.
- Image validation: jpg, png, webp. Max 5 MB each.
- Video validation: mp4, webm. Max 50 MB each.
- Media files are stored using Laravel's Storage facade (`public` disk). Store the path in `post_media.file_path`.
- `PostMedia` has a `display_order` integer for carousel ordering and optional `alt_text`.

**Feed:**
- Feed = posts from followed users, ordered by `created_at DESC`.
- Eager load: `author.profile`, `media`, `reactions` (grouped counts), `comments` (count).
- If the user follows nobody, fall back to a "discover" feed: recent posts ordered by reaction count (popular first).
- Paginate with 15 posts per page (cursor or offset — your choice).

**Reactions (Toggle Logic):**
- A user has at most ONE reaction per reactable entity.
- If no reaction exists → create it.
- If a reaction with the SAME type exists → delete it (toggle off).
- If a reaction with a DIFFERENT type exists → update the type.
- Return the updated reaction counts grouped by type after every toggle.

**Comments:**
- Comments belong to a post. They can reply to a parent comment (one level only).
- If a comment has `parent_comment_id`, it's a reply. Replies to replies should be stored flat under the original parent.
- Comments are displayed oldest-first. Replies are nested under their parent.
- Users can edit and delete their own comments.

**Follows:**
- A user cannot follow themselves. Validate and reject.
- `follows` table: `follower_id` + `following_id` with a unique constraint.
- Expose `followers_count` and `following_count` on the profile (use `withCount`).

### Frontend / UI

- **Livewire Volt** single-file components.
- **Tailwind CSS** only.
- **Alpine.js** for: image gallery/carousel, reaction picker dropdown, comment expand/collapse.
- Media gallery: show images in a grid (1 image = full width, 2 = side by side, 3+ = grid). Video gets a `<video>` tag with controls.

### Testing

- **Feature tests** for: reaction toggle (create, change type, remove), nested comment creation, feed returns only followed users' posts, media validation rejects oversized files, self-follow returns 422.
- **Unit tests** for: `ToggleReactionAction` (all three paths), `FeedService`, `MediaUploadService` (validation).
- Use `RefreshDatabase` trait. Use Factories with varied data.

### Git

- Imperative mood, max 72 chars: `Add polymorphic reactions with toggle logic`
- Branch names: `feature/polymorphic-reactions`, `fix/feed-pagination`
- One logical change per commit.

---

## Common Commands

```bash
php artisan serve                          # Start dev server
php artisan migrate:fresh --seed           # Reset DB with sample data
php artisan storage:link                   # Create public storage symlink (for media)
php artisan make:migration create_X_table  # New migration
php artisan test                           # Run all tests
php artisan test --filter=ReactionTest     # Run specific test
./vendor/bin/pint                          # Fix code style
php artisan optimize:clear                 # Clear all caches
```

---

## When Generating Code

- Place models under `app/Domain/{Context}/Models/`.
- Always create a Factory alongside a new Model.
- Feature order: Migration → Model → Factory → Seeder → Action/DTO → Volt Component → Route → Test.
- Never `$guarded = []`. Always explicit `$fillable`.
- Always type-hint parameters and return types.
- Use `morphMany` / `morphTo` for reactions — never separate reaction tables.
- Wrap multi-step writes in `DB::transaction()`.
- Use the Storage facade for file uploads — never move files manually.

---

## Example Prompts

Here are prompts that work well with this project's architecture:

**Scaffolding a feature:**
> "Create the Engagement domain: migration for `reactions` with `user_id`, `reactable_type`, `reactable_id`, and `type` (enum). Add a unique constraint on `[user_id, reactable_type, reactable_id]`. Create the Reaction model with `morphTo` reactable relationship, a `scopeForUser` scope, and cast `type` to `ReactionType` enum. Create the `ReactionType` enum with cases: like, love, laugh, wow, sad, angry."

**Building business logic:**
> "Create a `ToggleReactionAction` in the Engagement domain. It accepts a User, a reactable model (Post or Comment), and a ReactionType. Logic: (1) find existing reaction for this user + reactable, (2) if none exists, create one with the given type, (3) if same type exists, delete it, (4) if different type exists, update to the new type. Return an array of reaction counts grouped by type (e.g. `['like' => 12, 'love' => 3]`). Write it as a clean, testable class."

**Building the feed:**
> "Create a `FeedService` in the Content domain with a `getFeed(User $user, int $perPage = 15)` method. It should: get IDs of users the given user follows, query posts by those authors ordered by created_at DESC, eager load `author.profile`, `media`, and withCount for `comments`. Also include a `reactionSummary` that returns counts grouped by reaction type. If the user follows nobody, fall back to posts ordered by total reaction count (popular). Return a paginated result."

**Writing tests:**
> "Write Feature tests for reaction toggling. Test: (1) user can react to a post — reaction is created with correct type, (2) user reacts with same type again — reaction is deleted (toggle off), (3) user changes reaction type — existing reaction is updated, (4) reaction counts are correct after each operation, (5) user can react to a comment (polymorphic works for both)."

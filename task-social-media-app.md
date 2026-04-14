# Engineering Task: Mini Social Media Platform

## Overview

Build a small social media application where users create **profiles**, publish **posts** (with image and video support), and interact through **reactions** and **comments**. The goal is to assess your ability to handle media uploads, polymorphic relationships, and feed-building logic in a clean, well-structured codebase.

---

## Scope

**In scope:**

- User profiles (bio, avatar, cover image)
- Posts supporting both images and videos (upload & display)
- A feed showing posts from followed users, ordered by recency
- Reactions on posts (like, love, laugh, etc. — not just a binary like)
- Nested comments on posts (one level of replies)
- Follow/unfollow system
- Basic profile pages showing a user's posts and follower/following counts

**Out of scope:**

- Real-time updates (WebSockets / push notifications)
- Direct messaging
- Stories or disappearing content
- Content moderation / reporting
- Deployment / CI-CD

---

## Tasks

### 1. Data Modelling & Database

- Design the schema for: `users`, `profiles`, `posts`, `post_media`, `reactions`, `comments`, `follows`.
- A user has one profile (bio, avatar URL, cover image URL, location, website).
- Posts have text content and can have zero or more media attachments.
- `post_media` stores the file path/URL, `type` (image or video), display order, and an optional `alt_text`.
- Reactions are polymorphic — they can belong to a post or a comment. Store the reaction `type` (like, love, laugh, wow, sad, angry).
- Comments belong to a post and can optionally reference a `parent_comment_id` for one-level nesting.
- The `follows` table tracks follower → following relationships.
- Implement migrations and seeders with realistic sample data (10+ users, 30+ posts, mixed media).

### 2. Authentication & Profile Management

- Implement registration and login.
- Users can edit their profile: bio, avatar, cover image, location, website.
- Handle avatar and cover image uploads (store on disk or use a configurable storage driver).
- Display a public profile page with the user's posts, follower count, and following count.

### 3. Post Creation & Media Handling

- Users can create a post with text and attach one or more images/videos.
- Validate media: images must be jpg/png/webp (max 5 MB each), videos must be mp4/webm (max 50 MB each).
- Store media files and persist their metadata in `post_media`.
- Users can edit post text and delete their own posts (cascade-delete media, reactions, comments).
- Display posts with their media in a gallery/carousel-style layout.

### 4. Feed

- Build a feed endpoint/page that returns posts from users the authenticated user follows, ordered newest-first.
- Include pagination (cursor-based or offset-based).
- Each post in the feed should include: author info, media, reaction counts grouped by type, and comment count.
- If a user follows nobody, show a "discover" feed of recent popular posts as a fallback.

### 5. Reactions

- Users can react to a post or comment with one of: `like`, `love`, `laugh`, `wow`, `sad`, `angry`.
- A user can have only **one** reaction per post/comment — reacting again changes the type; reacting with the same type removes it (toggle behaviour).
- Display reaction counts grouped by type (e.g. 12 likes, 3 loves, 1 laugh).
- Show which reaction the current user has given, if any.

### 6. Comments

- Users can comment on a post.
- Users can reply to a comment (one level deep — replies to replies are flat under the parent).
- Comments can also receive reactions (reuse the polymorphic reaction system).
- Display comments under a post ordered by oldest-first, with replies nested under their parent.
- Users can edit and delete their own comments.

### 7. Follow System

- Users can follow and unfollow other users.
- Prevent self-follow.
- Expose follower and following lists on the profile page.
- The follow state should be reflected in the UI (follow/unfollow button toggle).

### 8. Tests

- Write at least **5 meaningful tests** covering: reaction toggle behaviour, nested comment creation, feed only showing followed users' posts, media validation rejection, and self-follow prevention.

---

## Hints

### Getting Started (Read This First)

If you're staring at a blank project and don't know where to begin — that's normal. Here's a concrete order of operations:

1. **Set up the project** — scaffold a fresh app in your framework of choice, connect a database, and confirm you can serve the app locally before writing any business logic.
2. **Sketch the schema on paper.** Draw boxes for `users`, `profiles`, `posts`, `post_media`, `reactions`, `comments`, `follows`. Label the lines: "has many", "belongs to", "polymorphic". Pay special attention to the `reactions` table — it's the trickiest one. This 15-minute exercise saves hours.
3. **Build vertically, not horizontally.** Don't write all migrations, then all models, then all controllers. Instead, pick one feature (e.g. "user can create a post with an image"), build it from database to UI, test it, commit it, move to the next feature.

### Architecture & Code Organisation

- **Polymorphic reactions are the most interesting design decision.** If you've never used polymorphism before: instead of making separate `post_reactions` and `comment_reactions` tables, you make one `reactions` table with `reactable_type` (e.g. `"Post"` or `"Comment"`) and `reactable_id`. This way one table handles reactions on any model. Most frameworks have built-in support for this pattern — look up "polymorphic relationships" in your framework's docs.
- **Handle media uploads as a separate concern.** Create a dedicated `MediaUploadService` or `StoreMediaAction` class. It should handle validation, storage, and metadata persistence. This keeps your post creation logic clean and makes it easy to reuse for avatar/cover image uploads too.
- **Don't overthink the feed query.** A simple `WHERE author_id IN (followed_user_ids) ORDER BY created_at DESC` with eager-loaded counts is perfectly fine at this scale. No need for fan-out, Redis, or any caching layer. Keep it simple.
- **Reaction toggle logic is easy to get wrong.** Write it as a single small method: "given a user, a reactable entity, and a reaction type → if no existing reaction, create one; if same type exists, remove it; if different type exists, update it." Test all three paths.

### Data & Seeding

- **Seed with variety.** Mix posts with: no media, one image, multiple images, a video, text-only. This surfaces rendering edge cases in the feed and gallery views that you'd never catch with uniform test data.
- **Create enough users and follows** to make the feed meaningful. 10+ users where each follows 3–5 others gives you a realistic feed with mixed content.
- **Use placeholder images.** Services like `https://picsum.photos/400/300` give you random images you can use as seed data URLs. For video, just use a static sample MP4 URL.

### Edge Cases to Think About

Don't just handle the happy path. Ask yourself these questions before you submit:

- What if a user you follow deletes their account — does the feed query break with a missing foreign key?
- What happens to reactions and comments when a post is deleted? (Hint: cascade deletes)
- What if someone uploads a 200 MB video? Your validation should reject it before it eats server memory.
- What if a user tries to follow themselves?
- Can someone react to a comment on a post they can't see? Does that matter in your design?

You don't need to solve all of these perfectly, but **acknowledging them** (even with a `// TODO: consider cascade behaviour` comment) shows awareness.

### Using AI Tools (Encouraged)

We **actively encourage** you to use AI coding assistants during this task. In fact, how effectively you use them is something we evaluate. Here's how to get the most out of them:

- **Set up an `AGENT.md` file** in your project root. This file tells your AI agent about your project's architecture, naming conventions, directory structure, and coding standards. Without it, your AI will make generic guesses. With it, every suggestion follows your rules. We've included a sample `AGENT.md` alongside this task — use it as a starting point and adapt it to your choices.
- **Use agents like Claude Code, Cursor, GitHub Copilot, or similar.** These tools are especially good at: generating migrations and polymorphic model relationships, writing file upload validation logic, scaffolding CRUD operations, building feed queries with eager loading, and writing test boilerplate.
- **Be specific in your prompts.** Instead of "make reactions work", try: "Create a `ToggleReactionAction` that accepts a User, a reactable model (Post or Comment via polymorphism), and a reaction type enum. If no reaction exists, create one. If the same type exists, delete it (toggle off). If a different type exists, update it. Return the updated reaction counts grouped by type." The more context you give, the better the output.
- **Don't blindly accept generated code.** Always read what the AI produces. Does the polymorphic setup actually work? Is the toggle logic handling all three cases? Are media files being validated before storage? The AI is your junior pair-programmer — you're still the senior in charge.
- **Use AI for debugging too.** File upload not working? Feed showing posts from unfollowed users? Paste the full error or unexpected behaviour into your AI agent and ask it to diagnose. This is often faster than trial-and-error.
- **Commit AI-generated code with the same standards as hand-written code.** We don't distinguish between the two — we care about the result. If generated code is sloppy, refactor it before committing.

### Git Workflow

- **Commit early, commit often.** Each feature slice should be its own commit: "Add user profiles with avatar upload", "Implement polymorphic reactions with toggle", "Build feed with pagination", etc.
- **Write clear commit messages** in imperative mood: "Add reaction toggle with polymorphic support" not "reactions done" or "WIP".
- **If you mess up, don't panic.** `git stash`, `git reset --soft HEAD~1`, and `git revert` are your friends. Ask your AI agent how to undo things safely.

---

## Evaluation Criteria

| Area | Weight |
|---|---|
| Code structure & readability | 25% |
| Domain modelling & relationships | 20% |
| Media handling & validation | 15% |
| Business logic correctness (reactions, feed, follows) | 20% |
| Test quality & coverage | 10% |
| Git hygiene (clear commits, sensible history) | 10% |
| AI tool usage (AGENT.md, prompt quality, not blindly accepting output) | Bonus |

---

## Included Files

You should have received the following alongside this task:

- **This document** — your task specification
- **`AGENT.md`** — a sample AI agent configuration file. Copy it into your project root, read through it, and adapt it to match your actual tech stack and architecture decisions. This is the single most impactful thing you can do to improve your AI-assisted workflow.

---

**Time guideline:** ~6–8 hours

Good luck!

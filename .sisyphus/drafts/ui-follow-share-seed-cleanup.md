# Draft: UI Follow/Share + Sidebar Seed Cleanup

## Requirements (confirmed)
- Remove hashtags from the right sidebar trending section.
- Remove hashtag usage everywhere in UI (not just sidebar).
- Add the right-sidebar sample users as real example users in data.
- Seed those users with a couple of example posts each.
- Fix sharing UI so share controls actually do something.
- Fix following UI so sidebar follow actions work.
- Add small random placeholder images for posts that have media.
- Reseed database and replace current seeded dataset.
- Use these example users/profiles explicitly: `@design`, `@frontend`, `@laravel`.
- Share behavior approved: copy post URL to clipboard + toast feedback.
- Media assets: use 2-3 internet images (<=10MB each), assign randomly to seeded posts.

## Technical Decisions
- Scope decision: hashtags are being removed from UI globally.
- Data decision: current seeded data will be replaced via reseed workflow.
- Share decision: lightweight copy-link UX is acceptable.
- Sidebar decision: sidebar follow section should be backed by real DB users and real follow actions.

## Research Findings
- None yet (pending user clarification before targeted exploration).

## Open Questions
- Test strategy choice still needed (TDD vs tests-after vs no automated tests).
- Clarify seed dataset shape after wipe: keep only these 3 sample users (+ auth baseline), or include broader realistic network too.
- Clarify placeholder behavior: use seeded internet images only for seeded media posts, or also runtime fallback for missing media files.

## Scope Boundaries
- INCLUDE: Sidebar cleanup, example-user seed data, follow/share wiring, missing-media placeholders.
- EXCLUDE: Major redesign, realtime features, and unrelated auth/profile refactors (unless requested).

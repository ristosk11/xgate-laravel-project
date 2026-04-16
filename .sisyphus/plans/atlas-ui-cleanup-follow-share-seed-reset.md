# UI Cleanup + Follow/Share Wiring + Seed Reset (Atlas Execution Plan)

## TL;DR

> Remove hashtag UI text globally, replace static sidebar mocks with real followable users, implement real share actions (copy URL + toast), and reseed the app to a minimal baseline with exactly `@design`, `@frontend`, `@laravel` and valid media assets.
>
> **Execution style**: TDD (RED → GREEN → REFACTOR) for each feature slice.

---

## Context

### User Decisions (Final)
- Remove hashtags everywhere in UI.
- Reset seeded data completely.
- Keep only core/auth baseline + exactly 3 example users: `@design`, `@frontend`, `@laravel`.
- Each example user gets a couple posts.
- Share behavior: copy post URL to clipboard + toast feedback.
- Sidebar follow must work with real users.
- Download 2–3 internet images (`<=10MB` each), assign randomly to seeded media posts.
- Test strategy: **TDD**.

### Scope Boundaries
**IN**
- UI text cleanup for hashtag references.
- Seeder reset and deterministic sample social graph.
- Sidebar follow integration.
- Share action integration.
- Media seed asset handling.

**OUT**
- New product features beyond requested cleanup.
- Realtime/share-to-third-party integrations.
- Full design overhaul.

---

## Work Objectives

### Core Objective
Deliver a clean, deterministic demo baseline where all visible social actions in scope are functional and seeded data matches the requested minimal dataset.

### Definition of Done
- [ ] No hashtag UI labels remain.
- [ ] Sidebar follow buttons operate on real DB users.
- [ ] Share buttons copy canonical post URL and show success/error toast.
- [ ] DB reseed produces only baseline + 3 requested users.
- [ ] Example users have seeded posts and media from 2–3 downloaded images.
- [ ] Tests added first (failing), then passing.

---

## Verification Strategy

### Test Decision
- **Infrastructure exists**: YES
- **Automated tests**: **TDD**
- **Framework**: PHPUnit/Pest via `php artisan test`

### QA Policy
- Every task includes runnable verification commands and agent-executed scenarios.
- No manual-only acceptance criteria.

---

## Execution Strategy

### Parallel Waves

Wave 1 (foundation)
- T1: Hashtag inventory + failing UI assertions
- T2: Seeder reset design + failing seed-shape test
- T3: Share behavior contract tests (component/feature)
- T4: Sidebar follow behavior tests

Wave 2 (implementation)
- T5: Remove hashtag UI text globally
- T6: Implement minimal deterministic seeders + 3 users + posts
- T7: Implement share copy-link + toast on post card and post show
- T8: Wire sidebar follow to real users/follow action

Wave 3 (media + integration)
- T9: Media asset downloader/seeder support (2–3 images <=10MB)
- T10: Random media assignment for seeded posts + guardrails for missing files

Wave FINAL (verification)
- F1: Plan compliance audit
- F2: Code quality/build/test audit
- F3: End-to-end UI QA scenarios
- F4: Scope fidelity check

---

## TODOs

- [x] T1. Hashtag inventory + failing UI assertions
- [x] T2. Seeder reset design + failing seed-shape test
- [x] T3. Share behavior contract tests (component/feature)
- [x] T4. Sidebar follow behavior tests
- [x] T5. Remove hashtag UI text globally
- [x] T6. Implement minimal deterministic seeders + 3 users + posts
- [x] T7. Implement share copy-link + toast on post card and post show
- [x] T8. Wire sidebar follow to real users/follow action
- [x] T9. Media asset downloader/seeder support (2–3 images <=10MB)
- [x] T10. Random media assignment for seeded posts + guardrails for missing files

---

## Final Verification Wave

- [x] F1. **Plan Compliance Audit**
  - Confirm all requested items implemented and only requested users in seeded social data.

- [x] F2. **Code Quality Review**
  - Run lint/type/tests and inspect changed files for dead code and placeholder leftovers.

- [x] F3. **Real QA Execution**
  - Validate share copy + toast, sidebar follow toggle, seeded users visibility, seeded media rendering.

- [x] F4. **Scope Fidelity Check**
  - Verify no extra feature creep beyond requested cleanup.

---

## Commit Strategy

- `test(ui): add failing specs for hashtag/share/follow/seed shape`
- `feat(ui): remove hashtags and wire share + sidebar follow`
- `feat(seed): reset seed baseline with design/frontend/laravel users and media`
- `test(seed): verify deterministic reseed output`

---

## Success Criteria

### Verification Commands
```bash
docker compose exec -T app php artisan migrate:fresh --seed
docker compose exec -T app php artisan test
```

### Final Checklist
- [x] All requested UI actions are functional
- [x] Seeded data shape matches exact user constraints
- [x] Media assets load without console flood
- [x] Tests pass

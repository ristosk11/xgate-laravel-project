# UI Bugfixes: Reactions, Nested Reply Mentions, Profile Menu, Media Gallery

## TL;DR

> **Quick Summary**: Fix 4 UI issues - reactions failing for guests, missing reply-with-mention for nested comments, profile menu opening off-screen, and inconsistent media aspect ratios.
> 
> **Deliverables**:
> - Reaction bar with `@auth` guard
> - Reply button on nested comments with `@username` pre-fill
> - Dropdown component opening upward when `align="top"`
> - Consistent 1:1 square media gallery
> 
> **Estimated Effort**: Short (4 focused tasks)
> **Parallel Execution**: YES - 4 waves (3 parallel fixes + 1 verification)
> **Critical Path**: All fixes independent → Final verification

---

## Context

### Original Request
User reported 4 UI issues:
1. Reactions do not work
2. Cannot reply to a nested reply
3. Profile menu pops out from the bottom, preventing access to options
4. Media attachments are disproportionate

### Interview Summary
**Key Discussions**:
- Nested replies: Keep 2-level limit, add @mention when replying to nested comment
- Media: Always use 1:1 square aspect ratio
- Profile menu: Issue occurs regardless of device (root cause identified in code)

**Research Findings**:
- Reactions: `Auth::user()` called without null-check, no `@auth` guard
- Nested replies: Intentional 2-level design, but no reply UI on nested comments
- Profile menu: Dropdown uses `mt-2` but menu is at bottom of sidebar
- Media: Mixed `aspect-[4/3]` and `aspect-square` causing inconsistency

---

## Work Objectives

### Core Objective
Fix 4 UI bugs to improve user experience: reactions should work for logged-in users, nested replies should support @mentions, profile menu should be accessible, and media should display consistently.

### Concrete Deliverables
- `reaction-bar.blade.php` with `@auth` guard
- `comment-thread.blade.php` with reply button on nested comments + mention pre-fill
- `dropdown.blade.php` with upward positioning for `align="top"`
- `media-gallery.blade.php` with consistent `aspect-square`

### Definition of Done
- [ ] Logged-in users can toggle reactions; guests see disabled/hidden reaction UI
- [ ] Clicking "Reply" on a nested comment pre-fills `@username` in input
- [ ] Profile menu opens upward and all options are visible
- [ ] All media previews display as 1:1 squares

### Must Have
- `@auth` guard prevents guest reaction clicks
- Reply button visible on nested comments
- Mention includes the `@` symbol and username
- Dropdown opens fully visible when at bottom of viewport
- All media uses `aspect-square`

### Must NOT Have (Guardrails)
- DO NOT change the 2-level comment threading limit
- DO NOT add multi-level nesting UI
- DO NOT change reaction persistence logic (only UI guard)
- DO NOT add dynamic aspect ratios for media
- DO NOT modify mobile navigation (only desktop dropdown)

---

## Verification Strategy

> **ZERO HUMAN INTERVENTION** - ALL verification is agent-executed. No exceptions.

### Test Decision
- **Infrastructure exists**: YES (PHPUnit)
- **Automated tests**: Tests-after for comment mention feature
- **Framework**: PHPUnit + Playwright for UI

### QA Policy
Every task includes agent-executed QA scenarios.
Evidence saved to `.sisyphus/evidence/task-{N}-{scenario-slug}.{ext}`.

- **Frontend/UI**: Use Playwright - Navigate, interact, assert DOM, screenshot

---

## Execution Strategy

### Parallel Execution Waves

```
Wave 1 (Start Immediately - all fixes are independent):
├── Task 1: Add @auth guard to reaction bar [quick]
├── Task 2: Add reply-with-mention to nested comments [unspecified-high]
├── Task 3: Fix dropdown upward positioning [quick]
└── Task 4: Standardize media gallery to aspect-square [quick]

Wave 2 (After Wave 1 - verification):
└── Task 5: Add feature test for reply mentions [quick]

Wave FINAL (After ALL tasks — reviews):
├── Task F1: Plan compliance audit (oracle)
├── Task F2: Code quality review (unspecified-high)
├── Task F3: Real manual QA with Playwright (unspecified-high)
└── Task F4: Scope fidelity check (deep)
-> Present results -> Get explicit user okay
```

### Dependency Matrix

| Task | Depends On | Blocks |
|------|------------|--------|
| 1 | - | F1-F4 |
| 2 | - | 5, F1-F4 |
| 3 | - | F1-F4 |
| 4 | - | F1-F4 |
| 5 | 2 | F1-F4 |

### Agent Dispatch Summary

- **Wave 1**: **4 parallel** - T1 → `quick`, T2 → `unspecified-high`, T3 → `quick`, T4 → `quick`
- **Wave 2**: **1** - T5 → `quick`
- **FINAL**: **4 parallel** - F1 → `oracle`, F2 → `unspecified-high`, F3 → `unspecified-high`, F4 → `deep`

---

## TODOs

- [ ] 1. Add @auth Guard to Reaction Bar

  **What to do**:
  - Open `/resources/views/livewire/components/reaction-bar.blade.php`
  - Wrap the clickable reaction buttons with `@auth` / `@endauth`
  - Optionally show a subtle disabled state or login prompt for guests with `@guest` / `@endguest`

  **Must NOT do**:
  - Do not modify `ToggleReactionAction.php` logic
  - Do not change reaction persistence behavior

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Simple Blade template modification with clear scope
  - **Skills**: [`playwright`]
    - `playwright`: For QA verification of reaction UI behavior

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1 (with Tasks 2, 3, 4)
  - **Blocks**: F1-F4
  - **Blocked By**: None

  **References**:

  **Pattern References**:
  - `/resources/views/livewire/components/reaction-bar.blade.php:1-100` - Current reaction UI implementation

  **API/Type References**:
  - Laravel `@auth` / `@guest` Blade directives

  **WHY Each Reference Matters**:
  - reaction-bar.blade.php contains the buttons that need wrapping with auth guards

  **Acceptance Criteria**:

  **QA Scenarios (MANDATORY):**

  ```
  Scenario: Logged-in user can see and click reactions
    Tool: Playwright
    Preconditions: User logged in, viewing a post with reaction bar
    Steps:
      1. Navigate to /feed
      2. Locate reaction button with selector `[wire\\:click*="toggle"]`
      3. Assert button is visible and clickable
      4. Click the reaction button
      5. Assert reaction state changes (count updates or active state)
    Expected Result: Reaction button visible, clickable, and functional
    Failure Indicators: Button not found, click has no effect, error in console
    Evidence: .sisyphus/evidence/task-1-reaction-logged-in.png

  Scenario: Guest user cannot interact with reactions
    Tool: Playwright
    Preconditions: No user logged in (guest), viewing feed or post
    Steps:
      1. Clear cookies/logout
      2. Navigate to /feed (will redirect to login)
      3. If accessible, locate reaction bar area
      4. Assert reaction buttons are either hidden or disabled
    Expected Result: Guest cannot click reaction buttons (hidden, disabled, or redirected to login)
    Failure Indicators: Guest can click reaction and trigger action
    Evidence: .sisyphus/evidence/task-1-reaction-guest.png
  ```

  **Commit**: YES
  - Message: `fix(reactions): add auth guard to prevent guest interaction`
  - Files: `resources/views/livewire/components/reaction-bar.blade.php`

---

- [ ] 2. Add Reply-with-Mention to Nested Comments

  **What to do**:
  - Open `/resources/views/livewire/components/comment-thread.blade.php`
  - Find the nested replies section (where `$comment->replies` are rendered)
  - Add a "Reply" button to each nested reply item
  - When clicked, set `reply_to` to the TOP-LEVEL parent comment ID (not the nested reply)
  - Pre-fill the comment input with `@{replyAuthorName} ` (with space after)
  - May need to add a new state variable for mention text (e.g., `$mentionText`)

  **Must NOT do**:
  - Do not change the 2-level threading limit
  - Do not attach new comments to nested replies (always to top-level parent)
  - Do not modify `CreateCommentAction.php`

  **Recommended Agent Profile**:
  - **Category**: `unspecified-high`
    - Reason: Requires understanding Livewire state management and existing comment structure
  - **Skills**: [`playwright`]
    - `playwright`: For QA verification of reply-with-mention flow

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1 (with Tasks 1, 3, 4)
  - **Blocks**: Task 5, F1-F4
  - **Blocked By**: None

  **References**:

  **Pattern References**:
  - `/resources/views/livewire/components/comment-thread.blade.php:140-160` - Top-level comment reply button pattern
  - `/resources/views/livewire/components/comment-thread.blade.php:220-280` - Nested replies rendering section

  **API/Type References**:
  - `/app/Domain/Engagement/Models/Comment.php` - Comment model with `user` relationship for author name

  **WHY Each Reference Matters**:
  - comment-thread.blade.php:140-160 shows existing reply button pattern to copy
  - comment-thread.blade.php:220-280 is where nested replies are rendered (add reply button here)
  - Comment model has `user` relationship to get author name for mention

  **Acceptance Criteria**:

  **QA Scenarios (MANDATORY):**

  ```
  Scenario: Reply button appears on nested comments
    Tool: Playwright
    Preconditions: Post exists with top-level comment that has at least one reply
    Steps:
      1. Login as test user
      2. Navigate to post show page with nested comments
      3. Locate a nested reply (child of top-level comment)
      4. Assert "Reply" button is visible on the nested reply
    Expected Result: Reply button visible on nested comment
    Failure Indicators: No reply button on nested comments, only on top-level
    Evidence: .sisyphus/evidence/task-2-reply-button-visible.png

  Scenario: Clicking reply on nested comment pre-fills @mention
    Tool: Playwright
    Preconditions: Post with nested comment by user "John Doe"
    Steps:
      1. Login as different user
      2. Navigate to post with nested comments
      3. Click "Reply" on a nested comment authored by "John Doe"
      4. Assert input field becomes visible
      5. Assert input value starts with "@John Doe " (with space)
    Expected Result: Input pre-filled with "@John Doe "
    Failure Indicators: Input empty, wrong username, missing @ symbol
    Evidence: .sisyphus/evidence/task-2-mention-prefill.png

  Scenario: Submitted reply appears at correct level with mention
    Tool: Playwright
    Preconditions: Setup as above
    Steps:
      1. Click reply on nested comment
      2. Add text after the pre-filled mention: "thanks for your input!"
      3. Submit the comment
      4. Assert new comment appears as sibling (same level) not deeper nested
      5. Assert comment text includes "@John Doe thanks for your input!"
    Expected Result: Comment at correct level with mention text preserved
    Failure Indicators: Comment nested deeper, mention lost, wrong parent
    Evidence: .sisyphus/evidence/task-2-mention-submitted.png
  ```

  **Commit**: YES
  - Message: `feat(comments): add reply-with-mention for nested comments`
  - Files: `resources/views/livewire/components/comment-thread.blade.php`

---

- [ ] 3. Fix Dropdown Upward Positioning for Bottom Menus

  **What to do**:
  - Open `/resources/views/components/dropdown.blade.php`
  - Modify the alignment logic: when `align="top"`, position dropdown to open UPWARD
  - Change from `mt-2` (margin-top, opens down) to `bottom-full mb-2` (positions above trigger)
  - Update `$alignmentClasses` match statement to handle `top` alignment properly

  **Must NOT do**:
  - Do not break existing `left` and `right` alignments
  - Do not modify navigation.blade.php (fix should be in reusable dropdown component)

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Small CSS/Tailwind change in single component
  - **Skills**: [`playwright`]
    - `playwright`: For QA verification of dropdown positioning

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1 (with Tasks 1, 2, 4)
  - **Blocks**: F1-F4
  - **Blocked By**: None

  **References**:

  **Pattern References**:
  - `/resources/views/components/dropdown.blade.php:1-35` - Full dropdown component
  - `/resources/views/livewire/layout/navigation.blade.php:62-94` - Profile menu using `align="top"`

  **WHY Each Reference Matters**:
  - dropdown.blade.php is the component to modify
  - navigation.blade.php shows how the dropdown is used with `align="top"` at bottom of sidebar

  **Acceptance Criteria**:

  **QA Scenarios (MANDATORY):**

  ```
  Scenario: Profile menu opens upward and is fully visible
    Tool: Playwright
    Preconditions: User logged in, on any page with left sidebar
    Steps:
      1. Navigate to /feed
      2. Locate profile menu trigger at bottom of sidebar
      3. Click the profile menu trigger button
      4. Assert dropdown menu appears ABOVE the trigger (not below)
      5. Assert all menu items are visible: "Profile" and "Log Out"
      6. Assert menu is not clipped by viewport edges
    Expected Result: Dropdown opens upward, all options visible
    Failure Indicators: Menu opens downward, items cut off, menu outside viewport
    Evidence: .sisyphus/evidence/task-3-dropdown-upward.png

  Scenario: Other dropdown alignments still work correctly
    Tool: Playwright
    Preconditions: Page with dropdown using default or "right" alignment
    Steps:
      1. Find any other dropdown on the page (if exists)
      2. Click to open
      3. Assert it opens in expected direction (downward for right/left align)
    Expected Result: Non-top dropdowns still open downward
    Failure Indicators: All dropdowns now open upward incorrectly
    Evidence: .sisyphus/evidence/task-3-dropdown-other.png
  ```

  **Commit**: YES
  - Message: `fix(dropdown): open upward when align="top" for bottom-positioned menus`
  - Files: `resources/views/components/dropdown.blade.php`

---

- [ ] 4. Standardize Media Gallery to Square Aspect Ratio

  **What to do**:
  - Open `/resources/views/livewire/components/media-gallery.blade.php`
  - Find all instances of `aspect-[4/3]` and change to `aspect-square`
  - Ensure all grid items consistently use `aspect-square`
  - Keep `object-cover` for proper image filling within square containers

  **Must NOT do**:
  - Do not change to `object-contain` (user wants cropped squares, not letterboxing)
  - Do not add dynamic aspect ratios
  - Do not modify modal/lightbox view (only grid previews)

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Simple find-and-replace of Tailwind classes
  - **Skills**: [`playwright`]
    - `playwright`: For visual QA verification

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1 (with Tasks 1, 2, 3)
  - **Blocks**: F1-F4
  - **Blocked By**: None

  **References**:

  **Pattern References**:
  - `/resources/views/livewire/components/media-gallery.blade.php` - Media rendering template

  **WHY Each Reference Matters**:
  - media-gallery.blade.php contains all aspect ratio classes that need standardization

  **Acceptance Criteria**:

  **QA Scenarios (MANDATORY):**

  ```
  Scenario: Single image displays as square
    Tool: Playwright
    Preconditions: Post with single image attachment
    Steps:
      1. Navigate to feed or post with single image
      2. Locate media gallery container
      3. Measure image container dimensions
      4. Assert width equals height (1:1 ratio)
    Expected Result: Image container is square
    Failure Indicators: Width != height, aspect ratio is 4:3
    Evidence: .sisyphus/evidence/task-4-single-image-square.png

  Scenario: Multiple images all display as squares
    Tool: Playwright
    Preconditions: Post with 3+ image attachments
    Steps:
      1. Navigate to post with multiple images
      2. Locate all media gallery items
      3. For each item, assert it has aspect-square class or 1:1 dimensions
    Expected Result: All images are square
    Failure Indicators: Any image not square, mixed aspect ratios
    Evidence: .sisyphus/evidence/task-4-multi-image-square.png
  ```

  **Commit**: YES
  - Message: `fix(media): standardize gallery to square aspect ratio`
  - Files: `resources/views/livewire/components/media-gallery.blade.php`

---

- [ ] 5. Add Feature Test for Reply-with-Mention

  **What to do**:
  - Create or update test file for comment mentions
  - Test that replying to a nested comment creates comment at correct level
  - Test that the `@username` pattern is preserved in content
  - Use existing Comment and User factories

  **Must NOT do**:
  - Do not test the UI (that's covered by Playwright QA)
  - Do not modify production code

  **Recommended Agent Profile**:
  - **Category**: `quick`
    - Reason: Standard PHPUnit test following existing patterns
  - **Skills**: []

  **Parallelization**:
  - **Can Run In Parallel**: NO
  - **Parallel Group**: Wave 2 (after Task 2)
  - **Blocks**: F1-F4
  - **Blocked By**: Task 2

  **References**:

  **Pattern References**:
  - `/tests/Feature/NestedCommentTest.php` - Existing nested comment tests
  - `/tests/Feature/PostShowPageTest.php` - Feature test patterns

  **Acceptance Criteria**:

  **QA Scenarios (MANDATORY):**

  ```
  Scenario: Test passes when run
    Tool: Bash
    Preconditions: Task 2 completed
    Steps:
      1. Run: docker compose exec -T app php artisan test --filter=MentionReply
    Expected Result: All tests pass
    Failure Indicators: Test failures, exceptions
    Evidence: .sisyphus/evidence/task-5-test-output.txt
  ```

  **Commit**: YES (group with Task 2)
  - Message: `test(comments): add feature test for reply-with-mention`
  - Files: `tests/Feature/CommentMentionTest.php` (or similar)

---

## Final Verification Wave (MANDATORY — after ALL implementation tasks)

> 4 review agents run in PARALLEL. ALL must APPROVE. Present consolidated results to user and get explicit "okay" before completing.

- [ ] F1. **Plan Compliance Audit** — `oracle`
  Read the plan end-to-end. For each "Must Have": verify implementation exists. For each "Must NOT Have": search for forbidden patterns. Check evidence files exist in .sisyphus/evidence/.
  Output: `Must Have [N/N] | Must NOT Have [N/N] | Tasks [5/5] | VERDICT: APPROVE/REJECT`

- [ ] F2. **Code Quality Review** — `unspecified-high`
  Review all changed files for: syntax errors, undefined variables, broken Blade directives. Run linter if available.
  Output: `Files [N clean/N issues] | VERDICT`

- [ ] F3. **Real Manual QA** — `unspecified-high` (+ `playwright` skill)
  Execute EVERY QA scenario from EVERY task. Save evidence to `.sisyphus/evidence/final-qa/`.
  Output: `Scenarios [N/N pass] | VERDICT`

- [ ] F4. **Scope Fidelity Check** — `deep`
  Verify only the 4 specified files were modified. No scope creep. No extra features added.
  Output: `Files Modified [4 expected] | Scope [CLEAN/CREEP] | VERDICT`

---

## Commit Strategy

| Task | Commit Message | Files |
|------|----------------|-------|
| 1 | `fix(reactions): add auth guard to prevent guest interaction` | `reaction-bar.blade.php` |
| 2+5 | `feat(comments): add reply-with-mention for nested comments` | `comment-thread.blade.php`, `CommentMentionTest.php` |
| 3 | `fix(dropdown): open upward when align="top"` | `dropdown.blade.php` |
| 4 | `fix(media): standardize gallery to square aspect ratio` | `media-gallery.blade.php` |

---

## Success Criteria

### Verification Commands
```bash
# Run tests
docker compose exec -T app php artisan test --filter=Comment

# Check for syntax errors
docker compose exec -T app php -l resources/views/livewire/components/reaction-bar.blade.php
docker compose exec -T app php -l resources/views/livewire/components/comment-thread.blade.php
docker compose exec -T app php -l resources/views/components/dropdown.blade.php
docker compose exec -T app php -l resources/views/livewire/components/media-gallery.blade.php
```

### Final Checklist
- [ ] Reactions: Guest cannot click, logged-in user can toggle
- [ ] Comments: Reply button on nested replies, @mention pre-filled
- [ ] Dropdown: Profile menu opens upward, all options visible
- [ ] Media: All gallery items are 1:1 square
- [ ] Tests: New mention test passes
- [ ] No scope creep: Only 4 files modified

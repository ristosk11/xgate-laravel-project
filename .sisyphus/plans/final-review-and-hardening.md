# Plan: Final Review and Hardening

## TL;DR
> **Quick Summary**: This plan creates a persistent test user for easier testing and performs a security audit on the codebase to identify potentially unsafe code patterns.
>
> **Deliverables**:
> - A new database seeder for the test user.
> - A report of potentially insecure code found in the project.
>
> **Estimated Effort**: Quick
> **Parallel Execution**: YES - 2 waves

---

## Context

### Original Request
The user requested a persistent test user to avoid registering during testing. They also asked for a security review of all generated code to find "unsafe methods." They require periodic updates on progress.

### Interview Summary
- **Test User Credentials**: `test@example.com` / `password`
- **Unsafe Patterns to check**: `DB::raw`, `eval(`, `shell_exec(`, `exec(`, `passthru(`, `system(`, `<?=` (short open tags), and `{!! !!}` in Blade templates.
- The user wants to be updated after each major step.

---

## Work Objectives

### Core Objective
To streamline testing with a default user and improve code quality by identifying potential security risks.

### Definition of Done
- [ ] A test user exists in the database with the specified credentials.
- [ ] A markdown report of security scan findings is available.

### Must Have
- A persistent test user.
- A security scan report.

### Must NOT Have (Guardrails)
- Do not fix any identified security issues in this plan. The goal is to report first.

---

## Verification Strategy

### QA Policy
Every task MUST include agent-executed QA scenarios.

---

## Execution Strategy

### Parallel Execution Waves
```
Wave 1 (Start Immediately - Seeder):
├── Task 1: Create TestUserSeeder.php
└── Task 2: Update DatabaseSeeder.php

Wave 2 (After Wave 1 - Execution & Analysis):
├── Task 3: Run database seeder
└── Task 4: Perform security analysis
```

---

## TODOs

- [ ] 1. Create TestUserSeeder.php

  **What to do**:
  - Create a new file at `database/seeders/TestUserSeeder.php`.
  - The seeder should create one user with the name 'Test User', email 'test@example.com', and password 'password'.
  - Use `Hash::make('password')` for the password.

  **Recommended Agent Profile**:
  - **Category**: `quick`
  - **Skills**: []

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1
  - **Blocks**: [Task 3]
  - **Blocked By**: None

  **QA Scenarios**:
  \`\`\`
  Scenario: Verify seeder file creation
    Tool: Bash
    Steps:
      1. Run `ls database/seeders/TestUserSeeder.php`
    Expected Result: The command should execute successfully and show the file exists.
    Evidence: .sisyphus/evidence/task-1-file-exists.txt
  \`\`\`

- [ ] 2. Update DatabaseSeeder.php

  **What to do**:
  - Edit `database/seeders/DatabaseSeeder.php`.
  - In the `run()` method, add a call to the new seeder: `$this->call(TestUserSeeder::class);`.

  **Recommended Agent Profile**:
  - **Category**: `quick`
  - **Skills**: []

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 1
  - **Blocks**: [Task 3]
  - **Blocked By**: None

  **QA Scenarios**:
  \`\`\`
  Scenario: Verify DatabaseSeeder update
    Tool: Grep
    Steps:
      1. Run `grep "TestUserSeeder::class" database/seeders/DatabaseSeeder.php`
    Expected Result: The command should find one match.
    Evidence: .sisyphus/evidence/task-2-seeder-updated.txt
  \`\`\`

- [ ] 3. Run database seeder

  **What to do**:
  - Run the command `php artisan db:seed`.

  **Recommended Agent Profile**:
  - **Category**: `quick`
  - **Skills**: []

  **Parallelization**:
  - **Can Run In Parallel**: NO (Sequential)
  - **Blocks**: None
  - **Blocked By**: [Task 1, Task 2]

  **QA Scenarios**:
  \`\`\`
  Scenario: Verify user exists in database
    Tool: Bash
    Steps:
      1. Run `php artisan tinker --execute="App\Models\User::where('email', 'test@example.com')->exists() ? exit(0) : exit(1)"`
    Expected Result: The command should exit with code 0.
    Evidence: .sisyphus/evidence/task-3-user-exists.txt
  \`\`\`

- [ ] 4. Perform Security Analysis

  **What to do**:
  - Search the entire project for the following patterns:
    - `DB::raw`
    - `eval(`
    - `shell_exec(`
    - `exec(`
    - `passthru(`
    - `system(`
    - `<?=`
    - `{!! !!}`
  - Compile all findings into a single markdown report at `.sisyphus/drafts/security-report.md`.

  **Recommended Agent Profile**:
  - **Category**: `deep`
  - **Skills**: []

  **Parallelization**:
  - **Can Run In Parallel**: YES
  - **Parallel Group**: Wave 2
  - **Blocks**: None
  - **Blocked By**: None

  **QA Scenarios**:
  \`\`\`
  Scenario: Verify security report generation
    Tool: Bash
    Steps:
      1. Run `ls .sisyphus/drafts/security-report.md`
    Expected Result: The command should execute successfully and show the file exists.
    Evidence: .sisyphus/evidence/task-4-report-exists.txt
  \`\`\`

---

## Final Verification Wave
- [ ] F1. Plan Compliance Audit (oracle)
- [ ] F2. Code Quality Review (unspecified-high)
- [ ] F3. Real Manual QA (unspecified-high)
- [ ] F4. Scope Fidelity Check (deep)

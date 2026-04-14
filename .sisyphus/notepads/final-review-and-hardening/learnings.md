## TestUserSeeder
- Added idempotent TestUserSeeder for a test user
- Path: database/seeders/TestUserSeeder.php
- Seeds a test user with name 'Test User', email 'test@example.com', password hashed via Hash::make('password')
- Uses updateOrCreate to prevent duplicates when run multiple times
- Next steps: Run `php artisan db:seed --class TestUserSeeder` in a test environment

- Change: Updated database/seeders/DatabaseSeeder.php to call TestUserSeeder::class within run(). This wires the main seeder to also seed a test user via the dedicated TestUserSeeder when running db:seed.
- Rationale: Ensures the test user is created as part of the standard seeding workflow and avoids separate manual seeding steps.
- Verification plan: Since PHP isn't available in the current environment, verify locally by running `php artisan db:seed` or `php artisan db:seed --class TestUserSeeder` in a Laravel environment. Also run `php -l` for syntax if PHP is available.

## Security scan (pattern-based)
- Date: 2026-04-14
- Output report: `.sisyphus/drafts/security-report.md`
- Patterns checked: `DB::raw`, `eval(`, `shell_exec(`, `exec(`, `passthru(`, `system(`, `<?=`, `{!! !!}`
- Findings: `exec(` occurrences were found only in vendored Livewire assets under `public/vendor/livewire/`.

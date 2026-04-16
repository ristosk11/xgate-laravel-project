# Mini Social

A modern social media application built with Laravel 11 and Livewire 3, demonstrating full-stack development skills with a focus on clean architecture, real-time UI, and comprehensive testing.

## Quick Start

```bash
docker compose up -d
docker compose exec app php artisan migrate --seed
docker compose exec node npm run build
```

Access the app at `http://localhost:8080`

## Demo Credentials

| Name     | Email                    | Password |
|----------|--------------------------|----------|
| Design   | design@example.test      | password |
| Frontend | frontend@example.test    | password |
| Laravel  | laravel@example.test     | password |

## Architecture Highlights

### Domain-Driven Design
The codebase follows DDD principles with clear separation of concerns:

```
app/
├── Domain/
│   ├── Content/           # Posts, comments, reactions, media
│   │   ├── Actions/       # Single-responsibility action classes
│   │   ├── DTOs/          # Data transfer objects
│   │   ├── Enums/         # Type-safe enumerations
│   │   ├── Models/        # Eloquent models
│   │   └── Services/      # Business logic services
│   └── IdentityAndAccess/ # Users, profiles, follows
├── Livewire/              # Real-time UI components
└── Models/                # Core Laravel models
```

### Key Patterns
- **Action Classes**: Encapsulated business operations (`CreatePostAction`, `ToggleReactionAction`)
- **DTOs**: Type-safe data transfer between layers
- **Service Layer**: Complex business logic abstraction (`FeedService`, `MediaUploadService`)
- **Volt Components**: Single-file Livewire components for rapid development

## Features

### Authentication & Authorization
- Full authentication flow (register, login, password reset, email verification)
- Profile management with avatar upload

### Social Features
- **Feed**: Personalized timeline showing posts from followed users
- **Posts**: Rich text posts with multi-media support (images, videos)
- **Reactions**: Polymorphic reaction system for posts and comments
- **Comments**: Threaded conversations with nested replies
- **Follow System**: Follow/unfollow with follower counts

### UI/UX
- **Dark Mode**: System-aware with manual toggle, persisted to localStorage
- **Responsive Design**: Mobile-first with adaptive layouts
- **Real-time Updates**: Livewire-powered without page reloads
- **Media Gallery**: Lightbox viewer with keyboard navigation

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11, PHP 8.3 |
| Frontend | Livewire 3, Alpine.js, Tailwind CSS v4 |
| Database | MySQL 8 |
| Testing | PHPUnit, Pest-style assertions |
| DevOps | Docker Compose |

## Testing

```bash
# Run all tests
docker compose exec app php artisan test

# Run with coverage
docker compose exec app php artisan test --coverage
```

**Test Coverage**: 57 tests covering authentication, domain logic, and feature integration.

## Development

```bash
# Watch mode for frontend assets
docker compose exec node npm run dev

# Code formatting
docker compose exec app ./vendor/bin/pint

# Fresh database with seed data
docker compose exec app php artisan migrate:fresh --seed
```

## Project Structure

```
├── app/Domain/           # Business logic (DDD)
├── database/seeders/     # Demo data seeders
├── resources/views/      # Blade templates & Volt components
├── routes/web.php        # Route definitions
└── tests/                # Feature & unit tests
```


# DDD + CQRS Laravel Boilerplate

A production-ready foundation for building modern applications with Laravel 12, following Domain-Driven Design (DDD), CQRS, and Clean Architecture principles.

## Features

- **Multi-tenant Workspaces** - Team management with roles, invitations, and ownership transfer
- **Authentication** - Email/password auth with email verification, 2FA, and password recovery
- **Stripe Billing** - Subscription management, usage tracking, and billing portal
- **Clean Architecture** - DDD, CQRS, domain events, and proper separation of concerns

## Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: React 19, Inertia.js v2, TypeScript
- **Styling**: Tailwind CSS v4, shadcn/ui
- **Database**: PostgreSQL (UUID primary keys)
- **Authentication**: Laravel session-based auth with 2FA
- **Payments**: Stripe (subscriptions, billing portal)
- **Testing**: Pest
- **Code Quality**: Laravel Pint, Larastan

## Architecture

This application follows strict architectural principles:

- **Domain-Driven Design (DDD)** - Clear domain boundaries with separate modules
- **CQRS** - Command/Query separation with dedicated handlers
- **Clean Architecture** - Domain, Application, Infrastructure layers
- **Domain Events** - Event-driven communication between modules
- **Repository Pattern** - Abstraction over data persistence
- **Value Objects** - Type safety and domain validation

## Project Structure

```
src/
├── Core/                    # CQRS infrastructure (Bus, Handlers)
├── Shared/                  # Shared kernel (BaseModel, etc.)
├── IAM/                     # Identity & Access Management
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
├── Workspaces/              # Multi-tenant workspaces
│   ├── Domain/
│   ├── Application/
│   └── Infrastructure/
└── Billing/                 # Stripe billing integration
    ├── Domain/
    ├── Application/
    └── Infrastructure/

app/                         # Laravel app (Controllers, Middleware)
resources/js/                # React frontend
```

### Module Structure

```
{Module}/
├── Domain/
│   ├── Models/              # Domain entities
│   ├── ValueObjects/        # Immutable value objects
│   ├── Events/              # Domain events
│   ├── Repositories/        # Repository interfaces
│   ├── Services/            # Domain services
│   └── Exceptions/          # Domain exceptions
├── Application/
│   ├── Commands/            # Command + Handler pairs
│   ├── Queries/             # Query + Handler pairs
│   ├── Responses/           # DTOs for query results
│   └── Listeners/           # Event listeners
└── Infrastructure/
    ├── Models/              # Eloquent models
    ├── Repositories/        # Repository implementations
    ├── Framework/
    │   ├── Providers/       # Service providers
    │   └── Migrations/      # Database migrations
    └── Services/            # External service integrations
```

## Installation

```bash
# Clone repository
git clone <repository-url>
cd project

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Start Docker environment
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Build frontend assets
npm run build
```

## Development

```bash
# Start development server (runs Laravel + Vite concurrently)
composer run dev

# Run tests
./vendor/bin/sail artisan test

# Code formatting
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse
```

## Environment Variables

Key environment variables to configure:

```env
# Application
APP_NAME="Your App Name"
APP_URL=http://localhost

# Database
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_DATABASE=your_database

# Stripe
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Mail
MAIL_MAILER=smtp
MAIL_HOST=mailpit
```

## Creating New Modules

1. Create module structure in `src/YourModule/`
2. Create service provider in `Infrastructure/Framework/Providers/`
3. Register provider in `bootstrap/providers.php`
4. Create migrations in `Infrastructure/Framework/Migrations/`

## License

MIT License

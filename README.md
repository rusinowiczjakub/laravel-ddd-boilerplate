# Notifyhub

Backend API for NotifyHub platform built with Laravel 12, following Domain-Driven Design (DDD), CQRS, and Clean Architecture principles.

## Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: React 19, Inertia.js v2, TypeScript
- **Styling**: Tailwind CSS v4
- **Database**: MySQL/PostgreSQL (UUID primary keys)
- **Authentication**: Laravel Sanctum (token-based API auth)
- **Testing**: Pest (PHP), PHPUnit
- **Code Quality**: Laravel Pint, Larastan
- **Deployment**: Laravel Sail (Docker)

## Architecture

This application follows strict architectural principles:

- **Domain-Driven Design (DDD)** - Clear domain boundaries with separate modules
- **CQRS** - Command/Query separation with dedicated handlers
- **Clean Architecture** - Domain, Application, Infrastructure layers
- **Event Sourcing** - Domain events for critical business operations
- **Repository Pattern** - Abstraction over data persistence
- **Value Objects** - Type safety and domain validation
- **Immutability** - Readonly classes for Commands, Queries, Responses, ValueObjects

## Project Structure

```
src/
├── {Module}/
│   ├── Domain/
│   │   ├── ValueObjects/
│   │   ├── Services/
│   │   ├── Repositories/
│   │   ├── Events/
│   │   └── Exceptions/
│   ├── Application/
│   │   ├── Commands/
│   │       ├── SomeCommand.php
│   │       └── SomeCommandHandler.php
│   │   ├── Queries/
│   │       ├── SomeQuery.php
│   │       └── SomeQueryHandler.php
│   │   └── Responses/
│   └── Infrastructure/
│       ├── Services/
│       ├── Repositories/
│       └── Framework/
```

## Installation

```bash
# Clone repository
git clone <repository-url>
cd driply

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
# Start development server
composer run dev

# Run tests
./vendor/bin/sail artisan test

# Code formatting
./vendor/bin/pint

# Static analysis
./vendor/bin/phpstan analyse
```

## API Documentation

API is available on subdomain `api.{domain}` (e.g., `api.localhost`)

- **Authentication**: Phone-based verification via Twilio
- **Authorization**: Sanctum token-based authentication
- **Format**: JSON-only responses

### Authentication Endpoints

```
POST   /auth/send-code           - Send verification code to phone
POST   /auth/verify-code         - Verify code and authenticate
GET    /auth/me                  - Get authenticated user (protected)
POST   /auth/logout              - Logout current session (protected)
POST   /auth/complete-onboarding - Complete user onboarding (protected)
```

## Modules

### ✅ Authentication
Phone-based authentication with Twilio Verify API v2
- SMS verification codes
- Sanctum token authentication
- User onboarding flow
- No password authentication

## License

Proprietary - All rights reserved

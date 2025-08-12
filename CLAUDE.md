# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Iotichthys is a Laravel-based IoT cloud service platform that integrates with AWS IoT Core MQTT services. It provides IoT device data collection, real-time notifications, and time-series analytics through interactive dashboards. The project uses Laravel Livewire for the frontend and follows a multi-tenant architecture with organizations, teams, and role-based permissions.

## Development Commands

### Starting Development Environment
```bash
composer dev
```
This command starts all development services concurrently:
- PHP artisan serve (web server)
- Queue listener
- Log monitoring (pail)
- Vite dev server (frontend assets)

### Testing
```bash
composer test
# or directly
php artisan test
```
Runs PHPUnit/Pest tests with configuration clearing.

### Building Assets
```bash
npm run build    # Production build
npm run dev      # Development build with watch
```

### Database
```bash
php artisan migrate
php artisan migrate:fresh --seed
```

### Code Quality
```bash
vendor/bin/pint   # Laravel Pint (code formatting)
```

## Architecture & Structure

### Multi-Tenant System
The application follows a hierarchical multi-tenant structure:
- **Organizations**: Top-level tenant entities
- **Teams**: Sub-units within organizations  
- **Users**: Can belong to multiple organizations/teams with different roles
- **Permissions**: Fine-grained access control through roles

### Key Models & Relationships
- `User` → belongs to many `Organization`s and `Team`s
- `Organization` → has many `User`s and `Team`s
- `Team` → belongs to `Organization`, has many `User`s
- `Role` → has many `Permission`s, assigned to users within organizations
- Pivot tables: `user_organizations`, `user_teams`, `user_roles`, `role_permissions`

### Livewire Components Structure
```
app/Livewire/
├── Auth/           # Authentication components
├── Organization/   # Organization management (CRUD, modals)
├── Teams/          # Team management
├── Users/          # User management
├── Settings/       # User settings (profile, password, appearance)
├── Rules/          # Business rules configuration
└── Actions/        # Reusable actions (logout, etc.)
```

### Middleware & Security
- `CheckPermission`: Role-based access control middleware
- Policy-based authorization (`OrganizationPolicy`)
- Laravel Sanctum/Passport for API authentication
- Form requests for validation (`OrganizationRequest`, `UpdateOrganizationRequest`)

### Frontend Architecture
- **Framework**: Laravel Livewire with Flux UI components
- **Styling**: TailwindCSS v4
- **Build Tool**: Vite
- **Real-time**: Laravel Reverb (WebSocket server)
- **JavaScript**: Minimal Alpine.js/Livewire approach

### AWS IoT Integration
The system is designed to integrate with AWS IoT Core:
- MQTT protocol for device communication
- Device registry management
- Time-series data storage (planned: AWS TimeStream)
- Lambda functions for data processing
- SNS/SQS for notifications

### Database Structure
- **Primary DB**: SQLite (development), MySQL (production)
- **Session Storage**: Database-driven
- **Queue Backend**: Database
- **Cache**: Database

### Event System
- `OrganizationCreating` event with `GenerateSlug` listener
- Event-driven architecture for organization lifecycle

### Testing Strategy
- **Testing Framework**: Pest (not PHPUnit)
- **Browser Tests**: Laravel Dusk for E2E testing
- **Feature Tests**: Livewire component testing
- **Unit Tests**: Model and listener testing
- Test organization follows feature-based structure
- **Test Language**: All test descriptions and comments must be written in Korean
- **Test Structure**: Use Pest syntax with `test()` function, not PHPUnit class-based tests

### Localization
- **Default Locale**: Korean (`ko`)
- **Fallback**: English (`en`)
- **Timezone**: Asia/Seoul
- Language files in `lang/ko/` and `lang/en/`

### Monitoring & Logging
- **Error Tracking**: Sentry integration
- **Logging**: Logtail (BetterStack) integration
- **Performance**: Laravel Octane with FrankenPHP
- **Development Monitoring**: Laravel Pail for real-time logs

## Development Notes

### Code Conventions
- Korean comments and documentation are used throughout the codebase
- Follow Laravel conventions and PSR standards
- Use Livewire naming patterns for components
- Database migrations follow timestamp-based naming

### Key Configuration
- **App URL**: Uses `.test` domain for local development (Valet/Herd)
- **Queue**: Database-driven with single retry attempt
- **Broadcasting**: Laravel Reverb for WebSocket connections
- **File Storage**: Local disk for development

### Testing Database
- Uses SQLite for testing environment
- Database recreated for each test run
- Seeders available for roles and permissions setup
# Iotichthys IoT Cloud Service Platform

**Always reference these instructions first and fallback to search or bash commands only when you encounter unexpected information that does not match the info here.**

Iotichthys is a Laravel 12 framework application with Livewire frontend that integrates with AWS IoT Core MQTT services for IoT device data collection, real-time notifications, and time-series analytics. Built in Korean but code is in English.

## Working Effectively

### Environment Setup and Dependencies
```bash
# 1. Copy environment file
cp .env.example .env

# 2. Install PHP dependencies (NEVER CANCEL: takes 3 minutes, set timeout to 300+ seconds)
# NOTE: One dependency (dallyger/laravel-ansi-logger) WILL FAIL due to codeberg.org connectivity
# REQUIRED WORKAROUND: Remove the problematic dependency first
sed -i '/"dallyger\/laravel-ansi-logger": ".*",/d' composer.json
rm -f composer.lock
composer install --no-interaction --prefer-dist --optimize-autoloader

# 3. Generate application key
php artisan key:generate

# 4. Install Node.js dependencies (takes ~15 seconds)
npm install

# 5. Create SQLite database
touch database/database.sqlite

# 6. Run database migrations (takes <1 second)
php artisan migrate
```

### Build and Asset Management
```bash
# Build frontend assets for production (takes <2 seconds)
npm run build

# Start development asset server (requires bypass in CI)
LARAVEL_BYPASS_ENV_CHECK=1 npm run dev
# Access at: http://localhost:5174/
```

### Running the Application
```bash
# Start Laravel development server
php artisan serve --port=8000
# Access at: http://127.0.0.1:8000

# Start both servers together (recommended for development)
# NOTE: In CI environments, use LARAVEL_BYPASS_ENV_CHECK=1 for Vite
composer dev
```

### Testing and Quality Assurance
```bash
# Run all tests (NEVER CANCEL: takes 20 seconds, set timeout to 60+ seconds)
./vendor/bin/pest --no-coverage

# Run tests via composer script (recommended)
composer test

# Run tests with coverage (requires Xdebug configuration)
./vendor/bin/pest --coverage

# Check code style (takes ~8 seconds)
./vendor/bin/pint --test

# Fix code style issues
./vendor/bin/pint

# CRITICAL: Always run before committing
vendor/bin/pint && composer test
```

## Validation Scenarios

**ALWAYS test these scenarios after making changes:**

1. **Basic Application Flow**: 
   - Start servers with `php artisan serve` and `LARAVEL_BYPASS_ENV_CHECK=1 npm run dev`
   - Visit http://127.0.0.1:8000 and verify 200 response
   - Check that Livewire components load properly

2. **Authentication and Dashboard**:
   - Register a new user account
   - Log in and access the dashboard
   - Verify user management features work

3. **Organization Management**:
   - Create a new organization
   - Add users to organizations
   - Test team creation and management

4. **Permissions System**:
   - Create permissions
   - Assign permissions to users
   - Test role-based access

## Known Issues and Workarounds

### Dependency Installation Issues
- **Problem**: `dallyger/laravel-ansi-logger` ALWAYS fails to download from codeberg.org
- **Required Workaround**: 
  ```bash
  # Remove the problematic dependency before installing
  sed -i '/"dallyger\/laravel-ansi-logger": ".*",/d' composer.json
  rm -f composer.lock
  composer install --no-interaction --prefer-dist --optimize-autoloader
  ```

### CI Environment Issues
- **Problem**: Vite dev server fails in CI with "You should not run the Vite HMR server in CI environments"
- **Solution**: Use `LARAVEL_BYPASS_ENV_CHECK=1 npm run dev`

### Test Coverage Issues
- **Problem**: Coverage reports fail with "Unable to get coverage using Xdebug"
- **Solution**: Use `./vendor/bin/pest --no-coverage` for basic testing

### Code Style Issues
- **Problem**: 73+ style issues exist in codebase
- **Solution**: Run `./vendor/bin/pint` to auto-fix, then commit changes

## Critical Timeouts and Expectations

- **NEVER CANCEL composer install**: Takes 1-3 minutes (85 seconds measured), set timeout to 300+ seconds
- **NEVER CANCEL tests**: Take 20-35 seconds (33 seconds measured), set timeout to 60+ seconds
- **Build process**: Takes <2 seconds, very fast
- **Migrations**: Take <1 second, very fast
- **NPM install**: Takes ~2-15 seconds

## Project Structure and Navigation

### Key Directories
```
app/
├── Http/Controllers/     # API and web controllers
├── Livewire/            # Livewire components (main UI logic)
│   ├── Organization/    # Organization management
│   ├── Permissions/     # Permission system
│   ├── Teams/          # Team management  
│   └── Users/          # User management
├── Models/             # Eloquent models
└── Policies/           # Authorization policies

tests/
├── Feature/            # Feature tests (main test suite)
├── Unit/              # Unit tests
└── Browser/           # Dusk browser tests

resources/
├── views/             # Blade templates and Livewire views
├── css/              # Frontend styles
└── js/               # Frontend JavaScript

database/
├── migrations/        # Database schema
├── factories/         # Model factories
└── seeders/          # Database seeders
```

### Important Files
- `composer.json` - PHP dependencies and scripts
- `package.json` - Node.js dependencies and build scripts
- `phpunit.xml` - Test configuration
- `vite.config.js` - Frontend build configuration
- `.env.example` - Environment template

## CI/CD Pipeline Information

The project uses GitHub Actions with three workflows:

1. **build.yml** - SonarQube code quality analysis
2. **tests.yml** - Full test suite with coverage (PHP 8.4, Node 22)
3. **lint.yml** - Code style checking with Laravel Pint

### Required Secrets for CI
- `FLUX_USERNAME` and `FLUX_LICENSE_KEY` for Livewire Flux UI components
- `SONAR_TOKEN` for SonarQube analysis

## AWS IoT Integration

The application integrates with AWS IoT Core for:
- MQTT device communication
- Real-time data streaming
- Time-series data storage
- Alert and notification systems

**Environment variables needed for full functionality:**
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_DEFAULT_REGION`
- `AWS_BUCKET`

## Common Commands Reference

```bash
# Database operations
php artisan migrate                    # Run migrations
php artisan migrate:rollback          # Rollback migrations
php artisan db:seed                   # Seed database

# Cache management
php artisan config:clear              # Clear config cache
php artisan cache:clear              # Clear application cache
php artisan optimize                 # Optimize for production

# Development helpers
php artisan tinker                   # Interactive shell
php artisan route:list               # List all routes
php artisan about                    # Application overview

# Queue management (for background jobs)
php artisan queue:work               # Process queue jobs
php artisan queue:listen             # Listen for queue jobs
```

## Technology Stack Details

- **Backend**: Laravel 12.22.1, PHP 8.2+
- **Frontend**: Livewire 3.6.4, Livewire Flux 2.2.4, TailwindCSS 4.0.7
- **Database**: SQLite (development), MySQL (production), AWS TimeStream (IoT data)
- **Testing**: Pest PHP 3.8.2, Laravel Dusk 8.3.3
- **Build Tools**: Vite 6.3.5, NPM
- **Code Quality**: Laravel Pint 1.24.0

## Multi-language Support

The application includes Korean language support:
- `lang/ko/validation.php` - Korean validation messages
- Korean test descriptions throughout test suite
- Mixed Korean/English UI labels

Always respect the existing language patterns when making changes.
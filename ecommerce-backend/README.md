<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Getting Started with Docker

This project uses Docker for local development. Follow these steps to get started.

### Prerequisites

Make sure you have the following installed on your system:

-   [Docker](https://docs.docker.com/get-docker/) (v20.10+)
-   [Docker Compose](https://docs.docker.com/compose/install/) (v2.0+)

### Installation Steps

1. **Clone the repository**

```bash
git clone
cd ecommerce-backend
```

2. **Copy environment file**

```bash
cp .env.example .env
```

3. **Update environment variables**

Edit your `.env` file with Docker-specific settings:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

4. **Make the run script executable**

```bash
chmod +x run
```

5. **Start Docker containers**

```bash
./run up
```

6. **Install and setup the application**

```bash
./run install
```

This command will automatically:

-   Install Composer dependencies
-   Generate application key
-   Run database migrations
-   Seed the database
-   Create storage symbolic link
-   Clear all caches

7. **Access the application**

-   **Application**: http://localhost:8000
-   **MySQL**: localhost:3306
-   **Redis**: localhost:6379

## Using the `./run` Helper Script

We provide a convenient `run` script to simplify Docker commands.

### Docker Management Commands

Start, stop, and manage your Docker containers:

```bash
./run up                    # Start all containers
./run down                  # Stop all containers
./run restart               # Restart all containers
./run build                 # Build/rebuild containers
./run rebuild               # Complete rebuild (down + build + up)
./run ps                    # Show running containers status
./run logs                  # Show all container logs
./run logs php              # Show specific service logs (php/nginx/mysql/redis)
```

### Laravel Artisan Commands

Run any Laravel Artisan command:

```bash
./run artisan migrate                          # Run database migrations
./run artisan make:model Product               # Create a new model
./run artisan make:controller ProductController
./run artisan make:migration create_products_table
./run artisan db:seed                          # Seed the database
./run artisan route:list                       # List all routes
./run tinker                                   # Open Laravel Tinker
```

### Database Management

Manage your database with these shortcuts:

```bash
./run migrate               # Run migrations
./run migrate-fresh         # Drop all tables and re-run migrations
./run migrate-rollback      # Rollback the last migration
./run seed                  # Run database seeder
./run fresh                 # Fresh database with seed (migrate:fresh --seed)
./run mysql                 # Access MySQL CLI
./run redis                 # Access Redis CLI
```

### Composer Commands

Manage PHP dependencies:

```bash
./run composer install              # Install all dependencies
./run composer update               # Update all dependencies
./run composer require vendor/package
./run composer-install              # Quick install shortcut
./run composer-update               # Quick update shortcut
```

### NPM Commands

Manage Node.js dependencies and assets:

```bash
./run npm install           # Install Node dependencies
./run npm update            # Update Node dependencies
./run npm run dev           # Start Vite development server
./run npm run build         # Build assets for production
./run npm-install           # Quick install shortcut
./run npm-dev               # Quick dev server shortcut
./run npm-build             # Quick build shortcut
```

### Cache & Optimization

Clear and manage Laravel caches:

```bash
./run cache-clear           # Clear all caches (config, route, view, cache)
./run config-cache          # Cache the configuration
./run route-cache           # Cache the routes
./run view-cache            # Cache the views
./run optimize              # Optimize the application
./run optimize-clear        # Clear optimization caches
```

### Testing Commands

Run your application tests:

```bash
./run test                  # Run all PHPUnit tests
./run test UserTest         # Run a specific test
```

### Shell Access

Access container shells for debugging:

```bash
./run bash                  # Access PHP container shell (as www-data user)
./run root                  # Access PHP container shell (as root user)
```

### Queue & Schedule

Run background jobs and scheduled tasks:

```bash
./run queue                 # Start the queue worker
./run schedule              # Run scheduled tasks (cron jobs)
```

### Code Quality Tools

Run code quality and analysis tools:

```bash
./run pint                  # Run Laravel Pint (code style fixer)
./run phpstan               # Run PHPStan (static analysis)
```

### Setup Commands

Quick setup and initialization:

```bash
./run install               # Complete fresh installation
./run key-generate          # Generate new application key
./run storage-link          # Create storage symbolic link
```

### View All Commands

```bash
./run help                  # Display all available commands
./run                       # Also displays help
```

---

## 📦 Docker Services

The application runs the following Docker services:

| Service   | Port | Description                                       |
| --------- | ---- | ------------------------------------------------- |
| **PHP**   | -    | PHP 8.2-FPM with Laravel                          |
| **Nginx** | 8000 | Web server                                        |
| **MySQL** | 3306 | Database server (user: laravel, password: secret) |
| **Redis** | 6379 | Cache and session storage                         |

---

## 🔧 Daily Development Workflow

### Starting Your Day

```bash
# Start all containers
./run up

# Check container status
./run ps

# View logs if needed
./run logs
```

### During Development

```bash
# Run migrations after pulling new code
./run migrate

# Clear cache when needed
./run cache-clear

# Run tests
./run test

# Access database if needed
./run mysql
```

### Ending Your Day

```bash
# Stop all containers
./run down
```

---

## 🐛 Troubleshooting

### View Logs

```bash
# View all logs
./run logs

# View specific service logs
./run logs php
./run logs nginx
./run logs mysql
./run logs redis
```

### Rebuild Containers

If you encounter issues, try rebuilding:

```bash
# Complete rebuild
./run rebuild

# Or manually
./run down
./run build
./run up
```

### Access Container Shell

Debug inside the container:

```bash
# Access as www-data user
./run bash
```

# Access as root user

./run root

````

### Clear All Caches
```bash
./run cache-clear
./run optimize-clear
````

### Check Container Status

```bash
./run ps
```

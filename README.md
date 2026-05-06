# GymFlow API

REST API backend for the GymFlow Gym Management System, built with Laravel 12 and PostgreSQL.

## Tech Stack

- **Laravel 12** — PHP web framework
- **PHP 8.2+**
- **PostgreSQL** — Primary database
- **Laravel Sanctum** — API token authentication
- **Pest** — Testing framework

## API Overview

| Endpoint | Description |
|---|---|
| `POST /api/loginWeb` | Pegawai login (web) |
| `POST /api/loginAndroid` | Member/Instruktur/Pegawai login (mobile) |
| `POST /api/logout` | Revoke current token |
| `GET /api/member` | List all members |
| `GET /api/instruktur` | List all instructors |
| `GET /api/jadwalUmum` | General schedules |
| `GET /api/jadwalHarian` | Daily schedules |
| `GET /api/presensiGym` | Gym attendance |
| `GET /api/presensiKelas` | Class attendance |
| `GET /api/laporanPendapatan/{tahun}` | Revenue report |

Full route list: `php artisan route:list`

## Prerequisites

- PHP 8.2+
- Composer
- PostgreSQL 14+

## Getting Started
```bash
# Clone the repository
git clone https://github.com/xkendrickz/gymflow-api.git
cd gymflow-api

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=gymflow
DB_USERNAME=postgres
DB_PASSWORD=your_password

SESSION_DRIVER=database
CACHE_STORE=database
```
```bash
# Run migrations and seed default accounts
php artisan migrate --seed

# Start development server
php artisan serve
```

API runs at `http://localhost:8000`

## Default Accounts

| Role | Username | Password |
|---|---|---|
| Admin | `admin` | `admin123` |
| Manajer Operasional | `mo` | `mo123` |
| Kasir | `kasir` | `kasir123` |

> Change these passwords before deploying to production.

## Project Structure
```
app/
├── Http/
│   └── Controllers/     # API controllers
├── Models/              # Eloquent models
│   ├── Pegawai.php
│   ├── Member.php
│   ├── Instruktur.php
│   └── ...
database/
├── migrations/          # Database schema
└── seeders/             # Default data
routes/
└── api.php              # API routes
```

## Running Tests
```bash
php artisan test
```

## Deployment (Railway)
```bash
# Set environment variables on Railway dashboard
# Run migrations automatically via railway.toml
php artisan migrate --force
php artisan db:seed --force
```

## Related Repositories

- [gymflow-web](https://github.com/xkendrickz/gymflow-web) — Vue.js frontend
- [gymflow-mobile](https://github.com/xkendrickz/gymflow-mobile) — Kotlin Android app
- [gymflow](https://github.com/xkendrickz/gymflow) — Project overview

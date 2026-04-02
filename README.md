# Cordoba Hotel Management System

## Team Members

- Mohamed Halawa
- Mohamed Tarek
- Maryam Elkhwaga
- Abdelhamid
- Ibrhaim Mohamed Eita

## Project Context

This project was built for **ITI Intake 46 – Open Source Track (Mansoura Branch)** as a combined submission for:

- Laravel Course
- Vue Course

---

## Instructor Quick Start

### 1) Requirements

- PHP 8.3+
- Composer 2+
- Node.js 20+
- npm 10+
- MySQL (or any database supported by Laravel)

### 2) Installation

From the project root:

1. Install backend dependencies
	- `composer install`
2. Install frontend dependencies
	- `npm install`
3. Create environment file
	- `cp .env.example .env`
4. Generate app key
	- `php artisan key:generate`
5. Configure database in `.env`
6. Run migrations + seeders
	- `php artisan migrate --seed`

> Alternative one-shot setup (from `composer.json`):
>
> - `composer run setup`

### 3) Run the application

Use the full development stack (Laravel server + queue worker + Vite):

- `composer run dev`

Then open:

- http://127.0.0.1:8000

---

## Demo Accounts (Seeded)

After running `php artisan migrate --seed`, these users are available:

- **Admin**: `admin@admin.admin`
- **Manager**: `manager@manager.manager`
- **Client/User**: `user@user.user`
- **Receptionist**: `rec@rec.rec`

Password for all seeded users:

- `password`

---

## What to Review in Demo

- Authentication and role-based access
- Manager area (floors, rooms, staff-related operations)
- Receptionist workflows
- Client-facing reservation flow
- Admin controls and statistics pages

---

## Quality Checks

### Run tests

- `php artisan test --compact`

### Format/Lint

- PHP format: `vendor/bin/pint --format agent`
- Frontend lint: `npm run lint:check`
- Frontend types: `npm run types:check`

---

## Troubleshooting

- If frontend changes are not visible:
  - keep `composer run dev` running, or run `npm run dev`
- If you get Vite manifest errors:
  - run `npm run build` (or run dev mode with Vite)
- If DB errors appear:
  - verify `.env` database credentials, then run `php artisan migrate --seed`

---

## Tech Stack

- Laravel 13 (PHP 8.3)
- Inertia.js v2 + Vue 3
- Tailwind CSS v4
- MySQL + Eloquent ORM
- PHPUnit 12

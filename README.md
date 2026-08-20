# Yabar ERP — Investment Client Portfolio Management System

A production-ready Laravel 13 application for managing investment clients and their portfolios at Yabar Finance Consult Limited. Built on the **Livewire / Volt** stack with Tailwind CSS, ApexCharts, RBAC via Spatie Permission, audit logging, PDF reports, and database notifications.

## Features

- **Authentication** — Login, password reset/confirm, email verification (Breeze Livewire). No public registration; staff accounts are created by admins.
- **Role-based access control** — `admin` (full access) and `staff` (view/create/edit clients, view portfolios, generate reports, search) roles with 12 granular permissions.
- **Client Information** — Full KYC-style registration form (personal, bank, next-of-kin, employment), auto-generated client IDs (`YFC-0001`), unique BVN / account number / email validation, soft deletes, and document uploads (ID card, utility bill, signature) with size/type validation and previews.
- **Client Portfolio** — One portfolio per client. Fixed-debt instruments with instrument details, payment breakdown schedules, payment history, and equity holdings (auto-computed values). Admin can fully CRUD all of it inside a single interactive Livewire manager.
- **Reports** — Per-instrument Print / PDF export, full portfolio PDF, and a columnar client PDF with selectable fields.
- **Dashboard** — Stat cards plus ApexCharts: investment distribution (pie), portfolio performance trend (line), and monthly investments (bar).
- **Global search** — Livewire-powered search across clients and portfolios.
- **Notifications** — Database notifications for new clients, new staff accounts, and due payments. `notifications:due-payments` runs daily at 08:00 (deduped).
- **Audit trail** — spatie/laravel-activitylog on every domain model plus login/logout/failed-login events.
- **Settings** — Company name, currency symbol, due-notice window, and upload size limit (admin-only).
- **Dark mode** — Toggleable (class-based) throughout.

## Tech Stack

- Laravel 13, PHP 8.4
- Livewire 4 + Volt (Breeze stack), Alpine.js
- Tailwind CSS 3 (PostCSS) + Vite
- ApexCharts
- spatie/laravel-permission, spatie/laravel-activitylog
- barryvdh/laravel-dompdf
- Pest (78 tests — unit + feature)

## Requirements

- PHP 8.3+ (with `pdo_mysql`, `gd`, `zip`)
- Composer 2
- Node 20+ and npm
- MySQL 8 (or SQLite for tests)

## Installation

```bash
composer install
copy .env.example .env        # Windows — or: cp .env.example .env
php artisan key:generate

# Configure DB_* in .env, then:
php artisan migrate --seed    # seeds roles/permissions, states+LGAs, banks, users, demo data

npm install
npm run build                 # production assets (npm run dev for development)
```

## Default Credentials

| Role  | Email               | Password  |
|-------|---------------------|-----------|
| Admin | `admin@yabarconsult.com` | `password` |
| Staff | `staff@yabar.local` | `password` |
| Staff | `john@yabar.local`  | `password` |

## Roles & Permissions

| Permission          | Admin | Staff |
|---------------------|:-----:|:-----:|
| view clients        | ✅     | ✅     |
| create clients      | ✅     | ✅     |
| edit clients        | ✅     | ✅     |
| delete clients      | ✅     | ❌     |
| view portfolios     | ✅     | ✅     |
| create portfolios   | ✅     | ❌     |
| edit portfolios     | ✅     | ❌     |
| delete portfolios   | ✅     | ❌     |
| generate reports    | ✅     | ✅     |
| use global search   | ✅     | ✅     |
| manage settings     | ✅     | ❌     |
| manage staff        | ✅     | ❌     |

## Tests

```bash
php artisan test
```

Tests run against an in-memory SQLite database (`phpunit.xml`) and cover authentication, RBAC, client CRUD + validation, portfolio CRUD + the interactive manager, staff creation, settings, PDF report generation, and the due-payment notification command.

## Scheduling

The due-payment notifier is registered in `routes/console.php`. Run the Laravel scheduler every minute on your server:

```bash
* * * * * php /path/to/app/artisan schedule:run >> /dev/null 2>&1
```

## Project Structure

```
app/
  Console/Commands/SendDuePaymentNotifications.php
  Http/Controllers/        # Dashboard, Client, Portfolio, Report, Settings, Staff
  Livewire/                # PortfolioManager, ClientIndex, PortfolioIndex, GlobalSearch, NotificationsBell
  Models/                  # Client, Portfolio, FixedDebtInstrument, InstrumentDetail, PaymentBreakdown, PaymentHistory, Equity, Document, Bank, State, Lga, NextOfKin, Setting, User
  Notifications/           # ClientRegistered, StaffCreated, PaymentDue
  Policies/  Services/  Support/helpers.php
  View/Components/         # AppLayout, GuestLayout
database/
  migrations/  factories/  seeders/ (roles, states+774 LGAs, banks, users, demo data)
resources/views/           # layouts, components, clients, portfolios, reports, staff, settings, errors, livewire
```
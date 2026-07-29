# HRM (Human Resource Management System)

A comprehensive, modular **Human Resource Management System** built with **Laravel 12** and **nwidart/laravel-modules**. This system provides end-to-end HR operations including employee management, attendance tracking, leave management, KPI/performance evaluation, payroll processing, loan management, and more.

---

## Features

### 👥 Employee Management
- **11-step employee creation wizard** — Collects comprehensive employee data including personal info, addresses, banking, documents, education, experience, job history, languages, skills, and dependents
- **Employee profiles** — View detailed employee information
- **Section-wise editing** — Update specific employee sections independently
- **Employee reports** — Attendance, overtime, salary, KPI, and loan reports
- **Employee search** — Advanced search with DataTables integration
- **Skill categories** — Manage skill categorization

### ⏰ Attendance Management
- **Daily attendance tracking** — Record and manage employee attendance
- **Attendance devices** — Manage biometric/attendance devices
- **Attendance approval workflow** — Approve/disapprove attendance records
- **Attendance reports** — Generate detailed attendance reports
- **Overtime tracking** — Calculate and report overtime
- **Employee attendance rules** — Define per-employee attendance policies
- **Employee weekends** — Configure weekly off-days per employee

### 🏖️ Leave Management
- **Leave types** — Configurable leave categories (annual, sick, casual, etc.)
- **Leave applications** — Apply for leave with balance validation
- **Approval workflow** — Multi-level approve/disapprove/reject workflow
- **Leave balance management** — Track and manage employee leave balances
- **Leave encashment** — Convert unused leave to monetary value

### 📊 KPI & Performance Management
- **Dashboard** — Performance overview and analytics
- **Daily performance tracking** — Monitor day-to-day performance
- **Monthly performance evaluation** — Monthly performance reviews with detailed breakdowns
- **Task management** — Create, assign, and track KPI-related tasks
- **Monthly reviews** — Evaluate behavior, bonuses, and penalties
- **KPI settings** — Configure categories and performance indicators

### 💰 Payroll & Salary Management
- **Salary components** — Define earnings, deductions, and allowances
- **Employee salary structure** — Assign salary components to employees
- **Payroll runs** — Generate monthly payroll with preview
- **Payroll approval** — Approve and lock payroll runs
- **Payment processing** — Mark salaries as paid with payment list management
- **Recalculation** — Recalculate payroll when needed
- **Salary grades** — Define salary grade structures

### 💳 Loan Management
- **Loan application** — Employees can apply for loans
- **Approval workflow** — Approve/reject loan applications
- **Disbursement** — Track loan disbursement
- **Loan calculation** — Calculate EMI and repayment schedules
- **My loans** — Employees can view their loan status

### 🏢 Organization Management
- **Company** — Manage company information
- **Branch** — Multi-branch support
- **Department** — Department management
- **Designation** — Job designation/position management
- **Shift** — Shift scheduling and management

### 🎉 Holidays
- **Holiday management** — Define public and company holidays
- **Holiday assignment** — Assign holidays to branches/departments

### 📢 Notices
- **Notice management** — Create and publish company notices

### ⚙️ Settings
- **System settings** — Configure global application settings

---

## Tech Stack

| Technology | Purpose |
|------------|---------|
| **Laravel 12** | PHP framework |
| **PHP 8.2+** | Server-side language |
| **MySQL** | Database |
| **nwidart/laravel-modules** | Modular architecture |
| **Tailwind CSS 3** | Utility-first CSS framework |
| **Alpine.js** | Lightweight JavaScript framework |
| **Vite** | Build tool and asset bundler |
| **Yajra DataTables** | Server-side data tables |
| **Select2** | Enhanced select inputs |
| **Chart.js** | Charts and analytics |
| **FullCalendar** | Calendar views |
| **Axios** | HTTP client |
| **Laravel Breeze** | Authentication scaffolding |

---

## Requirements

- PHP ^8.2
- Composer 2.x
- Node.js 18+ & npm
- MySQL 8.0+ (or MariaDB 10.3+)
- Web server (Apache/Nginx) or Laravel Valet/Herd

---

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/icfo-bookme/HRM.git
cd HRM
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure your database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hrm
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Install & Build Frontend Assets

```bash
npm install
npm run build
```

### 5. Run Database Migrations

```bash
php artisan migrate
```

### 6. (Optional) Seed Database

```bash
php artisan db:seed
```

### 7. Start Development Server

```bash
php artisan serve
```

Or use the all-in-one dev command (runs server, queue worker, logs, and Vite concurrently):

```bash
composer run dev
```

---

## Quick Setup

For a fresh installation, run the setup command which handles everything:

```bash
composer run setup
```

This will:
1. Install Composer dependencies
2. Create `.env` from `.env.example`
3. Generate application key
4. Run database migrations
5. Install npm dependencies
6. Build frontend assets

---

## Module Structure

The application uses a modular architecture with the following modules:

```
Modules/
├── Attendance/       # Attendance tracking, devices, reports, overtime
├── Branch/           # Branch management
├── Company/          # Company information
├── Department/       # Department management
├── Designation/      # Job designations
├── Employee/         # Employee lifecycle, profiles, reports
├── Holidays/         # Holiday management & assignment
├── Kpi/              # Performance management, tasks, reviews
├── Leave/            # Leave types, applications, encashment
├── Loan/             # Loan applications & disbursement
├── Notice/           # Company notices
├── Salary/           # Salary components, structure, payroll
├── SalaryGrade/      # Salary grade definitions
├── Setting/          # System settings
└── Shift/            # Shift scheduling
```

Each module follows the standard **nwidart/laravel-modules** structure:

```
ModuleName/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Requests/
│   ├── Models/
│   └── Providers/
├── config/
├── database/
│   └── migrations/
├── resources/
│   └── views/
├── routes/
│   └── web.php
├── Services/
└── tests/
```

---

## Available Commands

| Command | Description |
|---------|-------------|
| `composer run setup` | Full project setup (deps, env, migrate, build) |
| `composer run dev` | Start all dev services concurrently |
| `composer run test` | Run tests with fresh config |
| `npm run build` | Build frontend assets |
| `npm run dev` | Start Vite dev server |

---

## Testing

```bash
composer run test
```

Or run PHPUnit directly:

```bash
php artisan test
```

---

## Deployment

The project includes a GitHub Actions deployment workflow (`.github/workflows/deploy.yml`). Configure your deployment secrets in GitHub repository settings.

---

## Security

If you discover any security vulnerabilities, please report them via the repository's issue tracker.

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
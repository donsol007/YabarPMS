# Build Prompt — Investment Client Portfolio Management System (Yabar ERP)

> Greenfield Laravel project. Build a complete, production-ready full-stack web application.
> This document is the single source of truth for the build — follow it exactly.

## Stack

- Laravel 13 + PHP 8.4
- MySQL 8
- Livewire 4 + Tailwind CSS + Alpine.js (TALL stack, no separate JS framework)
- Pest for testing
- spatie/laravel-permission (RBAC), spatie/laravel-activitylog (audit), barryvdh/laravel-dompdf (PDF)
- ApexCharts for dashboard charts
- npm + Vite for asset bundling

## Scope

Authentication, admin-only staff signup, dashboard with charts, Client Information module
(with document uploads), Client Portfolio module (fixed debt, payment history, equity),
reports (PDF), global search, notifications, RBAC, audit logging, and the full UI/UX
requirements below.

---

## 1. Authentication

### Login page
- Fields: Email, Password.
- Features: authenticate, Remember Me, Forgot Password, Show/Hide Password toggle,
  loading spinner on submit, validation, redirect to Dashboard after login.
- Rate-limited login attempts (throttling).

### Staff signup page (admin-only, NOT public)
- Only an Admin can create staff accounts. No public registration.
- Fields: Full Name, Email, Phone Number, Password, Confirm Password.
- Features: password strength validation, duplicate-email prevention (unique email),
  and no auto-login (the admin is already authenticated; the new staff logs in later).
  Do NOT implement an "auto login after registration" flow — it is explicitly dead.
- Only reachable by admin role.

### Forgot password
- Send reset link by email (use Mailpit / log driver for local dev).
- Standard Laravel reset flow.

---

## 2. Roles & Permissions

Two roles via spatie/laravel-permission: **Admin** and **Staff**.

| Capability | Admin | Staff |
|---|---|---|
| Register & edit Client Information | ✅ | ✅ |
| Delete clients | ✅ | ❌ |
| Create / edit / delete portfolios | ✅ | ❌ |
| View portfolios (all, read-only) | ✅ | ✅ |
| Generate reports | ✅ | ✅ |
| Global search | ✅ | ✅ |
| Settings | ✅ | ❌ |
| Profile (own info + password) | ✅ | ✅ |

---

## 3. Dashboard

- Left sidebar menu: Dashboard, Client Portfolio, Client Information, Reports, Profile,
  Settings, Logout.
- Top navigation: user profile dropdown, notifications bell, dark-mode toggle.
- Cards: Total Clients, Total Investment (= sum of all portfolio values across all
  portfolios: Fixed Debt + Equity), Total Active Portfolios, Recent Registrations.
- Charts (ApexCharts):
  - Investment Distribution — pie by client portfolio value.
  - Portfolio Performance — line over time.
  - Monthly Investments — bar.
- Breadcrumb navigation on all pages.

---

## 4. Client Information Module

Complete registration form with validation. Client ID auto-generated as `YFC-0001`,
`YFC-0002`, ... (sequential, unique, read-only).

### Personal Information
- ID (auto generated)
- Surname (mandatory)
- First Name (mandatory)
- Middle Name (mandatory)
- Sex (mandatory) — select
- Date of Birth (mandatory)
- Mobile Number (mandatory, Nigerian format validation)
- Mother's Maiden Name
- Residential Address
- State of Origin — select from seeded 36 states + FCT
- Local Government Area (LGA) — dependent dropdown filtered by selected State
- Marital Status — select (Single / Married / Divorced / Widowed)
- Religion — select (Christianity / Islam / Traditional / Other)
- Email Address (mandatory, unique)

### Investment Information
- Amount to Invest

### Banking Information
- Bank Name (from seeded banks list)
- Account Name
- Account Number (10-digit validation)
- Account Type — select (Savings / Current / Domiciliary)
- BVN (11-digit validation)
- Date of Account Opening
- Bank Address

### Employment Information
- Occupation
- Employer's Name
- Employer's Address

### Personal Information (extras)
- Hobbies

### Next of Kin
- Next Kin Name
- Next Kin Address
- Next Kin Phone Number
- Next Kin Relationship — select
- Next Kin Email Address

### Document Uploads
- One file per type: ID Card, Utility Bill, Signature.
- Drag-and-drop upload, file preview modal, replace allowed.
- PDF + JPG/PNG, max 5MB each, stored on local public disk.
- Upload validation (type + size + required on create? no — optional).
- Staff can register and edit; Admin additionally can delete (soft-delete).

---

## 5. Client Portfolio Module

- One client has exactly one portfolio (1:1). Clients without a portfolio are allowed.
- When creating a portfolio, the "Client Name" select shows ONLY clients that do not
  already have a portfolio (populated from client name in the Client Information module).
- Admin: create / edit / delete portfolios. Staff: view-only (list + detail), no mutations.
- Staff list shows all client portfolios (no ownership filtering).

### Portfolio detail page — single scroll page
1. **Summary card** on top: Total Fixed Debt, Total Equity Value,
   Total Portfolio Value = Total Fixed Debt + Total Equity Value.
2. **Fixed Debt Instruments card** — table columns: Description, Amount, Status.
   Actions per row: View, Edit, Delete, Print, Export PDF.
   Status options: **Active / Matured / Closed**.
   - Each instrument has a details section (header select populated from the instrument's
     Description field): Subscription Date, Instrument (default `CP`), Tenor,
     Rental Rate, Settlement Date.
   - Subheading **Payment Breakdown** — table columns: ROI, Amount, Payment Date,
     Status (options: Paid, Unpaid, Blank; default Blank).
     Status badges mapping: Paid → "Paid", Unpaid → "Pending", Blank → "Not due".
     Manual entry only — no auto-generation from tenor/rate. One source of truth.
     Actions per row: View, Edit, Delete.
   - Add / remove / edit any instrument and its details.
3. **Payment History card** — table columns: Date, Amount Paid, Payment Status.
   Independent records; no cross-linking to payment breakdown. Actions: View, Edit, Delete.
4. **Equity card** — table columns: Stock, Unit, Price, Value (= Unit × Price, computed).
   Actions: View, Edit, Delete. Add / remove / edit any equity details.

---

## 6. Reports

Both roles can generate both reports. PDF via dompdf.

- **Portfolio Report**: shows a list of all portfolios; user selects one, then generates
  and downloads a PDF containing: portfolio summary + all fixed-debt instruments
  (with details and payment breakdown) + payment history + equity tables.
- **Client Report**: shows all clients as a list; user checks which fields to include,
  then generates a columnar PDF table of the selected fields.

---

## 7. Global Search

- Single search box in the top navigation.
- Searches: Client Name, Phone Number, Email, Account Number.
- Results link to the matching client / its portfolio.
- Visible to both roles.

---

## 8. Notifications

- In-app only (top-nav bell, unread indicator).
- Triggers:
  1. New client registered.
  2. New staff account created.
  3. ROI / payment becoming due within 7 days (daily scheduled command evaluates the
     payment breakdown / payment history and creates notifications).
- Stored via Laravel's database notification driver.

---

## 9. Audit Logging

spatie/laravel-activitylog, recording:
- Auth events (login, logout, failed login).
- All mutations on clients, portfolios, fixed-debt instruments, payment breakdowns,
  payment history, equity, documents (who did what, when).

---

## 10. Security

- Authentication (sessions), Role-Based Access Control (Admin / Staff) enforced on
  routes and Livewire components via middleware + policies.
- CSRF protection (default), input validation (Form Requests), file upload validation.
- Secure password hashing (bcrypt default), rate limiting on auth routes.
- Audit logging (above). Soft-deletes for clients, portfolios, instruments.
- Error pages: 404 and 500.

---

## 11. UI/UX

- Modern professional admin dashboard, responsive and mobile-friendly, professional
  color theme.
- Class-based Tailwind dark mode, persisted in localStorage, toggle in top nav.
- Hand-rolled Blade/Alpine components (no component kit).
- Sidebar navigation, breadcrumbs, pagination, sorting, search.
- Toast notifications (Livewire flash), confirmation dialogs for destructive actions.
- Loading skeletons, empty state screens.
- 404 / 500 error pages.

---

## 12. Locale & Formatting

- Currency: NGN (₦).
- Timezone: Africa/Lagos.
- Dates: d/m/Y.

---

## 13. Profile & Settings Pages

- **Profile** (all roles): view/edit own info, change password.
- **Settings** (admin-only): company name, currency symbol, due-notice days,
  upload max size.

---

## 14. Database Design

Normalized models with proper foreign keys:

- users
- roles / permissions (spatie)
- clients
- portfolios
- fixed_debt_instruments
- instrument_details (subscription date, instrument, tenor, rental rate, settlement date)
- payment_breakdowns (ROI, amount, payment date, status)
- payment_histories (date, amount paid, status)
- equities (stock, unit, price, value)
- banks (seeded bank name list)
- next_of_kin
- documents
- notifications (Laravel)
- activity_log (spatie)
- states (36 + FCT) and lgas (seeded, linked to states)

---

## 15. Seeder

Seed:
- First **Admin** account (documented default credentials, e.g. `admin@yabar.local` /
  a known password stated in the README).
- A couple of demo Staff accounts.
- Sample clients with portfolios (fixed debt instruments + breakdowns + payment history
  + equity) so dashboard charts, search, and the notification bell are populated on
  first login.
- Banks, states, and LGAs reference data.

---

## 16. Testing (Pest)

Cover at minimum:
- Auth: login success/failure, remember-me, forgot password, rate limiting.
- RBAC: staff blocked from portfolio create/edit/delete and settings; admin allowed;
  staff blocked from client delete.
- Client CRUD + validation rules (BVN, account number, email uniqueness, mandatory fields).
- Portfolio CRUD, 1:1 client constraint (select shows only portfolio-less clients).
- Payment breakdown status / badge mapping.
- Equity value computation.
- Report generation (PDF renders without error for both report types).
- Audit logging fires on key mutations.

---

## 17. Deliverables

- Complete Laravel 13 application in the project root.
- Migrations + seeders (run `php artisan migrate --seed` to get a working system).
- Pest test suite passing.
- README with: setup instructions, default credentials, and how to run the
  scheduled notification command.
- Dark mode, toasts, skeletons, empty states, 404/500 wired in.
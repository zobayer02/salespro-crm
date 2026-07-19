# SalesPro CRM

SalesPro CRM is a Laravel-based Sales, Inventory and Customer Relationship Management system built for the SinodTech technical assessment. It covers owner/admin operations, product inventory, branch-wise sales, customer purchase tracking, inactive customer follow-up, KPI conversion tracking, invoice email logging, and a secure product integration API.

## Tech Stack

- PHP 8.2+
- Laravel 12
- MySQL or MariaDB
- Blade templates
- Vite
- SMTP-ready email workflow

## Implemented Features

- Owner/Admin login system
- Product catalog with SKU, price and stock quantity
- Sales recording with automatic stock deduction
- Prevention of sales when stock is insufficient
- Customer purchase history tracking
- Automatic inactive/lost customer detection based on purchase history
- Inactive customer assignment to employees for follow-up
- Automatic KPI score update when an assigned inactive customer makes a new purchase
- Email-based customer re-engagement workflow
- Multi-branch structure with branch-wise inventory
- Branch-wise sales tracking
- Email invoice generation/logging after successful sales
- Secure product integration REST API with bearer token authentication
- Seeders with realistic sample data
- README with setup, migration, seeding and local run instructions

Some parts are implemented as core business logic and admin-side workflow according to the assessment scope. For example, KPI conversion currently happens when a sale is created inside the system. In a real production scenario, the same logic can be connected to an external e-commerce order API/webhook so that purchases from a third-party website automatically create sales and trigger KPI updates.

## Possible Future Extensions

- External e-commerce order webhook/API to receive customer purchases from another website
- Branch-specific admin/staff login portal
- Role-based access control for owner, branch admin and employees
- Branch-wise dashboard permissions
- SMS re-engagement integration if an SMS provider is provided
- Advanced reports/export features

## Requirements

- PHP 8.2 or higher
- Composer
- MySQL or MariaDB
- Node.js and npm, only required if rebuilding frontend assets
- SMTP credentials, only required if testing real email sending

## Setup Instructions

Create the database:

```sql
CREATE DATABASE IF NOT EXISTS `salespro` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Install PHP dependencies:

```bash
composer install
```

Copy environment file and generate app key:

```bash
copy .env.example .env
php artisan key:generate
```

Update the database values in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salespro
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seeders:

```bash
php artisan migrate:fresh --seed
```

Start the local development server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Open the application:

```text
http://127.0.0.1:8000/login
```

Default owner/admin login:

```text
Email: owner@salespro.test
Password: password
```

## Optional Frontend Build

The project is usable with the committed Blade/CSS structure. If frontend assets need to be rebuilt, run:

```bash
npm install
npm run build
```

For Vite development mode:

```bash
npm run dev
```

## Environment Configuration Notes

Important `.env` values:

```env
APP_NAME=SalesPro
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=Asia/Dhaka

LOST_CUSTOMER_DAYS=90
SALES_PRO_API_TOKEN=salespro-demo-token

MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail-address@gmail.com
MAIL_PASSWORD=your-gmail-app-password
MAIL_FROM_ADDRESS=your-gmail-address@gmail.com
MAIL_FROM_NAME="SalesPro"
```

Notes:

- `LOST_CUSTOMER_DAYS` controls when a customer becomes inactive/lost based on purchase history.
- `SALES_PRO_API_TOKEN` is used by the sample API client seeder.
- Real SMTP credentials are not required for local UI testing.
- Do not commit `.env`, real SMTP credentials, or production API tokens.

## Seeder Data

The main database seeder creates:

- Owner/admin user
- Sample products
- Branches
- Branch-wise inventory records
- Customers
- Employees
- Sales and sale items
- Customer assignments
- Invoice email logs
- API integration client

Seeder command:

```bash
php artisan db:seed
```

Clean database reset with all sample data:

```bash
php artisan migrate:fresh --seed
```

## Admin Routes

```text
GET  /login
POST /login
POST /logout
GET  /dashboard
GET  /admin/dashboard
GET  /admin/products
GET  /admin/products/create
POST /admin/products
GET  /admin/products/{product}/edit
PUT  /admin/products/{product}
DELETE /admin/products/{product}
GET  /admin/inventory
GET  /admin/branches
GET  /admin/branches/create
POST /admin/branches
GET  /admin/branches/{branch}/edit
PUT  /admin/branches/{branch}
GET  /admin/invoices
GET  /admin/sales
GET  /admin/sales/create
POST /admin/sales
GET  /admin/sales/{sale}
GET  /admin/customers
GET  /admin/customers/inactive
GET  /admin/customers/create
POST /admin/customers
GET  /admin/customers/{customer}
GET  /admin/customers/{customer}/edit
PUT  /admin/customers/{customer}
DELETE /admin/customers/{customer}
GET  /admin/employees
GET  /admin/employees/create
POST /admin/employees
GET  /admin/employees/{employee}/edit
PUT  /admin/employees/{employee}
DELETE /admin/employees/{employee}
GET  /admin/assignments
GET  /admin/assignments/create
POST /admin/assignments
GET  /admin/reengagements
POST /admin/reengagements
GET  /admin/kpi-overview
GET  /admin/api-integrations
GET  /admin/profile
PUT  /admin/profile
```

## Product Integration API

The API exposes active product data to third-party clients using bearer token authentication.

Create or rotate an integration token:

```bash
php artisan salespro:api-token "Third Party Store"
```

Create a deterministic local test token:

```bash
php artisan salespro:api-token "Third Party Store" --token=salespro-demo-token
```

Use the token in requests:

```http
Authorization: Bearer salespro-demo-token
Accept: application/json
```

Endpoints:

```text
GET /api/v1/products
GET /api/v1/products/{sku}
GET /api/v1/products?search=iphone
GET /api/v1/products?sku=IP15PRO
```

Example request:

```bash
curl.exe -H "Authorization: Bearer salespro-demo-token" -H "Accept: application/json" http://127.0.0.1:8000/api/v1/products
```

Example response:

```json
{
  "data": [
    {
      "sku": "IP15PRO",
      "product_name": "iPhone 15 Pro",
      "price": 135000,
      "available_stock": 24
    }
  ]
}
```

Invalid or missing token response:

```json
{
  "message": "Unauthenticated."
}
```

## Core Database Tables

- `users`
- `products`
- `customers`
- `employees`
- `branches`
- `branch_inventories`
- `sales`
- `sale_items`
- `customer_assignments`
- `reengagement_logs`
- `api_clients`
- `invoice_email_logs`

## Final Testing Checklist

Run the verification commands:

```bash
php artisan config:clear
php artisan migrate:fresh --seed
php artisan test
php artisan serve --host=127.0.0.1 --port=8000
```

Manual checks:

- Login works with the seeded owner/admin account.
- Dashboard loads with seeded sales, product, customer and branch stats.
- Product CRUD works and SKU validation blocks duplicates.
- Branch management works.
- Branch-wise inventory appears correctly.
- Sale creation deducts branch stock and global product stock.
- Sale creation blocks insufficient stock.
- Sale detail page displays sale items and branch information.
- Invoice email logs are created after sale creation.
- Customer purchase history shows total spent, purchase count and last purchase date.
- Inactive customers are detected from purchase history.
- Inactive customer assignment blocks active customers and duplicate active assignments.
- KPI score increases when an assigned inactive customer makes a new purchase.
- Re-engagement email workflow logs sent or failed attempts.
- API endpoint rejects missing/invalid bearer tokens.
- API endpoint returns active product data with a valid bearer token.

## GitHub Public Repository Readiness

Before pushing to GitHub:

- Keep `.env` untracked.
- Keep `vendor/`, `node_modules/`, logs and build cache untracked.
- Use `.env.example` only for placeholder config.
- Do not commit real Gmail app passwords or production API tokens.
- Run `php artisan test` successfully before final submission.
- Confirm README setup instructions work on a clean machine.

Suggested Git commands:

```bash
git init
git add .
git commit -m "Complete SalesPro CRM assessment"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/salespro-crm.git
git push -u origin main
```

## Documentation

- `docs/SUBMISSION_CHECKLIST.md`
- `docs/INTERVIEW_NOTES.md`

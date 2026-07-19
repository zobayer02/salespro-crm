# SalesPro Final Submission Checklist

Use this checklist before submitting the public GitHub repository.

## Required Environment

- PHP 8.2+
- Composer
- MySQL
- Node.js and npm only if rebuilding frontend assets
- SMTP credentials only if testing real email delivery

## Setup Verification

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan test
php artisan serve --host=127.0.0.1 --port=8000
```

## Login

```text
URL: http://127.0.0.1:8000/login
Email: owner@salespro.test
Password: password
```

## Admin Pages To Review

- Dashboard
- Products
- Branches
- Branch-wise Inventory
- Sales
- Sale Details
- Invoices
- Customers
- Customer Purchase History
- Inactive Customers
- Assign Customers
- Re-engagement Logs
- Employees
- KPI Overview
- API Integrations
- Profile

## Business Logic Checks

- Product SKU duplication is blocked.
- Sale total is calculated automatically.
- Sale creation deducts branch inventory and global product stock.
- Sale creation is blocked when branch stock is insufficient.
- Customer purchase history updates after sales.
- Inactive customers are detected based on `LOST_CUSTOMER_DAYS`.
- Inactive customers can be assigned to employees.
- Active customers cannot be assigned as inactive follow-up leads.
- Duplicate active assignments are blocked.
- Assigned inactive customer conversion updates assignment status and employee KPI.
- Re-engagement email attempts are logged as sent or failed.
- Invoice email attempts are logged after successful sales.

## API Verification

```bash
curl.exe -H "Authorization: Bearer salespro-demo-token" -H "Accept: application/json" http://127.0.0.1:8000/api/v1/products
```

Expected response shape:

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

Missing or invalid token must return:

```json
{
  "message": "Unauthenticated."
}
```

## Security Checklist

- `.env` is ignored by Git.
- `.env.example` contains placeholder SMTP credentials only.
- Real Gmail app password is not committed.
- Production API tokens are not committed.
- API tokens are stored as SHA-256 hashes.
- Owner/admin routes are protected by authentication and owner middleware.
- Third-party API routes require a bearer token.
- Invoice email failures are logged without rolling back completed sales.

## GitHub Public Repo Checklist

- README is complete.
- Setup/run/migration/seeder instructions are included.
- Feature list is included.
- Environment config notes are included.
- Final testing checklist is included.
- `vendor/`, `node_modules/`, `.env`, logs and cache files are ignored.
- Tests pass before final push.

## Final Commands

```bash
php artisan config:clear
php artisan migrate:fresh --seed
php artisan test
```

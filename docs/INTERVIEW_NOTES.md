# SalesPro Interview Notes

## Architecture

SalesPro is a Laravel MVC application with Blade views for the owner/admin dashboard, Eloquent models for business entities, form request classes for validation, and a service class for sale creation. Admin pages use session authentication with owner-only route protection. The e-commerce integration is exposed through versioned JSON API routes protected by a bearer token middleware.

## Core Modules

- Authentication: owner login, logout and owner-only admin access.
- Products: product catalog with SKU, price, stock quantity and active/inactive status.
- Branches and inventory: multiple store locations with branch-wise product stock.
- Sales: transaction-safe sale recording with branch-specific automatic stock deduction.
- Customers: purchase history, purchase frequency and last purchase calculation.
- CRM: automatic inactive customer detection using `LOST_CUSTOMER_DAYS`.
- Assignments: inactive customers can be assigned to employees for follow-up.
- KPI: employee KPI score automatically increases after an assigned inactive customer purchases again.
- Re-engagement: email compose flow and sent/failed attempt logs.
- Invoices: HTML invoice email attempt after successful sale with delivery logs.
- API Integration: secure product API for third-party e-commerce platforms.

## Database Design

The schema is normalized around business workflows:

- `products` stores catalog and inventory data.
- `branches` stores store locations.
- `branch_inventories` stores product stock per branch.
- `customers` stores CRM contact data.
- `employees` stores follow-up employee data and KPI score.
- `sales` stores order-level records.
- `sale_items` stores product line items for each sale.
- `customer_assignments` links inactive customers to employees.
- `reengagement_logs` stores email attempts and delivery status.
- `invoice_email_logs` stores invoice email delivery attempts.
- `api_clients` stores third-party API client tokens as hashes.

## Business Logic

Sale creation is handled in `CreateSaleService`. It validates selected branch stock using row locking, creates the sale and sale items inside a database transaction, deducts branch stock and global product stock automatically, and converts active customer assignments when the assigned customer purchases again. This keeps inventory and KPI updates consistent.

After the sale is committed, the system attempts to send an HTML invoice email to the customer. Invoice email success or failure is logged without rolling back the sale.

Inactive customer detection is automatic. A customer is inactive if they have never purchased or if their last purchase is older than the configured `LOST_CUSTOMER_DAYS` period.

The product API only returns active products and exposes limited fields required by the assessment: SKU, product name, price and available stock.

## AI Usage Policy Answer

AI-assisted development tools were used for implementation support, but the architecture, schema, business rules and code flow are explainable by the developer. The codebase uses Laravel patterns, validation requests, middleware, Eloquent relationships, transaction-safe business logic and automated tests to keep the implementation maintainable.

## Testing Strategy

Feature tests cover authentication, product management, customer management, sales, assignment and KPI workflows, re-engagement email logging, dashboard stats and product API authentication/response behavior.

Run:

```bash
php artisan test
```

# 📦 Warehouse Inventory & Stock Management RESTful API (WMS)

A production-grade RESTful API developed with **Laravel 11** for real-time stock management, physical location tracking, automated minimum stock alerts, and audit logging — implementing core **Warehouse Management System (WMS)** capabilities.

Developed as an academic software engineering project within the **Higher Technical Professional Diploma in Technologies and Information Systems Programming (TPSI)** at **IPMAIA** (*Instituto Politécnico da Maia*).

---

## ⚙️ Installation & Setup

### Prerequisites
- PHP >= 8.2
- Composer
- SQLite

### Setup Steps

```bash
# 1. Install dependencies
composer install

# 2. Configure environment file (.env)
# Ensure SQLite database connection is defined:
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite

# 3. Run database migrations & seed test data
php artisan migrate:fresh --seed

# 4. Start local development server
php artisan serve
```

The API will be accessible at `http://localhost:8000/api`.

### Test Credentials (Seeded)

| Email | Password | Role | Permissions |
|---|---|---|---|
| `admin@armazem.pt` | `password123` | admin | Full access (create products, manual adjustments) |
| `operador@armazem.pt` | `password123` | operator | Movements and queries (read-only, no product creation/adjustments) |

---

## 📡 API Routes & Endpoints

All endpoints (except `/login`) require Bearer Token authentication. Routes marked with 🔒 require the `admin` role.

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/api/login` | Authentication — returns Sanctum Bearer token |
| `POST` | `/api/logout` | Terminate current session (revokes token) |
| `GET` | `/api/products` | List products with total stock and cursor pagination |
| `POST` | `/api/products` 🔒 | Register a new product with minimum stock threshold |
| `POST` | `/api/products/{id}/adjust` 🔒 | Manual stock adjustment with mandatory audit log |
| `POST` | `/api/movements` | Log movement (inbound, outbound, transfer) |
| `GET` | `/api/movements` | Movement history with query filters |
| `GET` | `/api/locations` | List warehouse physical storage locations |
| `GET` | `/api/locations/{id}/products` | Query products and quantities at a specific location |
| `GET` | `/api/alerts/low-stock` | Retrieve products below designated minimum stock |
| `GET` | `/api/reports/rotation` | Stock turnover and rotation report by date range |

---

## 🧪 Postman API Testing Workflow

### Initial Configuration
1. Import the Postman collection `api-armazem.postman_collection.json` and environment `Armazem API.postman_environment.json`.
2. Select the **"Armazem API"** environment in the top right selector.

### Testing Execution Flow
The collection is structured for single active session testing. The token returned upon login is dynamically saved into the `{{token}}` collection variable, inherited across all child requests via *"Inherit auth from parent"*.

- **Testing as Administrator:**
  1. Execute **Login Admin** → captures Admin token in `{{token}}`.
  2. Execute any write request (Create Product, Stock Adjustment, etc.) → succeeds normally (200/201).
- **Testing RBAC with Operator:**
  1. Execute **Logout** to invalidate previous session.
  2. Execute **Login Operador** → captures Operator token in `{{token}}`.
  3. Attempt **Create Product** or **Stock Adjustment** → returns **403 Forbidden** (RBAC enforcement verified ✅).
  4. Execute query endpoints (`GET /products`, `/movements`) → succeeds normally.

### Automated Collection Runner
The collection includes automated assertion tests per route. Run the sequence in order:
`Authentication` → `Products` → `Movements` → `Locations` → `Reports`.

---

## 🗄️ Entity-Relationship (ER) Diagram

```text
┌─────────────┐       ┌──────────────────────────────────────────┐       ┌─────────────┐
│   products  │       │               movements                  │       │  locations  │
│─────────────│       │──────────────────────────────────────────│       │─────────────│
│ id          │◄──────│ product_id (FK)                          │       │ id          │
│ name        │       │ user_id (FK) ──────────────────────────► │users  │ name        │
│ sku (unique)│       │ type (in|out|transfer|adjust)            │       │ code(unique)│
│ min_stock   │       │ from_location_id (FK, nullable) ────────►│───────│ created_at  │
│ created_at  │       │ to_location_id   (FK, nullable) ────────►│       │ updated_at  │
│ updated_at  │       │ quantity                                 │       └─────────────┘
└─────────────┘       │ justification (nullable)                 │
                      │ created_at                               │       ┌─────────────┐
                      │ updated_at                               │       │    users    │
                      └──────────────────────────────────────────┘       │─────────────│
                                                                         │ id          │
                                                                         │ name        │
                                                                         │ email       │
                                                                         │ password    │
                                                                         │ role        │
                                                                         └─────────────┘
```

---

## 🎯 Architecture & Design Decisions

### Single-Record Transfers
Internal location transfers are persisted as a single movement record with `type='transfer'`, `from_location_id`, and `to_location_id`. The alternative (splitting into separate `out` + `in` events) would distort the global inventory count. Storing as a single atomic event preserves global stock accuracy while allowing location-specific derivation.

### Real-Time Dynamic Stock Calculation
Total product stock is calculated dynamically via the `getTotalStockAttribute()` accessor in the `Product` model by summing `in` and `adjust` operations and subtracting `out`. No redundant `stock` column exists in the database, eliminating data desynchronization between stored counters and the immutable audit ledger.

### Cursor-Based Pagination
Listing endpoints (`/products`, `/movements`) implement `cursorPaginate()` rather than offset-based pagination. For high-volume warehouse ledgers, offset pagination degrades rapidly with `OFFSET N`. Cursor pagination guarantees constant $O(1)$ query complexity regardless of depth.

### Strict Auditability on Manual Adjustments
The `POST /products/{id}/adjust` endpoint enforces mandatory logging of `user_id` (authenticated via `Auth::id()`) and a mandatory `justification`. Adjustment quantities can be positive or negative but non-zero. All adjustments are flagged with `type='adjust'` and automatically excluded from commercial turnover metrics.

### Reusable Middleware RBAC
The `CheckRole` middleware evaluates user roles dynamically, issuing a 403 Forbidden response on unauthorized access. Administrative routes are protected using `middleware('role:admin')`.

### Secure Token Revocation on Logout
The `POST /api/logout` endpoint explicitly revokes the active token via Sanctum (`currentAccessToken()->delete()`). Subsequent requests with the revoked token are immediately rejected with 401 Unauthenticated.

### API Rate Limiting
Authenticated endpoints implement `throttle:60,1` (maximum 60 requests per minute per authenticated user). Requests exceeding this quota are automatically throttled with HTTP 429 Too Many Requests.

### Automated Low-Stock Alerting
Low-stock alerts are evaluated automatically following every transaction in `POST /movements` and returned in the `meta.alert` response payload. Additionally, a dedicated `GET /alerts/low-stock` endpoint is provided for explicit monitoring.

### Turnover & Rotation Analytics
`GET /api/reports/rotation` aggregates true operational movements (`in`, `out`, `transfer`), excluding audit adjustments. Parameters `date_from` and `date_to` are required; pagination limit defaults to 10.

---

## 🌱 Test Scenarios & Database Seeders

| Product | Final Stock | Min Threshold | Status | Tested Scenario |
|---|---|---|---|---|
| Portátil Pro 15 | 85 | 10 | ✅ Normal | Inbound, outbound, transfer, adjustment |
| Rato Sem Fios | 30 | 50 | ⚠️ Low Stock Alert | Stock below safety threshold |
| Teclado Mecânico | 70 | 20 | ✅ Normal | High-velocity movement — top rotation |
| Monitor 4K 27" | 0 | 5 | ⚠️ Zero Stock Alert | Zero stock edge case |

---

## 🔒 Environment Configuration

| Variable | Description | Example |
|---|---|---|
| `APP_KEY` | Application encryption key | `base64:...` |
| `APP_ENV` | Application environment | `local` |
| `APP_DEBUG` | Debug mode | `true` |
| `DB_CONNECTION` | Database driver | `sqlite` |
| `DB_DATABASE` | Database storage path | `database/database.sqlite` |

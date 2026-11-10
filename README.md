# 📦 Warehouse & Stock Management RESTful API

A RESTful API for **Warehouse and Stock Management** developed in **PHP** using the **Laravel** framework.

This project bridges hands-on domain knowledge in industrial warehouse logistics with modern backend software engineering principles, featuring relational database architecture, Eloquent ORM, secure token authentication via Laravel Sanctum, and automated endpoint testing with Postman.

---

## 🎓 Academic Project Context

- **Institution:** Instituto Politécnico da Maia (IPMAIA)
- **Programme:** Higher Technical Professional Course (CTeSP) in Technologies and Information Systems Programming
- **Curricular Area:** Backend Programming
- **Objective:** Designed and developed as a hands-on academic capstone project to demonstrate mastery in building, securing, and testing enterprise-grade REST APIs, applying MVC design patterns, relational database normalization, and token-based authentication to a real-world supply chain and inventory management use case.

---

## 🚀 Key Features

- **Stock & Inventory Control:** Real-time tracking of item quantities, product thresholds, and catalog organization.
- **Relational Data Architecture:** Modeled using **Eloquent ORM** with complex entity relationships (`hasMany`, `belongsTo`, `belongsToMany`) between Products, Categories, Stock Movements, and Suppliers.
- **Token-Based Authentication:** Protected endpoints secured using **Laravel Sanctum** Bearer Tokens.
- **Role-Based Access Control (RBAC):** Distinct permission levels separating administrative management from warehouse operator actions.
- **Data Validation & Error Handling:** Strict input sanitization and standard JSON HTTP status response structures.
- **API Testing:** Complete test collection designed and executed via **Postman**.

---

## 🛠️ Tech Stack & Tools

- **Language:** PHP 8.x
- **Framework:** Laravel (MVC Architecture)
- **ORM:** Eloquent ORM
- **Database:** SQLite / MySQL with migrations and relational schemas
- **Authentication:** Laravel Sanctum (API Tokens)
- **API Testing & Documentation:** Postman

---

## 📋 API Endpoints Preview

| Method | Endpoint | Description | Access |
|---|---|---|---|
| `POST` | `/api/login` | Authenticate user & issue Sanctum Bearer Token | Public |
| `GET` | `/api/products` | Retrieve complete stock product list | Authenticated |
| `POST` | `/api/products` | Register a new product into inventory | Admin / Manager |
| `GET` | `/api/products/{id}` | Fetch detailed product & stock information | Authenticated |
| `PUT` | `/api/products/{id}` | Update stock quantities and item details | Admin / Manager |
| `DELETE` | `/api/products/{id}` | Remove product from inventory catalog | Admin only |
| `POST` | `/api/stock-movements` | Log inbound/outbound warehouse stock movement | Operator / Admin |

---

## ⚙️ Local Setup & Installation

To run this API locally on your development environment:

```bash
# 1. Clone the repository
git clone https://github.com/luis-pacheco8/laravel-warehouse-stock-api.git

# 2. Enter project directory
cd laravel-warehouse-stock-api

# 3. Install backend dependencies
composer install

# 4. Create local environment configuration
cp .env.example .env

# 5. Generate application security key
php artisan key:generate

# 6. Execute database migrations
php artisan migrate

# 7. Start local development server
php artisan serve
```

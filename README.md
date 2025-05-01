# 🎟️ Tickets Please API – Laravel RESTful API

This repository contains a **RESTful API** project built with **Laravel**, following the course taught by **Jeffrey McPeak** on **Laracasts**. The goal was to learn best practices for building, structuring, and documenting modern APIs using the Laravel ecosystem.

---

## 📚 Technologies Used

- Laravel
- Laravel Sanctum (token authentication)
- Laravel Policies & Gates
- Laravel API Resources
- Laravel Validation
- Postman
- Scribe (API documentation)

---

## ✅ Topics Covered & Implemented

### 🔐 Authentication & Security
- Token-based authentication with Laravel Sanctum
- Revoking tokens
- Access control with **abilities**
- Authorization using **policies**
- **Principle of Least Privilege**

### 🔄 Full CRUD Support
- `POST`: create resources
- `GET`: list and retrieve resources
- `PUT`: fully replace resources
- `PATCH`: update specific fields
- `DELETE`: remove resources

### 📦 API Responses & Resources
- Custom API response design
- Conditionally include or exclude data
- Standardized payloads using Laravel Resources

### 🔍 Filtering & Parameters
- Custom filters
- Include optional related data (e.g. `?include=tickets`)
- Nested resource filtering
- Sorting (`?sort=name,-created_at`)

### 🧑‍🤝‍🧑 Managing Relationships
- Nested resource controllers (e.g. `AuthorTicketsController`)
- Filtering related models

### 🧪 Error Handling & Validation
- Custom error responses
- Robust validation using Form Requests

### 📄 API Documentation
- Auto-generated documentation using **Scribe**
- API testing and development with **Postman**

---

## 🚀 Getting Started

```bash
git clone https://github.com/maurogigena/tickets-please.git
cd tickets-please
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

http://localhost:8000/docs

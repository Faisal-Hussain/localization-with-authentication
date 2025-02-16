# Localization CRUD with Authentication

## Application Summary
This is a **Localization CRUD** system with authentication, built in Laravel. The application uses **JWT-based authentication** with a middleware named `jwtAuth` to secure API routes. It provides a **TranslationController** and a **TranslationRepository** to manage translations efficiently.

The system includes:
- **Authentication via JWT** (Register, Login, Logout, Get Current User)
- **Translation Management** (Create, Update, Search, Export)
- **Database Migrations and Seeders** (Preloads one user and 100,000 translation records)
- **Unit Tests for API validation and functionality**

---

## Table of Contents
1. [Installation Guide](#installation-guide)
2. [JWT Authentication Setup](#jwt-authentication-setup)
3. [Available API Routes](#available-api-routes)
4. [Why Use POST Instead of PUT for Updates?](#why-use-post-instead-of-put-for-updates)
5. [Running Unit Tests](#running-unit-tests)
6. [Database Seeding](#database-seeding)

---

## Installation Guide

Follow these steps to set up the project:

### Step 1: Clone the Repository
```sh
 git clone https://github.com/your-repo/localization-crud.git
 cd localization-crud
```

### Step 2: Install Dependencies
```sh
 composer install
 npm install  # If frontend assets are needed
```

### Step 3: Configure the Environment
Copy the `.env.example` file and update database credentials.
```sh
 cp .env.example .env
```
Set your database details in the `.env` file:
```env
 DB_CONNECTION=mysql
 DB_HOST=127.0.0.1
 DB_PORT=3306
 DB_DATABASE=your_database
 DB_USERNAME=your_username
 DB_PASSWORD=your_password
```

### Step 4: Generate Application Key
```sh
 php artisan key:generate
```

### Step 5: Run Migrations
```sh
 php artisan migrate
```

### Step 6: Seed Database with Initial Data
```sh
 php artisan db:seed
```

---

## JWT Authentication Setup

### Install JWT Package
```sh
 composer require tymon/jwt-auth
```

### Publish JWT Configuration
```sh
 php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

### Generate JWT Secret Key
```sh
 php artisan jwt:secret
```
This will update your `.env` file with a secret key:
```env
 JWT_SECRET=your_generated_secret_key
```

---

## Available API Routes

### **Authentication Routes**
```php
Route::post('register-user', [JWTAuthController::class, 'register']);
Route::post('login', [JWTAuthController::class, 'login']);
```

### **Protected Routes (Require JWT Authentication)**
```php
Route::middleware('jwtAuth')->group(function () {
    Route::get('get-current-user', [JWTAuthController::class, 'getUser']);
    Route::post('logout', [JWTAuthController::class, 'logout']);

    Route::prefix('translations')->group(function () {
        Route::post('/', [TranslationController::class, 'store']);
        Route::post('/{translation}', [TranslationController::class, 'update']);
        Route::get('/', [TranslationController::class, 'show']);
        Route::get('/search', [TranslationController::class, 'search']);
        Route::get('/export', [TranslationController::class, 'exportTranslations']);
    });
});
```

---

## Why Use POST Instead of PUT for Updates?
In this application, **POST is used instead of PUT for updating records** due to the following reasons:

1. **Flexibility**: PUT requests are typically used for full resource replacements, while POST allows partial updates without requiring the full object.
2. **Middleware and Security Handling**: Some middleware and caching mechanisms handle PUT and POST differently. POST is often more universally supported.
3. **Simpler Frontend Integration**: Many frontend frameworks (like Vue, React) have easier support for POST requests in form submissions.

---

## Running Unit Tests
Unit tests are written for authentication, validation, and translation CRUD operations.

Run the tests using:
```sh
 php artisan test
```

---

## Database Seeding
The application includes seeders to populate test data.

```sh
 php artisan db:seed
```

This will:
- Create **one default user**
- Insert **100,000 translation records**

---



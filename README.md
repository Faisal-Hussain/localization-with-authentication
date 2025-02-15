# Laravel Blog API with JWT Authentication

## 📌 Project Overview
This Laravel project provides a **JWT-authenticated API** for user registration, login, and **CRUD operations** for blogs. Users can create an account, log in, and manage their blogs while ensuring secure authentication using **JSON Web Tokens (JWT).**

---

## ⚡ Features
- **JWT Authentication** for secure user access.
- **User Registration & Login.**
- **CRUD Operations** for blogs (only the owner can modify or delete their blogs).
- **Seeding Users and Blogs** for testing.
- **Secure API Routes** (protected with JWT middleware).
- **File Storage for Blog Images.**

---

## 🚀 Setup Instructions

### Step 1: Clone the Repository
```sh
git clone https://github.com/Faisal-Hussain/blog-crud-with-authentication.git
cd blog-crud-with-authentication

```

### Step 2: Install Dependencies
```sh
composer install
```

### Step 3: Copy Environment File
```sh
cp .env.example .env
```

### Step 4: Generate Encryption Key
```sh
php artisan key:generate
```

### Step 5: Configure Database
- Update `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### Step 6: Run Migrations
```sh
php artisan migrate
```

### Step 7: Run Seeders (To Generate Dummy Data)
```sh
php artisan db:seed
```

### Step 8: Install JWT Authentication Package
```sh
composer require tymon/jwt-auth
```

### Step 9: Publish JWT Configuration
```sh
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

### Step 10: Generate JWT Secret Key
```sh
php artisan jwt:secret
```

### Step 11: Create Storage Symlink for Blog Images
```sh
php artisan storage:link
```

### Step 12: Start the Laravel Development Server
```sh
php artisan serve
```

Your API will now be running at **http://127.0.0.1:8000** 🎉

---

## 🔐 Authentication
- **JWT Authentication is required** for all blog-related routes.
- You must include the token in the `Authorization` header as:
  ```sh
  Authorization: Bearer YOUR_JWT_TOKEN
  ```

---

## 📌 API Endpoints

### 🔑 Authentication Routes
| Method | Endpoint          | Description |
|--------|------------------|-------------|
| POST   | `/api/register-user` | Register a new user |
| POST   | `/api/login` | Login and receive a JWT token |
| GET    | `/api/get-current-user` | Get the authenticated user (Requires JWT) |
| POST   | `/api/logout` | Logout the user (Requires JWT) |

### 📝 Blog Routes (Protected with JWT)
| Method | Endpoint | Description |
|--------|---------|-------------|
| GET    | `/api/blogs` | Get all blogs |
| POST   | `/api/blogs` | Create a new blog |
| GET    | `/api/blogs/{u_id}` | Get a specific blog by unique ID |
| POST   | `/api/blogs/{u_id}` | Update a blog (Only owner can update) |
| DELETE | `/api/blogs/{u_id}` | Delete a blog (Only owner can delete) |

---

## 🔥 Additional Commands
| Command | Description |
|---------|-------------|
| `php artisan migrate:fresh --seed` | Reset database and seed new data |
| `php artisan route:list` | View all available routes |
| `php artisan cache:clear` | Clear cache |
| `php artisan config:clear` | Clear config cache |

---




# OCEI Data Archive Project – Laravel Docker Starter
---
````markdown
A fully isolated, reproducible development environment for the **OCEI Data Archive Project**.  
This starter kit runs the latest **Laravel** on **PHP-FPM**, **Nginx**, and **MySQL** without requiring local installations on your host machine.

This setup is specifically architected for:

- **Custom Authentication** (bypassing Breeze/Jetstream)
- **Secure file storage management** using `storage/app/public`

---

## 📑 Table of Contents

- [🛠 Tech Stack](#-tech-stack)
- [📂 Project Structure](#-project-structure)
- [✅ Prerequisites](#-prerequisites)
- [🚀 Installation & Setup](#-installation--setup)
  - [1. Clone the Repository](#1-clone-the-repository)
  - [2. Start Docker Services](#2-start-docker-services)
  - [3. Install / Configure Laravel](#3-install--configure-laravel)
  - [4. Database Configuration](#4-database-configuration)
  - [5. Finalize Setup](#5-finalize-setup)
- [🌐 Accessing the Application](#-accessing-the-application)
- [💻 Usage & Commands](#-usage--commands)
  - [1. Composer Commands](#1-composer-commands)
  - [2. Artisan Commands](#2-artisan-commands)
  - [3. NPM & Frontend (Node)](#3-npm--frontend-node)
  - [Useful Aliases (Optional)](#useful-aliases-optional)
- [📖 Feature Guides](#-feature-guides)
  - [Custom Authentication](#custom-authentication)
  - [Storage & Uploads](#storage--uploads)
- [🔧 Troubleshooting](#-troubleshooting)

---

## 🛠 Tech Stack

| Component   | Technology      | Description                                  |
|------------|-----------------|----------------------------------------------|
| Backend    | PHP 8.x (FPM)   | Running inside the `app` container          |
| Frontend   | Node.js 20      | Running inside the `node` container         |
| Web Server | Nginx           | Handles HTTP requests on port **8090**      |
| Database   | MySQL 8         | Persistent data storage                      |
| DB Admin   | phpMyAdmin      | Web interface on port **8091**              |

---

## 📂 Project Structure

```text
.
├─ Dockerfile              # PHP-FPM + Composer image
├─ docker-compose.yml      # Services: app, nginx, db, phpmyadmin, node
├─ docker/
│  └─ nginx/
│     └─ default.conf      # Nginx virtual host config
├─ app/                    # Laravel application code
│  ├─ public/              # Web root (index.php, assets)
│  ├─ storage/             # Logs, cache, uploaded files
│  └─ ...
└─ .gitignore
````

> All uploaded files are stored under `app/storage/app/public`.
> Git is configured to **keep the directory structure** but **ignore actual uploaded files**.

---

## ✅ Prerequisites

* **Docker Desktop** & **Docker Compose v2**

> You do **not** need PHP, Composer, Node, or MySQL installed on your local machine.
> All tools run inside Docker containers.

---

## 🚀 Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/<your-username>/ocei-data-archive.git
cd ocei-data-archive
```

### 2. Start Docker Services

Build and start the containers in detached mode:

```bash
docker compose up -d --build
```

### 3. Install / Configure Laravel

> Only needed if this is a fresh installation (empty `app/` folder).

Enter the app container:

```bash
docker compose exec app bash
cd /var/www/html
```

Install Laravel:

```bash
composer create-project laravel/laravel .
```

Copy the env file:

```bash
cp .env.example .env
```

### 4. Database Configuration

Edit your `.env` file (inside the `app` folder) to match the credentials defined in `docker-compose.yml`:

```env
APP_NAME="OCEI Data Archive"
APP_URL=http://localhost:8090

# Database credentials matching docker-compose.yml
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ocei_data_archive
DB_USERNAME=ocei_user
DB_PASSWORD=OceiArchive@2025
```

### 5. Finalize Setup

Run the following commands (from your host, they execute inside the container):

```bash
# Generate App Key
docker compose exec app php artisan key:generate

# Run Migrations
docker compose exec app php artisan migrate

# Link Storage
docker compose exec app php artisan storage:link
```

---

## 🌐 Accessing the Application

| Service     | URL                                            | Credentials                                         |
| ----------- | ---------------------------------------------- | --------------------------------------------------- |
| Laravel App | [http://localhost:8090](http://localhost:8090) | N/A                                                 |
| phpMyAdmin  | [http://localhost:8091](http://localhost:8091) | Server: `db` · User: `root` · Pass: `OceiRoot@2025` |
| Vite (Dev)  | [http://localhost:5173](http://localhost:5173) | N/A                                                 |

---

## 💻 Usage & Commands

All tools run inside Docker, so commands are executed **inside containers** via `docker compose exec`.

### 1. Composer Commands

Run Composer commands inside the **app** container:

```bash
# Install dependencies
docker compose exec app composer install

# Require a new package
docker compose exec app composer require guzzlehttp/guzzle
```

### 2. Artisan Commands

Run Laravel Artisan commands inside the **app** container:

```bash
# Run migrations
docker compose exec app php artisan migrate

# Create a controller
docker compose exec app php artisan make:controller Auth/LoginController

# Clear cache and optimized files
docker compose exec app php artisan optimize:clear
```

### 3. NPM & Frontend (Node)

This project includes a dedicated **node** container.
You do **not** need Node installed on your host.

```bash
# Install Node dependencies
docker compose exec node npm install

# Run Vite development server (Hot Reload)
docker compose exec node npm run dev

# Build for production
docker compose exec node npm run build
```

> The `node` container is configured so you can keep it running and execute commands quickly.

### Useful Aliases (Optional)

Add these to your shell profile (`~/.zshrc` or `~/.bashrc`) to simplify your workflow:

```bash
alias art='docker compose exec app php artisan'
alias comp='docker compose exec app composer'
alias dnode='docker compose exec node npm'
```

Usage examples:

```bash
art migrate
dnode run dev
```

---

## 📖 Feature Guides

### Custom Authentication

This project **intentionally excludes** Breeze/Jetstream to allow a fully custom auth implementation.

* **Controllers:**
  `app/Http/Controllers/Auth/`

* **Views:**
  `resources/views/auth/` (e.g. `login.blade.php`, `register.blade.php`)

* **Routes:**
  Defined by you in `routes/web.php` (e.g. `/login`, `/register`, `/dashboard`)

You are responsible for:

* Defining the authentication fields (email, username, phone, etc.)
* Implementing validation, login logic, registration, and redirects

### Storage & Uploads

* **Public Access:**
  Files are stored in `storage/app/public` and accessed via the symlink `public/storage`.

* **Git Strategy:**

    * The directory structure under `storage/` is tracked.
    * Actual uploaded files are **ignored by Git** (using `.gitignore` rules).

* **Saving Files (example):**

  ```php
  $path = $request->file('doc')->store('documents', 'public');
  ```

  The file will be available at:

  ```text
  http://localhost:8090/storage/documents/<filename>
  ```

---

## 🔧 Troubleshooting

### “Vite not connecting?”

Ensure your `vite.config.js` allows external access so the container can serve the host:

```js
export default defineConfig({
    // ...
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost',
        },
        port: 5173,
    },
});
```

### “Database connection refused?”

* Make sure the `db` service is running:

  ```bash
  docker compose ps
  ```

* Ensure you are using **`DB_HOST=db`** in your `.env` file, **not** `localhost` or `127.0.0.1`:

  ```env
  DB_HOST=db
  DB_PORT=3306
  DB_DATABASE=ocei_data_archive
  DB_USERNAME=ocei_user
  DB_PASSWORD=OceiArchive@2025
  ```

---

Happy coding with the **OCEI Data Archive Project** 🚀

```
::contentReference[oaicite:0]{index=0}
```

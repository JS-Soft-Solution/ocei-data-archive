# OCEI Data Archive Project – Laravel Docker Starter

A fully isolated, reproducible development environment for the **OCEI Data Archive Project**.  
This starter kit runs **Laravel 12** on **PHP-FPM 8.3**, **Nginx**, **MySQL 8.0**, and **Vite / Node 20** with **instant local hot-reloading**.

---

## 📑 Table of Contents

- [🛠 Tech Stack](#-tech-stack)
- [📂 Project Structure](#-project-structure)
- [⚡ Real-time Hot-Reloading & Local Sync](#-real-time-hot-reloading--local-sync)
- [✅ Prerequisites](#-prerequisites)
- [🚀 Quick Start (One Command)](#-quick-start-one-command)
- [🌐 Accessing the Application](#-accessing-the-application)
- [💻 Common Commands](#-common-commands)
  - [Artisan Commands](#artisan-commands)
  - [Composer Commands](#composer-commands)
  - [Node / Frontend Commands](#node--frontend-commands)
  - [Running Automated Tests](#running-automated-tests)
- [Default Seeded Credentials](#-default-seeded-credentials)
- [🔧 Troubleshooting & FAQ](#-troubleshooting--faq)

---

## 🛠 Tech Stack

| Component | Technology | Container Name | Host Port | Container Port |
| :--- | :--- | :--- | :--- | :--- |
| **Backend** | PHP 8.3 (FPM) | `ocei-archive-app` | Internal | `9000` |
| **Web Server** | Nginx (Stable) | `ocei-archive-nginx` | **8090** | `80` |
| **Frontend** | Node.js 20 + Vite + Tailwind v4 | `ocei-archive-node` | **5173** | `5173` |
| **Database** | MySQL 8.0 | `ocei-archive-db` | **3307** | `3306` |
| **DB Admin** | phpMyAdmin | `ocei-phpmyadmin` | **8091** | `80` |

---

## 📂 Project Structure

```text
.
├── Dockerfile                  # PHP-FPM + Composer + automated startup entrypoint
├── docker-compose.yml          # Services: app, nginx, db, phpmyadmin, node
├── docker/
│   ├── entrypoint.sh           # Auto-initialization (env, keys, permissions, storage)
│   ├── nginx/
│   │   └── default.conf        # Nginx vhost (IPv4+IPv6, FastCGI buffers, 100M uploads)
│   └── php/
│       └── local.ini           # PHP dev config (OPcache revalidate_freq=0, 100M upload limits)
├── app/                        # Laravel application codebase
│   ├── app/                    # Controllers, Models, Middleware
│   ├── bootstrap/              # App bootstrap & cache
│   ├── config/                 # Laravel configuration files
│   ├── database/               # Migrations, seeders, factories
│   ├── public/                 # Web root (index.php, static assets, storage symlink)
│   ├── resources/              # Blade views, CSS, JS
│   ├── routes/                 # Web and console routes
│   ├── storage/                # Logs, sessions, uploaded permits
│   ├── tests/                  # Unit and Feature automated tests
│   ├── phpunit.xml             # Test suite configuration
│   └── vite.config.js          # Vite config with Docker polling & HMR
└── README.md
```

---

## ⚡ Real-time Hot-Reloading & Local Sync

The Docker environment is configured for **instant local change reflection**:

1. **PHP / Backend Code**:
   - The `./app` directory is bind-mounted directly to `/var/www/html`.
   - PHP OPcache timestamp revalidation is set to `opcache.revalidate_freq = 0` via `docker/php/local.ini`.
   - Any change to PHP files (Controllers, Models, Routes, Services, Blade templates) is picked up **instantly on the next browser request** without restarting containers.
2. **Frontend Assets (CSS, JS, Tailwind v4)**:
   - The `node` container runs Vite development server (`npm run dev -- --host 0.0.0.0`) automatically on startup.
   - File polling is enabled (`CHOKIDAR_USEPOLLING=true`, `watch: { usePolling: true }` in `vite.config.js`). This ensures Windows filesystem events across WSL2 / Hyper-V mounts trigger hot-module replacement (HMR) seamlessly on host port `5173`.
3. **Docker Compose Watch**:
   - Services include `develop: watch:` blocks for real-time synchronization if running with `docker compose watch` or `docker compose up --watch`.

---

## ✅ Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows with WSL2 backend or Hyper-V, macOS, or Linux)
- Docker Compose v2 (included with Docker Desktop)

> You do **not** need PHP, Composer, Node.js, or MySQL installed on your host machine. Everything runs inside Docker.

---

## 🚀 Quick Start (One Command)

### 1. Start the Containers

From the repository root directory, run:

```bash
docker compose up -d --build
```

The container automatically:
- Checks and creates `app/.env` from `.env.example` if it does not exist.
- Sets appropriate read/write permissions on `storage/` and `bootstrap/cache/`.
- Installs Composer dependencies if `vendor/` is missing.
- Generates `APP_KEY` if not set.
- Creates the `public/storage` symlink.
- Installs npm packages if missing and starts the **Vite dev server with hot reload**.

### 2. Run Database Migrations & Seeds

Run migrations and seed the initial administrative accounts:

```bash
# Run migrations
docker compose exec app php artisan migrate

# Seed administrative users
docker compose exec app php artisan db:seed --class=UserSeeder
```

---

## 🌐 Accessing the Application

| Service | URL | Credentials |
| :--- | :--- | :--- |
| **Laravel App** | [http://localhost:8090](http://localhost:8090) | See default accounts below |
| **Vite Dev Server** | [http://localhost:5173](http://localhost:5173) | N/A (HMR active) |
| **phpMyAdmin** | [http://localhost:8091](http://localhost:8091) | Server: `db` · User: `root` · Pass: `OceiRoot@2025` |
| **MySQL Direct** | `localhost:3307` | User: `ocei_user` · Pass: `OceiArchive@2025` · DB: `ocei_data_archive` |

> **Tip for Windows users**: If your browser has DNS issues resolving `localhost` with IPv6, you can also use `http://127.0.0.1:8090`.

---

## 🔑 Default Seeded Credentials

All seeded accounts use default password: `password`

| Role | User ID | Email | Password |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `SA-0001` | `superadmin@ocei.local` | `password` |
| **Office Assistant** | `OA-0001` | `office.assistant@ocei.local` | `password` |
| **Secretary** | `SEC-0001` | `secretary@ocei.local` | `password` |
| **Chairman** | `CH-0001` | `chairman@ocei.local` | `password` |
| **Inspector** | `INSP-0001` | `inspector@ocei.local` | `password` |
| **Data Entry Operator** | `DEO-0001` | `deo@ocei.local` | `password` |

---

## 💻 Common Commands

### Artisan Commands
```bash
# Run migrations
docker compose exec app php artisan migrate

# Rollback migrations
docker compose exec app php artisan migrate:rollback

# Create a controller / model
docker compose exec app php artisan make:controller MyController
docker compose exec app php artisan make:model MyModel -m

# Clear application cache
docker compose exec app php artisan optimize:clear
```

### Composer Commands
```bash
# Install new package
docker compose exec app composer require vendor/package

# Update autoload dump
docker compose exec app composer dump-autoload
```

### Node / Frontend Commands
```bash
# Install new npm package
docker compose exec node npm install <package-name>

# Build production assets
docker compose exec node npm run build

# Check Vite dev server logs
docker compose logs -f node
```

### Running Automated Tests
```bash
docker compose exec app php artisan test
```

---

## 🔧 Troubleshooting & FAQ

### 1. Changes made on host aren't reflecting?
- **PHP files**: OPcache is set to `opcache.revalidate_freq = 0`. Simply refresh your browser tab. If you cached config or routes, run `docker compose exec app php artisan optimize:clear`.
- **Frontend / CSS / JS**: Ensure the `node` container is running (`docker compose ps`). Check its logs via `docker compose logs -f node`. Polling is enabled by default to work across Windows NTFS mounts.

### 2. Permission denied in `storage/` or `bootstrap/cache/`?
Run:
```bash
docker compose exec app chmod -R 777 storage bootstrap/cache
```

### 3. Database connection refused?
Make sure `ocei-archive-db` is healthy (`docker compose ps`). In `app/.env`, ensure:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ocei_data_archive
DB_USERNAME=ocei_user
DB_PASSWORD=OceiArchive@2025
```
*(Use `DB_HOST=db` inside Docker, not `localhost` or `127.0.0.1`)*.

---

Happy coding with the **OCEI Data Archive Project** 🚀

# OCEI Data Archive System (v2.0 - High Performance)

A modernized, enterprise-grade archive and application management platform for the **Office of the Chief Electrical Inspector (OCEI)**, powered by **Laravel 13.x**, **PHP 8.6 / 8.4 FPM**, **MySQL 9.x**, **Node.js 26 LTS**, **Tailwind CSS v4.3.5**, and **Vite 8.2**.

The application features full workflow automation for Electrician, Supervisor, and Contractor legacy archive digitization, renewal processing, role-based approval funnels, high-performance covering database indexing, server-side pagination, cross-tab notification synchronization, and KPI analytics dashboards.

---

## 📑 Table of Contents

- **Architecture & Performance**
  1. [🛠 System Architecture & Stack](#-system-architecture--stack)
  2. [⚡ Large-Scale Data Indexing & Query Optimizations](#-large-scale-data-indexing--query-optimizations)
  3. [🌐 Accessing Services & Port Map](#-accessing-services--port-map)
- **Local Development**
  4. [⚡ 1-Step Local Docker Setup (Zero Configuration)](#-1-step-local-docker-setup-zero-configuration)
  5. [🔑 Default Test Accounts & Roles](#-default-test-accounts--roles)
  6. [💻 Useful Development Commands](#-useful-development-commands)
- **Method 1: Regular / Native Deployment (No Docker)**
  7. [📦 cPanel / Shared Hosting Guide (Native - No Docker)](#-cpanel--shared-hosting-guide-native---no-docker)
  8. [🐧 Ubuntu 24.04 LTS Server (Native LEMP - No Docker)](#-ubuntu-2404-lts-server-native-lemp---no-docker)
  9. [🎩 CentOS / RHEL / Rocky Linux 9 Server (Native LEMP - No Docker)](#-centos--rhel--rocky-linux-9-server-native-lemp---no-docker)
- **Method 2: Dockerized Production Deployment (With Docker & Compose)**
  10. [🐳 Production Docker Architecture & Setup](#-production-docker-architecture--setup)
  11. [🐧 Ubuntu / Debian Dockerized Production Deployment](#-ubuntu--debian-dockerized-production-deployment)
  12. [🎩 CentOS / RHEL / Rocky Linux 9 Dockerized Production Deployment](#-centos--rhel--rocky-linux-9-dockerized-production-deployment)
  13. [🔄 Production Docker Updates, Migrations & Zero-Downtime](#-production-docker-updates-migrations--zero-downtime)
- **Documentation & User Guide**
  14. [📘 Comprehensive User Guide & Workflows](#-comprehensive-user-guide--workflows)
  15. [🔒 Production Security & Maintenance](#-production-security--maintenance)

---

## 🛠 System Architecture & Stack

| Component | Technology | Version / Configuration |
|---|---|---|
| **Backend Runtime** | PHP 8.6 / 8.4 (PHP-FPM) | JIT Enabled (`opcache.jit=1255`, `jit_buffer_size=128M`), OPcache 256MB |
| **Framework** | Laravel 13.x / 12.x | Eloquent ORM, Server-Side Pagination, Event Emitter |
| **Database** | MySQL 9.x / 8.4 LTS | InnoDB Buffer Pool 512M, `utf8mb4_unicode_ci` |
| **Web Server** | Nginx Alpine | FastCGI Proxy with 128k/256k buffers on port **`8095`** |
| **Database GUI** | phpMyAdmin | Web-based database management on port **`8096`** |
| **Frontend Runtime**| Node.js 26 LTS | "Lithium" engine with native Temporal API |
| **Styling & Assets**| Tailwind CSS & Vite | **Tailwind CSS v4.3.5** & **Vite 8.2** on port **`5174`** |
| **Dependency Manager**| Composer | **Composer 2.11.x** |

---

## ⚡ Large-Scale Data Indexing & Query Optimizations

To ensure sub-second page loads even across tables with hundreds of thousands of archive records:

1. **Covering & Composite Database Indexes**:
   - `idx_elec_status_created (status, created_at)`: Instant filtered pagination for Electrician tables.
   - `idx_sup_status_created (status, created_at)`: Instant filtered pagination for Supervisor tables.
   - `idx_cont_status_created (status, created_at)`: Instant filtered pagination for Contractor tables.
   - `idx_cont_class (class)`: Fast filtering by contractor grade (Class A, B, C).
   - Single-column indexes on `registration_no`, `nid_number`, `trade_license_number`, and `book_number` for instant searches.
   - Composite `(attachable_type, attachable_id)` for polymorphic attachment lookups.
   - Composite `(user_id, read_at, created_at)` for real-time notification counting.
2. **Server-Side Pagination & Column Pruning**:
   - Universal `$query->latest('id')->paginate($perPage)->withQueryString()`.
   - Explicit column selection (`select(['id', 'tracking_no', ...])`) so heavy JSON/text blobs are never loaded during list pagination.
   - Strict eager-loading (`with(['attachments', 'entryBy'])`) to eliminate N+1 queries.
3. **Cross-Tab Notification Synchronization**:
   - Implements **single-poller leader election** via `localStorage` and HTML5 `BroadcastChannel`. Only one tab polls every 30s regardless of how many tabs are open, broadcasting updates to all tabs with 0 duplicate HTTP requests.

---

## 🌐 Accessing Services & Port Map

All services run on dedicated, non-conflicting host ports:

| Service | Local URL | Port | Default Credentials |
|---|---|---|---|
| **Web Application** | `http://localhost:8095` | `8095` | See accounts below |
| **phpMyAdmin** | `http://localhost:8096` | `8096` | User: `root` / Pass: `OceiRoot@2025` |
| **MySQL Database** | `localhost:3308` | `3308` | User: `ocei_user` / Pass: `OceiArchive@2025` / DB: `ocei_data_archive` |
| **Vite Dev Server** | `http://localhost:5174` | `5174` | Active during `npm run dev` |

---

## ⚡ 1-Step Local Docker Setup (Zero Configuration)

You do **not** need PHP, Composer, Node.js, or MySQL installed on your host machine. Docker handles the entire initialization automatically.

### Running the Project

From this project directory (`ocei-data-archive-latest`), run:

```bash
docker compose up -d --build
```

### What Happens Automatically Behind the Scenes:
1. **Container Build**: Builds the PHP 8.6/8.4 FPM image with OPcache, JIT, and required extensions (`pdo_mysql`, `mbstring`, `gd`, `zip`, `bcmath`, `exif`, `pcntl`).
2. **Environment File**: Copies `.env.example` to `.env` if missing.
3. **Dependencies**: Runs `composer install` automatically.
4. **Application Key**: Generates `APP_KEY` via `php artisan key:generate` if empty.
5. **Database Initialization**: Waits for MySQL 9.x to become healthy.
6. **Migrations & Seeders**: Runs `php artisan migrate --force` and seeds default roles and test users.
7. **Storage Symlink**: Links `storage/app/public` to `public/storage`.
8. **PHP-FPM**: Launches high-concurrency worker pool ready to handle requests.

Open `http://localhost:8095` in your browser.

---

## 🔑 Default Test Accounts & Roles

All accounts share the default password: **`password`**

| Role | Email / Login ID | Default Password | Access Scope |
|---|---|---|---|
| **Super Admin** | `admin@ocei.gov.bd` | `password` | Complete system access & user management |
| **Chairman** | `chairman@ocei.gov.bd` | `password` | Final application approvals and rejections |
| **Secretary** | `secretary@ocei.gov.bd` | `password` | Review & recommendation to Chairman |
| **Office Assistant**| `assistant@ocei.gov.bd`| `password` | Primary document verification |
| **Data Entry Operator**| `operator@ocei.gov.bd` | `password` | Entry, draft editing, and submission |

---

## 💻 Useful Development Commands

```bash
# View live logs
docker compose logs -f app
docker compose logs -f nginx

# Run Artisan commands
docker compose exec app php artisan migrate:status
docker compose exec app php artisan route:list

# Clear application caches
docker compose exec app php artisan optimize:clear

# Restart stack
docker compose restart
```

---

## 📦 cPanel / Shared Hosting Guide (Native - No Docker)

> [!NOTE]
> This method deploys Laravel natively into cPanel using standard Apache/LiteSpeed web servers, MultiPHP, and MySQL without running Docker.

### Step 1: Prepare the Codebase
Build production frontend assets locally:
```bash
npm install
npm run build
```

### Step 2: Upload Files to cPanel
Structure files outside public web root:
```
/home/cpaneluser/
├── ocei-data-archive/          <-- Application root
└── public_html/archive/        <-- Web public root (or public_html directly)
```

1. Upload all directories (`app`, `bootstrap`, `config`, `database`, `routes`, `vendor`, `artisan`, `composer.json`) to `/home/cpaneluser/ocei-data-archive/`.
2. Move the contents of `public/` (including `build/`, `assets/`, `index.php`, `.htaccess`) to your target public folder (e.g. `/home/cpaneluser/public_html/`).

### Step 3: Configure `index.php`
Edit `public_html/index.php`:
```php
require __DIR__.'/../ocei-data-archive/vendor/autoload.php';
$app = require_once __DIR__.'/../ocei-data-archive/bootstrap/app.php';
```

### Step 4: MultiPHP Version
In cPanel **MultiPHP Manager**, set PHP version to **PHP 8.3 or PHP 8.4** (or 8.6 if supported by your host). Ensure extensions `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `zip`, `bcmath`, `opcache` are enabled.

### Step 5: Database & Environment
1. In cPanel **MySQL Databases**, create database and assign user with `ALL PRIVILEGES`.
2. Edit `/home/cpaneluser/ocei-data-archive/.env`:
   ```ini
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=cpaneluser_ocei_archive
   DB_USERNAME=cpaneluser_dbuser
   DB_PASSWORD=YourStrongPassword
   ```

### Step 6: Migrations & Cron
Run migrations via cPanel Terminal or SSH:
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Add cPanel Cron job (`* * * * *`):
```bash
/usr/local/bin/php /home/cpaneluser/ocei-data-archive/artisan schedule:run >> /dev/null 2>&1
```

---

## 🐧 Ubuntu 24.04 LTS Server (Native LEMP - No Docker)

### Step 1: System Packages & PHP 8.4
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl git unzip zip certbot python3-certbot-nginx

# Add Ondrej PHP PPA for PHP 8.4
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update -y

sudo apt install -y php8.4-fpm php8.4-cli php8.4-mysql php8.4-mbstring \
    php8.4-xml php8.4-curl php8.4-zip php8.4-gd php8.4-bcmath php8.4-intl \
    php8.4-opcache mysql-server nginx
```

### Step 2: Clone & Configure
```bash
sudo git clone -b upgrade-v2-latest git@github.com:JS-Soft-Solution/ocei-data-archive.git /var/www/ocei-archive
cd /var/www/ocei-archive/app

sudo cp .env.example .env
sudo nano .env # Set production DB credentials, APP_ENV=production, APP_DEBUG=false
```

### Step 3: Permissions & Dependencies
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link

sudo chown -R www-data:www-data /var/www/ocei-archive/app/storage /var/www/ocei-archive/app/bootstrap/cache
sudo chmod -R 775 /var/www/ocei-archive/app/storage /var/www/ocei-archive/app/bootstrap/cache
```

### Step 4: Nginx Server Block & SSL
Create `/etc/nginx/sites-available/ocei-archive`:
```nginx
server {
    listen 80;
    server_name archive.ocei.gov.bd;
    root /var/www/ocei-archive/app/public;

    index index.php index.html;
    client_max_body_size 64M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Enable and reload:
```bash
sudo ln -s /etc/nginx/sites-available/ocei-archive /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d archive.ocei.gov.bd
```

---

## 🎩 CentOS / RHEL / Rocky Linux 9 Server (Native LEMP - No Docker)

```bash
sudo dnf install -y epel-release
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.4 -y

sudo dnf install -y nginx mariadb-server php-fpm php-mysqlnd php-mbstring \
    php-xml php-gd php-bcmath php-zip php-opcache git certbot python3-certbot-nginx

# SELinux permissions for storage and networking
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/ocei-archive/app/storage(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/ocei-archive/app/bootstrap/cache(/.*)?"
sudo restorecon -Rv /var/www/ocei-archive/app/storage
sudo restorecon -Rv /var/www/ocei-archive/app/bootstrap/cache
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_network_connect_db 1
```

---

## 🐳 Production Docker Architecture & Setup

```
Internet (Port 80/443)
       │
   [Host Nginx + Let's Encrypt SSL]
       │ Reverse Proxy to 127.0.0.1:8095
       ▼
 [ocei-v2-nginx (Port 8095)]
       │ FastCGI socket (Port 9000)
       ▼
 [ocei-v2-app (PHP 8.6/8.4 FPM)] ── MySQL (3306) ──▶ [ocei-v2-db (MySQL 9.x)]
```

### Host Reverse Proxy Configuration:
```nginx
server {
    listen 80;
    server_name archive.ocei.gov.bd;

    client_max_body_size 64M;

    location / {
        proxy_pass http://127.0.0.1:8095;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 300s;
    }
}
```

### Systemd Auto-Start (`/etc/systemd/system/ocei-v2-docker.service`):
```ini
[Unit]
Description=OCEI Data Archive v2 Docker Stack
Requires=docker.service
After=docker.service

[Service]
Type=oneshot
RemainAfterExit=yes
WorkingDirectory=/opt/ocei-data-archive-latest
ExecStart=/usr/bin/docker compose up -d
ExecStop=/usr/bin/docker compose down
TimeoutStartSec=0

[Install]
WantedBy=multi-user.target
```
Enable service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable ocei-v2-docker
```

---

## 📘 Comprehensive User Guide & Workflows

For step-by-step role walkthroughs, state machines, and visual field references:
👉 **Read [USER_GUIDE.md](USER_GUIDE.md)**

---

## 🔒 Production Security & Maintenance

1. **Always Set `APP_DEBUG=false`** in production `.env`.
2. **Optimize Cache on Deployment**:
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. **Automated Database Backup**:
   Schedule daily backups inside cron using `docker compose exec -T db mysqldump ... | gzip > /var/backups/ocei_$(date +%F).sql.gz`.

# OCEI Data Archive System

A robust enterprise archive and application management platform for the **Office of the Chief Electrical Inspector (OCEI)**, built with **Laravel 11**, **PHP 8.3**, **MySQL 8**, and **Nginx**.

The application features full workflow automation for Electrician, Supervisor, and Contractor legacy archive digitization, renewal processing, role-based workflows (Data Entry Operator, Office Assistant, Secretary, Chairman, Admin), KPI analytics dashboards, and multi-parameter filtering.

---

## 📑 Table of Contents

- **Development & Architecture**
  1. [🛠 System Architecture & Stack](#-system-architecture--stack)
  2. [⚡ 1-Step Local Docker Setup (Zero Configuration)](#-1-step-local-docker-setup-zero-configuration)
  3. [🔄 Live Code Reload (Development on Disk)](#-live-code-reload-development-on-disk)
  4. [🔑 Default Test Accounts & Roles](#-default-test-accounts--roles)
  5. [🌐 Accessing Services](#-accessing-services)
  6. [💻 Useful Development Commands](#-useful-development-commands)
- **Method 1: Regular / Native Deployment (No Docker)**
  7. [📦 cPanel / Shared Hosting Guide (Native - No Docker)](#-cpanel--shared-hosting-guide-native---no-docker)
  8. [🐧 Ubuntu 22.04 / 24.04 LTS Server (Native LEMP - No Docker)](#-ubuntu-2204--2404-lts-server-native-lemp---no-docker)
  9. [🎩 CentOS / RHEL / Rocky Linux 9 Server (Native LEMP - No Docker)](#-centos--rhel--rocky-linux-9-server-native-lemp---no-docker)
- **Method 2: Dockerized Production Deployment (With Docker & Compose)**
  10. [🐳 Production Docker Architecture & Setup](#-production-docker-architecture--setup)
  11. [🐧 Ubuntu / Debian Dockerized Production Deployment](#-ubuntu--debian-dockerized-production-deployment)
  12. [🎩 CentOS / RHEL / Rocky Linux 9 Dockerized Production Deployment](#-centos--rhel--rocky-linux-9-dockerized-production-deployment)
  13. [🔄 Production Docker Updates, Migrations & Zero-Downtime](#-production-docker-updates-migrations--zero-downtime)
- **Security & Backups**
  14. [🔒 Production Security & Maintenance](#-production-security--maintenance)

---

## 🛠 System Architecture & Stack

| Component | Technology | Details |
|---|---|---|
| **Backend** | PHP 8.3 (PHP-FPM) | Laravel 11 framework, Eloquent ORM, Flatpickr, ECharts |
| **Database** | MySQL 8.0 | InnoDB, `utf8mb4_unicode_ci` |
| **Web Server** | Nginx Alpine | FastCGI reverse proxy on port `8090` |
| **Database GUI**| phpMyAdmin | Web-based database management on port `8091` |
| **Frontend/Assets**| Node.js 20 / Vite | Vite development container on port `5173` |

---

## ⚡ 1-Step Local Docker Setup (Zero Configuration)

You do **not** need PHP, Composer, Node.js, or MySQL installed on your host machine. Docker handles the entire initialization automatically.

### Running the Project

From the project root directory, run:

```bash
docker compose up -d --build
```

### What Happens Automatically Behind the Scenes:
1. **Container Build**: Builds the PHP 8.3 FPM image with all required extensions (`pdo_mysql`, `mbstring`, `gd`, `zip`, `bcmath`, `exif`, `pcntl`).
2. **Environment File**: Automatically copies `.env.example` to `.env` if missing.
3. **Dependencies**: Runs `composer install` automatically.
4. **Application Key**: Generates a new `APP_KEY` via `php artisan key:generate` if empty.
5. **Storage Permissions**: Configures `775` permissions for `storage/` and `bootstrap/cache/` for `www-data`.
6. **Database Healthcheck**: Waits until MySQL 8 is fully initialized and healthy.
7. **Migrations**: Automatically executes all database migrations (`php artisan migrate --force`).
8. **Role & User Seeding**: Seeds test accounts for all roles if the database is fresh.
9. **Storage Link**: Automatically runs `php artisan storage:link` for uploaded documents.
10. **PHP-FPM & Nginx**: Boots web services ready to serve requests.

---

## 🔄 Live Code Reload (Development on Disk)

The application directory (`./app`) is bind-mounted directly into the container (`/var/www/html`):

```yaml
volumes:
  - ./app:/var/www/html
```

- Any file you edit on your computer (VS Code, PhpStorm, Notepad) is **immediately reflected** inside the container.
- Blade views, Controllers, Models, Routes, and Configs take effect on your next browser refresh without rebuilding containers.

---

## 🔑 Default Test Accounts & Roles

All seeded accounts use the default password: **`password`**

| Role | Email (`.gov.bd`) | Email (`.local`) | Access Privileges |
|---|---|---|---|
| **Super Admin** | `admin@ocei.gov.bd` | `superadmin@ocei.local` | Full system control, user management, KPI dashboards, reports |
| **Data Entry Operator** | `operator@ocei.gov.bd` | `deo@ocei.local` | Quick entry forms, archive draft creation, permit submission |
| **Office Assistant** | `oa@ocei.gov.bd` | `office.assistant@ocei.local` | Application verification, forward to Secretary, reject to Operator |
| **Secretary** | `secretary@ocei.gov.bd` | `secretary@ocei.local` | Permit review, approval/rejection, verification reports |
| **Chairman** | `chairman@ocei.gov.bd` | `chairman@ocei.local` | Executive overview, high-level reporting |
| **Inspector** | — | `inspector@ocei.local` | Field inspection & record review |

---

## 🌐 Accessing Services

| Service | Host URL | Default Credentials |
|---|---|---|
| **Laravel Web App** | [http://localhost:8090](http://localhost:8090) | Use any seeded account above (`password`) |
| **phpMyAdmin** | [http://localhost:8091](http://localhost:8091) | Server: `db` · User: `root` · Pass: `OceiRoot@2025` |
| **MySQL Direct (Host)**| `localhost:3307` | Database: `ocei_data_archive` · User: `ocei_user` · Pass: `OceiArchive@2025` |
| **Vite Dev Server** | [http://localhost:5173](http://localhost:5173) | Node container live-reload |

---

## 💻 Useful Development Commands

Run all Artisan and Composer commands through Docker without installing PHP locally:

```bash
# Artisan commands
docker compose exec app php artisan migrate:status
docker compose exec app php artisan db:seed
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan route:list

# Composer commands
docker compose exec app composer require <package-name>
docker compose exec app composer update

# Node / Vite commands
docker compose exec node npm run build
docker compose exec node npm run dev

# Stop containers
docker compose down

# Wipe database and restart from a completely fresh slate
docker compose down -v
docker compose up -d --build
```

---

## 📦 cPanel / Shared Hosting Guide (Native - No Docker)

> [!NOTE]
> This method deploys Laravel natively into cPanel using standard Apache/LiteSpeed web servers, MultiPHP, and MySQL without running Docker.

This guide covers deploying the OCEI Data Archive on shared hosting or a VPS managed with cPanel.

### Step 1: Prepare the Codebase
On your development machine or repository, build the frontend production assets:
```bash
cd app
npm install
npm run build
```

### Step 2: Set PHP Version in cPanel
1. Log into **cPanel**.
2. Open **MultiPHP Manager**.
3. Select your domain and set the PHP version to **PHP 8.2** or **PHP 8.3**.
4. Open **MultiPHP INI Editor** and ensure these extensions/settings:
   - `memory_limit` = `256M` or `512M`
   - `upload_max_filesize` = `64M`
   - `post_max_size` = `64M`
   - Enable extensions: `fileinfo`, `gd`, `mbstring`, `pdo_mysql`, `zip`, `intl`.

### Step 3: Create MySQL Database & User
1. In cPanel, navigate to **MySQL Databases**.
2. Create a database: `cpaneluser_ocei_archive`.
3. Create a user: `cpaneluser_ocei_user` with a strong password.
4. Assign the user to the database with **ALL PRIVILEGES**.

### Step 4: Directory Structure Setup (Security Best Practice)
Never upload the entire Laravel folder into `public_html`. Place source files outside the web root to protect `.env` and sensitive logic:

```text
/home/cpaneluser/
├── ocei-data-archive/      <-- All Laravel project files (except public/)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env
└── public_html/            <-- ONLY the contents of Laravel's public/ directory
    ├── index.php
    ├── .htaccess
    ├── build/
    ├── vendors/
    └── storage (symlink)
```

1. Upload the project folder to `/home/cpaneluser/ocei-data-archive/` via FTP or Git Version Control in cPanel.
2. Move the contents of `ocei-data-archive/public/` into `/home/cpaneluser/public_html/`.
3. Edit `/home/cpaneluser/public_html/index.php` to point to the correct paths:
   ```php
   // Change:
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';

   // To:
   require __DIR__.'/../ocei-data-archive/vendor/autoload.php';
   $app = require_once __DIR__.'/../ocei-data-archive/bootstrap/app.php';
   ```

### Step 5: Configure `.env` in cPanel
In `/home/cpaneluser/ocei-data-archive/.env`:
```ini
APP_NAME="OCEI Data Archive"
APP_ENV=production
APP_KEY=base64:... (generate via artisan or copy)
APP_DEBUG=false
APP_URL=https://yourdomain.gov.bd

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpaneluser_ocei_archive
DB_USERNAME=cpaneluser_ocei_user
DB_PASSWORD=YourStrongDatabasePassword

SESSION_DRIVER=database
CACHE_STORE=database
FILESYSTEM_DISK=public
```

### Step 6: Create Storage Symlink & Run Migrations via SSH or Terminal
Open **Terminal** in cPanel:
```bash
cd /home/cpaneluser/ocei-data-archive

# Install dependencies if vendor wasn't uploaded
composer install --no-dev --optimize-autoloader

# Generate key if not set
php artisan key:generate --force

# Run database migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# Create public storage symlink in public_html
ln -s /home/cpaneluser/ocei-data-archive/storage/app/public /home/cpaneluser/public_html/storage

# Optimize caching for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 7: Setup Cron Job in cPanel
In cPanel **Cron Jobs**, add a new cron running **Every Minute** (`* * * * *`):
```bash
/usr/local/bin/php /home/cpaneluser/ocei-data-archive/artisan schedule:run >> /dev/null 2>&1
```

---

## 🐧 Ubuntu 22.04 / 24.04 LTS Server (Native LEMP - No Docker)

> [!NOTE]
> This method installs Nginx, PHP 8.3-FPM, and MySQL 8 directly onto Ubuntu OS packages (bare-metal or VPS) without using Docker.

### Step 1: System Update & Dependencies
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl git unzip zip certbot python3-certbot-nginx

# Add PHP PPA (Ondřej Surý)
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 & Required Extensions
sudo apt install -y php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
                    php8.3-bcmath php8.3-curl php8.3-gd php8.3-zip \
                    php8.3-intl php8.3-cli nginx mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 2: Configure MySQL 8
```bash
sudo mysql -u root
```
In MySQL console:
```sql
CREATE DATABASE ocei_data_archive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ocei_user'@'localhost' IDENTIFIED BY 'Strong_Production_Pass_2026!';
GRANT ALL PRIVILEGES ON ocei_data_archive.* TO 'ocei_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Clone Application & Configure Permissions
```bash
# Clone to /var/www/ocei-archive
sudo git clone https://github.com/JS-Soft-Solution/ocei-data-archive.git /var/www/ocei-archive
cd /var/www/ocei-archive/app

# Set file permissions
sudo chown -R www-data:www-data /var/www/ocei-archive/app
sudo find /var/www/ocei-archive/app -type f -exec chmod 644 {} \;
sudo find /var/www/ocei-archive/app -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/ocei-archive/app/storage /var/www/ocei-archive/app/bootstrap/cache
```

### Step 4: Install Dependencies & Bootstrap Laravel
```bash
cd /var/www/ocei-archive/app

# Install PHP dependencies
sudo -u www-data composer install --no-dev --optimize-autoloader

# Setup environment file
sudo -u www-data cp .env.example .env
sudo nano .env # Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD, APP_URL

# Generate key & migrate
sudo -u www-data php artisan key:generate --force
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --force
sudo -u www-data php artisan storage:link --force

# Production optimizations
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

### Step 5: Configure Nginx Virtual Host
Create `/etc/nginx/sites-available/ocei`:
```nginx
server {
    listen 80;
    server_name archive.ocei.gov.bd; # Change to your domain or server IP
    root /var/www/ocei-archive/app/public;

    index index.php index.html;
    charset utf-8;

    client_max_body_size 64M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site & test:
```bash
sudo ln -s /etc/nginx/sites-available/ocei /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 6: SSL Certificate (Let's Encrypt)
```bash
sudo certbot --nginx -d archive.ocei.gov.bd
```

### Step 7: Background Queue Worker & Scheduler
Create `/etc/systemd/system/ocei-worker.service`:
```ini
[Unit]
Description=OCEI Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/ocei-archive/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```
Enable & start:
```bash
sudo systemctl daemon-reload
sudo systemctl enable --now ocei-worker
```

Add Laravel scheduler to crontab:
```bash
sudo crontab -u www-data -e
```
Add:
```bash
* * * * * cd /var/www/ocei-archive/app && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🎩 CentOS / RHEL / Rocky Linux 9 Server (Native LEMP - No Docker)

> [!NOTE]
> This method installs native Nginx, Remi RPM PHP 8.3-FPM, and MySQL 8 with full SELinux and FirewallD configuration without using Docker.

### Step 1: Enable Repositories & Install Stack
```bash
sudo dnf update -y
sudo dnf install -y epel-release dnf-plugins-core git curl unzip zip

# Enable Remi Repository for PHP 8.3
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-9.rpm
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.3 -y

# Install Nginx, PHP 8.3, and MySQL Server
sudo dnf install -y nginx mysql-server \
                    php php-fpm php-mysqlnd php-mbstring php-xml \
                    php-bcmath php-gd php-zip php-intl php-cli

# Start & Enable Services
sudo systemctl enable --now nginx mysqld php-fpm
```

### Step 2: Configure MySQL
```bash
sudo mysql_secure_installation
sudo mysql -u root -p
```
In MySQL console:
```sql
CREATE DATABASE ocei_data_archive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ocei_user'@'localhost' IDENTIFIED BY 'Strong_Production_Pass_2026!';
GRANT ALL PRIVILEGES ON ocei_data_archive.* TO 'ocei_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Clone Project & Setup Permissions
```bash
sudo git clone https://github.com/JS-Soft-Solution/ocei-data-archive.git /var/www/ocei-archive
cd /var/www/ocei-archive/app

# Ownership for nginx user in RHEL/CentOS
sudo chown -R nginx:nginx /var/www/ocei-archive/app
sudo chmod -R 775 /var/www/ocei-archive/app/storage /var/www/ocei-archive/app/bootstrap/cache
```

### Step 4: SELinux Configuration (Critical on RHEL/CentOS)
```bash
# Allow Nginx & PHP-FPM to write to storage and cache
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/ocei-archive/app/storage(/.*)?"
sudo semanage fcontext -a -t httpd_sys_rw_content_t "/var/www/ocei-archive/app/bootstrap/cache(/.*)?"
sudo restorecon -Rv /var/www/ocei-archive/app/

# Allow web server network connect (for DB & Mail)
sudo setsebool -P httpd_can_network_connect 1
sudo setsebool -P httpd_can_network_connect_db 1
```

### Step 5: Firewall Rules
```bash
sudo firewall-cmd --permanent --zone=public --add-service=http
sudo firewall-cmd --permanent --zone=public --add-service=https
sudo firewall-cmd --reload
```

### Step 6: Configure PHP-FPM for Nginx
Edit `/etc/php-fpm.d/www.conf`:
```ini
user = nginx
group = nginx
listen = /run/php-fpm/www.sock
listen.owner = nginx
listen.group = nginx
listen.mode = 0660
```
Restart PHP-FPM:
```bash
sudo systemctl restart php-fpm
```

### Step 7: Configure Nginx VHost
Create `/etc/nginx/conf.d/ocei.conf`:
```nginx
server {
    listen 80;
    server_name archive.ocei.gov.bd;
    root /var/www/ocei-archive/app/public;

    index index.php index.html;
    charset utf-8;
    client_max_body_size 64M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Test and reload:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🐳 Production Docker Architecture & Setup

If you prefer to deploy using **Docker** on your own Ubuntu, Debian, CentOS, or RHEL server instead of installing PHP and MySQL directly on the host, this section provides the complete step-by-step guide.

### Recommended Production Architecture

```text
[ Internet Users ]
       │ HTTPS (:443) / HTTP (:80)
       ▼
┌────────────────────────────────────────────────────────┐
│ Host Reverse Proxy (Nginx + Let's Encrypt Certbot)     │
│ Handles SSL, auto-renewal, DDoS protection & buffering │
└───────────────────────┬────────────────────────────────┘
                        │ Reverse Proxy to localhost:8090
                        ▼
┌────────────────────────────────────────────────────────┐
│ Docker Network: ocei-archive-net                       │
│                                                        │
│  ┌──────────────────┐       ┌───────────────────────┐  │
│  │ ocei-archive-nginx│──────▶│ ocei-archive-app      │  │
│  │ (Nginx Alpine)   │ FastCGI│ (PHP 8.3 FPM Laravel) │  │
│  └──────────────────┘       └──────────┬────────────┘  │
│                                        │ TCP :3306     │
│                                        ▼               │
│                             ┌───────────────────────┐  │
│                             │ ocei-archive-db       │  │
│                             │ (MySQL 8.0)           │  │
│                             │ Volume: db_data       │  │
│                             └───────────────────────┘  │
└────────────────────────────────────────────────────────┘
```

#### Why This Production Pattern?
1. **Host Nginx + Certbot**: Let's Encrypt handles SSL certificate renewals automatically on the host without needing to mount certificates or reload Docker containers.
2. **Container Isolation**: PHP 8.3, Laravel, and MySQL 8 run in secure isolated network bridges without exposing MySQL to the public internet.
3. **Persistent Volumes**: All uploaded files (`storage/app/public`) and MySQL database tables (`db_data`) persist on disk across container rebuilds and reboots.

---

## 🐧 Ubuntu / Debian Dockerized Production Deployment

### Step 1: Install Official Docker & Docker Compose
```bash
# Update package list & install prerequisites
sudo apt update && sudo apt upgrade -y
sudo apt install -y ca-certificates curl gnupg lsb-release git ufw

# Add Docker's official GPG key
sudo install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
sudo chmod a+r /etc/apt/keyrings/docker.gpg

# Add Docker repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker Engine & Compose plugin
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Enable & start Docker
sudo systemctl enable --now docker
```

### Step 2: Clone the Repository
```bash
# Clone to /opt/ocei-data-archive
sudo git clone https://github.com/JS-Soft-Solution/ocei-data-archive.git /opt/ocei-data-archive
cd /opt/ocei-data-archive
```

### Step 3: Configure Production `.env`
```bash
cp .env.example .env
nano .env
```
Ensure the following production settings:
```ini
APP_NAME="OCEI Data Archive"
APP_ENV=production
APP_KEY= # Left empty; will be generated on boot or via artisan
APP_DEBUG=false
APP_URL=https://archive.ocei.gov.bd

# Database configuration
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ocei_data_archive
DB_USERNAME=ocei_user
DB_PASSWORD=YourStrongProductionDBPassword!
DB_ROOT_PASSWORD=YourStrongRootPassword2026!

# Sessions & Cache
SESSION_DRIVER=database
CACHE_STORE=database
FILESYSTEM_DISK=public
```

Also verify the matching passwords in `app/.env`:
```bash
cp .env app/.env
```

### Step 4: Build & Launch Docker Containers
```bash
cd /opt/ocei-data-archive

# Build images and start containers in the background
docker compose up -d --build

# Verify all containers are healthy and running
docker compose ps
```

The container entrypoint will automatically:
- Wait for MySQL to become healthy.
- Run database migrations (`php artisan migrate --force`).
- Seed initial users and roles if fresh.
- Create the storage symlink.
- Configure permissions for `www-data`.

### Step 5: Install Host Nginx for SSL Termination
```bash
sudo apt install -y nginx certbot python3-certbot-nginx
```

Create `/etc/nginx/sites-available/ocei-docker.conf`:
```nginx
server {
    listen 80;
    server_name archive.ocei.gov.bd; # Replace with your domain

    client_max_body_size 64M;

    location / {
        proxy_pass http://127.0.0.1:8090;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 300s;
        proxy_connect_timeout 300s;
    }
}
```

Enable site & reload:
```bash
sudo ln -s /etc/nginx/sites-available/ocei-docker.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

Obtain free SSL certificate:
```bash
sudo certbot --nginx -d archive.ocei.gov.bd
```

### Step 6: Systemd Auto-Start on System Boot
Create `/etc/systemd/system/ocei-docker.service`:
```ini
[Unit]
Description=OCEI Data Archive Docker Stack
Requires=docker.service
After=docker.service

[Service]
Type=oneshot
RemainAfterExit=yes
WorkingDirectory=/opt/ocei-data-archive
ExecStart=/usr/bin/docker compose up -d
ExecStop=/usr/bin/docker compose down
TimeoutStartSec=0

[Install]
WantedBy=multi-user.target
```
Enable the service:
```bash
sudo systemctl daemon-reload
sudo systemctl enable ocei-docker
```

### Step 7: Host Scheduler Cron for Docker
Add the Laravel schedule runner to the host crontab:
```bash
sudo crontab -e
```
Add:
```bash
* * * * * cd /opt/ocei-data-archive && docker compose exec -T app php artisan schedule:run >> /dev/null 2>&1
```

---

## 🎩 CentOS / RHEL / Rocky Linux 9 Dockerized Production Deployment

### Step 1: Install Docker CE on RHEL / Rocky Linux 9
```bash
sudo dnf update -y
sudo dnf install -y yum-utils git curl

# Add Docker CE repository
sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo

# Install Docker CE & Compose
sudo dnf install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin

# Start and enable Docker daemon
sudo systemctl enable --now docker
```

### Step 2: Clone Project & Configure Environment
```bash
sudo git clone https://github.com/JS-Soft-Solution/ocei-data-archive.git /opt/ocei-data-archive
cd /opt/ocei-data-archive

cp .env.example .env
cp .env app/.env
sudo nano .env # Set APP_ENV=production, APP_DEBUG=false, DB passwords, and APP_URL
```

### Step 3: SELinux Configuration for Docker Volumes
In RHEL/CentOS, SELinux requires volume permissions if files are bind-mounted:
```bash
# Allow container access to application directory
sudo chcon -Rt svirt_sandbox_file_t /opt/ocei-data-archive/app

# Allow network connections through firewall
sudo firewall-cmd --permanent --zone=public --add-service=http
sudo firewall-cmd --permanent --zone=public --add-service=https
sudo firewall-cmd --reload
```

### Step 4: Boot Containers
```bash
cd /opt/ocei-data-archive
docker compose up -d --build
```

### Step 5: Host Reverse Proxy (Nginx) & SSL
Install native Nginx on CentOS/RHEL:
```bash
sudo dnf install -y nginx epel-release
sudo dnf install -y certbot python3-certbot-nginx
```

Create `/etc/nginx/conf.d/ocei-docker.conf`:
```nginx
server {
    listen 80;
    server_name archive.ocei.gov.bd;

    client_max_body_size 64M;

    location / {
        proxy_pass http://127.0.0.1:8090;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 300s;
        proxy_connect_timeout 300s;
    }
}
```

Allow Nginx to connect to network socket via SELinux:
```bash
sudo setsebool -P httpd_can_network_connect 1
sudo systemctl enable --now nginx
sudo certbot --nginx -d archive.ocei.gov.bd
```

---

## 🔄 Production Docker Updates, Migrations & Zero-Downtime

To deploy a new update or bugfix to a Dockerized production server:

```bash
cd /opt/ocei-data-archive

# 1. Pull latest code from main branch
git pull origin main

# 2. Rebuild app container if Dockerfile or PHP packages changed
docker compose build app

# 3. Restart containers with new build
docker compose up -d

# 4. Run database migrations safely
docker compose exec -T app php artisan migrate --force

# 5. Clear and refresh caches
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache
```

### Daily Docker Database Backup Script

Create a backup script at `/opt/backup_db.sh`:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/ocei_docker"
mkdir -p "$BACKUP_DIR"
DATE=$(date +%F_%H%M%S)

cd /opt/ocei-data-archive
docker compose exec -T db mysqldump -u root -p'YourRootPassword' ocei_data_archive | gzip > "$BACKUP_DIR/ocei_db_$DATE.sql.gz"

# Keep only the last 30 days of backups
find "$BACKUP_DIR" -type f -name "*.sql.gz" -mtime +30 -delete
```
Make executable and schedule in host crontab:
```bash
chmod +x /opt/backup_db.sh
# Add to crontab:
# 0 2 * * * /opt/backup_db.sh
```

---

## 🔒 Production Security & Maintenance

1. **Always Set `APP_DEBUG=false`** in production `.env` to prevent leaking stack traces and environment credentials.
2. **Optimize Cache on Deployment**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. **Database Backups**:
   Schedule daily MySQL dumps using cron:
   ```bash
   mysqldump -u ocei_user -p'YourPassword' ocei_data_archive | gzip > /var/backups/ocei_$(date +\%F).sql.gz
   ```
4. **File Permissions**: Keep `644` for files and `755` for directories. Only `storage/` and `bootstrap/cache/` require `775` write access by the web server user (`www-data` or `nginx`).

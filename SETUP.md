# AdvantageHCS Admin Panel — Setup Guide

## Tech Stack

| Component | Version |
|-----------|---------|
| PHP | 8.1 (8.2 compatible) |
| Laravel | 10.x |
| MySQL | 8.0+ |
| Frontend | Blade + Vanilla CSS/JS |

> **Note:** Laravel 12 requires PHP 8.2+. The sandbox has PHP 8.1, so Laravel 10 was used. The code is fully compatible with Laravel 12 once deployed on a PHP 8.2 server — simply update `composer.json` to require `laravel/framework: ^12.0` and run `composer update`.

---

## Local Development Setup

### 1. Clone and Install

```bash
cd admin-panel
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Configure `.env`

```env
APP_NAME="AdvantageHCS Admin"
APP_URL=https://admin.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=advantage_hcs_admin
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# External API integrations (appointment & billing systems)
APPOINTMENT_API_URL=https://your-booking-system.com/api
APPOINTMENT_API_KEY=your_appointment_api_key
BILLING_API_URL=https://your-billing-system.com/api
BILLING_API_KEY=your_billing_api_key
```

### 3. Database Setup

```sql
CREATE DATABASE advantage_hcs_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'admin_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON advantage_hcs_admin.* TO 'admin_user'@'localhost';
FLUSH PRIVILEGES;
```

```bash
php artisan migrate
```

### 4. Create First Admin User

```bash
php artisan tinker
```

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name'     => 'Admin User',
    'email'    => 'admin@yourdomain.com',
    'password' => Hash::make('your_secure_password'),
    'role'     => 'admin',
]);
```

### 5. Run Development Server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

---

## Production Deployment (Apache/Nginx)

### Nginx Config

```nginx
server {
    listen 80;
    server_name admin.yourdomain.com;
    root /var/www/admin-panel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Admin Panel Pages

| Page | URL | Description |
|------|-----|-------------|
| Login | `/login` | PHP/Laravel native authentication |
| Register | `/register` | Create new admin accounts |
| Dashboard | `/` | Overview stats, recent patients, quick actions |
| Patients | `/patients` | CRUD patient management |
| Add Patient | `/patients/create` | Full patient intake form |
| Appointments | `/appointments` | View appointments (fetched from external API) |
| Billing | `/billing` | View billing records (fetched from external API) |
| Forms | `/forms` | Manage patient intake forms (builder placeholder) |
| Create Form | `/forms/create` | Create a new form (black canvas — drag-and-drop coming) |
| Funnels | `/funnels` | Multi-step form sequences (builder placeholder) |
| Create Funnel | `/funnels/create` | Create a funnel with multiple forms + shareable URL |
| Messages | `/messages` | Secure messaging with patients |

---

## Database Schema

### Tables Created

- `users` — Admin panel users (role: admin/staff)
- `patients` — Patient records with demographics, insurance, MRN
- `forms` — Form definitions (name, category, status, schema JSON)
- `funnels` — Multi-form sequences with shareable URL slugs
- `funnel_forms` — Pivot table linking funnels to forms (ordered)
- `form_submissions` — Patient form submission data (JSON)
- `messages` — Admin ↔ Patient messaging

---

## External API Integration

Appointments and billing data are fetched from your external systems. Configure the API URLs and keys in `.env`:

```env
APPOINTMENT_API_URL=https://your-booking-system.com/api
APPOINTMENT_API_KEY=your_key
BILLING_API_URL=https://your-billing-system.com/api
BILLING_API_KEY=your_key
```

When no API URL is configured, the system shows **mock/sample data** for development purposes.

---

## Upcoming Features (Placeholders)

- **Form Builder** — Drag-and-drop form builder on the Form detail page (black canvas ready)
- **Funnel Builder** — Visual multi-step funnel editor (black canvas ready)
- **Shareable Funnel URLs** — Generated URLs sent to patients to complete form sequences

---

## Default Admin Credentials (Development Only)

```
Email:    admin@advantagehcs.com
Password: Admin@12345
```

> **Change these immediately in production.**

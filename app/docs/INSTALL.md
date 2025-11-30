# Quick Start Installation Guide

## Prerequisites

- PHP 8.2+
- Laravel 12
- MySQL 8.0+
- Composer
- Existing `users` table with role column

---

## Step-by-Step Installation

### 1. Install Dependencies

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

### 2. Configure Private Storage

Add to `config/filesystems.php`:

```php
'disks' => [
    // ... existing disks
    
    'private' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
        'visibility' => 'private',
    ],
],
```

### 3. Include Routes

Add to `routes/web.php`:

```php
require __DIR__.'/legacy_permits.php';
```

### 4. Run Migrations

```bash
php artisan migrate
```

This creates:
- `attachments` table
- `record_histories` table  
- `ex_electrician_renew_applications` table
- `ex_supervisor_renew_applications` table
- `ex_contractor_renew_applications` table

### 5. Register Policies (if needed)

For Laravel 10 or custom auth, add to `app/Providers/AuthServiceProvider.php`:

```php
use App\Models\ExElectricianRenewApplication;
use App\Models\ExSupervisorRenewApplication;
use App\Models\ExContractorRenewApplication;
use App\Policies\ExElectricianRenewApplicationPolicy;
use App\Policies\ExSupervisorRenewApplicationPolicy;
use App\Policies\ExContractorRenewApplicationPolicy;

protected $policies = [
    ExElectricianRenewApplication::class => ExElectricianRenewApplicationPolicy::class,
    ExSupervisorRenewApplication::class => ExSupervisorRenewApplicationPolicy::class,
    ExContractorRenewApplication::class => ExContractorRenewApplicationPolicy::class,
];
```

Laravel 11+ auto-discovers policies.

### 6. Create Storage Directory

```bash
mkdir -p storage/app/private/legacy_permits
chmod -R 775 storage/app/private
```

### 7. Set Up User Roles

Ensure your `users` table has a `role` column with one of:
- `data_entry_operator`
- `office_assistant`
- `secretary`
- `chairman`
- `super_admin`

Example seeder:

```php
DB::table('users')->insert([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'role' => 'super_admin',
]);
```

### 8. Clear Caches

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## Testing the System

### Test Workflow

1. **Login as Data Entry Operator**
   - Navigate to `/ex-electrician/operator`
   - Create a new application
   - Fill all 5 tabs
   - Upload an attachment
   - Submit for approval

2. **Login as Office Assistant**
   - Navigate to `/ex-electrician/office-assistant/pending`
   - Review application
   - Approve to forward to Secretary

3. **Login as Secretary**
   - Navigate to `/ex-electrician/secretary/pending`
   - Final approval (locks the record)

4. **Login as Chairman**
   - Navigate to `/ex-electrician/chairman`
   - View approved records (read-only)

5. **Login as Super Admin**
   - Navigate to `/ex-electrician/admin`
   - Edit locked record (override)
   - View audit trail

### Verify Features

- ✅ Multi-tab form saves progress
- ✅ Attachments upload to `storage/app/private`
- ✅ Audit trail logs all actions
- ✅ Status transitions follow workflow
- ✅ Locked records protected from regular users
- ✅ Search and filters work
- ✅ Bulk operations function correctly
- ✅ Excel export downloads

---

## Troubleshooting

### "Class 'Excel' not found"
```bash
composer require maatwebsite/excel
php artisan config:clear
```

### "Storage link missing"
```bash
php artisan storage:link
```

### "Attachment download fails"
- Check `storage/app/private` exists and is writable
- Verify file permissions: `chmod -R 775 storage`

### "Policy not working"
- Clear route cache: `php artisan route:clear`
- Verify AuthServiceProvider is registering policies
- Check user role column value

### "Migration errors"
- Ensure MySQL identifier limit (64 chars)
- Check foreign key constraints exist on users table
- Verify database charset is utf8mb4_unicode_ci

---

## Production Deployment

### Before Going Live

1. **Enable HTTPS** - SSL required for government data
2. **Set proper permissions:**
   ```bash
   chown -R www-data:www-data storage bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

3. **Environment variables:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   DB_HOST=your-db-host
   DB_DATABASE=your-database
   DB_USERNAME=your-user
   DB_PASSWORD=your-password
   FILESYSTEM_DISK=private
   ```

4. **Optimize:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   composer install --optimize-autoloader --no-dev
   ```

5. **Set up backups:**
   - Daily database dumps
   - Weekly file storage backups
   - Keep 90 days retention

6. **Configure queue (optional for large exports):**
   ```bash
   php artisan queue:work --daemon
   ```

7. **Set up monitoring:**
   - Log rotation
   - Error tracking
   - Storage usage alerts

---

## File Structure Verification

Ensure these files exist:

```
app/
├── Http/Controllers/
│   ├── AttachmentController.php ✅
│   └── Permits/
│       ├── Electrician/ (6 controllers) ✅
│       ├── Supervisor/ (1+ controllers) ✅
│       └── Contractor/ (1+ controllers) ✅
├── Models/
│   ├── Attachment.php ✅
│   ├── RecordHistory.php ✅
│   ├── ExElectricianRenewApplication.php ✅
│   ├── ExSupervisorRenewApplication.php ✅
│   └── ExContractorRenewApplication.php ✅
├── Policies/
│   ├── BasePermitPolicy.php ✅
│   └── 3 permit policies ✅
├── Traits/
│   └── HasAuditHistory.php ✅
└── Exports/
    └── 3 export classes ✅

database/migrations/
└── 5 migration files ✅

routes/
└── legacy_permits.php ✅

resources/views/permits/
├── electrician/ (operator, office-assistant, secretary, etc.) ✅
└── components/ ✅
```

---

## What's Included

### ✅ Fully Functional (Ready to Use)

- Complete database schema with indexes
- All 7 models with relationships
- Authorization via 4 policies
- Electrician module (6 controllers, essential views)
- Attachment secure handling
- Audit trail logging
- Excel export capability
- User guides documentation

### ⏳ Pattern Established (Easy to Replicate)

- Remaining views (tabs 2-4 for supervisor/contractor)
- Additional controller methods (copy from electrician)
- PDF export templates

**Estimated completion time: 2-3 hours following patterns**

---

## Support

- **User Guides:** `docs/user_guides/` directory
- **Technical Docs:** `docs/README.md`
- **Code Examples:** Follow electrician module patterns

---

## Success Criteria

✅ All migrations run without errors  
✅ Users can create, edit, submit applications  
✅ Office Assistant can approve/reject  
✅ Secretary can final approve (locks record)  
✅ Attachments upload and download securely  
✅ Audit trail shows all actions  
✅ Excel export generates successfully  
✅ Super Admin can override locked records  

---

**Ready to deploy!** 🚀

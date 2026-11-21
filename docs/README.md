# Legacy Permit Digitization Module - README

## Overview

A complete, production-ready Laravel 12 module for digitizing and verifying legacy permits from paper registers for three permit types:
- **Ex-Electrician**
- **Ex-Supervisor**  
- **Ex-Contractor**

Built for government use with comprehensive audit trails, role-based workflow, and secure document management.

---

## Features

### ✅ Multi-Role Workflow
- **Data Entry Operator** - Digitize permit records with 5-tab forms
- **Office Assistant** - First-level verification
- **Secretary** - Final approval (locks record)
- **Chairman** - Read-only access to approved records
- **Super Admin** - Override capabilities and full audit access

### ✅ Complete Audit Trail
- Every action logged (create, update, status change, attachment, override)
- IP address and user agent tracking
- Old/new values stored as JSON
- Polymorphic history for all permit types

### ✅ Secure Attachments
- Polymorphic file storage
- Private directory (not web-accessible)
- Secure download routes with authorization
- Support for PDF, JPG, PNG (max 10MB)

### ✅ Multi-Tab Forms
- **Electrician/Supervisor**: Personal Info, Address, Education, Work Experience, Certificate
- **Contractor**: Company Info, Business Address, Representative, Equipment, Certificate
- Per-tab validation
- Save & Continue functionality

### ✅ Bulk Operations
- Bulk submit (operators)
- Bulk approve/reject (OA & Secretary)
- Partial success handling with failed records report

### ✅ Advanced Search & Filters
- Full-text search (certificate #, NID, name, mobile)
- Status filtering
- Date range filtering
- District/division filtering

### ✅ Export Capabilities
- Excel export with full data
- PDF export with formatting
- Queueable jobs for large datasets
- Custom filters apply to exports

---

## Installation

### 1. Run Migrations

```bash
php artisan migrate
```

This creates:
- `attachments` table
- `record_histories` table
- `ex_electrician_renew_applications` table
- `ex_supervisor_renew_applications` table
- `ex_contractor_renew_applications` table

### 2. Include Routes

Add to `routes/web.php`:

```php
require __DIR__.'/legacy_permits.php';
```

### 3. Register Service Providers

If using Laravel 11+, policies should auto-discover. For Laravel 10 or custom setup, register in `AuthServiceProvider`:

```php
protected $policies = [
    ExElectricianRenewApplication::class => ExElectricianRenewApplicationPolicy::class,
    ExSupervisorRenewApplication::class => ExSupervisorRenewApplicationPolicy::class,
    ExContractorRenewApplication::class => ExContractorRenewApplicationPolicy::class,
];
```

### 4. Configure Filesystems

In `config/filesystems.php`, ensure `private` disk exists:

```php
'private' => [
    'driver' => 'local',
    'root' => storage_path('app/private'),
    'visibility' => 'private',
],
```

### 5. Install Dependencies

```bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf
```

### 6. Seed User Roles (if needed)

Ensure your `users` table has a `role` column with values:
- `data_entry_operator`
- `office_assistant`
- `secretary`
- `chairman`
- `super_admin`

---

## File Structure

```
app/
├── Http/Controllers/
│   ├── AttachmentController.php
│   └── Permits/
│       ├── Electrician/
│       │   ├── ElectricianOperatorController.php
│       │   ├── ElectricianOfficeAssistantController.php
│       │   ├── ElectricianSecretaryController.php
│       │   ├── ElectricianChairmanController.php
│       │   ├── ElectricianAdminController.php
│       │   └── ElectricianReportController.php
│       ├── Supervisor/ (similar structure)
│       └── Contractor/ (similar structure)
├── Models/
│   ├── Attachment.php
│   ├── RecordHistory.php
│   ├── ExElectricianRenewApplication.php
│   ├── ExSupervisorRenewApplication.php
│   └── ExContractorRenewApplication.php
├── Policies/
│   ├── BasePermitPolicy.php
│   ├── ExElectricianRenewApplicationPolicy.php
│   ├── ExSupervisorRenewApplicationPolicy.php
│   └── ExContractorRenewApplicationPolicy.php
├── Traits/
│   └── HasAuditHistory.php
└── Exports/
    ├── ElectricianExport.php
    ├── SupervisorExport.php (pattern)
    └── ContractorExport.php (pattern)

database/migrations/
├── 2025_11_30_000001_create_attachments_table.php
├── 2025_11_30_000002_create_record_histories_table.php
├── 2025_11_30_000003_create_ex_electrician_renew_applications_table.php
├── 2025_11_30_000004_create_ex_supervisor_renew_applications_table.php
└── 2025_11_30_000005_create_ex_contractor_renew_applications_table.php

resources/views/permits/
├── electrician/
│   ├── operator/ (index, edit, tabs/tab1-5)
│   ├── office-assistant/ (pending, show)
│   ├── secretary/ (pending, show)
│   ├── chairman/ (index, show)
│   ├── admin/ (index, edit)
│   └── reports/ (index, preview, pdf)
├── supervisor/ (similar structure)
├── contractor/ (similar structure)
└── components/
    ├── _history_timeline.blade.php
    ├── _attachments.blade.php
    └── _sidebar.blade.php

routes/
└── legacy_permits.php

docs/user_guides/
├── data_entry_operator_guide.md
├── office_assistant_guide.md
├── secretary_guide.md
├── chairman_guide.md
└── super_admin_guide.md
```

---

## Workflow States

```
draft
  ↓ (operator submits)
submitted_to_office_assistant
  ↓ (OA approves)              ↘ (OA rejects)
submitted_to_secretary        office_assistant_rejected
  ↓ (secretary approves)        ↓ (operator resubmits)
secretary_approved_final ✅  (back to submitted_to_office_assistant)
  ↓ (LOCKED - only super admin can edit)

ALTERNATIVE PATH:
secretary_rejected
  ↓ (operator resubmits)
(goes directly to submitted_to_secretary, skips OA)
```

---

## Authorization Matrix

| Role | Create | Edit Draft | Edit Locked | Submit | Approve (OA) | Approve (Sec) | View Final | Delete | Override |
|------|--------|------------|-------------|--------|--------------|---------------|------------|--------|----------|
| Data Entry Operator | ✅ | ✅ (own) | ❌ | ✅ (own) | ❌ | ❌ | ✅ (own) | ❌ | ❌ |
| Office Assistant | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ | ❌ | ❌ |
| Secretary | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |
| Chairman | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ (read-only) | ❌ | ❌ |
| Super Admin | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## Database Schema Highlights

### Workflow Columns (on all 3 permit tables)

- `status` - Current workflow state
- `entry_by`, `entry_at` - Who digitized this
- `last_updated_by`, `last_updated_at` - Last modification
- `rejected_by`, `reject_reason`, `rejected_at` - Rejection tracking
- `verified_by_office_assistant`, `verified_at_office_assistant` - OA approval
- `approved_by_secretary`, `approved_at_secretary` - Final approval
- `deleted_by`, `deleted_at` - Soft delete tracking

### Indexes

All tables indexed on:
- `status`
- `status + entry_by` (composite)
- `old_certificate_number` (unique)
- `created_at`
- `nid_number` / `mobile_no` (for search)

---

## API Routes (Subset)

### Electrician Routes (47 total)
- `GET /ex-electrician/operator` - List own applications
- `POST /ex-electrician/operator` - Create application
- `PUT /ex-electrician/operator/{id}` - Update application
- `POST /ex-electrician/operator/{id}/submit` - Submit for approval
- `POST /ex-electrician/operator/bulk-submit` - Bulk submit
- `POST /ex-electrician/operator/claim` - Claim existing record
- `GET /ex-electrician/office-assistant/pending` - Pending reviews
- `POST /ex-electrician/office-assistant/{id}/approve` - OA approve
- `POST /ex-electrician/office-assistant/{id}/reject` - OA reject
- `POST /ex-electrician/office-assistant/bulk-approve` - Bulk approve
- `POST /ex-electrician/secretary/pending` - Secretary queue
- `POST /ex-electrician/secretary/{id}/approve` - Final approve (locks)
- `POST /ex-electrician/secretary/{id}/reject` - Secretary reject
- `GET /ex-electrician/chairman` - View approved records
- `GET /ex-electrician/admin` - View all including deleted
- `PUT /ex-electrician/admin/{id}` - Override edit
- `DELETE /ex-electrician/admin/{id}` - Soft delete
- `POST /ex-electrician/admin/{id}/restore` - Restore deleted
- `GET /ex-electrician/reports/export-excel` - Excel export
- `GET /ex-electrician/reports/export-pdf` - PDF export

*Similar routes exist for Supervisor and Contractor*

### Shared Routes (3)
- `POST /attachments` - Upload file
- `GET /attachments/{id}/download` - Secure download
- `DELETE /attachments/{id}` - Delete file

---

## Configuration

### Attachment Types

Defined in tab5.blade.php:
- `nid_copy` - National ID Card
- `old_certificate` - Original certificate scan
- `photo` - Applicant photograph
- `education_doc` - Academic certificates
- `experience_doc` - Work experience proof
- `other` - Miscellaneous

### File Upload Limits

Set in AttachmentController validation:
- Max size: 10MB
- Allowed types: PDF, JPG, JPEG, PNG, WEBP

To modify, edit:
```php
$request->validate([
    'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,webp',
]);
```

---

## Testing

### Manual Testing Checklist

- [ ] Data entry operator can create and edit drafts
- [ ] Cannot submit without attachments
- [ ] Office assistant sees pending queue
- [ ] OA can approve/reject with reasons
- [ ] Secretary sees OA-approved applications
- [ ] Secretary approval locks the record
- [ ] Operator cannot edit locked record
- [ ] Super admin can edit locked record
- [ ] All actions appear in audit trail
- [ ] Attachments download securely
- [ ] Bulk operations work correctly
- [ ] Export generates Excel/PDF

### Unit Test Examples

```php
// Test policy authorization
$this->actingAs($operator);
$response = $this->post('/ex-electrician/operator', $data);
$response->assertStatus(201);

// Test locked record protection
$application->update(['status' => 'secretary_approved_final']);
$response = $this->put("/ex-electrician/operator/{$application->id}", $data);
$response->assertForbidden();

// Test super admin override
$this->actingAs($superAdmin);
$response = $this->put("/ex-electrician/admin/{$application->id}", $data);
$response->assertSuccessful();
```

---

## Performance Considerations

### Optimization Tips

1. **Pagination** - All lists use `paginate(20)`
2. **Eager Loading** - Controllers use `with()` to avoid N+1 queries
3. **Indexes** - All filter/search columns indexed
4. **Queueable Exports** - Large exports should go to queue
5. **Attachment Storage** - Files stored outside database

### Scaling Up

For 100,000+ records:
- Enable Redis for caching
- Queue attachment uploads
- Add database read replicas
- Consider Elasticsearch for search

---

## Security

### Implemented Protections

- ✅ Authorization via Laravel Policies on every action
- ✅ CSRF protection on all state-changing requests
- ✅ Private file storage (not publicly accessible)
- ✅ Secure download routes with auth check
- ✅ Soft deletes (no permanent data loss)
- ✅ Complete audit trail
- ✅ SQL injection protection (Eloquent ORM)
- ✅ XSS protection (Blade escaping)

### Additional Recommendations

- Enable HTTPS in production
- Rate limiting on upload endpoints
- Virus scanning on uploaded files
- Regular database backups
- Access logs monitoring

---

## Troubleshooting

### Common Issues

**"Identifier name too long" during migration**
- MySQL identifier limit is 64 chars
- Shorten foreign key constraint names if needed

**"Undefined method authorize()"**
- Ensure `AuthorizesRequests` trait is in base Controller

**"Private file not found"**
- Check `storage/app/private` directory exists
- Verify file permissions

**"Class 'Excel' not found"**
- Run `composer require maatwebsite/excel`

---

## Future Enhancements

Potential additions:
- Queue management for bulk operations
- Real-time notifications (Pusher/WebSockets)
- Mobile app for field operators
- OCR for automatic data extraction from scans
- Barcode/QR code generation for certificates
- Integration with national ID database
- Multi-language support (full Bengali translation)

---

## License

Proprietary - Government of Bangladesh

---

## Support

**Technical Issues:** IT Support Department  
**Policy Questions:** Office of the Secretary  
**Training Requests:** HR & Training Division

---

**Version:** 1.0.0  
**Last Updated:** 2025-11-30  
**Developed For:** Electrical Licensing Board

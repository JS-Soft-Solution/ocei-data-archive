# Super Admin User Guide
## Legacy Permit Digitization System

---

## Overview

As **Super Admin**, you have unrestricted access to the entire system with the ability to override locked records, change statuses, delete/restore applications, and access the complete audit trail.

---

## Special Capabilities

Your unique powers:

1. 🔓 **Edit locked records** (secretary-approved applications)
2. 🔄 **Change any status** manually
3. 🗑️ **Soft delete** applications
4. ♻️ **Restore** deleted applications
5. 📊 **View all applications** (including soft-deleted)
6. ✍️ **Override any workflow rule**
7. 📜 **Full audit trail access**

**⚠️ Important:** All your actions are logged with "super_admin_override" flag in the audit trail.

---

## Accessing Admin Panel

**Path:** Legacy Permits → Ex-Electrician → Admin Panel

**View options:**
- All applications (including deleted)
- Filter by any status
- Search across all users' records
- View soft-deleted records only

---

## Managing Applications

### Viewing All Records

The admin index shows:
- All users' applications
- Deleted records (with trash icon)
- Complete workflow history
- Override history

**Filters:**
- ☑️ Show deleted records only
- Status filter (all statuses)
- Search by any field

---

### Editing Locked Records

**When needed:**
- Correcting critical errors in approved records
- Updating information after government audit
- Fixing data entry mistakes post-approval

**How to:**
1. Find the application (even if secretary_approved_final)
2. Click **"Edit"** (you bypass the lock)
3. Make necessary changes
4. **Enter override reason** (mandatory)
5. Save changes

**Result:**
- Audit log shows: "super_admin_override"
- Old values and new values recorded
- Your reason is saved
- Original approval status can remain

---

### Manual Status Changes

**Use cases:**
- Rolling back workflow errors
- Expediting emergency applications
- Correcting status after system issues

**Process:**
1. Open application
2. Click **"Change Status"**
3. Select new status from dropdown:
   - draft
   - submitted_to_office_assistant
   - office_assistant_rejected
   - submitted_to_secretary
   - secretary_rejected
   - secretary_approved_final
4. **Enter reason for change** (required)
5. Confirm

**⚠️ Warning:** This bypasses normal workflow - use sparingly!

---

### Soft Delete Applications

**When to use:**
- Duplicate entries
- Test data cleanup
- Invalid/fraudulent applications
- Data entry errors beyond repair

**How to:**
1. Navigate to application
2. Click **"Delete"** button
3. Confirm action
4. Application is soft-deleted (not permanently removed)
5. Moved to trash, visible with "Show Deleted" filter

**Data preserved:**
- All application data retained
- Attachments remain in storage
- Full audit trail kept
- Can be restored anytime

---

### Restore Deleted Applications

**Process:**
1. Enable **"Show Deleted"** filter
2. Find the deleted application
3. Click **"Restore"** button
4. Confirm action
5. Application returns to its previous status
6. `deleted_by` field cleared

---

## Audit Trail Review

### Why this matters

Government digitization requires full accountability. The audit trail shows:
- Every action on every record
- Who did what and when
- Old values vs new values
- IP addresses and user agents
- Override reasons

### Accessing audit history

On any application view:
- Scroll to **"History Timeline"** section
- See chronological list of all events:
  - ✏️ Created
  - 📝 Updated (field changes)
  - 🔄 Status changed
  - 📎 Attachment added/deleted
  - ⚡ Super admin override
  - 🗑️ Soft deleted
  - ♻️ Restored

### Investigating overrides

**To see all admin interventions:**
1. Filter history by action: "super_admin_override"
2. Review reasons provided
3. Check old/new values JSON
4. Identify who made override

---

## Bulk Operations

### Bulk Status Change (Advanced)

**Scenario:** Emergency status rollback for a batch

1. Select multiple applications
2. Use custom script or contact IT
3. Provide detailed justification

**Note:** No UI for bulk override yet - contact development team for implementation if needed frequently.

---

## Security Best Practices

### DO:
- ✅ Always provide detailed override reasons
- ✅ Document why you're bypassing workflow
- ✅ Inform Secretary/Chairman of major changes
- ✅ Review audit trail regularly
- ✅ Use soft delete instead of permanent delete
- ✅ Restore mistakenly deleted records promptly

### DON'T:
- ❌ Make frivolous status changes
- ❌ Edit records without clear need
- ❌ Use override power for convenience
- ❌ Delete records permanently (always soft delete)
- ❌ Bypass workflow without documentation

---

## Emergency Procedures

### System-Wide Issues

**If workflow is stuck:**
1. Identify affected applications
2. Document the issue
3. Manual status change to correct state
4. Log detailed override reason
5. Notify users of the correction

### Data Corruption

**If data is corrupted:**
1. Soft delete the corrupted record
2. Create new record from paper source
3. Copy any salvageable data
4. Document in audit log

### Urgent Approvals

**If emergency approval needed:**
1. Verify with Secretary/Chairman
2. Change status to "secretary_approved_final"
3. Reason: "Emergency approval requested by [Authority] for [Reason]"
4. Follow up with proper documentation

---

## Reporting & Analytics

### Generate Admin Reports

**Path:** Reports → Advanced

**Available reports:**
- All overrides by date
- Deleted records log
- Status change history
- User activity summary

**Export options:**
- Excel (detailed data)
- PDF (formatted report)

---

## User Management Integration

While user roles are managed elsewhere, you can:
- See which user created each application
- Track data entry operator productivity
- Identify users needing training (high rejection rates)
- Monitor approval patterns

---

## Workflow Override Scenarios

### Scenario 1: Post-Approval Correction

**Situation:** Secretary approved, but found typo in NID

**Action:**
1. Edit locked record
2. Fix NID number
3. Reason: "Correcting NID typo: [old] → [new]. Verified with original document."
4. Status remains "secretary_approved_final"

### Scenario 2: Stuck in Workflow

**Situation:** OA on leave, urgent application

**Action:**
1. Change status from "submitted_to_office_assistant" to "submitted_to_secretary"
2. Reason: "OA unavailable. Fast-tracked per Chairman directive [Date]."

### Scenario 3: Wrongful Rejection

**Situation:** Secretary rejected by mistake

**Action:**
1. Change status from "secretary_rejected" to "secretary_approved_final"
2. Reason: "Overriding rejection. Secretary confirmed approval via memo [Date/Ref]."

---

## Compliance & Audit

**Your actions are subject to:**
- Internal review by Chairman
- External government audits
- RTI (Right to Information) requests

**Ensure:**
- Every override is justifiable
- Reasons are professional and detailed
- Actions align with government policy
- No personal data is inappropriately accessed

---

## Advanced Features

### Database Console (if enabled)

Some super admins have direct database access:
- Use only for bulk operations
- Always backup before mass updates
- Document all database-level changes

### API Access (if enabled)

If you have API keys:
- Automate bulk corrections
- Integrate with external systems
- Generate custom reports

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Can't edit locked record | Verify super_admin role in Users table |
| Restore button missing | Check application is shown in deleted filter |
| Override reason required | Never leave reason field empty |
| Audit trail not updating | Check HasAuditHistory trait on model |

---

## Best Practices Summary

1. **Document everything** - Future auditors will review your actions
2. **Use least privilege** - Only override when truly necessary
3. **Communicate** - Inform stakeholders of major changes
4. **Monitor** - Review override patterns monthly
5. **Train others** - Reduce need for overrides through proper training

---

## Workflow Complete Picture

```
DATA ENTRY OPERATOR (Draft)
    ↓ Submit
OFFICE ASSISTANT (Verify)
    ↓ Approve         ↘ Reject back to operator
SECRETARY (Final Review)
    ↓ Approve         ↘ Reject back to operator
SECRETARY_APPROVED_FINAL 🔒
    ↓ (Only Super Admin can edit now)
YOU (Super Admin) ⚡
    - Edit locked records
    - Change any status
    - Delete/Restore
    - Override anything
    - Full audit access
```

---

**Remember:** With great power comes great responsibility. Your override capabilities exist for system integrity, not convenience. Every action should be auditable and justifiable.

---

## Emergency Contacts

- **Chairman:** [Contact]
- **Secretary:** [Contact]
- **IT Support:** [Contact]
- **Database Admin:** [Contact]

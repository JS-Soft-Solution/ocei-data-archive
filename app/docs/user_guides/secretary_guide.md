# Secretary User Guide
## Legacy Permit Digitization System

---

## Overview

As **Secretary**, you have the final say on all permit applications. Your approval locks records permanently and makes them part of the official government archive.

---

## Your Critical Role

You are the **final checkpoint** before permits become official:

1. ✅ Review applications verified by Office Assistant
2. ✅ Make final approval decision (locks record)
3. ❌ Reject applications that need significant corrections
4. 📊 Maintain high standards for archived records

**⚠️ Important:** Your approval is FINAL and PERMANENT. Only Super Admin can edit after you approve.

---

## Accessing Your Queue

**Path:** Legacy Permits → Ex-Electrician → Secretary → Pending

You'll see applications with status **"Submitted to Secretary"** that have already been verified by the Office Assistant.

---

## Review Process

### Step 1: Open Application

Click **"Review"** to see full details

### Step 2: Verify Information

The Office Assistant has already verified, but you should:
- ✓ Confirm overall accuracy
- ✓ Check for any inconsistencies
- ✓ Verify attachments are legitimate
- ✓ Ensure data meets government standards

**Note:** You can see who verified it in the Office Assistant section.

### Step 3: Make Final Decision

You have TWO options:

---

## Option 1: APPROVE (Final) ✅

### When to Approve

- Office Assistant verification is solid
- All information appears accurate and complete
- Attachments are clear and authentic
- Record meets archival standards
- No major concerns

### What Happens

1. Click **"Final Approve & LOCK"**
2. Confirm in modal (read the warning!)
3. Status changes to **"Secretary Approved Final"**
4. **Record is LOCKED permanently**
5. Regular users can no longer edit
6. Only Super Admin can modify after this
7. Your name is logged as final approver

### ⚠️ Lock Warning

**Before clicking approve, confirm:**
- All data is accurate
- Attachments are legitimate
- You've reviewed thoroughly
- There are no missing details

**You cannot undo this yourself!**

---

## Option 2: REJECT ❌

### When to Reject

- Significant errors found
- Data doesn't meet standards
- Attachments are problematic
- Major inconsistencies discovered
- Additional verification needed

### How to Reject

1. Click **"Reject to Operator"**
2. **Write a detailed rejection reason**
3. Be specific about what's wrong
4. Confirm action

### After Rejection

- Application returns to **Data Entry Operator**
- Operator fixes issues
- **Important:** When resubmitted, goes DIRECTLY back to you
- **Skips Office Assistant on next submission**

---

## Writing Effective Rejection Reasons

### ❌ Bad Examples:
- "Not acceptable"
- "Needs fixing"
- "Incorrect data"

### ✅ Good Examples:
- "Certificate number format doesn't match government records. Should be ELC-2015-XXXX, not ELX-2015-XXXX. Please verify with original certificate."
- "NID number appears invalid - fails checksum validation. Operator should re-enter from original NID card."
- "Old certificate attachment is too blurry to verify authenticity. Need higher resolution scan or photo."
- "Work experience duration contradicts passing year. Applicant passed in 2018 but claims 10 years experience. Please verify and correct."

**Tip:** Your rejection reason should enable the operator to fix the exact issue without guessing.

---

## Bulk Operations

### Bulk Approve

**Use carefully!** Only for applications you're certain about.

1. Check boxes next to applications
2. Click **"Bulk Approve & LOCK"**
3. **Read the warning carefully**
4. Confirm

**All selected applications will be LOCKED.**

---

### Bulk Reject

1. Check boxes next to applications
2. Click **"Bulk Reject"**
3. Enter a common rejection reason
4. Confirm

**Good for common issues:**
- "Batch contains poor quality attachment scans across multiple applications. All need clearer scans."
- "Multiple applications show invalid NID format. All operators should re-verify NID numbers."

---

## Search and Filters

**Search by:**
- Certificate number
- Applicant name
- NID
- Mobile number

**Filter by date:**
- Submission date range
- Helps prioritize oldest submissions

---

## Workflow Path

### Normal Approval Path

```
DATA ENTRY OPERATOR
    ↓ Submit
OFFICE ASSISTANT
    ↓ Verify & Approve
YOU (Secretary)
    ↓ Final Approve
LOCKED ✅ PERMANENT RECORD
    ↓
CHAIRMAN can view (read-only)
```

### Rejection Paths

**If YOU reject:**
```
YOU (Reject)
    ↓
OPERATOR (Fix)
    ↓ Resubmit
YOU (Directly back to you, skips OA)
```

**If OA rejected previously:**
```
OPERATOR
    ↓ Fix & Resubmit
OFFICE ASSISTANT (Again)
    ↓ Re-verify
YOU
```

---

## Best Practices

### DO:
- ✅ Take your time - this is the final review
- ✅ Check attachment authenticity carefully
- ✅ Verify critical fields (NID, certificate numbers)
- ✅ Provide detailed rejection reasons
- ✅ Consider long-term archival value
- ✅ Review OA's verification notes (in audit trail)
- ✅ Process applications within 48-72 hours

### DON'T:
- ❌ Rush through applications
- ❌ Approve just because OA did
- ❌ Give vague rejection reasons
- ❌ Bulk approve without reviewing each
- ❌ Ignore your instincts if something seems off

---

## Special Scenarios

### Scenario 1: Conflicting Information

**Problem:** Father's name spelled differently in different fields

**Action:** Reject with specific correction:
"Father's name inconsistent. Field shows 'Abdul Rahman' but attachment shows 'Abdur Rahman'. Please verify original document and use consistent spelling throughout."

### Scenario 2: Suspicious Document

**Problem:** Attachment looks altered or fake

**Action:** Reject and escalate:
"Old certificate attachment appears to have been digitally altered (uneven resolution, visible editing artifacts). Please submit original physical certificate scan. Escalated to Chairman for review."

### Scenario 3: Borderline Case

**Problem:** Everything mostly fine but one minor issue

**Decision Tree:**
- **Minor typo** (e.g., "Enginer" instead of "Engineer") → Approve, note in comments
- **Data quality issue** (e.g., missing postcode) → Reject for completion
- **Unclear attachment** → Reject for better quality

---

## Understanding Audit Trail

Every application shows complete history:
- Who created it (Operator name)
- When OA verified it
- All previous rejections (if any)
- All data changes

**Use this to:**
- See if application was rejected before
- Check previous rejection reasons
- Identify repeat issues from same operator
- Verify workflow compliance

---

## Performance Expectations

**Processing Time:**
- ⏱️ Review each application: 10-15 minutes
- 🎯 Daily target: 15-20 applications
- 📅 Maximum turnaround: 72 hours

**Quality Metrics:**
- ✅ Approval rate: 80-90% (if much lower, OA needs guidance)
- 🔄 Re-rejection rate: <5% (measure of rejection quality)

---

## Communication

### With Office Assistant

- Review their verification notes in audit trail
- If OA approval rate is low, provide feedback
- Coordinate on standards and policies

### With Operators

- Your rejection reasons train operators
- Clear feedback reduces repeat mistakes
- Consistent standards improve data quality

### With Chairman

- Chairman can view all approved records
- Escalate suspicious cases
- Report on workflow issues

---

## After You Approve

1. Record status becomes "Secretary Approved Final"
2. Record is LOCKED 🔒
3. Application leaves your queue
4. Chairman can view it (read-only)
5. Super Admin can still edit (with override reason)
6. Your approval is logged permanently

---

## Handling Backlogs

If queue is backed up:

1. **Prioritize** oldest submissions first
2. **Focus** on quality over speed
3. **Communicate** delays to stakeholders
4. **Request help** if overwhelmed
5. **Never** rush to clear queue

**Remember:** Better to process 10 correctly than 50 incorrectly.

---

## Red Flags to Watch For

🚩 Multiple applications from same operator with similar errors
🚩 Unusually perfect data (might be fabricated)
🚩 Attachments that look identical across applications
🚩 Certificate numbers that don't follow known patterns
🚩 NID numbers that fail validation
🚩 Work experience that seems implausible

**If you see red flags:** Reject and escalate to Chairman or Super Admin.

---

## Emergency Procedures

### Urgent Approval Needed

1. Chairman can request expedited review
2. Review thoroughly anyway (don't skip steps)
3. Note urgency in comments
4. Log reason for expedited processing

### System Issues

If technical problems:
- Contact IT Support
- Don't approve without reviewing
- Document the issue
- Inform Super Admin

---

## Your Authority

As Secretary, you have:
- ✅ Final approval authority
- ✅ Ability to reject any application
- ✅ Power to set quality standards
- ❌ Cannot edit locked records (only Super Admin)
- ❌ Cannot see drafts or OA-rejected records

---

## Success Criteria

**You're doing well if:**
- Applications are processed within 72 hours
- Rejection reasons are clear and actionable
- Low rate of re-rejections
- Chairman reviews show high quality
- Operators improve over time (fewer errors)

---

## Need Help?

**Contact:**
- Office Assistant for coordination
- Chairman for policy questions
- Super Admin for technical issues
- IT Support for system problems

---

**Remember:** Your signature (approval) makes this record official government documentation. Take that responsibility seriously. Every approval should pass the test: "Would I defend this record in an audit?"

---

**Role Summary:**
- **Authority:** Final approval
- **Responsibility:** Quality assurance
- **Impact:** Permanent government archive
- **Accountability:** Fully logged and auditable

You are the guardian of data integrity. 🛡️

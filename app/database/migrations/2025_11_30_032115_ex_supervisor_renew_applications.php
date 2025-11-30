<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ex_supervisor_renew_applications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();

            // Domain Fields
            $table->string('applicant_name_bn', 255)->nullable();
            $table->string('old_certificate_number', 100)->unique();
            $table->string('mobile_no', 255)->nullable();
            $table->string('result', 15)->nullable();
            $table->string('applicant_name_en', 255)->nullable();
            $table->string('father_name', 255)->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('date_of_birth', 255)->nullable();
            $table->string('nid_number', 255)->nullable();
            $table->text('village')->nullable();
            $table->unsignedInteger('postcode')->nullable();
            $table->string('post_office', 255)->nullable();
            $table->string('upazilla', 255)->nullable();
            $table->string('district', 255)->nullable();
            $table->string('division', 255)->nullable();
            $table->string('degree', 255)->nullable();
            $table->string('subject', 255)->nullable();
            $table->string('board', 255)->nullable();
            $table->string('academic_result', 255)->nullable();
            $table->string('passing_year', 255)->nullable();
            $table->string('company', 255)->nullable();
            $table->string('designation', 255)->nullable();
            $table->string('total_job_duration', 255)->nullable();
            $table->string('certificate_number', 255)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedInteger('renewal_period')->nullable();
            $table->date('last_renewal_date')->nullable();

            // Workflow & Audit (Changed foreignId -> unsignedBigInteger to prevent auto-naming error)
            $table->unsignedBigInteger('entry_by')->nullable();
            $table->timestamp('entry_at')->nullable();

            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->timestamp('last_updated_at')->nullable();

            $table->string('status')->default('draft');

            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('reject_reason')->nullable();

            $table->unsignedBigInteger('verified_by_office_assistant')->nullable();
            $table->timestamp('verified_at_office_assistant')->nullable();

            $table->unsignedBigInteger('approved_by_secretary')->nullable();
            $table->timestamp('approved_at_secretary')->nullable();

            // Legacy Fields
            $table->string('application_status', 255)->default('pending');
            $table->string('renewal_status', 255)->default('pending');
            $table->string('engagement_status_with_contractor', 20)->nullable();
            $table->unsignedInteger('contractor_id')->nullable();
            $table->string('inspector_approval', 255)->nullable();
            $table->string('secretary_approval', 255)->nullable();
            $table->string('chairman_approval', 255)->nullable();
            $table->string('application_created_by', 100)->nullable();

            $table->softDeletes();
            $table->timestamps();

            // --- FIXED INDEXES (Custom Short Names) ---
            $table->index('status');
            $table->index('entry_by');

            // Fix 1: Short name for verifying officer index
            $table->index('verified_by_office_assistant', 'idx_exsup_verified_oa');

            // Fix 2: Short name for secretary index
            $table->index('approved_by_secretary', 'idx_exsup_approved_sec');

            // Fix 3: Short name for compound index
            $table->index(['entry_at', 'last_updated_at'], 'idx_exsup_entry_upd');

            $table->index('mobile_no');
            $table->index('applicant_name_en');
            $table->index('district');
            $table->index('contractor_id');
        });

        // --- FIXED FOREIGN KEYS (Custom Short Names) ---
        Schema::table('ex_supervisor_renew_applications', function (Blueprint $table) {

            $table->foreign('entry_by', 'fk_exsup_entry')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('last_updated_by', 'fk_exsup_upd')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('rejected_by', 'fk_exsup_rej')
                ->references('id')->on('users')->onDelete('set null');

            // Fix 4: Short name for verifying officer FK
            $table->foreign('verified_by_office_assistant', 'fk_exsup_oa')
                ->references('id')->on('users')->onDelete('set null');

            // Fix 5: Short name for secretary FK
            $table->foreign('approved_by_secretary', 'fk_exsup_sec')
                ->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ex_supervisor_renew_applications');
    }
};

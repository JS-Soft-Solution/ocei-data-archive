<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ex_electrician_renew_applications', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();

            // Domain Fields
            $table->string('applicant_name_bn')->nullable();
            $table->string('old_certificate_number', 100)->unique();
            $table->string('mobile_no')->nullable();
            $table->string('result', 15)->nullable();
            $table->string('applicant_name_en')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('email')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nid_number')->nullable();
            $table->text('village')->nullable();
            $table->unsignedInteger('postcode')->nullable();
            $table->string('post_office')->nullable();
            $table->string('upazilla')->nullable();
            $table->string('district')->nullable();
            $table->string('division')->nullable();
            $table->string('degree')->nullable();
            $table->string('subject')->nullable();
            $table->string('board')->nullable();
            $table->string('academic_result')->nullable();
            $table->string('passing_year')->nullable();
            $table->string('company')->nullable();
            $table->string('designation')->nullable();
            $table->string('total_job_duration')->nullable();
            $table->string('certificate_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->unsignedInteger('renewal_period')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('last_renewal_date')->nullable();

            // Workflow & Audit
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

            // Legacy
            $table->string('application_status')->default('pending');
            $table->string('renewal_status')->default('not initiated');
            $table->string('inspector_approval')->nullable();
            $table->string('secretary_approval')->nullable();
            $table->string('chairman_approval')->nullable();
            $table->string('application_created_by', 100)->nullable();

            $table->softDeletes();
            $table->timestamps();

            // --- FIXED INDEXES BELOW ---

            // Standard indexes
            $table->index('status');
            $table->index('entry_by');

            // Fix 1: Manually name this index to keep it under 64 chars
            $table->index('verified_by_office_assistant', 'idx_exer_verified_oa');

            // Fix 2: Also shortened this to be safe (it was borderline/too long)
            $table->index('approved_by_secretary', 'idx_exer_approved_sec');

            // Fix 3: Compound index name was 65 chars, renamed to shorten
            $table->index(['entry_at', 'last_updated_at'], 'idx_exer_entry_last_upd');

            $table->index('mobile_no');
            $table->index('applicant_name_en');
            $table->index('district');
        });

        // Foreign Keys — Short Names (These were already correct in your code)
        Schema::table('ex_electrician_renew_applications', function (Blueprint $table) {

            $table->foreign('entry_by', 'fk_exer_entry')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('last_updated_by', 'fk_exer_lastupd')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('rejected_by', 'fk_exer_reject')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('verified_by_office_assistant', 'fk_exer_oa')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('approved_by_secretary', 'fk_exer_sec')
                ->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ex_electrician_renew_applications');
    }
};

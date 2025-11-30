<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ex_supervisor_renew_applications', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('old_certificate_number', 100)->unique();
            $table->string('applicant_name_bn')->nullable();
            $table->string('applicant_name_en')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('nid_number')->nullable();

            // Address Information
            $table->text('village')->nullable();
            $table->string('post_office')->nullable();
            $table->integer('postcode')->nullable();
            $table->string('upazilla')->nullable();
            $table->string('district')->nullable();
            $table->string('division')->nullable();

            // Education Information
            $table->string('degree')->nullable();
            $table->string('subject')->nullable();
            $table->string('board')->nullable();
            $table->string('academic_result')->nullable();
            $table->string('passing_year')->nullable();

            // Work Experience
            $table->string('company')->nullable();
            $table->string('designation')->nullable();
            $table->string('total_job_duration')->nullable();

            // Contractor Engagement
            $table->string('engagement_status_with_contractor', 20)->nullable();
            $table->integer('contractor_id')->nullable();

            // Certificate Details
            $table->string('certificate_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('renewal_period')->nullable();
            $table->date('last_renewal_date')->nullable();
            $table->string('result', 15)->nullable();

            // Workflow Columns
            $table->string('status')->default('draft')->comment('draft, submitted_to_office_assistant, office_assistant_rejected, submitted_to_secretary, secretary_rejected, secretary_approved_final');

            // Entry tracking
            $table->unsignedBigInteger('entry_by')->nullable();
            $table->timestamp('entry_at')->nullable();

            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->timestamp('last_updated_at')->nullable();

            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->unsignedBigInteger('verified_by_office_assistant')->nullable();
            $table->timestamp('verified_at_office_assistant')->nullable();

            // Secretary approval
            $table->unsignedBigInteger('approved_by_secretary')->nullable();
            $table->timestamp('approved_at_secretary')->nullable();

            // Soft deletes
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();

            $table->timestamps();

            // Indexes for performance
            $table->index('status');
            $table->index(['status', 'entry_by']);
            $table->index('entry_at');
            $table->index('created_at');
            $table->index('old_certificate_number');
            $table->index('nid_number');
            $table->index('mobile_no');

            // Foreign keys with short names (MySQL 64-char limit)
            $table->foreign('entry_by', 'ex_super_entry_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('last_updated_by', 'ex_super_upd_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by', 'ex_super_rej_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('verified_by_office_assistant', 'ex_super_oa_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by_secretary', 'ex_super_sec_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by', 'ex_super_del_fk')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ex_supervisor_renew_applications');
    }
};

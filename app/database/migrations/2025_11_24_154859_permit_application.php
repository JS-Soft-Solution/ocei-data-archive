<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Electrician Permits
        Schema::create('electrician_applications', function (Blueprint $table) {
            $table->id();
            // Core Data from your SQL
            $table->string('old_certificate_number', 100)->unique();
            $table->string('applicant_name_bn')->nullable();
            $table->string('applicant_name_en')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nid_number')->nullable();
            $table->text('village')->nullable();
            $table->integer('postcode')->nullable();
            $table->string('post_office')->nullable();
            $table->string('upazilla')->nullable();
            $table->string('district')->nullable();
            $table->string('division')->nullable();

            // Profession Info
            $table->string('result', 15)->nullable();
            $table->string('degree')->nullable();
            $table->string('subject')->nullable();
            $table->string('board')->nullable();
            $table->string('academic_result')->nullable();
            $table->string('passing_year')->nullable();
            $table->string('company')->nullable();
            $table->string('designation')->nullable();
            $table->string('total_job_duration')->nullable();

            // Certificate Info
            $table->string('certificate_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('last_renewal_date')->nullable();
            $table->integer('renewal_period')->nullable();

            // Workflow & Tracking (Standard across all 3 tables)
            $this->addWorkflowColumns($table);
        });

        // 2. Supervisor Permits
        Schema::create('supervisor_applications', function (Blueprint $table) {
            $table->id();
            $table->string('old_certificate_number', 100)->unique();
            $table->string('applicant_name_bn')->nullable();
            $table->string('applicant_name_en')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('date_of_birth')->nullable(); // Kept as string per your SQL, preferably date
            $table->string('nid_number')->nullable();
            // ... Address fields (same as above) ...
            $table->text('village')->nullable();
            $table->integer('postcode')->nullable();
            $table->string('post_office')->nullable();
            $table->string('upazilla')->nullable();
            $table->string('district')->nullable();
            $table->string('division')->nullable();

            // Specific Supervisor Fields
            $table->string('engagement_status_with_contractor', 20)->nullable();
            $table->integer('contractor_id')->nullable();

            // Profession Info
            $table->string('result', 15)->nullable();
            $table->string('degree')->nullable();
            $table->string('subject')->nullable();
            $table->string('board')->nullable();
            $table->string('academic_result')->nullable();
            $table->string('passing_year')->nullable();
            $table->string('company')->nullable();
            $table->string('designation')->nullable();
            $table->string('total_job_duration')->nullable();

            // Certificate Info
            $table->string('certificate_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('last_renewal_date')->nullable();
            $table->integer('renewal_period')->nullable();

            $this->addWorkflowColumns($table);
        });

        // 3. Contractor Permits
        Schema::create('contractor_applications', function (Blueprint $table) {
            $table->id();
            $table->string('old_certificate_number', 100)->unique();
            $table->string('company_name_bn')->nullable();
            $table->string('company_name_en')->nullable();
            $table->string('owner_shareholder_name')->nullable();
            $table->string('elb_certified_supervisor_no')->nullable();
            $table->string('company_type')->nullable();
            $table->string('representative_name')->nullable();
            $table->string('representative_designation')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->string('result', 15)->nullable();

            // Business Address (BOA)
            $table->string('boa_village', 150)->nullable();
            $table->string('boa_road', 150)->nullable();
            $table->string('boa_post_office')->nullable();
            $table->string('boa_division', 150)->nullable();
            $table->string('boa_district', 150)->nullable();
            $table->string('boa_upozila', 150)->nullable();
            $table->integer('boa_post_code')->nullable();

            // Branch Address (BRA)
            $table->string('bra_village', 150)->nullable();
            $table->string('bra_road', 150)->nullable();
            $table->string('bra_post_office')->nullable();
            $table->string('bra_division', 150)->nullable();
            $table->string('bra_district', 150)->nullable();
            $table->string('bra_upozila', 150)->nullable();
            $table->integer('bra_post_code')->nullable();

            // Company Info
            $table->string('company_registration_number')->nullable();
            $table->string('trade_license_number')->nullable();

            // Equipment Info (ET, MG, CM series)
            $table->string('et_serial_no')->nullable();
            $table->string('et_manufacturer_name')->nullable();
            $table->string('et_country_origin')->nullable();
            $table->string('mg_serial_no')->nullable();
            $table->string('mg_manufacturer_name')->nullable();
            $table->string('mg_country_origin')->nullable();
            $table->string('cm_serial_no')->nullable();
            $table->string('cm_manufacturer_name')->nullable();
            $table->string('cm_country_origin')->nullable();

            // Certificate Info
            $table->string('certificate_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('last_renewal_date')->nullable();
            $table->integer('renewal_period')->nullable();
            $table->integer('supervisor_acknowledgement')->nullable();

            $this->addWorkflowColumns($table);
        });
    }

    /**
     * Helper to add common workflow columns to all tables.
     */
    private function addWorkflowColumns(Blueprint $table)
    {
        // Status
        $table->string('application_status')->default('draft'); // draft, submitted, verified, approved, rejected
        $table->string('renewal_status')->default('not initiated');

        // Approvals (Names/Signatures if needed, but ID linking is better for audit)
        $table->string('inspector_approval')->nullable();
        $table->string('secretary_approval')->nullable();
        $table->string('chairman_approval')->nullable();

        // Files
        $table->text('archive_file')->nullable();

        // Tracking
        $table->foreignId('entry_by')->nullable()->constrained('users'); // Operator
        $table->timestamp('entry_at')->nullable();
        $table->foreignId('last_update_by')->nullable()->constrained('users');

        $table->foreignId('verified_by')->nullable()->constrained('users'); // Office Assistant
        $table->timestamp('verified_at')->nullable();

        $table->foreignId('approved_by')->nullable()->constrained('users'); // Secretary
        $table->timestamp('approved_at')->nullable();

        $table->foreignId('rejected_by')->nullable()->constrained('users');
        $table->text('reject_reason')->nullable();
        $table->timestamp('rejected_at')->nullable();

        $table->softDeletes(); // deleted_by, deleted_at handled by SoftDeletes trait + custom column if needed
        $table->string('deleted_by')->nullable();
        $table->timestamps();
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_applications');
        Schema::dropIfExists('supervisor_applications');
        Schema::dropIfExists('electrician_applications');
    }
};

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
        Schema::create('ex_contractor_renew_applications', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('old_certificate_number', 100)->unique();
            $table->string('company_name_bn')->nullable();
            $table->string('company_name_en')->nullable();
            $table->string('owner_shareholder_name')->nullable();
            $table->string('company_type')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();

            // Representative Information
            $table->string('representative_name')->nullable();
            $table->string('representative_designation')->nullable();

            // Supervisor Information
            $table->string('elb_certified_supervisor_no')->nullable();
            $table->integer('supervisor_acknowledgement')->nullable();

            // Business Office Address (BOA)
            $table->string('boa_village', 150)->nullable();
            $table->string('boa_road', 150)->nullable();
            $table->string('boa_post_office')->nullable();
            $table->integer('boa_post_code')->nullable();
            $table->string('boa_upozila', 150)->nullable();
            $table->string('boa_district', 150)->nullable();
            $table->string('boa_division', 150)->nullable();

            // Business Residential Address (BRA)
            $table->string('bra_village', 150)->nullable();
            $table->string('bra_road', 150)->nullable();
            $table->string('bra_post_office')->nullable();
            $table->integer('bra_post_code')->nullable();
            $table->string('bra_upozila', 150)->nullable();
            $table->string('bra_district', 150)->nullable();
            $table->string('bra_division', 150)->nullable();

            // Registration Information
            $table->string('company_registration_number')->nullable();
            $table->string('trade_license_number')->nullable();

            // Legacy address fields (for compatibility)
            $table->text('village')->nullable();
            $table->string('post_office')->nullable();
            $table->integer('postcode')->nullable();
            $table->string('upazilla')->nullable();
            $table->string('district')->nullable();
            $table->string('division')->nullable();
            $table->string('degree')->nullable();

            // Equipment Testing (ET)
            $table->string('et_serial_no')->nullable();
            $table->string('et_manufacturer_name')->nullable();
            $table->string('et_country_origin')->nullable();

            // Megger (MG)
            $table->string('mg_serial_no')->nullable();
            $table->string('mg_manufacturer_name')->nullable();
            $table->string('mg_country_origin')->nullable();

            // Clamp Meter (CM)
            $table->string('cm_serial_no')->nullable();
            $table->string('cm_manufacturer_name')->nullable();
            $table->string('cm_country_origin')->nullable();

            // Certificate Details
            $table->string('certificate_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('renewal_period')->nullable();
            $table->date('last_renewal_date')->nullable();
            $table->string('result', 15)->nullable();

            // Workflow Columns
            $table->string('status')->default('draft')->comment('draft, submitted_to_office_assistant, office_assistant_rejected, submitted_to_secretary, secretary_rejected, secretary_approved_final');

            // Foreign keys with custom short names            // Entry tracking
            $table->unsignedBigInteger('entry_by')->nullable();
            $table->timestamp('entry_at')->nullable();

            // Last update tracking
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->timestamp('last_updated_at')->nullable();

            // Rejection tracking
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('reject_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // Office Assistant verification
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
            $table->index('mobile_no');
            $table->index('company_name_en');

            // Foreign keys with short names (MySQL 64-char limit)
            $table->foreign('entry_by', 'ex_contr_entry_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('last_updated_by', 'ex_contr_upd_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by', 'ex_contr_rej_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('verified_by_office_assistant', 'ex_contr_oa_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by_secretary', 'ex_contr_sec_fk')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by', 'ex_contr_del_fk')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ex_contractor_renew_applications');
    }
};

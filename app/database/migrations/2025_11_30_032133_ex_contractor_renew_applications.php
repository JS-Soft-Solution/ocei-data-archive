<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ex_contractor_renew_applications', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();

            // Domain Fields
            $table->string('old_certificate_number', 100)->unique();
            $table->string('mobile_no', 255)->nullable();
            $table->string('result', 15)->nullable();
            $table->string('company_name_bn', 255)->nullable();
            $table->string('company_name_en', 255)->nullable();
            $table->string('owner_shareholder_name', 255)->nullable();
            $table->string('elb_certified_supervisor_no', 255)->nullable();
            $table->string('company_type', 255)->nullable();
            $table->string('representative_name', 255)->nullable();
            $table->string('representative_designation', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('boa_village', 150)->nullable();
            $table->string('boa_road', 150)->nullable();
            $table->string('boa_post_office', 255)->nullable();
            $table->string('boa_division', 150)->nullable();
            $table->string('boa_district', 150)->nullable();
            $table->string('boa_upozila', 150)->nullable();
            $table->unsignedInteger('boa_post_code')->nullable();
            $table->string('bra_village', 150)->nullable();
            $table->string('bra_road', 150)->nullable();
            $table->string('bra_post_office', 255)->nullable();
            $table->string('bra_division', 150)->nullable();
            $table->string('bra_district', 150)->nullable();
            $table->string('bra_upozila', 150)->nullable();
            $table->unsignedInteger('bra_post_code')->nullable();
            $table->string('company_registration_number', 255)->nullable();
            $table->string('trade_license_number', 255)->nullable();
            $table->text('village')->nullable();
            $table->unsignedInteger('postcode')->nullable();
            $table->string('post_office', 255)->nullable();
            $table->string('upazilla', 255)->nullable();
            $table->string('district', 255)->nullable();
            $table->string('division', 255)->nullable();
            $table->string('degree', 255)->nullable();
            $table->string('et_serial_no', 255)->nullable();
            $table->string('et_manufacturer_name', 255)->nullable();
            $table->string('et_country_origin', 255)->nullable();
            $table->string('mg_serial_no', 255)->nullable();
            $table->string('mg_manufacturer_name', 255)->nullable();
            $table->string('mg_country_origin', 255)->nullable();
            $table->string('cm_serial_no', 255)->nullable();
            $table->string('cm_manufacturer_name', 255)->nullable();
            $table->string('cm_country_origin', 255)->nullable();
            $table->string('certificate_number', 255)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('last_renewal_date')->nullable();

            // Workflow & Audit (Changed foreignId -> unsignedBigInteger)
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
            $table->unsignedInteger('renewal_period')->nullable();
            $table->unsignedInteger('supervisor_acknowledgement')->nullable();
            $table->string('inspector_approval', 255)->nullable();
            $table->string('secretary_approval', 255)->nullable();
            $table->string('chairman_approval', 255)->nullable();

            $table->softDeletes();
            $table->timestamps();

            // --- FIXED INDEXES (Custom Short Names) ---
            $table->index('status');
            $table->index('entry_by');

            // Fix 1: Short name for verifying officer index
            $table->index('verified_by_office_assistant', 'idx_excon_verified_oa');

            // Fix 2: Short name for secretary index
            $table->index('approved_by_secretary', 'idx_excon_approved_sec');

            // Fix 3: Short name for compound index
            $table->index(['entry_at', 'last_updated_at'], 'idx_excon_entry_upd');

            $table->index('mobile_no');
            $table->index('company_name_en');
            $table->index('district');
        });

        // --- FIXED FOREIGN KEYS (Custom Short Names) ---
        Schema::table('ex_contractor_renew_applications', function (Blueprint $table) {

            $table->foreign('entry_by', 'fk_excon_entry')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('last_updated_by', 'fk_excon_upd')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('rejected_by', 'fk_excon_rej')
                ->references('id')->on('users')->onDelete('set null');

            // Fix 4: Short name for verifying officer FK
            $table->foreign('verified_by_office_assistant', 'fk_excon_oa')
                ->references('id')->on('users')->onDelete('set null');

            // Fix 5: Short name for secretary FK
            $table->foreign('approved_by_secretary', 'fk_excon_sec')
                ->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ex_contractor_renew_applications');
    }
};

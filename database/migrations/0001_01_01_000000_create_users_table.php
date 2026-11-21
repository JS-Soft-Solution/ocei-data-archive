<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Engine & charset (good defaults for modern MySQL / large data)
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Primary key
            $table->bigIncrements('id');

            // Core identity / auth fields
            $table->string('user_id', 100)->nullable();                // external/legacy ID
            $table->string('otp', 100)->nullable();
            $table->string('otp_status', 100)->default('unverified');

            $table->string('full_name', 100)->nullable();
            $table->string('business_organization_name', 255)->nullable();
            $table->string('full_name_bn', 100)->nullable();

            $table->string('father_name', 100)->nullable();
            $table->string('spouse_name', 100)->nullable();
            $table->string('father_name_bn', 100)->nullable();
            $table->string('spouse_name_bn', 100)->nullable();

            $table->string('mother_name', 100)->nullable();
            $table->string('mother_name_bn', 100)->nullable();

            $table->string('nid_no', 20)->nullable();

            $table->date('dob')->nullable();

            $table->string('email', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('password', 255); // keep as-is for compatibility

            $table->string('mobile_no', 18)->nullable();
            $table->string('gender', 255)->nullable();

            // Permanent address
            $table->string('per_village', 150)->nullable();
            $table->string('per_road', 150)->nullable();
            $table->string('per_division', 150)->nullable();
            $table->string('per_district', 150)->nullable();
            $table->string('per_upozila', 150)->nullable();
            $table->integer('per_post_code')->nullable();
            $table->string('per_post_office', 255)->nullable();

            // Present address
            $table->string('pre_village', 150)->nullable();
            $table->string('pre_road', 150)->nullable();
            $table->string('pre_division', 150)->nullable();
            $table->string('pre_district', 150)->nullable();
            $table->string('pre_upozila', 150)->nullable();
            $table->integer('pre_post_code')->nullable();
            $table->string('pre_post_office', 255)->nullable();

            // Documents / media
            $table->text('nid_image')->nullable();          // consider storing path, not raw blob
            $table->text('applicant_image')->nullable();    // same comment
            $table->string('applicant_sign', 255)->nullable();

            // Admin / roles
            $table->string('admin_type', 255)->nullable();
            $table->string('admin_designation', 255)->nullable();
            $table->string('admin_designation_bn', 255)->nullable();

            // Tokens & temp auth
            $table->rememberToken(); // varchar(100) nullable
            $table->string('temporary_pass', 255)->nullable();
            $table->integer('otp_password')->nullable();
            $table->string('temp_password', 255)->nullable();

            // Error tracking
            $table->string('mobile_no_err_letter', 255)->nullable();
            $table->string('email_err_letter', 255)->nullable();

            // Soft deletes + timestamps
            $table->softDeletes();      // deleted_at
            $table->timestamps();       // created_at, updated_at

            /*
             * Indexing strategy for large datasets
             * -----------------------------------
             * - Frequently filtered fields get indexes
             * - Composite indexes for common query patterns
             */

            // Likely lookups
            $table->index('user_id');
            $table->index('email');
            $table->index('mobile_no');
            $table->index('nid_no');

            // OTP flow optimization (e.g. WHERE mobile_no = ? AND otp_status = 'unverified')
            $table->index(['mobile_no', 'otp_status']);

            // Admin / role-based queries
            $table->index('admin_type');

            // Soft delete & time-based queries
            $table->index('deleted_at');
            $table->index('created_at');

            $table->index('full_name');
            $table->index('full_name_bn');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

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
        Schema::table('ex_electrician_renew_applications', function (Blueprint $table) {
            $table->string('class')->nullable()->after('old_certificate_number');
            $table->string('book_number')->nullable()->after('class');
        });

        Schema::table('ex_supervisor_renew_applications', function (Blueprint $table) {
            $table->string('class')->nullable()->after('old_certificate_number');
            $table->string('book_number')->nullable()->after('class');
        });

        Schema::table('ex_contractor_renew_applications', function (Blueprint $table) {
            $table->string('class')->nullable()->after('old_certificate_number');
            $table->string('book_number')->nullable()->after('class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ex_electrician_renew_applications', function (Blueprint $table) {
            $table->dropColumn(['class', 'book_number']);
        });

        Schema::table('ex_supervisor_renew_applications', function (Blueprint $table) {
            $table->dropColumn(['class', 'book_number']);
        });

        Schema::table('ex_contractor_renew_applications', function (Blueprint $table) {
            $table->dropColumn(['class', 'book_number']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * High-performance composite & lookup indexes for massive archive datasets.
     */
    public function up(): void
    {
        Schema::table('ex_electrician_renew_applications', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_elec_status_created');
            $table->index(['status', 'updated_at'], 'idx_elec_status_updated');
            if (Schema::hasColumn('ex_electrician_renew_applications', 'class')) {
                $table->index('class', 'idx_elec_class');
            }
            if (Schema::hasColumn('ex_electrician_renew_applications', 'book_number')) {
                $table->index('book_number', 'idx_elec_book_number');
            }
            $table->index('applicant_name_en', 'idx_elec_name_en');
        });

        Schema::table('ex_supervisor_renew_applications', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_sup_status_created');
            $table->index(['status', 'updated_at'], 'idx_sup_status_updated');
            if (Schema::hasColumn('ex_supervisor_renew_applications', 'class')) {
                $table->index('class', 'idx_sup_class');
            }
            if (Schema::hasColumn('ex_supervisor_renew_applications', 'book_number')) {
                $table->index('book_number', 'idx_sup_book_number');
            }
            $table->index('applicant_name_en', 'idx_sup_name_en');
        });

        Schema::table('ex_contractor_renew_applications', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'idx_cont_status_created');
            $table->index(['status', 'updated_at'], 'idx_cont_status_updated');
            if (Schema::hasColumn('ex_contractor_renew_applications', 'class')) {
                $table->index('class', 'idx_cont_class');
            }
            if (Schema::hasColumn('ex_contractor_renew_applications', 'book_number')) {
                $table->index('book_number', 'idx_cont_book_number');
            }
            $table->index('company_name_en', 'idx_cont_company_en');
            $table->index('trade_license_number', 'idx_cont_trade_lic');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['user_id', 'read_at', 'created_at'], 'idx_notif_user_read_created');
        });

        Schema::table('record_histories', function (Blueprint $table) {
            $table->index(['action', 'performed_at'], 'idx_hist_action_performed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ex_electrician_renew_applications', function (Blueprint $table) {
            $table->dropIndex('idx_elec_status_created');
            $table->dropIndex('idx_elec_status_updated');
            if (Schema::hasColumn('ex_electrician_renew_applications', 'class')) {
                $table->dropIndex('idx_elec_class');
            }
            if (Schema::hasColumn('ex_electrician_renew_applications', 'book_number')) {
                $table->dropIndex('idx_elec_book_number');
            }
            $table->dropIndex('idx_elec_name_en');
        });

        Schema::table('ex_supervisor_renew_applications', function (Blueprint $table) {
            $table->dropIndex('idx_sup_status_created');
            $table->dropIndex('idx_sup_status_updated');
            if (Schema::hasColumn('ex_supervisor_renew_applications', 'class')) {
                $table->dropIndex('idx_sup_class');
            }
            if (Schema::hasColumn('ex_supervisor_renew_applications', 'book_number')) {
                $table->dropIndex('idx_sup_book_number');
            }
            $table->dropIndex('idx_sup_name_en');
        });

        Schema::table('ex_contractor_renew_applications', function (Blueprint $table) {
            $table->dropIndex('idx_cont_status_created');
            $table->dropIndex('idx_cont_status_updated');
            if (Schema::hasColumn('ex_contractor_renew_applications', 'class')) {
                $table->dropIndex('idx_cont_class');
            }
            if (Schema::hasColumn('ex_contractor_renew_applications', 'book_number')) {
                $table->dropIndex('idx_cont_book_number');
            }
            $table->dropIndex('idx_cont_company_en');
            $table->dropIndex('idx_cont_trade_lic');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notif_user_read_created');
        });

        Schema::table('record_histories', function (Blueprint $table) {
            $table->dropIndex('idx_hist_action_performed');
        });
    }
};

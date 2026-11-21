<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * High-speed composite indexes for Secretary approved and rejected queues.
     */
    public function up(): void
    {
        Schema::table('ex_electrician_renew_applications', function (Blueprint $table) {
            $table->index(['status', 'approved_at_secretary'], 'idx_elec_status_approved');
            $table->index(['status', 'rejected_at'], 'idx_elec_status_rejected');
        });

        Schema::table('ex_supervisor_renew_applications', function (Blueprint $table) {
            $table->index(['status', 'approved_at_secretary'], 'idx_sup_status_approved');
            $table->index(['status', 'rejected_at'], 'idx_sup_status_rejected');
        });

        Schema::table('ex_contractor_renew_applications', function (Blueprint $table) {
            $table->index(['status', 'approved_at_secretary'], 'idx_cont_status_approved');
            $table->index(['status', 'rejected_at'], 'idx_cont_status_rejected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ex_electrician_renew_applications', function (Blueprint $table) {
            $table->dropIndex('idx_elec_status_approved');
            $table->dropIndex('idx_elec_status_rejected');
        });

        Schema::table('ex_supervisor_renew_applications', function (Blueprint $table) {
            $table->dropIndex('idx_sup_status_approved');
            $table->dropIndex('idx_sup_status_rejected');
        });

        Schema::table('ex_contractor_renew_applications', function (Blueprint $table) {
            $table->dropIndex('idx_cont_status_approved');
            $table->dropIndex('idx_cont_status_rejected');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds patient_id to the local users table.
     * This stores the AHCS patient ID (ahcs_patients.id on the external DB)
     * so a portal user account can be linked to their AHCS patient record.
     *
     * NOTE: No foreign key constraint is added here because the referenced
     * table lives on a different database server (the AHCS system).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')
                  ->nullable()
                  ->after('id')
                  ->comment('AHCS patient ID — references ahcs_patients.id on the external AHCS database');

            // Index for fast lookups by patient_id
            $table->index('patient_id', 'users_patient_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_patient_id_index');
            $table->dropColumn('patient_id');
        });
    }
};

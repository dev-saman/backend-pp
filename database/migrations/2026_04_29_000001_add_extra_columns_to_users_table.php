<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add new columns from the patient-portal users table.
     * Existing columns (id, patient_id, name, email, role, phone,
     * is_active, last_login_at, email_verified_at, password,
     * remember_token, created_at, updated_at) are NOT touched.
     *
     * Column name differences (keeping OUR names):
     *   their `type`          = our `role`       → no change
     *   their `active_status` = our `is_active`  → no change
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable()->after('id');
            $table->string('avatar')->default('uploads/avatar/avatar.png')->after('remember_token');
            $table->string('address')->nullable()->after('avatar');
            $table->string('country')->nullable()->after('address');
            $table->string('messenger_color')->default('#2180f3')->after('country');
            $table->string('country_code')->nullable()->after('messenger_color');
            $table->timestamp('phone_verified_at')->nullable()->after('country_code');
            $table->string('UniqueId')->nullable()->after('phone_verified_at');
            $table->boolean('dark_mode')->default(false)->after('UniqueId');
            $table->string('lang')->nullable()->after('dark_mode');
            $table->string('social_type')->nullable()->after('lang');
            $table->softDeletes()->after('updated_at'); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'address',
                'country',
                'messenger_color',
                'country_code',
                'phone_verified_at',
                'UniqueId',
                'dark_mode',
                'lang',
                'social_type',
                'deleted_at',
            ]);
        });
    }
};

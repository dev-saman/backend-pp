<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            // slug already exists in the live DB — only add if missing
            if (!Schema::hasColumn('forms', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('description');
            }

            // Email / branding columns
            if (!Schema::hasColumn('forms', 'logo')) {
                $table->string('logo')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('forms', 'bccemail')) {
                $table->string('bccemail')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('forms', 'email')) {
                $table->string('email')->nullable()->after('bccemail');
            }
            if (!Schema::hasColumn('forms', 'ccemail')) {
                $table->string('ccemail')->nullable()->after('email');
            }

            // Message columns
            if (!Schema::hasColumn('forms', 'success_msg')) {
                $table->text('success_msg')->nullable()->after('ccemail');
            }
            if (!Schema::hasColumn('forms', 'thanks_msg')) {
                $table->text('thanks_msg')->nullable()->after('success_msg');
            }

            // Payment columns
            if (!Schema::hasColumn('forms', 'amount')) {
                $table->double('amount', 8, 2)->nullable()->after('thanks_msg');
            }
            if (!Schema::hasColumn('forms', 'currency_symbol')) {
                $table->string('currency_symbol')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('forms', 'currency_name')) {
                $table->string('currency_name')->nullable()->after('currency_symbol');
            }

            // Flags
            if (!Schema::hasColumn('forms', 'is_active')) {
                $table->integer('is_active')->default(1)->after('currency_name');
            }
            if (!Schema::hasColumn('forms', 'allow_share_section')) {
                $table->bigInteger('allow_share_section')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('forms', 'allow_comments')) {
                $table->bigInteger('allow_comments')->nullable()->after('allow_share_section');
            }
            if (!Schema::hasColumn('forms', 'payment_status')) {
                $table->tinyInteger('payment_status')->default(0)->after('allow_comments');
            }
            if (!Schema::hasColumn('forms', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('forms', 'assign_type')) {
                $table->string('assign_type')->nullable()->after('payment_type');
            }

            // HTML output column
            if (!Schema::hasColumn('forms', 'html')) {
                $table->text('html')->nullable()->after('assign_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $columns = [
                'logo', 'bccemail', 'email', 'ccemail',
                'success_msg', 'thanks_msg',
                'amount', 'currency_symbol', 'currency_name',
                'is_active', 'allow_share_section', 'allow_comments',
                'payment_status', 'payment_type', 'assign_type',
                'html',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('forms', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender_name')->nullable()->after('admin_id');
            $table->string('sender_type')->nullable()->default('admin')->after('sender_name');
            $table->boolean('is_read')->default(false)->after('has_attachment');
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('messages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['sender_name', 'sender_type', 'is_read', 'parent_id']);
        });
    }
};

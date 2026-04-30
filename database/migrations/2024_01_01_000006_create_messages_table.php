<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject');
            $table->text('body');
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            $table->enum('status', ['unread', 'read', 'replied', 'archived'])->default('unread');
            $table->string('category')->nullable()->comment('clinical, billing, administrative');
            $table->boolean('has_attachment')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

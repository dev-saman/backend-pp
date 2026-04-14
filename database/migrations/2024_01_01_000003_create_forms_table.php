<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('fields')->nullable()->comment('Form fields configuration (JSON)');
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->string('category')->nullable()->comment('e.g., intake, consent, follow-up');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('submission_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};

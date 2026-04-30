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
        Schema::create('funnel_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('patient_funnel_assignments')->onDelete('cascade');
            $table->foreignId('funnel_id')->constrained('funnels')->onDelete('cascade');
            $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
            $table->unsignedSmallInteger('step_index')->default(0);
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->json('data')->nullable();
            $table->timestamp('last_saved_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['assignment_id', 'form_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funnel_progress');
    }
};

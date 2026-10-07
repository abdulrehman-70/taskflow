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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignId('task_id')
                ->nullable()
                ->constrained('tasks')
                ->cascadeOnDelete();

            $table->string('action', 100);
            $table->text('description');
            $table->json('metadata')->nullable();


            $table->index('project_id');
            $table->index('task_id');
            $table->index('user_id');
            $table->index('action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

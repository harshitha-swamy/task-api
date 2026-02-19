<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])
                  ->default('pending');
            $table->foreignId('assigned_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('status');
            $table->index('due_date');
            $table->index('assigned_user_id');
            $table->index('created_by');
            $table->index(['status', 'assigned_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
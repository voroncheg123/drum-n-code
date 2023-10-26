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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key.
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Foreign key to users table.
            $table->unsignedBigInteger('parent_id')->nullable(); // For sub-tasks.
            $table->enum('status', ['todo', 'done']);
            $table->integer('priority')->unsigned()->default(1); // Values between 1 and 5.
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('completedAt')->nullable();

            // Indexes
            $table->index('parent_id');
            $table->index(['status', 'priority', 'createdAt', 'completedAt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

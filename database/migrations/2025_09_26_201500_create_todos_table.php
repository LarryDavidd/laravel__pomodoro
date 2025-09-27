<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_todos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_todo')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->integer('pomodoro_value');
            $table->timestamp('time_create');
            $table->boolean('is_complete')->default(false);
            $table->enum('priority', ['No priority', 'Low priority', 'Medium priority', 'High priority'])->default('No priority');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
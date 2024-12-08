<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Create the 'tasks' table with specified schema
        Schema::create('tasks', function (Blueprint $table) {
            // Primary key 'id' field auto-incremented
            $table->id();

            // 'title' column to store the title of the task
            $table->string('content', 255); // Ganti dari 'content' menjadi 'title'

            // 'info_file' column to store information about attached files (nullable)
            $table->string('info_file')->nullable();

            // 'is_completed' column to track whether the task is completed or not (default: 0)
            $table->unsignedTinyInteger('is_completed')->default(0);

            // 'created_at' and 'updated_at' columns to store timestamps of creation and updates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop the 'tasks' table if it exists
        Schema::dropIfExists('tasks');
    }
};

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
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 100)->unique();
            $table->text('content');
            $table->enum('status', ['to-do', 'in-progress', 'done', 'deleted']);
            $table->string('image_path')->nullable();
            $table->boolean('is_draft')->default(false);
            $table->timestamps();
            $table->softDeletes(); // For trashing           
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

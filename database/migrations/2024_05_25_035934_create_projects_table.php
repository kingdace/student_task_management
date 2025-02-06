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
        Schema::defaultStringLength(191);
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('startDate');
            $table->string('endDate');
            $table->string('status');
            $table->string('slug');
            $table->boolean('is_archived')->default(false); // This will be used to soft delete projects
            $table->timestamp('archived_at')->nullable(); // Add timestamp for when project was archived
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::defaultStringLength(191);
        Schema::dropIfExists('projects');
    }
};

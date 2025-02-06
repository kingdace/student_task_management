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
        Schema::create('task_progress', function (Blueprint $table) {
            $table->id();
            $table->integer('projectId');
            $table->integer('pinned_on_dashbaord');
            $table->string('progress');
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
        Schema::dropIfExists('task_progress');
    }
};

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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('projectId');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('status');
            $table->date('due_date')->nullable();
            $table->integer('priority')->default(0);
            $table->integer('progress')->default(0);
            $table->timestamps();
        });

        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->integer('taskId');
            $table->integer('memberId');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // Schema::create('task_comments', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('taskId');
        //     $table->unsignedBigInteger('userId')->nullable();
        //     $table->unsignedBigInteger('memberId')->nullable();
        //     $table->text('comment');
        //     $table->timestamps();

        //     $table->foreign('taskId')->references('id')->on('tasks')->onDelete('cascade');
        //     $table->foreign('userId')->references('id')->on('users')->onDelete('set null');
        //     $table->foreign('memberId')->references('id')->on('members')->onDelete('set null');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::defaultStringLength(191);
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('task_attachments');
        // Schema::dropIfExists('task_comments');
    }
};

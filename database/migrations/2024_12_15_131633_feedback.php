// database/migrations/2024_05_25_create_task_comments_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('taskId');
            $table->unsignedBigInteger('userId')->nullable();
            $table->unsignedBigInteger('memberId')->nullable();
            $table->text('comment');
            $table->timestamps();

            $table->foreign('taskId')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('userId')->references('id')->on('users')->onDelete('set null');
            $table->foreign('memberId')->references('id')->on('members')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_comments');
    }
};

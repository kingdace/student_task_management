<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_documents', function (Blueprint $table) {
            $table->id();
            $table->integer('taskId');
            $table->integer('memberId')->nullable();
            $table->integer('userId')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_type');  // 'document' or 'link'
            $table->string('document_type')->nullable(); // 'pdf', 'doc', 'xls', etc.
            $table->text('link_url')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_documents');
    }
};

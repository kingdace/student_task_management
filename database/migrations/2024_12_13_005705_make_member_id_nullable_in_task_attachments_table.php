<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeMemberIdNullableInTaskAttachmentsTable extends Migration
{
    public function up()
    {
        Schema::table('task_attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('memberId')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('task_attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('memberId')->nullable(false)->change();
        });
    }
};

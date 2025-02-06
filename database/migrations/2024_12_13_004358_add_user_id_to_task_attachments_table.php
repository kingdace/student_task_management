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
    Schema::table('task_attachments', function (Blueprint $table) {
        $table->unsignedBigInteger('userId')->nullable();
        $table->foreign('userId')->references('id')->on('users');
    });
}

public function down()
{
    Schema::table('task_attachments', function (Blueprint $table) {
        $table->dropForeign(['userId']);
        $table->dropColumn('userId');
    });
}
};

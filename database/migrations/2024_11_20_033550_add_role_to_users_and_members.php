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
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('administrator'); // For Users
    });

    Schema::table('members', function (Blueprint $table) {
        $table->string('role')->default('member'); // For Members
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });

    Schema::table('members', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
};

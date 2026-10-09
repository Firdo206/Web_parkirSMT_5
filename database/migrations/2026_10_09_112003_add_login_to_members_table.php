<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // nullable: member lama (tanpa akun) tidak error; kata sandinya diatur lewat "reset kata sandi"
            $table->string('password')->nullable();
            $table->boolean('must_change_password')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['password', 'must_change_password']);
        });
    }
};
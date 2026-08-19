<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_branches', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');
        });
    }

    public function down(): void
    {
        Schema::table('user_branches', function (Blueprint $table) {
            $table->string('id', 36)->primary()->first();
        });
    }
};

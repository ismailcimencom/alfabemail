<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siniflar', function (Blueprint $table) {
            $table->string('durum', 20)->default('aktif')->after('ad');
        });
    }

    public function down(): void
    {
        Schema::table('siniflar', function (Blueprint $table) {
            $table->dropColumn('durum');
        });
    }
};

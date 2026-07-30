<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ogrenciler', function (Blueprint $table) {
            $table->string('anne_telefon', 20)->nullable()->after('anne_email');
            $table->string('baba_telefon', 20)->nullable()->after('baba_email');
        });
    }

    public function down(): void
    {
        Schema::table('ogrenciler', function (Blueprint $table) {
            $table->dropColumn(['anne_telefon', 'baba_telefon']);
        });
    }
};

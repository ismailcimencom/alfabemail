<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('haftalik_programlar', function (Blueprint $table) {
            $table->dropForeign(['okul_id']);
            $table->dropColumn('okul_id');
        });

        Schema::table('siniflar', function (Blueprint $table) {
            $table->dropForeign(['okul_id']);
            $table->dropColumn('okul_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['okul_id']);
            $table->dropColumn('okul_id');
        });

        Schema::dropIfExists('okullar');
    }

    public function down(): void
    {
        Schema::create('okullar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('yonetici_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ad');
            $table->string('adres')->nullable();
            $table->string('telefon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('yonetici_ad_soyad')->nullable();
            $table->string('yonetici_email')->nullable();
            $table->string('ulke')->nullable();
            $table->string('sehir')->nullable();
            $table->string('ilce')->nullable();
            $table->string('mahalle')->nullable();
            $table->string('durum')->default('beklemede');
            $table->string('red_nedeni')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('okul_id')->nullable()->after('is_active')->constrained('okullar')->nullOnDelete();
        });

        Schema::table('siniflar', function (Blueprint $table) {
            $table->foreignId('okul_id')->nullable()->after('id')->constrained('okullar')->cascadeOnDelete();
        });

        Schema::table('haftalik_programlar', function (Blueprint $table) {
            $table->foreignId('okul_id')->nullable()->after('id')->constrained('okullar')->cascadeOnDelete();
        });
    }
};

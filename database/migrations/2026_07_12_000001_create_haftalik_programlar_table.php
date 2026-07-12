<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('haftalik_programlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okul_id')->constrained('okullar')->onDelete('cascade');
            $table->foreignId('sinif_id')->nullable()->constrained('siniflar')->onDelete('cascade');
            $table->enum('gun', ['pazartesi', 'sali', 'carsamba', 'persembe', 'cuma']);
            $table->time('baslangic');
            $table->time('bitis');
            $table->string('ders_adi');
            $table->foreignId('ogretmen_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('haftalik_programlar');
    }
};

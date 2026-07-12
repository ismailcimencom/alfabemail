<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bekleyen_takvim_etkinlikleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ogrenci_id')->constrained('ogrenciler')->onDelete('cascade');
            $table->foreignId('odev_id')->constrained('odevler')->onDelete('cascade');
            $table->string('baslik');
            $table->text('aciklama')->nullable();
            $table->date('teslim_tarihi');
            $table->boolean('eklendi_mi')->default(false);
            $table->boolean('hata_mi')->default(false);
            $table->text('hata_mesaji')->nullable();
            $table->timestamps();

            $table->unique(['ogrenci_id', 'odev_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bekleyen_takvim_etkinlikleri');
    }
};

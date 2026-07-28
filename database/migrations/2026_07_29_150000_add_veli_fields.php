<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('veliler', function (Blueprint $table) {
            $table->string('veli_type')->nullable()->after('user_id');
            $table->string('child_email')->nullable()->after('veli_type');
        });

        Schema::table('pending_users', function (Blueprint $table) {
            $table->string('veli_type')->nullable()->after('school');
            $table->string('child_email')->nullable()->after('veli_type');
        });
    }

    public function down(): void
    {
        Schema::table('veliler', function (Blueprint $table) {
            $table->dropColumn(['veli_type', 'child_email']);
        });

        Schema::table('pending_users', function (Blueprint $table) {
            $table->dropColumn(['veli_type', 'child_email']);
        });
    }
};

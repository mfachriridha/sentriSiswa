<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biodata_siswa', function (Blueprint $table) {
            $table->unique('profil_siswa_id');
        });
    }

    public function down(): void
    {
        Schema::table('biodata_siswa', function (Blueprint $table) {
            $table->dropUnique(['profil_siswa_id']);
        });
    }
};

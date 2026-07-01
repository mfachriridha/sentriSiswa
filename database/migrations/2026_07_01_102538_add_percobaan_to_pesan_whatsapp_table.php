<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesan_whatsapp', function (Blueprint $table) {
            $table->unsignedTinyInteger('percobaan')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('pesan_whatsapp', function (Blueprint $table) {
            $table->dropColumn('percobaan');
        });
    }
};

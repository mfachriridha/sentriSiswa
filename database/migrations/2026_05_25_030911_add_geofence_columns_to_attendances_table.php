<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('selfie_path');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->float('accuracy')->nullable()->after('longitude');
            $table->float('distance_meters')->nullable()->after('accuracy');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'accuracy', 'distance_meters']);
        });
    }
};

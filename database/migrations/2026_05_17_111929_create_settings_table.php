<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $defaults = [
            ['key' => 'jam_masuk', 'value' => '07:00'],
            ['key' => 'jam_pulang', 'value' => '15:00'],
            ['key' => 'batas_telat', 'value' => '15'],
        ];

        foreach ($defaults as $d) {
            DB::table('settings')->insertOrIgnore($d);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

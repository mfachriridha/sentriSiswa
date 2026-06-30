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
        Schema::create('token_akses_absensi', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->date('tanggal');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->unique(['class_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_akses_absensi');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tata_tertib', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('file');
            $table->boolean('diterbitkan')->default(false)->index();
            $table->foreignId('diunggah_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tata_tertib');
    }
};

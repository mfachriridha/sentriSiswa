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
        Schema::create('poin_pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['ringan', 'sedang', 'berat', 'amat_berat']);
            $table->string('jenis_pelanggaran', 255);
            $table->integer('poin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poin_pelanggaran');
    }
};

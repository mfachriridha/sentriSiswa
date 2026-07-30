<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Melebarkan NISN jadi 12 karakter, dari sebelumnya 10.
 *
 * NISN adalah kunci utama profil_siswa dan dirujuk tiga tabel lain, jadi kolom
 * perujuknya harus dilebarkan berbarengan - MySQL menolak kunci asing yang tipe
 * kolomnya tidak sama persis dengan kolom yang dirujuk. Urutannya: kunci asing
 * dilepas, semua kolom dilebarkan, lalu kunci asingnya dipasang kembali beserta
 * aturan hapus yang semula.
 */
return new class extends Migration
{
    private const LEBAR_BARU = 12;

    private const LEBAR_LAMA = 10;

    public function up(): void
    {
        $this->ubahLebar(self::LEBAR_BARU);
    }

    public function down(): void
    {
        // Menyempitkan kembali hanya berhasil kalau belum ada NISN yang lebih
        // panjang dari 10 karakter. Kalau sudah ada, MySQL menolak - dan itu
        // memang yang diinginkan, daripada memotong NISN siswa tanpa peringatan.
        $this->ubahLebar(self::LEBAR_LAMA);
    }

    private function ubahLebar(int $lebar): void
    {
        // SQLite tidak pernah memaksakan panjang VARCHAR, jadi di sana tidak ada
        // yang perlu diubah. Membongkar-pasang tabel berkunci utama beserta kunci
        // asingnya justru menambah risiko tanpa manfaat.
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $perujuk = DB::select("
            SELECT k.TABLE_NAME AS tabel, k.COLUMN_NAME AS kolom, k.CONSTRAINT_NAME AS nama_kunci,
                   r.DELETE_RULE AS aturan_hapus
            FROM information_schema.KEY_COLUMN_USAGE k
            JOIN information_schema.REFERENTIAL_CONSTRAINTS r
                ON r.CONSTRAINT_SCHEMA = k.TABLE_SCHEMA
                AND r.CONSTRAINT_NAME = k.CONSTRAINT_NAME
            WHERE k.TABLE_SCHEMA = DATABASE()
              AND k.REFERENCED_TABLE_NAME = 'profil_siswa'
              AND k.REFERENCED_COLUMN_NAME = 'nisn'
        ");

        foreach ($perujuk as $fk) {
            DB::statement("ALTER TABLE `{$fk->tabel}` DROP FOREIGN KEY `{$fk->nama_kunci}`");
        }

        foreach ($perujuk as $fk) {
            DB::statement("ALTER TABLE `{$fk->tabel}` MODIFY `{$fk->kolom}` VARCHAR({$lebar}) NOT NULL");
        }

        DB::statement("ALTER TABLE `profil_siswa` MODIFY `nisn` VARCHAR({$lebar}) NOT NULL");

        foreach ($perujuk as $fk) {
            DB::statement(
                "ALTER TABLE `{$fk->tabel}` ADD CONSTRAINT `{$fk->nama_kunci}` ".
                "FOREIGN KEY (`{$fk->kolom}`) REFERENCES `profil_siswa` (`nisn`) ".
                "ON DELETE {$fk->aturan_hapus}"
            );
        }
    }
};

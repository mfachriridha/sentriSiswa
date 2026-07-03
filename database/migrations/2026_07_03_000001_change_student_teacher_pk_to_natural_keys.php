<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->assertNoBlankNaturalKeys();

        $mysql = DB::getDriverName() === 'mysql';

        if ($mysql) {
            $this->upMysql();
        } else {
            $this->upSqlite();
        }
    }

    public function down(): void
    {
        $mysql = DB::getDriverName() === 'mysql';

        if ($mysql) {
            $this->downMysql();
        } else {
            $this->downSqlite();
        }
    }

    /**
     * Refuse to migrate if any existing row is missing the natural key
     * it's about to become (part of) the primary key. These must be
     * backfilled by an admin first — they are data gaps, not a normal state.
     */
    protected function assertNoBlankNaturalKeys(): void
    {
        $badStudents = DB::table('profil_siswa')
            ->where(function ($q) {
                $q->whereNull('nisn')->orWhere('nisn', '')
                    ->orWhereNull('nis')->orWhere('nis', '');
            })
            ->join('pengguna', 'pengguna.id', '=', 'profil_siswa.pengguna_id')
            ->pluck('pengguna.nama');

        if ($badStudents->isNotEmpty()) {
            throw new \RuntimeException(
                'Tidak bisa migrasi: siswa berikut belum punya NISN/NIS lengkap — '
                .'lengkapi dulu lewat form edit siswa sebelum migrate: '
                .$badStudents->implode(', ')
            );
        }

        $badTeachers = DB::table('profil_guru')
            ->where(function ($q) {
                $q->whereNull('nip')->orWhere('nip', '');
            })
            ->join('pengguna', 'pengguna.id', '=', 'profil_guru.pengguna_id')
            ->pluck('pengguna.nama');

        if ($badTeachers->isNotEmpty()) {
            throw new \RuntimeException(
                'Tidak bisa migrasi: guru berikut belum punya NIP — '
                .'lengkapi dulu (boleh kode internal kalau honorer) sebelum migrate: '
                .$badTeachers->implode(', ')
            );
        }
    }

    protected function upMysql(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // profil_siswa: add unique(nis), retype 3 dependent FKs to varchar(10), swap PK to nisn.
        DB::statement('ALTER TABLE `profil_siswa` ADD UNIQUE KEY `profil_siswa_nis_unique` (`nis`)');

        foreach (['biodata_siswa', 'absensi', 'pelanggaran_siswa'] as $table) {
            $fk = DB::selectOne("
                SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = 'profil_siswa_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table]);

            if ($fk) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }

            DB::statement("ALTER TABLE `{$table}` ADD COLUMN `profil_siswa_nisn_tmp` VARCHAR(10) NULL AFTER `profil_siswa_id`");
            DB::statement("UPDATE `{$table}` t JOIN `profil_siswa` p ON t.profil_siswa_id = p.id SET t.profil_siswa_nisn_tmp = p.nisn");
            DB::statement("ALTER TABLE `{$table}` DROP COLUMN `profil_siswa_id`");
            DB::statement("ALTER TABLE `{$table}` CHANGE COLUMN `profil_siswa_nisn_tmp` `profil_siswa_id` VARCHAR(10) NOT NULL");
            DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$table}_profil_siswa_id_foreign` FOREIGN KEY (`profil_siswa_id`) REFERENCES `profil_siswa` (`nisn`) ON DELETE CASCADE");
        }

        DB::statement('ALTER TABLE `profil_siswa` MODIFY `id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `profil_siswa` DROP PRIMARY KEY');
        DB::statement('ALTER TABLE `profil_siswa` MODIFY `nisn` VARCHAR(10) NOT NULL');
        DB::statement('ALTER TABLE `profil_siswa` ADD PRIMARY KEY (`nisn`)');
        DB::statement('ALTER TABLE `profil_siswa` DROP COLUMN `id`');

        // profil_guru: zero dependents, just swap PK to nip.
        DB::statement('ALTER TABLE `profil_guru` MODIFY `id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `profil_guru` DROP PRIMARY KEY');
        DB::statement('ALTER TABLE `profil_guru` MODIFY `nip` VARCHAR(30) NOT NULL');
        DB::statement('ALTER TABLE `profil_guru` ADD PRIMARY KEY (`nip`)');
        DB::statement('ALTER TABLE `profil_guru` DROP COLUMN `id`');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function downMysql(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::statement('ALTER TABLE `profil_guru` ADD COLUMN `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST');

        DB::statement('ALTER TABLE `profil_siswa` DROP PRIMARY KEY');
        DB::statement('ALTER TABLE `profil_siswa` ADD COLUMN `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST');
        DB::statement('ALTER TABLE `profil_siswa` MODIFY `nisn` VARCHAR(10) NULL');
        DB::statement('ALTER TABLE `profil_siswa` ADD UNIQUE KEY `profil_siswa_nisn_unique` (`nisn`)');
        DB::statement('ALTER TABLE `profil_siswa` DROP INDEX `profil_siswa_nis_unique`');

        foreach (['biodata_siswa', 'absensi', 'pelanggaran_siswa'] as $table) {
            $fk = DB::selectOne("
                SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = 'profil_siswa_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table]);

            if ($fk) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            }

            DB::statement("ALTER TABLE `{$table}` ADD COLUMN `profil_siswa_id_tmp` BIGINT UNSIGNED NULL AFTER `profil_siswa_id`");
            DB::statement("UPDATE `{$table}` t JOIN `profil_siswa` p ON t.profil_siswa_id = p.nisn SET t.profil_siswa_id_tmp = p.id");
            DB::statement("ALTER TABLE `{$table}` DROP COLUMN `profil_siswa_id`");
            DB::statement("ALTER TABLE `{$table}` CHANGE COLUMN `profil_siswa_id_tmp` `profil_siswa_id` BIGINT UNSIGNED NOT NULL");
            DB::statement("ALTER TABLE `{$table}` ADD CONSTRAINT `{$table}_profil_siswa_id_foreign` FOREIGN KEY (`profil_siswa_id`) REFERENCES `profil_siswa` (`id`) ON DELETE CASCADE");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function upSqlite(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::table('profil_siswa', function (Blueprint $table) {
            $table->unique('nis');
        });

        // profil_siswa itself must be rebuilt last since it hosts the new PK,
        // but dependent tables need to reference it (via unique nisn) first —
        // the existing unique(nisn) on the not-yet-rebuilt table already satisfies that.
        $this->rebuildSqliteDependent('biodata_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('profil_siswa_id', 10);
            $table->foreign('profil_siswa_id')->references('nisn')->on('profil_siswa')->cascadeOnDelete();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama', 30)->nullable();
            $table->string('status_keluarga', 50)->nullable();
            $table->unsignedSmallInteger('anak_ke')->nullable();
            $table->string('asal_sekolah', 150)->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->string('nama_ayah', 100)->nullable();
            $table->string('pekerjaan_ayah', 100)->nullable();
            $table->string('nama_ibu', 100)->nullable();
            $table->string('pekerjaan_ibu', 100)->nullable();
            $table->text('alamat_ortu')->nullable();
            $table->string('telepon_ortu', 20)->nullable();
            $table->string('nama_wali', 100)->nullable();
            $table->string('pekerjaan_wali', 100)->nullable();
            $table->text('alamat_wali')->nullable();
            $table->string('telepon_wali', 20)->nullable();
            $table->timestamps();
        });

        $this->rebuildSqliteDependent('absensi', function (Blueprint $table) {
            $table->id();
            $table->string('profil_siswa_id', 10);
            $table->foreign('profil_siswa_id')->references('nisn')->on('profil_siswa')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha', 'belum_absen'])->default('belum_absen');
            $table->time('waktu_masuk')->nullable();
            $table->string('path_selfie')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->float('akurasi')->nullable();
            $table->float('jarak_meter')->nullable();
            $table->timestamps();
            $table->unique(['profil_siswa_id', 'tanggal']);
        });

        $this->rebuildSqliteDependent('pelanggaran_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('profil_siswa_id', 10);
            $table->foreign('profil_siswa_id')->references('nisn')->on('profil_siswa')->cascadeOnDelete();
            $table->foreignId('jenis_pelanggaran_id')->nullable()->constrained('jenis_pelanggaran')->nullOnDelete();
            $table->foreignId('dicatat_oleh_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->date('tanggal_pelanggaran')->index();
            $table->string('nama_pelanggaran', 150);
            $table->enum('kategori_pelanggaran', ['light', 'medium', 'heavy', 'severe'])->index();
            $table->unsignedTinyInteger('pengurangan_poin');
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->index();
            $table->foreignId('disetujui_oleh_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();

            $table->index(['profil_siswa_id', 'tanggal_pelanggaran']);
            $table->index(['jenis_pelanggaran_id', 'tanggal_pelanggaran']);
            $table->index(['kategori_pelanggaran', 'tanggal_pelanggaran']);
        });

        // Now rebuild profil_siswa itself with nisn as the primary key.
        Schema::create('profil_siswa_new', function (Blueprint $table) {
            $table->string('nisn', 10)->primary();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('nis', 20)->unique();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto', 300)->nullable();
            $table->timestamps();
        });
        DB::statement('INSERT INTO profil_siswa_new (nisn, pengguna_id, nis, kelas_id, telepon, alamat, foto, created_at, updated_at)
            SELECT nisn, pengguna_id, nis, kelas_id, telepon, alamat, foto, created_at, updated_at FROM profil_siswa');
        Schema::drop('profil_siswa');
        Schema::rename('profil_siswa_new', 'profil_siswa');

        // profil_guru: zero dependents, rebuild directly with nip as PK.
        Schema::create('profil_guru_new', function (Blueprint $table) {
            $table->string('nip', 30)->primary();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('telepon', 20)->nullable();
            $table->enum('tipe_guru', ['homeroom', 'counselor', 'student_affairs'])->nullable();
            $table->enum('tingkat', ['10', '11', '12'])->nullable();
            $table->string('foto', 300)->nullable();
            $table->timestamps();
        });
        DB::statement('INSERT INTO profil_guru_new (nip, pengguna_id, telepon, tipe_guru, tingkat, foto, created_at, updated_at)
            SELECT nip, pengguna_id, telepon, tipe_guru, tingkat, foto, created_at, updated_at FROM profil_guru');
        Schema::drop('profil_guru');
        Schema::rename('profil_guru_new', 'profil_guru');

        DB::statement('PRAGMA foreign_keys=ON');
    }

    /**
     * Rebuild a dependent table with profil_siswa_id retyped to varchar(10),
     * backfilling values by joining the old (not-yet-rebuilt) profil_siswa table.
     */
    protected function rebuildSqliteDependent(string $table, \Closure $newSchema): void
    {
        $columns = collect(Schema::getColumnListing($table))
            ->reject(fn ($c) => $c === 'profil_siswa_id')
            ->values();

        Schema::create("{$table}_new", $newSchema);

        $selectCols = $columns->map(fn ($c) => "t.{$c}")->implode(', ');
        $insertCols = $columns->prepend('profil_siswa_id')->implode(', ');
        $selectColsFull = "p.nisn AS profil_siswa_id, {$selectCols}";

        DB::statement("INSERT INTO {$table}_new ({$insertCols})
            SELECT {$selectColsFull}
            FROM {$table} t
            JOIN profil_siswa p ON t.profil_siswa_id = p.id");

        Schema::drop($table);
        Schema::rename("{$table}_new", $table);
    }

    protected function downSqlite(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');

        Schema::create('profil_siswa_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('nisn', 10)->unique()->nullable();
            $table->string('nis', 20)->nullable();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto', 300)->nullable();
            $table->timestamps();
        });
        DB::statement('INSERT INTO profil_siswa_old (pengguna_id, nisn, nis, kelas_id, telepon, alamat, foto, created_at, updated_at)
            SELECT pengguna_id, nisn, nis, kelas_id, telepon, alamat, foto, created_at, updated_at FROM profil_siswa');

        foreach (['biodata_siswa', 'absensi', 'pelanggaran_siswa'] as $table) {
            $columns = collect(Schema::getColumnListing($table))
                ->reject(fn ($c) => $c === 'profil_siswa_id')
                ->values();

            $selectCols = $columns->map(fn ($c) => "t.{$c}")->implode(', ');
            $insertCols = $columns->prepend('profil_siswa_id')->implode(', ');

            DB::statement("CREATE TABLE {$table}_old AS SELECT o.id AS profil_siswa_id, {$selectCols}
                FROM {$table} t JOIN profil_siswa_old o ON t.profil_siswa_id = o.nisn LIMIT 0");

            DB::statement("INSERT INTO {$table}_old ({$insertCols})
                SELECT o.id, {$selectCols}
                FROM {$table} t JOIN profil_siswa_old o ON t.profil_siswa_id = o.nisn");

            Schema::drop($table);
            Schema::rename("{$table}_old", $table);
        }

        Schema::drop('profil_siswa');
        Schema::rename('profil_siswa_old', 'profil_siswa');

        Schema::create('profil_guru_old', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->constrained('pengguna')->cascadeOnDelete();
            $table->string('nip', 30)->unique()->nullable();
            $table->string('telepon', 20)->nullable();
            $table->enum('tipe_guru', ['homeroom', 'counselor', 'student_affairs'])->nullable();
            $table->enum('tingkat', ['10', '11', '12'])->nullable();
            $table->string('foto', 300)->nullable();
            $table->timestamps();
        });
        DB::statement('INSERT INTO profil_guru_old (pengguna_id, nip, telepon, tipe_guru, tingkat, foto, created_at, updated_at)
            SELECT pengguna_id, nip, telepon, tipe_guru, tingkat, foto, created_at, updated_at FROM profil_guru');
        Schema::drop('profil_guru');
        Schema::rename('profil_guru_old', 'profil_guru');

        DB::statement('PRAGMA foreign_keys=ON');
    }
};

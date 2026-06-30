<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $mysql = DB::getDriverName() === 'mysql';

        if ($mysql) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        // ── 1. Rename tables ─────────────────────────────────────────────
        Schema::rename('users', 'pengguna');
        Schema::rename('teacher_profiles', 'profil_guru');
        Schema::rename('student_profiles', 'profil_siswa');
        Schema::rename('classes', 'kelas');
        Schema::rename('attendances', 'absensi');
        Schema::rename('student_violations', 'pelanggaran_siswa');
        Schema::rename('violation_types', 'jenis_pelanggaran');
        Schema::rename('student_biodata', 'biodata_siswa');
        Schema::rename('school_rules', 'tata_tertib');
        Schema::rename('settings', 'pengaturan');
        Schema::rename('whatsapp_messages', 'pesan_whatsapp');
        Schema::rename('email_otp_tokens', 'token_otp');

        // ── 2. Rename + resize columns ───────────────────────────────────

        if ($mysql) {
            // pengguna
            DB::statement("ALTER TABLE `pengguna`
                CHANGE COLUMN `name`            `nama`      VARCHAR(100) NOT NULL,
                CHANGE COLUMN `role`            `peran`     ENUM('admin','siswa','wali_kelas','bk','kesiswaan') NOT NULL DEFAULT 'siswa',
                CHANGE COLUMN `photo`           `foto`      VARCHAR(300) NULL,
                CHANGE COLUMN `whatsapp_number` `nomor_wa`  VARCHAR(20) NULL,
                CHANGE COLUMN `google_id`       `id_google` VARCHAR(50) NULL
            ");

            // profil_guru
            DB::statement("ALTER TABLE `profil_guru`
                RENAME COLUMN `user_id`      TO `pengguna_id`,
                RENAME COLUMN `phone`        TO `telepon`,
                RENAME COLUMN `photo`        TO `foto`,
                RENAME COLUMN `teacher_type` TO `tipe_guru`,
                RENAME COLUMN `grade`        TO `tingkat`
            ");
            DB::statement("ALTER TABLE `profil_guru`
                MODIFY COLUMN `telepon` VARCHAR(20) NULL,
                MODIFY COLUMN `foto`    VARCHAR(300) NULL
            ");

            // profil_siswa
            DB::statement("ALTER TABLE `profil_siswa`
                RENAME COLUMN `user_id`  TO `pengguna_id`,
                RENAME COLUMN `class_id` TO `kelas_id`,
                RENAME COLUMN `phone`    TO `telepon`,
                RENAME COLUMN `address`  TO `alamat`,
                RENAME COLUMN `photo`    TO `foto`
            ");
            DB::statement("ALTER TABLE `profil_siswa`
                MODIFY COLUMN `telepon` VARCHAR(20) NULL,
                MODIFY COLUMN `foto`    VARCHAR(300) NULL
            ");

            // kelas
            DB::statement('ALTER TABLE `kelas` DROP INDEX `classes_name_grade_unique`');
            DB::statement("ALTER TABLE `kelas`
                RENAME COLUMN `name`                TO `nama`,
                RENAME COLUMN `grade`               TO `tingkat`,
                RENAME COLUMN `homeroom_teacher_id` TO `wali_kelas_id`,
                RENAME COLUMN `student_count`       TO `jumlah_siswa`
            ");
            DB::statement("ALTER TABLE `kelas`
                MODIFY COLUMN `nama`         VARCHAR(20) NOT NULL,
                MODIFY COLUMN `jumlah_siswa` INT UNSIGNED NOT NULL DEFAULT 0
            ");
            DB::statement('ALTER TABLE `kelas` ADD UNIQUE KEY `kelas_nama_tingkat_unique` (`nama`, `tingkat`)');

            // absensi
            DB::statement("ALTER TABLE `absensi`
                RENAME COLUMN `student_profile_id` TO `profil_siswa_id`,
                RENAME COLUMN `date`               TO `tanggal`,
                RENAME COLUMN `check_in_time`      TO `waktu_masuk`,
                RENAME COLUMN `selfie_path`        TO `path_selfie`,
                RENAME COLUMN `accuracy`           TO `akurasi`,
                RENAME COLUMN `distance_meters`    TO `jarak_meter`
            ");
            DB::statement('ALTER TABLE `absensi` RENAME INDEX `attendances_student_profile_id_date_unique` TO `absensi_profil_siswa_id_tanggal_unique`');

            // pelanggaran_siswa
            DB::statement("ALTER TABLE `pelanggaran_siswa`
                RENAME COLUMN `student_profile_id`  TO `profil_siswa_id`,
                RENAME COLUMN `violation_type_id`   TO `jenis_pelanggaran_id`,
                RENAME COLUMN `recorded_by_user_id` TO `dicatat_oleh_id`,
                RENAME COLUMN `approved_by_user_id` TO `disetujui_oleh_id`,
                RENAME COLUMN `violation_date`      TO `tanggal_pelanggaran`,
                RENAME COLUMN `violation_name`      TO `nama_pelanggaran`,
                RENAME COLUMN `violation_category`  TO `kategori_pelanggaran`,
                RENAME COLUMN `point_deduction`     TO `pengurangan_poin`,
                RENAME COLUMN `notes`               TO `catatan`,
                RENAME COLUMN `approved_at`         TO `disetujui_pada`,
                RENAME COLUMN `rejection_reason`    TO `alasan_penolakan`
            ");
            DB::statement("ALTER TABLE `pelanggaran_siswa`
                MODIFY COLUMN `nama_pelanggaran` VARCHAR(150) NOT NULL
            ");

            // jenis_pelanggaran
            DB::statement("ALTER TABLE `jenis_pelanggaran`
                CHANGE COLUMN `name`            `nama`             VARCHAR(100) NOT NULL,
                CHANGE COLUMN `category`        `kategori`         ENUM('light','medium','heavy','severe') NOT NULL,
                CHANGE COLUMN `point_deduction` `pengurangan_poin` TINYINT UNSIGNED NOT NULL,
                CHANGE COLUMN `description`     `keterangan`       TEXT NULL,
                CHANGE COLUMN `is_active`       `aktif`            TINYINT(1) NOT NULL DEFAULT 1
            ");

            // biodata_siswa
            DB::statement("ALTER TABLE `biodata_siswa`
                RENAME COLUMN `student_profile_id`  TO `profil_siswa_id`,
                RENAME COLUMN `place_of_birth`      TO `tempat_lahir`,
                RENAME COLUMN `date_of_birth`       TO `tanggal_lahir`,
                RENAME COLUMN `gender`              TO `jenis_kelamin`,
                RENAME COLUMN `religion`            TO `agama`,
                RENAME COLUMN `family_status`       TO `status_keluarga`,
                RENAME COLUMN `child_number`        TO `anak_ke`,
                RENAME COLUMN `school_of_origin`    TO `asal_sekolah`,
                RENAME COLUMN `admission_date`      TO `tanggal_masuk`,
                RENAME COLUMN `father_name`         TO `nama_ayah`,
                RENAME COLUMN `father_occupation`   TO `pekerjaan_ayah`,
                RENAME COLUMN `mother_name`         TO `nama_ibu`,
                RENAME COLUMN `mother_occupation`   TO `pekerjaan_ibu`,
                RENAME COLUMN `parent_address`      TO `alamat_ortu`,
                RENAME COLUMN `parent_phone`        TO `telepon_ortu`,
                RENAME COLUMN `guardian_name`       TO `nama_wali`,
                RENAME COLUMN `guardian_occupation` TO `pekerjaan_wali`,
                RENAME COLUMN `guardian_address`    TO `alamat_wali`,
                RENAME COLUMN `guardian_phone`      TO `telepon_wali`
            ");
            DB::statement("ALTER TABLE `biodata_siswa`
                MODIFY COLUMN `tempat_lahir`    VARCHAR(100) NULL,
                MODIFY COLUMN `agama`           VARCHAR(30) NULL,
                MODIFY COLUMN `status_keluarga` VARCHAR(50) NULL,
                MODIFY COLUMN `asal_sekolah`    VARCHAR(150) NULL,
                MODIFY COLUMN `nama_ayah`       VARCHAR(100) NULL,
                MODIFY COLUMN `pekerjaan_ayah`  VARCHAR(100) NULL,
                MODIFY COLUMN `nama_ibu`        VARCHAR(100) NULL,
                MODIFY COLUMN `pekerjaan_ibu`   VARCHAR(100) NULL,
                MODIFY COLUMN `telepon_ortu`    VARCHAR(20) NULL,
                MODIFY COLUMN `nama_wali`       VARCHAR(100) NULL,
                MODIFY COLUMN `pekerjaan_wali`  VARCHAR(100) NULL,
                MODIFY COLUMN `telepon_wali`    VARCHAR(20) NULL
            ");

            // tata_tertib
            DB::statement("ALTER TABLE `tata_tertib`
                RENAME COLUMN `title`               TO `judul`,
                RENAME COLUMN `file_path`           TO `path_file`,
                RENAME COLUMN `is_published`        TO `dipublikasikan`,
                RENAME COLUMN `uploaded_by_user_id` TO `diunggah_oleh_id`
            ");
            DB::statement("ALTER TABLE `tata_tertib`
                MODIFY COLUMN `judul`     VARCHAR(200) NOT NULL,
                MODIFY COLUMN `path_file` VARCHAR(300) NOT NULL
            ");

            // pengaturan
            DB::statement('ALTER TABLE `pengaturan` DROP INDEX `settings_key_unique`');
            DB::statement("ALTER TABLE `pengaturan`
                CHANGE COLUMN `key`   `kunci` VARCHAR(100) NOT NULL,
                CHANGE COLUMN `value` `nilai` TEXT NULL
            ");
            DB::statement('ALTER TABLE `pengaturan` ADD UNIQUE KEY `pengaturan_kunci_unique` (`kunci`)');

            // pesan_whatsapp
            DB::statement("ALTER TABLE `pesan_whatsapp`
                RENAME COLUMN `school_class_id`     TO `kelas_id`,
                RENAME COLUMN `recipient_phone`     TO `telepon_penerima`,
                RENAME COLUMN `recipient_name`      TO `nama_penerima`,
                RENAME COLUMN `message_type`        TO `tipe_pesan`,
                RENAME COLUMN `provider_message_id` TO `id_pesan_provider`,
                RENAME COLUMN `message`             TO `isi_pesan`,
                RENAME COLUMN `response`            TO `respons`,
                RENAME COLUMN `sent_at`             TO `dikirim_pada`
            ");
            DB::statement("ALTER TABLE `pesan_whatsapp`
                MODIFY COLUMN `telepon_penerima`  VARCHAR(20) NOT NULL,
                MODIFY COLUMN `nama_penerima`     VARCHAR(100) NULL,
                MODIFY COLUMN `tipe_pesan`        VARCHAR(30) NOT NULL DEFAULT 'attendance_report',
                MODIFY COLUMN `id_pesan_provider` VARCHAR(100) NULL
            ");

            // token_otp
            DB::statement("ALTER TABLE `token_otp`
                RENAME COLUMN `user_id`      TO `pengguna_id`,
                RENAME COLUMN `type`         TO `tipe`,
                RENAME COLUMN `new_email`    TO `email_baru`,
                RENAME COLUMN `new_password` TO `sandi_baru`,
                RENAME COLUMN `expires_at`   TO `kadaluwarsa_pada`,
                RENAME COLUMN `used_at`      TO `digunakan_pada`
            ");

            // token_akses_absensi
            DB::statement("ALTER TABLE `token_akses_absensi`
                RENAME COLUMN `class_id`   TO `kelas_id`,
                RENAME COLUMN `expires_at` TO `kadaluwarsa_pada`
            ");
            DB::statement('ALTER TABLE `token_akses_absensi` RENAME INDEX `token_akses_absensi_class_id_tanggal_unique` TO `token_akses_absensi_kelas_id_tanggal_unique`');

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } else {
            // SQLite: use Schema Builder (renameColumn uses DBAL / native ALTER TABLE)
            Schema::table('pengguna', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
                $table->renameColumn('role', 'peran');
                $table->renameColumn('photo', 'foto');
                $table->renameColumn('whatsapp_number', 'nomor_wa');
                $table->renameColumn('google_id', 'id_google');
            });

            Schema::table('profil_guru', function (Blueprint $table) {
                $table->renameColumn('user_id', 'pengguna_id');
                $table->renameColumn('phone', 'telepon');
                $table->renameColumn('photo', 'foto');
                $table->renameColumn('teacher_type', 'tipe_guru');
                $table->renameColumn('grade', 'tingkat');
            });

            Schema::table('profil_siswa', function (Blueprint $table) {
                $table->renameColumn('user_id', 'pengguna_id');
                $table->renameColumn('class_id', 'kelas_id');
                $table->renameColumn('phone', 'telepon');
                $table->renameColumn('address', 'alamat');
                $table->renameColumn('photo', 'foto');
            });

            Schema::table('kelas', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
                $table->renameColumn('grade', 'tingkat');
                $table->renameColumn('homeroom_teacher_id', 'wali_kelas_id');
                $table->renameColumn('student_count', 'jumlah_siswa');
            });

            Schema::table('absensi', function (Blueprint $table) {
                $table->renameColumn('student_profile_id', 'profil_siswa_id');
                $table->renameColumn('date', 'tanggal');
                $table->renameColumn('check_in_time', 'waktu_masuk');
                $table->renameColumn('selfie_path', 'path_selfie');
                $table->renameColumn('accuracy', 'akurasi');
                $table->renameColumn('distance_meters', 'jarak_meter');
            });

            Schema::table('pelanggaran_siswa', function (Blueprint $table) {
                $table->renameColumn('student_profile_id', 'profil_siswa_id');
                $table->renameColumn('violation_type_id', 'jenis_pelanggaran_id');
                $table->renameColumn('recorded_by_user_id', 'dicatat_oleh_id');
                $table->renameColumn('approved_by_user_id', 'disetujui_oleh_id');
                $table->renameColumn('violation_date', 'tanggal_pelanggaran');
                $table->renameColumn('violation_name', 'nama_pelanggaran');
                $table->renameColumn('violation_category', 'kategori_pelanggaran');
                $table->renameColumn('point_deduction', 'pengurangan_poin');
                $table->renameColumn('notes', 'catatan');
                $table->renameColumn('approved_at', 'disetujui_pada');
                $table->renameColumn('rejection_reason', 'alasan_penolakan');
            });

            Schema::table('jenis_pelanggaran', function (Blueprint $table) {
                $table->renameColumn('name', 'nama');
                $table->renameColumn('category', 'kategori');
                $table->renameColumn('point_deduction', 'pengurangan_poin');
                $table->renameColumn('description', 'keterangan');
                $table->renameColumn('is_active', 'aktif');
            });

            Schema::table('biodata_siswa', function (Blueprint $table) {
                $table->renameColumn('student_profile_id', 'profil_siswa_id');
                $table->renameColumn('place_of_birth', 'tempat_lahir');
                $table->renameColumn('date_of_birth', 'tanggal_lahir');
                $table->renameColumn('gender', 'jenis_kelamin');
                $table->renameColumn('religion', 'agama');
                $table->renameColumn('family_status', 'status_keluarga');
                $table->renameColumn('child_number', 'anak_ke');
                $table->renameColumn('school_of_origin', 'asal_sekolah');
                $table->renameColumn('admission_date', 'tanggal_masuk');
                $table->renameColumn('father_name', 'nama_ayah');
                $table->renameColumn('father_occupation', 'pekerjaan_ayah');
                $table->renameColumn('mother_name', 'nama_ibu');
                $table->renameColumn('mother_occupation', 'pekerjaan_ibu');
                $table->renameColumn('parent_address', 'alamat_ortu');
                $table->renameColumn('parent_phone', 'telepon_ortu');
                $table->renameColumn('guardian_name', 'nama_wali');
                $table->renameColumn('guardian_occupation', 'pekerjaan_wali');
                $table->renameColumn('guardian_address', 'alamat_wali');
                $table->renameColumn('guardian_phone', 'telepon_wali');
            });

            Schema::table('tata_tertib', function (Blueprint $table) {
                $table->renameColumn('title', 'judul');
                $table->renameColumn('file_path', 'path_file');
                $table->renameColumn('is_published', 'dipublikasikan');
                $table->renameColumn('uploaded_by_user_id', 'diunggah_oleh_id');
            });

            Schema::table('pengaturan', function (Blueprint $table) {
                $table->renameColumn('key', 'kunci');
                $table->renameColumn('value', 'nilai');
            });

            Schema::table('pesan_whatsapp', function (Blueprint $table) {
                $table->renameColumn('school_class_id', 'kelas_id');
                $table->renameColumn('recipient_phone', 'telepon_penerima');
                $table->renameColumn('recipient_name', 'nama_penerima');
                $table->renameColumn('message_type', 'tipe_pesan');
                $table->renameColumn('provider_message_id', 'id_pesan_provider');
                $table->renameColumn('message', 'isi_pesan');
                $table->renameColumn('response', 'respons');
                $table->renameColumn('sent_at', 'dikirim_pada');
            });

            Schema::table('token_otp', function (Blueprint $table) {
                $table->renameColumn('user_id', 'pengguna_id');
                $table->renameColumn('type', 'tipe');
                $table->renameColumn('new_email', 'email_baru');
                $table->renameColumn('new_password', 'sandi_baru');
                $table->renameColumn('expires_at', 'kadaluwarsa_pada');
                $table->renameColumn('used_at', 'digunakan_pada');
            });

            Schema::table('token_akses_absensi', function (Blueprint $table) {
                $table->renameColumn('class_id', 'kelas_id');
                $table->renameColumn('expires_at', 'kadaluwarsa_pada');
            });
        }
    }

    public function down(): void
    {
        $mysql = DB::getDriverName() === 'mysql';

        if ($mysql) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        // Reverse token_akses_absensi
        if ($mysql) {
            DB::statement("ALTER TABLE `token_akses_absensi`
                RENAME COLUMN `kelas_id`         TO `class_id`,
                RENAME COLUMN `kadaluwarsa_pada` TO `expires_at`
            ");
            DB::statement('ALTER TABLE `token_akses_absensi` RENAME INDEX `token_akses_absensi_kelas_id_tanggal_unique` TO `token_akses_absensi_class_id_tanggal_unique`');
        } else {
            Schema::table('token_akses_absensi', function (Blueprint $table) {
                $table->renameColumn('kelas_id', 'class_id');
                $table->renameColumn('kadaluwarsa_pada', 'expires_at');
            });
        }

        // Reverse token_otp
        if ($mysql) {
            DB::statement("ALTER TABLE `token_otp`
                RENAME COLUMN `pengguna_id`      TO `user_id`,
                RENAME COLUMN `tipe`             TO `type`,
                RENAME COLUMN `email_baru`       TO `new_email`,
                RENAME COLUMN `sandi_baru`       TO `new_password`,
                RENAME COLUMN `kadaluwarsa_pada` TO `expires_at`,
                RENAME COLUMN `digunakan_pada`   TO `used_at`
            ");
        } else {
            Schema::table('token_otp', function (Blueprint $table) {
                $table->renameColumn('pengguna_id', 'user_id');
                $table->renameColumn('tipe', 'type');
                $table->renameColumn('email_baru', 'new_email');
                $table->renameColumn('sandi_baru', 'new_password');
                $table->renameColumn('kadaluwarsa_pada', 'expires_at');
                $table->renameColumn('digunakan_pada', 'used_at');
            });
        }
        Schema::rename('token_otp', 'email_otp_tokens');

        // Reverse pesan_whatsapp
        if ($mysql) {
            DB::statement("ALTER TABLE `pesan_whatsapp`
                RENAME COLUMN `kelas_id`          TO `school_class_id`,
                RENAME COLUMN `telepon_penerima`  TO `recipient_phone`,
                RENAME COLUMN `nama_penerima`     TO `recipient_name`,
                RENAME COLUMN `tipe_pesan`        TO `message_type`,
                RENAME COLUMN `id_pesan_provider` TO `provider_message_id`,
                RENAME COLUMN `isi_pesan`         TO `message`,
                RENAME COLUMN `respons`           TO `response`,
                RENAME COLUMN `dikirim_pada`      TO `sent_at`
            ");
            DB::statement("ALTER TABLE `pesan_whatsapp`
                MODIFY COLUMN `recipient_phone`     VARCHAR(20) NOT NULL,
                MODIFY COLUMN `recipient_name`      VARCHAR(255) NULL,
                MODIFY COLUMN `message_type`        VARCHAR(255) NOT NULL DEFAULT 'attendance_report',
                MODIFY COLUMN `provider_message_id` VARCHAR(255) NULL
            ");
        } else {
            Schema::table('pesan_whatsapp', function (Blueprint $table) {
                $table->renameColumn('kelas_id', 'school_class_id');
                $table->renameColumn('telepon_penerima', 'recipient_phone');
                $table->renameColumn('nama_penerima', 'recipient_name');
                $table->renameColumn('tipe_pesan', 'message_type');
                $table->renameColumn('id_pesan_provider', 'provider_message_id');
                $table->renameColumn('isi_pesan', 'message');
                $table->renameColumn('respons', 'response');
                $table->renameColumn('dikirim_pada', 'sent_at');
            });
        }
        Schema::rename('pesan_whatsapp', 'whatsapp_messages');

        // Reverse pengaturan
        if ($mysql) {
            DB::statement('ALTER TABLE `pengaturan` DROP INDEX `pengaturan_kunci_unique`');
            DB::statement("ALTER TABLE `pengaturan`
                CHANGE COLUMN `kunci` `key`   VARCHAR(255) NOT NULL,
                CHANGE COLUMN `nilai` `value` TEXT NULL
            ");
            DB::statement('ALTER TABLE `pengaturan` ADD UNIQUE KEY `settings_key_unique` (`key`)');
        } else {
            Schema::table('pengaturan', function (Blueprint $table) {
                $table->renameColumn('kunci', 'key');
                $table->renameColumn('nilai', 'value');
            });
        }
        Schema::rename('pengaturan', 'settings');

        // Reverse tata_tertib
        if ($mysql) {
            DB::statement("ALTER TABLE `tata_tertib`
                RENAME COLUMN `judul`            TO `title`,
                RENAME COLUMN `path_file`        TO `file_path`,
                RENAME COLUMN `dipublikasikan`   TO `is_published`,
                RENAME COLUMN `diunggah_oleh_id` TO `uploaded_by_user_id`
            ");
            DB::statement("ALTER TABLE `tata_tertib`
                MODIFY COLUMN `title`     VARCHAR(255) NOT NULL,
                MODIFY COLUMN `file_path` VARCHAR(255) NOT NULL
            ");
        } else {
            Schema::table('tata_tertib', function (Blueprint $table) {
                $table->renameColumn('judul', 'title');
                $table->renameColumn('path_file', 'file_path');
                $table->renameColumn('dipublikasikan', 'is_published');
                $table->renameColumn('diunggah_oleh_id', 'uploaded_by_user_id');
            });
        }
        Schema::rename('tata_tertib', 'school_rules');

        // Reverse biodata_siswa
        if ($mysql) {
            DB::statement("ALTER TABLE `biodata_siswa`
                RENAME COLUMN `profil_siswa_id` TO `student_profile_id`,
                RENAME COLUMN `tempat_lahir`    TO `place_of_birth`,
                RENAME COLUMN `tanggal_lahir`   TO `date_of_birth`,
                RENAME COLUMN `jenis_kelamin`   TO `gender`,
                RENAME COLUMN `agama`           TO `religion`,
                RENAME COLUMN `status_keluarga` TO `family_status`,
                RENAME COLUMN `anak_ke`         TO `child_number`,
                RENAME COLUMN `asal_sekolah`    TO `school_of_origin`,
                RENAME COLUMN `tanggal_masuk`   TO `admission_date`,
                RENAME COLUMN `nama_ayah`       TO `father_name`,
                RENAME COLUMN `pekerjaan_ayah`  TO `father_occupation`,
                RENAME COLUMN `nama_ibu`        TO `mother_name`,
                RENAME COLUMN `pekerjaan_ibu`   TO `mother_occupation`,
                RENAME COLUMN `alamat_ortu`     TO `parent_address`,
                RENAME COLUMN `telepon_ortu`    TO `parent_phone`,
                RENAME COLUMN `nama_wali`       TO `guardian_name`,
                RENAME COLUMN `pekerjaan_wali`  TO `guardian_occupation`,
                RENAME COLUMN `alamat_wali`     TO `guardian_address`,
                RENAME COLUMN `telepon_wali`    TO `guardian_phone`
            ");
            DB::statement("ALTER TABLE `biodata_siswa`
                MODIFY COLUMN `place_of_birth`      VARCHAR(255) NULL,
                MODIFY COLUMN `religion`            VARCHAR(255) NULL,
                MODIFY COLUMN `family_status`       VARCHAR(255) NULL,
                MODIFY COLUMN `school_of_origin`    VARCHAR(255) NULL,
                MODIFY COLUMN `father_name`         VARCHAR(255) NULL,
                MODIFY COLUMN `father_occupation`   VARCHAR(255) NULL,
                MODIFY COLUMN `mother_name`         VARCHAR(255) NULL,
                MODIFY COLUMN `mother_occupation`   VARCHAR(255) NULL,
                MODIFY COLUMN `parent_phone`        VARCHAR(20) NULL,
                MODIFY COLUMN `guardian_name`       VARCHAR(255) NULL,
                MODIFY COLUMN `guardian_occupation` VARCHAR(255) NULL,
                MODIFY COLUMN `guardian_phone`      VARCHAR(20) NULL
            ");
        } else {
            Schema::table('biodata_siswa', function (Blueprint $table) {
                $table->renameColumn('profil_siswa_id', 'student_profile_id');
                $table->renameColumn('tempat_lahir', 'place_of_birth');
                $table->renameColumn('tanggal_lahir', 'date_of_birth');
                $table->renameColumn('jenis_kelamin', 'gender');
                $table->renameColumn('agama', 'religion');
                $table->renameColumn('status_keluarga', 'family_status');
                $table->renameColumn('anak_ke', 'child_number');
                $table->renameColumn('asal_sekolah', 'school_of_origin');
                $table->renameColumn('tanggal_masuk', 'admission_date');
                $table->renameColumn('nama_ayah', 'father_name');
                $table->renameColumn('pekerjaan_ayah', 'father_occupation');
                $table->renameColumn('nama_ibu', 'mother_name');
                $table->renameColumn('pekerjaan_ibu', 'mother_occupation');
                $table->renameColumn('alamat_ortu', 'parent_address');
                $table->renameColumn('telepon_ortu', 'parent_phone');
                $table->renameColumn('nama_wali', 'guardian_name');
                $table->renameColumn('pekerjaan_wali', 'guardian_occupation');
                $table->renameColumn('alamat_wali', 'guardian_address');
                $table->renameColumn('telepon_wali', 'guardian_phone');
            });
        }
        Schema::rename('biodata_siswa', 'student_biodata');

        // Reverse jenis_pelanggaran
        if ($mysql) {
            DB::statement("ALTER TABLE `jenis_pelanggaran`
                CHANGE COLUMN `nama`             `name`            VARCHAR(255) NOT NULL,
                CHANGE COLUMN `kategori`         `category`        ENUM('light','medium','heavy','severe') NOT NULL,
                CHANGE COLUMN `pengurangan_poin` `point_deduction` TINYINT UNSIGNED NOT NULL,
                CHANGE COLUMN `keterangan`       `description`     TEXT NULL,
                CHANGE COLUMN `aktif`            `is_active`       TINYINT(1) NOT NULL DEFAULT 1
            ");
        } else {
            Schema::table('jenis_pelanggaran', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
                $table->renameColumn('kategori', 'category');
                $table->renameColumn('pengurangan_poin', 'point_deduction');
                $table->renameColumn('keterangan', 'description');
                $table->renameColumn('aktif', 'is_active');
            });
        }
        Schema::rename('jenis_pelanggaran', 'violation_types');

        // Reverse pelanggaran_siswa
        if ($mysql) {
            DB::statement("ALTER TABLE `pelanggaran_siswa`
                RENAME COLUMN `profil_siswa_id`      TO `student_profile_id`,
                RENAME COLUMN `jenis_pelanggaran_id` TO `violation_type_id`,
                RENAME COLUMN `dicatat_oleh_id`      TO `recorded_by_user_id`,
                RENAME COLUMN `disetujui_oleh_id`    TO `approved_by_user_id`,
                RENAME COLUMN `tanggal_pelanggaran`  TO `violation_date`,
                RENAME COLUMN `nama_pelanggaran`     TO `violation_name`,
                RENAME COLUMN `kategori_pelanggaran` TO `violation_category`,
                RENAME COLUMN `pengurangan_poin`     TO `point_deduction`,
                RENAME COLUMN `catatan`              TO `notes`,
                RENAME COLUMN `disetujui_pada`       TO `approved_at`,
                RENAME COLUMN `alasan_penolakan`     TO `rejection_reason`
            ");
            DB::statement("ALTER TABLE `pelanggaran_siswa`
                MODIFY COLUMN `violation_name` VARCHAR(255) NOT NULL
            ");
        } else {
            Schema::table('pelanggaran_siswa', function (Blueprint $table) {
                $table->renameColumn('profil_siswa_id', 'student_profile_id');
                $table->renameColumn('jenis_pelanggaran_id', 'violation_type_id');
                $table->renameColumn('dicatat_oleh_id', 'recorded_by_user_id');
                $table->renameColumn('disetujui_oleh_id', 'approved_by_user_id');
                $table->renameColumn('tanggal_pelanggaran', 'violation_date');
                $table->renameColumn('nama_pelanggaran', 'violation_name');
                $table->renameColumn('kategori_pelanggaran', 'violation_category');
                $table->renameColumn('pengurangan_poin', 'point_deduction');
                $table->renameColumn('catatan', 'notes');
                $table->renameColumn('disetujui_pada', 'approved_at');
                $table->renameColumn('alasan_penolakan', 'rejection_reason');
            });
        }
        Schema::rename('pelanggaran_siswa', 'student_violations');

        // Reverse absensi
        if ($mysql) {
            DB::statement("ALTER TABLE `absensi`
                RENAME COLUMN `profil_siswa_id` TO `student_profile_id`,
                RENAME COLUMN `tanggal`         TO `date`,
                RENAME COLUMN `waktu_masuk`     TO `check_in_time`,
                RENAME COLUMN `path_selfie`     TO `selfie_path`,
                RENAME COLUMN `akurasi`         TO `accuracy`,
                RENAME COLUMN `jarak_meter`     TO `distance_meters`
            ");
            DB::statement('ALTER TABLE `absensi` RENAME INDEX `absensi_profil_siswa_id_tanggal_unique` TO `attendances_student_profile_id_date_unique`');
        } else {
            Schema::table('absensi', function (Blueprint $table) {
                $table->renameColumn('profil_siswa_id', 'student_profile_id');
                $table->renameColumn('tanggal', 'date');
                $table->renameColumn('waktu_masuk', 'check_in_time');
                $table->renameColumn('path_selfie', 'selfie_path');
                $table->renameColumn('akurasi', 'accuracy');
                $table->renameColumn('jarak_meter', 'distance_meters');
            });
        }
        Schema::rename('absensi', 'attendances');

        // Reverse kelas
        if ($mysql) {
            DB::statement('ALTER TABLE `kelas` DROP INDEX `kelas_nama_tingkat_unique`');
            DB::statement("ALTER TABLE `kelas`
                RENAME COLUMN `nama`          TO `name`,
                RENAME COLUMN `tingkat`       TO `grade`,
                RENAME COLUMN `wali_kelas_id` TO `homeroom_teacher_id`,
                RENAME COLUMN `jumlah_siswa`  TO `student_count`
            ");
            DB::statement("ALTER TABLE `kelas`
                MODIFY COLUMN `name`          VARCHAR(255) NOT NULL,
                MODIFY COLUMN `student_count` INT UNSIGNED NOT NULL DEFAULT 0
            ");
            DB::statement('ALTER TABLE `kelas` ADD UNIQUE KEY `classes_name_grade_unique` (`name`, `grade`)');
        } else {
            Schema::table('kelas', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
                $table->renameColumn('tingkat', 'grade');
                $table->renameColumn('wali_kelas_id', 'homeroom_teacher_id');
                $table->renameColumn('jumlah_siswa', 'student_count');
            });
        }
        Schema::rename('kelas', 'classes');

        // Reverse profil_siswa
        if ($mysql) {
            DB::statement("ALTER TABLE `profil_siswa`
                RENAME COLUMN `pengguna_id` TO `user_id`,
                RENAME COLUMN `kelas_id`    TO `class_id`,
                RENAME COLUMN `telepon`     TO `phone`,
                RENAME COLUMN `alamat`      TO `address`,
                RENAME COLUMN `foto`        TO `photo`
            ");
            DB::statement("ALTER TABLE `profil_siswa`
                MODIFY COLUMN `phone` VARCHAR(255) NULL,
                MODIFY COLUMN `photo` VARCHAR(255) NULL
            ");
        } else {
            Schema::table('profil_siswa', function (Blueprint $table) {
                $table->renameColumn('pengguna_id', 'user_id');
                $table->renameColumn('kelas_id', 'class_id');
                $table->renameColumn('telepon', 'phone');
                $table->renameColumn('alamat', 'address');
                $table->renameColumn('foto', 'photo');
            });
        }
        Schema::rename('profil_siswa', 'student_profiles');

        // Reverse profil_guru
        if ($mysql) {
            DB::statement("ALTER TABLE `profil_guru`
                RENAME COLUMN `pengguna_id` TO `user_id`,
                RENAME COLUMN `telepon`     TO `phone`,
                RENAME COLUMN `foto`        TO `photo`,
                RENAME COLUMN `tipe_guru`   TO `teacher_type`,
                RENAME COLUMN `tingkat`     TO `grade`
            ");
            DB::statement("ALTER TABLE `profil_guru`
                MODIFY COLUMN `phone` VARCHAR(255) NULL,
                MODIFY COLUMN `photo` VARCHAR(255) NULL
            ");
        } else {
            Schema::table('profil_guru', function (Blueprint $table) {
                $table->renameColumn('pengguna_id', 'user_id');
                $table->renameColumn('telepon', 'phone');
                $table->renameColumn('foto', 'photo');
                $table->renameColumn('tipe_guru', 'teacher_type');
                $table->renameColumn('tingkat', 'grade');
            });
        }
        Schema::rename('profil_guru', 'teacher_profiles');

        // Reverse pengguna
        if ($mysql) {
            DB::statement("ALTER TABLE `pengguna`
                CHANGE COLUMN `nama`      `name`            VARCHAR(255) NOT NULL,
                CHANGE COLUMN `peran`     `role`            ENUM('admin','siswa','wali_kelas','bk','kesiswaan') NOT NULL DEFAULT 'siswa',
                CHANGE COLUMN `foto`      `photo`           VARCHAR(255) NULL,
                CHANGE COLUMN `nomor_wa`  `whatsapp_number` VARCHAR(20) NULL,
                CHANGE COLUMN `id_google` `google_id`       VARCHAR(255) NULL
            ");
        } else {
            Schema::table('pengguna', function (Blueprint $table) {
                $table->renameColumn('nama', 'name');
                $table->renameColumn('peran', 'role');
                $table->renameColumn('foto', 'photo');
                $table->renameColumn('nomor_wa', 'whatsapp_number');
                $table->renameColumn('id_google', 'google_id');
            });
        }
        Schema::rename('pengguna', 'users');

        if ($mysql) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};

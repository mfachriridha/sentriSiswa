<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

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

        // pengguna (was users) — no FK columns, CHANGE COLUMN ok
        DB::statement("ALTER TABLE `pengguna`
            CHANGE COLUMN `name`            `nama`      VARCHAR(100) NOT NULL,
            CHANGE COLUMN `role`            `peran`     ENUM('admin','siswa','wali_kelas','bk','kesiswaan') NOT NULL DEFAULT 'siswa',
            CHANGE COLUMN `photo`           `foto`      VARCHAR(300) NULL,
            CHANGE COLUMN `whatsapp_number` `nomor_wa`  VARCHAR(20) NULL,
            CHANGE COLUMN `google_id`       `id_google` VARCHAR(50) NULL
        ");

        // profil_guru (was teacher_profiles) — pengguna_id is FK → RENAME then MODIFY
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

        // profil_siswa (was student_profiles) — pengguna_id, kelas_id are FK → RENAME then MODIFY
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

        // kelas (was classes) — wali_kelas_id is FK → RENAME then MODIFY
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

        // absensi (was attendances) — profil_siswa_id is FK, unique idx used as FK covering index
        // → RENAME COLUMN first (auto-updates index definition), then RENAME INDEX (avoids FK conflict)
        DB::statement("ALTER TABLE `absensi`
            RENAME COLUMN `student_profile_id` TO `profil_siswa_id`,
            RENAME COLUMN `date`               TO `tanggal`,
            RENAME COLUMN `check_in_time`      TO `waktu_masuk`,
            RENAME COLUMN `selfie_path`        TO `path_selfie`,
            RENAME COLUMN `accuracy`           TO `akurasi`,
            RENAME COLUMN `distance_meters`    TO `jarak_meter`
        ");
        DB::statement('ALTER TABLE `absensi` RENAME INDEX `attendances_student_profile_id_date_unique` TO `absensi_profil_siswa_id_tanggal_unique`');

        // pelanggaran_siswa (was student_violations) — 4 FK columns → RENAME then MODIFY
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

        // jenis_pelanggaran (was violation_types) — no FK columns, CHANGE COLUMN ok
        DB::statement("ALTER TABLE `jenis_pelanggaran`
            CHANGE COLUMN `name`            `nama`             VARCHAR(100) NOT NULL,
            CHANGE COLUMN `category`        `kategori`         ENUM('light','medium','heavy','severe') NOT NULL,
            CHANGE COLUMN `point_deduction` `pengurangan_poin` TINYINT UNSIGNED NOT NULL,
            CHANGE COLUMN `description`     `keterangan`       TEXT NULL,
            CHANGE COLUMN `is_active`       `aktif`            TINYINT(1) NOT NULL DEFAULT 1
        ");

        // biodata_siswa (was student_biodata) — profil_siswa_id is FK → RENAME then MODIFY
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

        // tata_tertib (was school_rules) — diunggah_oleh_id is FK → RENAME then MODIFY
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

        // pengaturan (was settings) — no FK columns, CHANGE COLUMN ok
        DB::statement('ALTER TABLE `pengaturan` DROP INDEX `settings_key_unique`');
        DB::statement("ALTER TABLE `pengaturan`
            CHANGE COLUMN `key`   `kunci` VARCHAR(100) NOT NULL,
            CHANGE COLUMN `value` `nilai` TEXT NULL
        ");
        DB::statement('ALTER TABLE `pengaturan` ADD UNIQUE KEY `pengaturan_kunci_unique` (`kunci`)');

        // pesan_whatsapp (was whatsapp_messages) — kelas_id is FK → RENAME then MODIFY
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

        // token_otp (was email_otp_tokens) — pengguna_id is FK → RENAME only (no type changes)
        DB::statement("ALTER TABLE `token_otp`
            RENAME COLUMN `user_id`      TO `pengguna_id`,
            RENAME COLUMN `type`         TO `tipe`,
            RENAME COLUMN `new_email`    TO `email_baru`,
            RENAME COLUMN `new_password` TO `sandi_baru`,
            RENAME COLUMN `expires_at`   TO `kadaluwarsa_pada`,
            RENAME COLUMN `used_at`      TO `digunakan_pada`
        ");

        // token_akses_absensi — kelas_id is FK, unique idx used as FK covering index → RENAME INDEX
        DB::statement("ALTER TABLE `token_akses_absensi`
            RENAME COLUMN `class_id`   TO `kelas_id`,
            RENAME COLUMN `expires_at` TO `kadaluwarsa_pada`
        ");
        DB::statement('ALTER TABLE `token_akses_absensi` RENAME INDEX `token_akses_absensi_class_id_tanggal_unique` TO `token_akses_absensi_kelas_id_tanggal_unique`');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Reverse token_akses_absensi
        DB::statement("ALTER TABLE `token_akses_absensi`
            RENAME COLUMN `kelas_id`         TO `class_id`,
            RENAME COLUMN `kadaluwarsa_pada` TO `expires_at`
        ");
        DB::statement('ALTER TABLE `token_akses_absensi` RENAME INDEX `token_akses_absensi_kelas_id_tanggal_unique` TO `token_akses_absensi_class_id_tanggal_unique`');

        // Reverse token_otp
        DB::statement("ALTER TABLE `token_otp`
            RENAME COLUMN `pengguna_id`      TO `user_id`,
            RENAME COLUMN `tipe`             TO `type`,
            RENAME COLUMN `email_baru`       TO `new_email`,
            RENAME COLUMN `sandi_baru`       TO `new_password`,
            RENAME COLUMN `kadaluwarsa_pada` TO `expires_at`,
            RENAME COLUMN `digunakan_pada`   TO `used_at`
        ");
        Schema::rename('token_otp', 'email_otp_tokens');

        // Reverse pesan_whatsapp
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
        Schema::rename('pesan_whatsapp', 'whatsapp_messages');

        // Reverse pengaturan
        DB::statement('ALTER TABLE `pengaturan` DROP INDEX `pengaturan_kunci_unique`');
        DB::statement("ALTER TABLE `pengaturan`
            CHANGE COLUMN `kunci` `key`   VARCHAR(255) NOT NULL,
            CHANGE COLUMN `nilai` `value` TEXT NULL
        ");
        DB::statement('ALTER TABLE `pengaturan` ADD UNIQUE KEY `settings_key_unique` (`key`)');
        Schema::rename('pengaturan', 'settings');

        // Reverse tata_tertib
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
        Schema::rename('tata_tertib', 'school_rules');

        // Reverse biodata_siswa
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
        Schema::rename('biodata_siswa', 'student_biodata');

        // Reverse jenis_pelanggaran
        DB::statement("ALTER TABLE `jenis_pelanggaran`
            CHANGE COLUMN `nama`             `name`            VARCHAR(255) NOT NULL,
            CHANGE COLUMN `kategori`         `category`        ENUM('light','medium','heavy','severe') NOT NULL,
            CHANGE COLUMN `pengurangan_poin` `point_deduction` TINYINT UNSIGNED NOT NULL,
            CHANGE COLUMN `keterangan`       `description`     TEXT NULL,
            CHANGE COLUMN `aktif`            `is_active`       TINYINT(1) NOT NULL DEFAULT 1
        ");
        Schema::rename('jenis_pelanggaran', 'violation_types');

        // Reverse pelanggaran_siswa
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
        Schema::rename('pelanggaran_siswa', 'student_violations');

        // Reverse absensi
        DB::statement("ALTER TABLE `absensi`
            RENAME COLUMN `profil_siswa_id` TO `student_profile_id`,
            RENAME COLUMN `tanggal`         TO `date`,
            RENAME COLUMN `waktu_masuk`     TO `check_in_time`,
            RENAME COLUMN `path_selfie`     TO `selfie_path`,
            RENAME COLUMN `akurasi`         TO `accuracy`,
            RENAME COLUMN `jarak_meter`     TO `distance_meters`
        ");
        DB::statement('ALTER TABLE `absensi` RENAME INDEX `absensi_profil_siswa_id_tanggal_unique` TO `attendances_student_profile_id_date_unique`');
        Schema::rename('absensi', 'attendances');

        // Reverse kelas
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
        Schema::rename('kelas', 'classes');

        // Reverse profil_siswa
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
        Schema::rename('profil_siswa', 'student_profiles');

        // Reverse profil_guru
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
        Schema::rename('profil_guru', 'teacher_profiles');

        // Reverse pengguna
        DB::statement("ALTER TABLE `pengguna`
            CHANGE COLUMN `nama`      `name`            VARCHAR(255) NOT NULL,
            CHANGE COLUMN `peran`     `role`            ENUM('admin','siswa','wali_kelas','bk','kesiswaan') NOT NULL DEFAULT 'siswa',
            CHANGE COLUMN `foto`      `photo`           VARCHAR(255) NULL,
            CHANGE COLUMN `nomor_wa`  `whatsapp_number` VARCHAR(20) NULL,
            CHANGE COLUMN `id_google` `google_id`       VARCHAR(255) NULL
        ");
        Schema::rename('pengguna', 'users');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};

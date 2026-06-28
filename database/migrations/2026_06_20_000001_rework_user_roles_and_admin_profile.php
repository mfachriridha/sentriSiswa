<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'whatsapp_number')) {
                $table->string('whatsapp_number', 20)->nullable()->after('photo');
            }
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'siswa', 'wali_kelas', 'bk', 'kesiswaan') NOT NULL DEFAULT 'siswa'");
        }

        DB::table('users')
            ->join('teacher_profiles', 'users.id', '=', 'teacher_profiles.user_id')
            ->where('users.role', 'teacher')
            ->where('teacher_profiles.teacher_type', 'homeroom')
            ->update(['users.role' => 'wali_kelas']);

        DB::table('users')
            ->join('teacher_profiles', 'users.id', '=', 'teacher_profiles.user_id')
            ->where('users.role', 'teacher')
            ->where('teacher_profiles.teacher_type', 'counselor')
            ->update(['users.role' => 'bk']);

        DB::table('users')
            ->join('teacher_profiles', 'users.id', '=', 'teacher_profiles.user_id')
            ->where('users.role', 'teacher')
            ->where('teacher_profiles.teacher_type', 'student_affairs')
            ->update(['users.role' => 'kesiswaan']);

        DB::table('users')
            ->where('role', 'teacher')
            ->update(['role' => 'wali_kelas']);

        DB::table('users')
            ->where('role', 'student')
            ->update(['role' => 'siswa']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'siswa', 'wali_kelas', 'bk', 'kesiswaan') NOT NULL DEFAULT 'siswa'");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'siswa', 'wali_kelas', 'bk', 'kesiswaan') NOT NULL DEFAULT 'student'");
        }

        DB::table('users')
            ->whereIn('role', ['wali_kelas', 'bk', 'kesiswaan'])
            ->update(['role' => 'teacher']);

        DB::table('users')
            ->where('role', 'siswa')
            ->update(['role' => 'student']);

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student'");
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'photo')) {
                $table->dropColumn('photo');
            }

            if (Schema::hasColumn('users', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
        });
    }
};

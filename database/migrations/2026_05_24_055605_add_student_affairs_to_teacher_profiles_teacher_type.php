<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE teacher_profiles MODIFY COLUMN teacher_type ENUM('homeroom', 'counselor', 'student_affairs') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE teacher_profiles MODIFY COLUMN teacher_type ENUM('homeroom', 'counselor') NULL DEFAULT NULL");
    }
};

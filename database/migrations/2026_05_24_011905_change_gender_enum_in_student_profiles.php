<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE student_profiles MODIFY COLUMN gender ENUM('L', 'P') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE student_profiles MODIFY COLUMN gender ENUM('Laki-laki', 'Perempuan') NULL");
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 22)->nullable()->unique()->after('email');
            $table->boolean('is_active')->default(false)->after('role');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])->default('hadir');
            $table->string('selfie_path')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_inside_geofence')->default(false);
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'date']);
        });

        Schema::create('teacher_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->enum('role', ['wali_kelas', 'bk'])->default('wali_kelas');
            $table->timestamps();

            $table->unique(['user_id', 'school_class_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_classes');
        Schema::dropIfExists('attendances');

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'is_active']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_biodata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained('student_profiles')->cascadeOnDelete();
            $table->string('place_of_birth', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('family_status', 50)->nullable();
            $table->unsignedSmallInteger('child_number')->nullable();
            $table->string('school_of_origin', 255)->nullable();
            $table->date('admission_date')->nullable();
            $table->string('father_name', 255)->nullable();
            $table->string('father_occupation', 255)->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->string('mother_occupation', 255)->nullable();
            $table->text('parent_address')->nullable();
            $table->string('parent_phone', 20)->nullable();
            $table->string('guardian_name', 255)->nullable();
            $table->string('guardian_occupation', 255)->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_biodata');
    }
};

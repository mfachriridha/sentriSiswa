<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->nullable()->index();
            $table->string('nisn')->nullable();
            $table->string('name');
            $table->string('gender')->nullable();
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->string('phone')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->string('family_status')->nullable();
            $table->string('birth_order')->nullable();
            $table->text('address')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('prev_school')->nullable();
            $table->string('admission_class')->nullable();
            $table->string('admission_date')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_job')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('parent_address')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_job')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
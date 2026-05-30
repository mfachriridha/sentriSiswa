<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('violation_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('violation_date')->index();
            $table->string('violation_name');
            $table->enum('violation_category', ['light', 'medium', 'heavy', 'severe'])->index();
            $table->unsignedTinyInteger('point_deduction');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_profile_id', 'violation_date']);
            $table->index(['violation_type_id', 'violation_date']);
            $table->index(['violation_category', 'violation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_violations');
    }
};

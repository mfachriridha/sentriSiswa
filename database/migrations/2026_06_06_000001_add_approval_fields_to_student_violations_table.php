<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_violations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('notes')->index();
            $table->foreignId('approved_by_user_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by_user_id');
            $table->text('rejection_reason')->nullable()->after('approved_at');
        });

        DB::table('student_violations')
            ->where('status', 'approved')
            ->whereNull('approved_at')
            ->update(['approved_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('student_violations', function (Blueprint $table) {
            $table->dropForeign(['approved_by_user_id']);
            $table->dropColumn(['status', 'approved_by_user_id', 'approved_at', 'rejection_reason']);
        });
    }
};

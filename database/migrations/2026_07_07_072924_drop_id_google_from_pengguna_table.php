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
        Schema::table('pengguna', function (Blueprint $table) {
            // Index kept its original name from before the users->pengguna /
            // google_id->id_google rename, so it must be dropped explicitly by
            // that literal name before the column (SQLite refuses to drop a
            // column that's still part of an index).
            $table->dropUnique('users_google_id_unique');
            $table->dropColumn('id_google');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $table) {
            $table->string('id_google')->nullable()->unique()->after('remember_token');
        });
    }
};

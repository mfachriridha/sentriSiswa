<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The 2026_07_03_000001 natural-key migration dropped and re-added
     * `profil_siswa_id` on `absensi` via raw ALTER statements (to retype it
     * to varchar). Dropping a column that belongs to a multi-column unique
     * index does not drop the index on MySQL — it silently shrinks it to
     * whatever columns remain, so the composite unique(profil_siswa_id,
     * tanggal) became unique(tanggal) alone, allowing only one attendance
     * row per date across all students. This restores the composite.
     */
    public function up(): void
    {
        $this->dropUniqueIndexCovering(['tanggal']);

        Schema::table('absensi', function (Blueprint $table) {
            $table->unique(['profil_siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        $this->dropUniqueIndexCovering(['profil_siswa_id', 'tanggal']);

        Schema::table('absensi', function (Blueprint $table) {
            $table->unique('tanggal');
        });
    }

    /**
     * Find and drop whichever unique index currently covers exactly the
     * given columns, regardless of its actual name (MySQL's silent
     * index-shrinking after a column drop can leave a name that no
     * longer matches Laravel's naming convention for its columns).
     *
     * @param  list<string>  $columns
     */
    private function dropUniqueIndexCovering(array $columns): void
    {
        $existing = collect(Schema::getIndexes('absensi'))
            ->first(fn (array $index): bool => $index['unique'] && $index['columns'] === $columns);

        if ($existing) {
            Schema::table('absensi', function (Blueprint $table) use ($existing) {
                $table->dropUnique($existing['name']);
            });
        }
    }
};

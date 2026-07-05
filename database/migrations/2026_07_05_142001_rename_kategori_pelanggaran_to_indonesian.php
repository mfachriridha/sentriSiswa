<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const array OLD_VALUES = ['light', 'medium', 'heavy', 'severe'];

    private const array NEW_VALUES = ['ringan', 'sedang', 'berat', 'sangat_berat'];

    public function up(): void
    {
        $this->migrate('jenis_pelanggaran', 'kategori', self::OLD_VALUES, self::NEW_VALUES);
        $this->migrate('pelanggaran_siswa', 'kategori_pelanggaran', self::OLD_VALUES, self::NEW_VALUES);
    }

    public function down(): void
    {
        $this->migrate('jenis_pelanggaran', 'kategori', self::NEW_VALUES, self::OLD_VALUES);
        $this->migrate('pelanggaran_siswa', 'kategori_pelanggaran', self::NEW_VALUES, self::OLD_VALUES);
    }

    /**
     * @param  list<string>  $from
     * @param  list<string>  $to
     */
    private function migrate(string $table, string $column, array $from, array $to): void
    {
        $sqlite = DB::getDriverName() === 'sqlite';
        $both = array_values(array_unique([...$from, ...$to]));

        if ($sqlite) {
            Schema::table($table, function (Blueprint $blueprint) use ($column, $both) {
                $blueprint->enum($column, $both)->change();
            });
        } else {
            $list = "'".implode("','", $both)."'";
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` ENUM({$list}) NOT NULL");
        }

        $case = collect($from)->zip($to)
            ->map(fn ($pair) => "WHEN '{$pair[0]}' THEN '{$pair[1]}'")
            ->implode(' ');
        DB::statement("UPDATE `{$table}` SET `{$column}` = CASE `{$column}` {$case} END");

        if ($sqlite) {
            Schema::table($table, function (Blueprint $blueprint) use ($column, $to) {
                $blueprint->enum($column, $to)->change();
            });
        } else {
            $list = "'".implode("','", $to)."'";
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `{$column}` ENUM({$list}) NOT NULL");
        }
    }
};

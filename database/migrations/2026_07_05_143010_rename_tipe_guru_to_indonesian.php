<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const array OLD_VALUES = ['homeroom', 'counselor', 'student_affairs'];

    private const array NEW_VALUES = ['wali_kelas', 'bk', 'kesiswaan'];

    public function up(): void
    {
        $this->migrate(self::OLD_VALUES, self::NEW_VALUES);
    }

    public function down(): void
    {
        $this->migrate(self::NEW_VALUES, self::OLD_VALUES);
    }

    /**
     * @param  list<string>  $from
     * @param  list<string>  $to
     */
    private function migrate(array $from, array $to): void
    {
        $sqlite = DB::getDriverName() === 'sqlite';
        $both = array_values(array_unique([...$from, ...$to]));

        if ($sqlite) {
            Schema::table('profil_guru', function (Blueprint $blueprint) use ($both) {
                $blueprint->enum('tipe_guru', $both)->nullable()->change();
            });
        } else {
            $list = "'".implode("','", $both)."'";
            DB::statement("ALTER TABLE `profil_guru` MODIFY COLUMN `tipe_guru` ENUM({$list}) NULL");
        }

        $case = collect($from)->zip($to)
            ->map(fn ($pair) => "WHEN '{$pair[0]}' THEN '{$pair[1]}'")
            ->implode(' ');
        DB::statement("UPDATE `profil_guru` SET `tipe_guru` = CASE `tipe_guru` {$case} ELSE `tipe_guru` END");

        if ($sqlite) {
            Schema::table('profil_guru', function (Blueprint $blueprint) use ($to) {
                $blueprint->enum('tipe_guru', $to)->nullable()->change();
            });
        } else {
            $list = "'".implode("','", $to)."'";
            DB::statement("ALTER TABLE `profil_guru` MODIFY COLUMN `tipe_guru` ENUM({$list}) NULL");
        }
    }
};

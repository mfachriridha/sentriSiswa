<?php

namespace App\Models;

use Database\Factories\SchoolRuleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['judul', 'path_file', 'dipublikasikan', 'diunggah_oleh_id'])]
class TataTertib extends Model
{
    /** @use HasFactory<SchoolRuleFactory> */
    use HasFactory;

    protected $table = 'tata_tertib';

    public function diunggahOleh(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'diunggah_oleh_id');
    }

    protected function casts(): array
    {
        return [
            'dipublikasikan' => 'boolean',
        ];
    }
}

<?php

namespace App\Models;

use Database\Factories\ProfilGuruFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nip', 'telepon', 'foto', 'tipe_guru', 'tingkat'])]
class ProfilGuru extends Model
{
    /** @use HasFactory<ProfilGuruFactory> */
    use HasFactory;

    protected $table = 'profil_guru';

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }
}

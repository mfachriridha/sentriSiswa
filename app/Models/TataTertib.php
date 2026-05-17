<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TataTertib extends Model
{
    protected $table = 'tata_tertib';

    protected $fillable = ['judul', 'file_pdf'];
}

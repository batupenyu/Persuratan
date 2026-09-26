<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DaftarDudika extends Model
{
    protected $table = 'daftar_dudika';

    protected $fillable = [
        'nama_dudika',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratSkorsing extends Model
{
    protected $table = 'surat_skorsings';

    protected $fillable = [
        'nomor_surat',
        'siswa_id',
        'penandatangan_id',
        'pelanggaran',
        'durasi_skorsing',
        'tanggal_mulai',
        'tanggal_selesai',
        'tempat_ditetapkan',
        'tanggal_ditetapkan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_ditetapkan' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }

    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(Asn::class, 'penandatangan_id');
    }
}

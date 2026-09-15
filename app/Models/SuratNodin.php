<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratNodin extends Model
{
    protected $table = 'surat_nodins';

    protected $fillable = [
        'nomor',
        'sifat',
        'lampiran',
        'hal',
        'kepada',
        'dari',
        'dari_plt',
        'dari_an',
        'tanggal',
        'dasar_surat',
        'isi_surat',
        'penandatangan_id',
        'penandatangan_plt',
        'penandatangan_an',
        'pegawai_tugas_id',
        'kop_surat',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'dari_plt' => 'boolean',
        'dari_an' => 'boolean',
        'penandatangan_plt' => 'boolean',
        'penandatangan_an' => 'boolean',
    ];

    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(Asn::class, 'penandatangan_id');
    }

    public function pegawaiTugas(): BelongsTo
    {
        return $this->belongsTo(Asn::class, 'pegawai_tugas_id');
    }

    public function pesertaSuratUsulans(): HasMany
    {
        return $this->hasMany(PesertaSuratUsulan::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PhotoNodin::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPkl extends Model
{
    protected $table = 'surat_pkls';

    protected $fillable = [
        'nomor',
        'sifat',
        'lampiran',
        'perihal',
        'pejabat_tujuan_surat',
        'kota_tujuan_surat',
        'tempat_ditetapkan',
        'tanggal_ditetapkan',
        'penandatangan_id',
        'jumlah_siswa',
        'nama_kelas',
        'tahun_ajaran',
        'nama_jurusan',
        'jenis_instansi_yang_dituju',
        'lama_magang',
        'tgl_awal_magang',
        'tgl_akhir_magang',
        'no_contact',
    ];

    protected $casts = [
        'tanggal_ditetapkan' => 'date',
        'tgl_awal_magang' => 'date',
        'tgl_akhir_magang' => 'date',
    ];

    public function penandatangan(): BelongsTo
    {
        return $this->belongsTo(Asn::class, 'penandatangan_id');
    }
}

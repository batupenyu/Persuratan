<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrhSatyalancana extends Model
{
    protected $table = 'drh_satyalancana';

    protected $fillable = [
        'asn_id',
        'kop_surat',
        'nip_lama',
        'pendidikan_terakhir',
        'pangkat',
        'tmt_pangkat',
        'no_sk_cpns',
        'tgl_sk_cpns',
        'tmt_cpns',
        'jabatan_terakhir',
        'tmt_jabatan',
        'tanda_kehormatan',
        'tgl_kepres',
        'no_kepres',
        'hukuman_disiplin',
        'cltn',
        'atasan_nama',
        'atasan_nip',
        'dasar',
        'untuk',
        'tgl_ditetapkan',
    ];

    protected $casts = [
        'tmt_pangkat' => 'date',
        'tgl_sk_cpns' => 'date',
        'tmt_cpns' => 'date',
        'tmt_jabatan' => 'date',
        'tgl_kepres' => 'date',
        'tgl_ditetapkan' => 'date',
    ];

    public function asn(): BelongsTo
    {
        return $this->belongsTo(Asn::class, 'asn_id');
    }
}

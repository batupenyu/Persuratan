<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Dinas - {{ $suratNodin->nomor ?? '' }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            background: #eee;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        .page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            margin: 10mm auto;
            padding: 12mm 15mm 22mm 15mm;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.20);
            overflow: hidden;
        }

        .kop-surat-container {
            width: 100%;
            margin: 0 0 8mm 0;
            padding: 0;
            text-align: center;
            page-break-inside: avoid;
        }

        .kop-surat-image {
            display: block;
            width: 100%;
            max-width: 100%;
            height: auto;
            margin: 0 auto;
            object-fit: contain;
        }

        .judul-surat {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            margin: 3mm 0 6mm 0;
            text-transform: uppercase;
        }

        .identitas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2mm;
            font-size: 10pt;
        }

        .identitas td {
            border: none;
            padding: 1mm 0;
            vertical-align: top;
        }

        .identitas .label {
            width: 25mm;
        }

        .identitas .colon {
            width: 5mm;
            text-align: center;
        }

        .garis-pembatas {
            border: none;
            border-top: 1.5px solid #000;
            margin: 4mm 0 5mm 0;
        }

        .isi-surat {
            font-size: 10pt;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 3mm;
            text-indent: 10mm;
        }

        .tabel-peserta {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4mm;
            margin-bottom: 5mm;
            font-size: 8pt;
            page-break-inside: auto;
        }

        .tabel-peserta thead {
            display: table-header-group;
        }

        .tabel-peserta tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .tabel-peserta th,
        .tabel-peserta td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top !important;
        }

        .tabel-peserta th {
            text-align: center;
            vertical-align: middle !important;
            font-weight: bold;
            background: #f2f2f2;
        }

        .text-center {
            text-align: center !important;
        }

        .pegawai-nama {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .label-siswa, .label-nis, .label-kelas {
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 2px;
        }

        .dudika-ol {
            margin: 0;
            padding-left: 15px;
        }

        .kegiatan-tanggal {
            text-align: center;
            vertical-align: middle !important;
        }

        /* --- BAGIAN TANDA TANGAN --- */
        .signature-wrapper {
            width: 100%;
            margin-top: 7mm;
            page-break-inside: avoid;
            display: flex;
            justify-content: flex-end;
        }

        .signature {
            width: auto;
            min-width: 250px;
            font-size: 10pt;
            line-height: 1.4;
        }

        .signature-table {
            display: table;
            width: 100%;
        }

        .signature-row {
            display: table-row;
        }

        .signature-prefix-cell {
            display: table-cell;
            white-space: nowrap;
            padding-right: 1px;
            vertical-align: top;
        }

        .signature-content-cell {
            display: table-cell;
            vertical-align: top;
            text-align: left;
            text-transform: uppercase;
        }

        .signature-space {
            height: 22mm;
        }

        .signature-tugas {}

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: none; 
        }

        .signature-pangkat,
        .signature-nip {
            text-transform: none;
        }

        .no-print {
            width: 210mm;
            margin: 15px auto;
            text-align: center;
        }

        .no-print a,
        .no-print button {
            display: inline-block;
            padding: 8px 18px;
            margin: 0 3px;
            border: none;
            border-radius: 4px;
            font-size: 10pt;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-back {
            background: #6b7280;
            color: #fff;
        }

        .btn-print {
            background: #2563eb;
            color: #fff;
        }

        @media print {
            html, body {
                background: #fff;
            }
            .page {
                width: 210mm;
                min-height: 297mm;
                margin: 0;
                padding: 12mm 15mm 22mm 15mm;
                box-shadow: none;
                overflow: visible;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

<div class="page">

    {{-- KOP SURAT --}}
    @if(!empty($kopSuratBase64))
        <div class="kop-surat-container">
            <img src="{{ $kopSuratBase64 }}" class="kop-surat-image" alt="Kop Surat">
        </div>
    @endif

    {{-- JUDUL --}}
    <div class="judul-surat">
        NOTA DINAS
    </div>

    @php
        $atasan = $suratNodin->penandatangan ?? null;
        $pegawaiTugas = $suratNodin->pegawaiTugas ?? null;

        $jabatanAtasan   = $atasan->jabatan ?? '';
        $unitKerjaAtasan = $atasan->unit_kerja ?? '';

        $rawNamaAtasan = $atasan->nama ?? '';
        if (str_contains($rawNamaAtasan, ',')) {
            $parts = explode(',', $rawNamaAtasan, 2);
            $namaAtasan = mb_strtoupper(trim($parts[0])) . ', ' . trim($parts[1]);
        } else {
            $namaAtasan = mb_strtoupper(trim($rawNamaAtasan));
        }

        $pangkatAtasan = $atasan->pangkat_golongan ?? '';
        $nipAtasan     = $atasan->nip ?? '';
        $opd           = $atasan->nama_opd_indu ?? '';

        $jabatanTugas   = $pegawaiTugas->jabatan ?? '';
        $unitKerjaTugas = $pegawaiTugas->unit_kerja ?? '';

        $toBool = function ($val) {
            if (is_bool($val)) return $val;
            if (is_numeric($val)) return (int) $val === 1;
            if (is_string($val)) {
                return in_array(strtolower(trim($val)), ['1', 'true', 'on', 'yes', 'ya', 'y'], true);
            }
            return false;
        };

        $isPlt = $toBool($suratNodin->penandatangan_plt ?? false);
        $isAn  = $toBool($suratNodin->penandatangan_an  ?? false);

        if ($isPlt && $isAn) {
            $isAn = false;
        }

        $prefix    = '';
        $showTugas = false;
        $unitKerja = $unitKerjaAtasan;

        if ($isPlt) {
            $prefix    = 'Plt. ';
            $showTugas = true;
            $unitKerja = $unitKerjaTugas ?: $unitKerjaAtasan;
        } elseif ($isAn) {
            $prefix    = 'a.n. ';
            $showTugas = true;
            $unitKerja = $unitKerjaAtasan;
        }

        $prefixDari = '';
        if ($toBool($suratNodin->dari_plt ?? false)) {
            $prefixDari = 'Plt. ';
        } elseif ($toBool($suratNodin->dari_an ?? false)) {
            $prefixDari = 'a.n. ';
        }
    @endphp

    {{-- IDENTITAS SURAT --}}
    <table class="identitas">
        <tr>
            <td class="label">Yth.</td>
            <td class="colon">:</td>
            <td>{!! nl2br(e($suratNodin->kepada ?: '-')) !!}</td>
        </tr>
        <tr>
            <td class="label">Dari</td>
            <td class="colon">:</td>
            <td>{!! nl2br(e($prefixDari . ($suratNodin->dari ?: '-'))) !!}</td>
        </tr>
        <tr>
            <td class="label">Tanggal</td>
            <td class="colon">:</td>
            <td>
                {{
                    $suratNodin->tanggal
                        ? \App\Http\Controllers\SuratNodinController::formatTanggal(
                            $suratNodin->tanggal,
                            '%d %B %Y'
                          )
                        : '-'
                }}
            </td>
        </tr>
        <tr>
            <td class="label">Nomor</td>
            <td class="colon">:</td>
            <td>{{ $suratNodin->nomor ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Sifat</td>
            <td class="colon">:</td>
            <td>{{ $suratNodin->sifat ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td class="colon">:</td>
            <td>{{ $suratNodin->lampiran ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hal</td>
            <td class="colon">:</td>
            <td><strong>{{ $suratNodin->hal ?: '-' }}</strong></td>
        </tr>
    </table>

    <hr class="garis-pembatas">

    @if($suratNodin->dasar_surat)
        <div class="isi-surat">
            {!! nl2br(e($suratNodin->dasar_surat)) !!}
        </div>
    @endif

    @if($suratNodin->isi_surat)
        <div class="isi-surat">
            {!! nl2br(e($suratNodin->isi_surat)) !!}
        </div>
    @endif

    {{-- LOGIC & PENGELOMPOKAN PESERTA --}}
    @php
        $normalisasiTanggal = function ($tanggal) {
            if (!$tanggal) return '';
            try {
                return \Carbon\Carbon::parse($tanggal)->format('Y-m-d');
            } catch (\Throwable $e) {
                return (string) $tanggal;
            }
        };

        $formatTanggal = function ($tanggal, $format = '%d %B %Y') {
            if (!$tanggal) return '';
            try {
                return \App\Http\Controllers\SuratNodinController::formatTanggal($tanggal, $format);
            } catch (\Throwable $e) {
                try {
                    return \Carbon\Carbon::parse($tanggal)->format('d F Y');
                } catch (\Throwable $e2) {
                    return (string) $tanggal;
                }
            }
        };

        $formatTanggalRange = function ($awal, $akhir) use ($normalisasiTanggal, $formatTanggal) {
            $awal = $normalisasiTanggal($awal);
            $akhir = $normalisasiTanggal($akhir);

            if ($awal && $akhir) {
                $awalCarbon = \Carbon\Carbon::parse($awal);
                $akhirCarbon = \Carbon\Carbon::parse($akhir);

                if ($awalCarbon->isSameDay($akhirCarbon)) {
                    return $formatTanggal($awalCarbon, '%d %B %Y');
                }

                if ($awalCarbon->format('m') === $akhirCarbon->format('m') && $awalCarbon->format('Y') === $akhirCarbon->format('Y')) {
                    return $formatTanggal($awalCarbon, '%d') . ' s.d. ' . $formatTanggal($akhirCarbon, '%d %B %Y');
                }

                return $formatTanggal($awalCarbon, '%d %B %Y') . ' s.d. ' . $formatTanggal($akhirCarbon, '%d %B %Y');
            }

            if ($awal) {
                return $formatTanggal(\Carbon\Carbon::parse($awal), '%d %B %Y');
            }

            return '';
        };

        $pesertaList = collect($suratNodin->pesertaSuratUsulans ?? [])
            ->filter(fn($p) => $p->pegawai_id || $p->siswa_id || $p->siswa)
            ->values();

        $rawGroups = [];

        foreach ($pesertaList as $peserta) {
            $awalRaw  = $normalisasiTanggal($peserta->tgl_awal_kegiatan ?? null);
            $akhirRaw = $normalisasiTanggal($peserta->tgl_akhir_kegiatan ?? null);
            
            $dudikaVal = '';
            if (isset($peserta->dudika)) {
                $dudikaVal = is_object($peserta->dudika) ? ($peserta->dudika->nama_dudika ?? $peserta->dudika->nama ?? $peserta->dudika->name ?? '') : $peserta->dudika;
            }
            if (empty($dudikaVal)) {
                $dudikaVal = $peserta->tempat_kegiatan ?? '-';
            }
            $tempat = trim((string) $dudikaVal);

            /*
             * Satu baris peserta bisa memuat pegawai
             * dan siswa sekaligus (hasil kombinasi
             * pegawai x siswa saat penyimpanan), sehingga
             * kedua tipe diproses terpisah supaya data
             * siswa tetap ikut ditampilkan.
             */
            $entities = [];

            if ($peserta->pegawai_id && $peserta->pegawai) {
                $entities[] = [
                    'type' => 'pegawai',
                    'key'  => 'pegawai_' . $peserta->pegawai_id,
                    'data' => $peserta->pegawai,
                ];
            }

            $siswa = $peserta->siswa ?? $peserta->pesertaSiswa ?? null;

            if ($siswa) {
                $entities[] = [
                    'type' => 'siswa',
                    'key'  => 'siswa_' . ($siswa->id ?? $peserta->siswa_id ?? 'unknown'),
                    'data' => $siswa,
                ];
            } elseif (!empty($peserta->siswa_id)) {
                $entities[] = [
                    'type' => 'siswa',
                    'key'  => 'siswa_' . $peserta->siswa_id,
                    'data' => $peserta,
                ];
            }

            if (empty($entities)) {
                continue;
            }

            foreach ($entities as $entityItem) {
                $groupKey = $entityItem['key'] . '_' . $awalRaw . '_' . $akhirRaw;

                if (!isset($rawGroups[$groupKey])) {
                    $rawGroups[$groupKey] = [
                        'tanggal_formatted' => $formatTanggalRange($awalRaw, $akhirRaw),
                        'type'              => $entityItem['type'],
                        'entity'            => $entityItem['data'],
                        'tempat_list'       => [],
                    ];
                }

                if ($tempat && !in_array($tempat, $rawGroups[$groupKey]['tempat_list'])) {
                    $rawGroups[$groupKey]['tempat_list'][] = $tempat;
                }
            }
        }

        $displayGroups = array_values($rawGroups);

        $hasSiswa = collect($displayGroups)
            ->contains(function ($g) {
                return $g['type'] === 'siswa';
            });
    @endphp

    {{-- TABEL PESERTA --}}
    <table class="tabel-peserta">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 21%;">Nama {{ $hasSiswa ? 'Pegawai / Siswa' : 'Pegawai' }}</th>
                <th style="width: 15%;">{{ $hasSiswa ? 'NIP / NIS' : 'NIP' }}</th>
                <th style="width: 15%;">{{ $hasSiswa ? 'Pangkat / Gol / Kelas' : 'Pangkat / Gol' }}</th>
                <th style="width: 14%;">Jabatan</th>
                <th style="width: 15%;">Tanggal Kegiatan</th>
                <th style="width: 16%;">Tempat </th>
            </tr>
        </thead>
        
        <tbody>
            @php $nomor = 1; @endphp

            @forelse($displayGroups as $group)
                @php
                    $entity = $group['entity'];
                    $isPegawai = ($group['type'] === 'pegawai');
                    $tempatList = $group['tempat_list'];
                @endphp

                <tr>
                    <td class="text-center">{{ $nomor++ }}</td>
                    
                    {{-- Nama --}}
                    <td>
                        @if($isPegawai)
                            <div class="pegawai-nama">{{ $entity->nama ?: '-' }}</div>
                        @else
                            <div class="label-siswa">Siswa:</div>
                            <div>{{ strtoupper($entity->nama ?? '-') }}</div>
                        @endif
                    </td>

                    {{-- NIP / NIS --}}
                    <td>
                        @if($isPegawai)
                            <div>{{ $entity->nip ?: '-' }}</div>
                        @else
                            <div class="label-nis">NIS:</div>
                            <div>{{ $entity->nis ?? $entity->nisn ?? '-' }}</div>
                        @endif
                    </td>

                    {{-- Pangkat / Golongan / Kelas --}}
                    <td>
                        @if($isPegawai)
                            <div>{{ $entity->pangkat_golongan ?: '-' }}</div>
                        @else
                            <div class="label-kelas">Kelas:</div>
                            <div>{{ $entity->kelas ?? '-' }}</div>
                        @endif
                    </td>

                    {{-- Jabatan --}}
                    <td>
                        @if($isPegawai)
                            <div>{{ $entity->jabatan ?: '-' }}</div>
                        @else
                            <div class="label-siswa">Siswa Peserta Didik</div>
                        @endif
                    </td>

                    {{-- Tanggal Kegiatan --}}
                    <td class="kegiatan-tanggal">
                        {{ $group['tanggal_formatted'] ?: '-' }}
                    </td>

                    {{-- Tempat / Dudika --}}
                    <td>
                        @if(count($tempatList) > 1)
                            <ol class="dudika-ol">
                                @foreach($tempatList as $tpt)
                                    <li>{{ $tpt }}</li>
                                @endforeach
                            </ol>
                        @elseif(count($tempatList) === 1)
                            {{ $tempatList[0] }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">-</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($suratNodin->isi_surat)
    <div class="isi-surat">
        Demikian surat permohonan ini kami sampaikan atas perhatian Bapak, Kami ucapkan terima kasih.
    </div>
    @endif

    {{-- TANDA TANGAN --}}
    <div class="signature-wrapper">
        <div class="signature">
            <div class="signature-table">
                <div class="signature-row">
                    @if($isPlt || $isAn)
                        <div class="signature-prefix-cell">{{ $prefix }}</div>
                    @endif
                    <div class="signature-content-cell">
                        <div>{{ $jabatanAtasan }} {{ $unitKerja }}</div>
                        <div>{{ $opd }}</div>

                        <div class="signature-space"></div>

                        @if($showTugas && $jabatanTugas)
                            <div class="signature-tugas">{{ $jabatanTugas }}</div>
                        @endif

                        <div class="signature-name">{{ $namaAtasan }}</div>

                        @if($pangkatAtasan && $pangkatAtasan != '-')
                            <div class="signature-pangkat">{{ $pangkatAtasan }}</div>
                        @endif

                        <div class="signature-nip">NIP. {{ $nipAtasan }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="no-print">
    <a href="{{ route('surat-nodins.index') }}" class="btn-back">Kembali</a>
    <button type="button" onclick="window.print()" class="btn-print">Cetak</button>
</div>

</body>
</html>
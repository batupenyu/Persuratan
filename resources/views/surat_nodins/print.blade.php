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

        .penutup-surat {
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
            vertical-align: top;
        }

        .tabel-peserta th {
            text-align: center;
            vertical-align: middle;
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

        .daftar-siswa {
            margin: 0;
            padding-left: 15px;
        }

        .daftar-siswa li, .siswa-item {
            margin-bottom: 2px;
        }

        .kegiatan-tanggal, .kegiatan-tempat {
            vertical-align: middle !important;
            line-height: 1.3;
        }

        .kegiatan-tanggal {
            text-align: center;
        }

        .lampiran-link {
            text-align: center;
            margin: 5mm 0;
            font-size: 9pt;
        }

        /* --- BAGIAN TANDA TANGAN (YANG DIPERBAIKI) --- */
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
        }

        .signature-space {
            height: 22mm;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        /* ------------------------------------------- */

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
            html,
            body {
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
            <td>{!! nl2br(e($suratNodin->dari ?: '-')) !!}</td>
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

    {{-- LOGIC & TABEL PESERTA --}}
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
            ->filter(fn($p) => $p->pegawai_id || $p->siswa_id)
            ->values();

        $rawGroups = [];

        foreach ($pesertaList as $peserta) {
            $awalRaw  = $normalisasiTanggal($peserta->tgl_awal_kegiatan ?? null);
            $akhirRaw = $normalisasiTanggal($peserta->tgl_akhir_kegiatan ?? null);
            $tempat   = trim((string) ($peserta->tempat_kegiatan ?? ''));

            $activityKey = $awalRaw . '|' . $akhirRaw . '|' . mb_strtolower($tempat);

            if (!isset($rawGroups[$activityKey])) {
                $rawGroups[$activityKey] = [
                    'tanggal_formatted' => $formatTanggalRange($awalRaw, $akhirRaw),
                    'tempat'            => $tempat,
                    'participants'      => [],
                    'participant_keys'  => [],
                ];
            }

            if ($peserta->pegawai_id) {
                $pKey = 'pegawai_' . $peserta->pegawai_id;
                if (!in_array($pKey, $rawGroups[$activityKey]['participant_keys'])) {
                    $rawGroups[$activityKey]['participant_keys'][] = $pKey;
                    $rawGroups[$activityKey]['participants'][] = [
                        'type'    => 'pegawai',
                        'pegawai' => $peserta->pegawai ?? null,
                        'siswa'   => [],
                    ];
                }
            } elseif ($peserta->siswa_id && $peserta->siswa) {
                $pKey = 'siswa_' . $peserta->siswa_id;
                if (!in_array($pKey, $rawGroups[$activityKey]['participant_keys'])) {
                    $rawGroups[$activityKey]['participant_keys'][] = $pKey;
                    $rawGroups[$activityKey]['participants'][] = [
                        'type'    => 'siswa_only',
                        'pegawai' => null,
                        'siswa'   => [[
                            'nama'  => $peserta->siswa->nama ?? '-',
                            'nis'   => $peserta->siswa->nis ?? '-',
                            'kelas' => $peserta->siswa->kelas ?? '-',
                        ]],
                    ];
                }
            }
        }

        $displayGroups = array_values($rawGroups);
    @endphp

    <table class="tabel-peserta">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 21%;">Nama Pegawai / Siswa</th>
                <th style="width: 15%;">NIP / NIS</th>
                <th style="width: 15%;">Pangkat / Gol / Kelas</th>
                <th style="width: 14%;">Jabatan</th>
                <th style="width: 14%;">Tanggal</th>
                <th style="width: 17%;">Tempat Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @php $nomor = 1; @endphp

            @forelse($displayGroups as $group)
                @php
                    $participants = $group['participants'];
                    $rowspan = count($participants);
                    if ($rowspan === 0) $rowspan = 1;
                @endphp

                @forelse($participants as $index => $p)
                    <tr>
                        <td class="text-center">{{ $nomor++ }}</td>
                        <td>
                            @if($p['type'] === 'pegawai' && $p['pegawai'])
                                <div class="pegawai-nama">{{ $p['pegawai']->nama ?: '-' }}</div>
                            @elseif(count($p['siswa']))
                                <div class="label-siswa">Siswa:</div>
                                <ol class="daftar-siswa">
                                    @foreach($p['siswa'] as $itemSiswa)
                                        <li>{{ strtoupper($itemSiswa['nama']) }}</li>
                                    @endforeach
                                </ol>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($p['type'] === 'pegawai' && $p['pegawai'])
                                <div>{{ $p['pegawai']->nip ?: '-' }}</div>
                            @elseif(count($p['siswa']))
                                <div class="label-nis">NIS:</div>
                                @foreach($p['siswa'] as $itemSiswa)
                                    <div class="siswa-item">{{ $itemSiswa['nis'] ?: '-' }}</div>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($p['type'] === 'pegawai' && $p['pegawai'])
                                <div>{{ $p['pegawai']->pangkat_golongan ?: '-' }}</div>
                            @elseif(count($p['siswa']))
                                <div class="label-kelas">Kelas:</div>
                                @foreach($p['siswa'] as $itemSiswa)
                                    <div class="siswa-item">{{ $itemSiswa['kelas'] ?: '-' }}</div>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($p['type'] === 'pegawai' && $p['pegawai'])
                                <div>{{ $p['pegawai']->jabatan ?: '-' }}</div>
                            @elseif(count($p['siswa']))
                                <div class="label-siswa">Siswa</div>
                            @else
                                -
                            @endif
                        </td>

                        @if($index === 0)
                            <td class="kegiatan-tanggal" rowspan="{{ $rowspan }}">
                                {{ $group['tanggal_formatted'] ?: '-' }}
                            </td>
                            <td class="kegiatan-tempat" rowspan="{{ $rowspan }}">
                                {{ $group['tempat'] ?: '-' }}
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">-</td></tr>
                @endforelse
            @empty
                <tr><td colspan="7" class="text-center">Tidak ada data peserta.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- @if(count($displayGroups) > 15)
        <div class="lampiran-link">
            <em>Daftar lengkap peserta tercantum pada lampiran.</em>
        </div>
    @endif --}}

    @if($suratNodin->isi_surat)
    <div class="isi-surat">
        {{-- {!! nl2br(e($suratNodin->isi_surat)) !!} --}}
        Demikian surat permohonan ini kami sampaikan atas perhatian Bapak, Kami ucapkan terima kasih.
    </div>
    @endif

    {{-- TANDA TANGAN --}}
    @php
        $atasan = $suratNodin->penandatangan;
        $pegawaiTugas = $suratNodin->pegawaiTugas;

        $jabatanAtasan   = $atasan->jabatan ?? '';
        $unitKerjaAtasan = $atasan->unit_kerja ?? '';
        $namaAtasan      = $atasan->nama ?? '';
        $pangkatAtasan   = $atasan->pangkat_golongan ?? '';
        $nipAtasan       = $atasan->nip ?? '';
        $opd             = $atasan->nama_opd_indu ?? '';

        $jabatanTugas   = $pegawaiTugas->jabatan ?? '';
        $unitKerjaTugas = $pegawaiTugas->unit_kerja ?? '';

        $isPlt = $suratNodin->penandatangan_plt ?? false;
        $isAn  = $suratNodin->penandatangan_an ?? false;

        if ($isPlt && $isAn) {
            $isAn = false;
        }

        $prefix = '';
        $showTugas = false;
        $unitKerja = '';

        if ($isPlt) {
            $prefix    = 'Plt. ';
            $showTugas = true;
            $unitKerja = $unitKerjaTugas ?: $unitKerjaAtasan;
        } elseif ($isAn) {
            $prefix    = 'a.n. ';
            $showTugas = true;
            $unitKerja = $unitKerjaAtasan;
        } else {
            $prefix    = '';
            $showTugas = false;
            $unitKerja = $unitKerjaAtasan;
        }
    @endphp

    <div class="signature-wrapper">
        <div class="signature">
            <div class="signature-table">
                <div class="signature-row">
                    @if($isPlt || $isAn)
                        <div class="signature-prefix-cell">{{ $prefix }}</div>
                    @endif
                    <div class="signature-content-cell">
                        {{ $jabatanAtasan }}
                        {{-- <br> --}}
                        {{ $unitKerja }}
                        <br>
                        {{ $opd }}
                        {{-- @if(!empty($opd))
                            {{ $opd }}
                        @endif --}}

                        <div class="signature-space"></div>

                        @if($showTugas && $jabatanTugas)
                            <div>{{ $jabatanTugas }}</div>
                        @endif

                        <div class="signature-name">{{ $namaAtasan }}</div>

                        @if($pangkatAtasan && $pangkatAtasan != '-')
                            <div>{{ $pangkatAtasan }}</div>
                        @endif

                        <div>NIP. {{ $nipAtasan }}</div>
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
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Lampiran Surat Nodin - {{ $suratNodin->hal }}</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            font-size: 10pt;
        }

        .header-info {
            margin-bottom: 20px;
            line-height: 1.0;
            padding-left: 500px;
            font-style: italic;
            page-break-after: avoid;
        }

        .header-info table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .header-info td {
            padding: 2px 0;
            vertical-align: top;
            border: none;
        }

        .header-info .label {
            width: 100px;
        }

        .header-info .colon {
            width: 10px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 20px;
            page-break-after: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            page-break-inside: auto;
        }

        table tr {
            page-break-inside: avoid;
        }

        table thead {
            display: table-header-group;
        }

        table tbody {
            display: table-row-group;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center !important;
        }

        .signature-container {
            margin-top: 10px;
            float: right;
            width: 50%;
            text-align: left;
            font-size: 11pt;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 11pt;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .page {
            width: 297mm;
            min-height: 210mm;
            padding: 15mm 18mm;
            margin: 20px auto;
            background: white;
            position: relative;
            box-shadow: 0 0 6px rgba(0,0,0,0.3);
        }

        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .page {
                box-shadow: none;
                margin: 0;
                padding-top: 0;
                page-break-after: always;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

<div class="page">

    {{-- =====================================================
         HEADER
         ===================================================== --}}
    <div class="header-info">
        <table>
            <tr>
                <td class="label">LAMPIRAN I</td>
                <td class="colon">:</td>
                <td>Kepala Dinas Pendidikan Prov. Kepulauan Bangka Belitung</td>
            </tr>
            <tr>
                <td class="label">NOMOR</td>
                <td class="colon">:</td>
                <td>{{ $suratNodin->nomor ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">TANGGAL</td>
                <td class="colon">:</td>
                <td>
                    {{ $suratNodin->tanggal
                        ? \App\Http\Controllers\SuratNodinController::formatTanggal(
                            $suratNodin->tanggal,
                            '%d %B %Y'
                        )
                        : '-'
                    }}
                </td>
            </tr>
        </table>
    </div>

    {{-- =====================================================
         JUDUL
         ===================================================== --}}
    <div class="title">
        DAFTAR NAMA
        {{ strtoupper($suratNodin->hal ?: 'UNDANGAN') }}[cite: 5]
    </div>

    {{-- =====================================================
         TABEL PESERTA
         ===================================================== --}}
    <table>
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:22%;">Nama Pegawai / Siswa</th>
                <th style="width:16%;">NIP / NIS</th>
                <th style="width:14%;">Pangkat / Gol / Kelas</th>
                <th style="width:14%;">Jabatan</th>
                <th style="width:14%;">Tanggal Kegiatan</th>
                <th style="width:15%;">Tempat Kegiatan</th>
            </tr>
        </thead>

        <tbody>
        @php
            $pesertaList = $suratNodin->pesertaSuratUsulans ?? [];
            $nomorPeserta = 1;
        @endphp

        @forelse($pesertaList as $peserta)
            @php
                $pegawai = $peserta->pegawai ?? null;
                $siswa   = $peserta->siswa ?? null;

                $awal  = $peserta->tgl_awal_kegiatan ? \Carbon\Carbon::parse($peserta->tgl_awal_kegiatan) : null;
                $akhir = $peserta->tgl_akhir_kegiatan ? \Carbon\Carbon::parse($peserta->tgl_akhir_kegiatan) : null;

                if ($awal && $akhir && $awal->isSameDay($akhir)) {
                    $tanggalText = \App\Http\Controllers\SuratNodinController::formatTanggal($awal, '%d %B %Y');
                } elseif ($awal && $akhir) {
                    $tanggalText = \App\Http\Controllers\SuratNodinController::formatTanggal($awal, '%d %B %Y') . ' s.d. ' . \App\Http\Controllers\SuratNodinController::formatTanggal($akhir, '%d %B %Y');
                } elseif ($awal) {
                    $tanggalText = \App\Http\Controllers\SuratNodinController::formatTanggal($awal, '%d %B %Y');
                } else {
                    $tanggalText = '-';
                }

                $tempat = trim((string) ($peserta->tempat_kegiatan ?? '-'));
            @endphp

            <tr>
                <td class="text-center">{{ $nomorPeserta++ }}</td>

                {{-- Nama --}}
                <td>
                    @if($pegawai)
                        {{ $pegawai->nama ?: '-' }}
                    @elseif($siswa)
                        {{ strtoupper($siswa->nama ?? '-') }}
                    @else
                        {{ $peserta->nama_peserta ?? '-' }}
                    @endif
                </td>

                {{-- NIP / NIS --}}
                <td>
                    @if($pegawai)
                        {{ $pegawai->nip ?: '-' }}
                    @elseif($siswa)
                        {{ $siswa->nis ?? $siswa->nisn ?? '-' }}
                    @else
                        {{ $peserta->nomor_identitas ?? '-' }}
                    @endif
                </td>

                {{-- Pangkat / Gol / Kelas --}}
                <td>
                    @if($pegawai)
                        {{ $pegawai->pangkat_golongan ?? $pegawai->pangkat ?? '-' }}
                    @elseif($siswa)
                        {{ $siswa->kelas ?? '-' }}
                    @else
                        -
                    @endif
                </td>

                {{-- Jabatan --}}
                <td>
                    @if($pegawai)
                        {{ $pegawai->jabatan ?: '-' }}
                    @elseif($siswa)
                        Siswa
                    @else
                        {{ $peserta->jabatan ?? '-' }}
                    @endif
                </td>

                {{-- Tanggal Kegiatan --}}
                <td class="text-center">
                    {{ $tanggalText }}
                </td>

                {{-- Tempat Kegiatan --}}
                <td>
                    {{ $tempat }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data peserta.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- =====================================================
         TANDA TANGAN
         ===================================================== --}}
    <div class="clearfix">
        <div class="signature-container">
            <div class="signature-title">
                @php
                    $atasan = $suratNodin->penandatangan;
                    $pegawaiTugas = $suratNodin->pegawaiTugas;

                    $jabatanAtasan = $atasan->jabatan ?? '';
                    $unitKerjaAtasan = $atasan->unit_kerja ?? '';
                    $nama = $atasan->nama ?? '';
                    $pangkat = $atasan->pangkat_golongan ?? '';
                    $nip = $atasan->nip ?? '';

                    $jabatanTugas = $pegawaiTugas->jabatan ?? '';
                    $unitKerjaTugas = $pegawaiTugas->unit_kerja ?? '';

                    $isPlt = $suratNodin->penandatangan_plt ?? false;
                    $isPlh = $suratNodin->penandatangan_plh ?? false;
                    $isAn  = $suratNodin->penandatangan_an  ?? false;

                    if ($isPlt) {
                        $isPlh = false;
                        $isAn = false;
                    } elseif ($isPlh) {
                        $isAn = false;
                    }

                    $prefix = '';
                    $indent = false;
                    $showTugas = false;
                    $unitKerja = '';

                    if ($isPlt) {
                        $prefix = 'Plt.' . html_entity_decode('&nbsp;');
                        $indent = true;
                        $showTugas = true;
                        $unitKerja = $unitKerjaTugas ?: $unitKerjaAtasan;
                    } elseif ($isPlh) {
                        $prefix = 'Plh.' . html_entity_decode('&nbsp;');
                        $indent = true;
                        $showTugas = true;
                        $unitKerja = $unitKerjaTugas ?: $unitKerjaAtasan;
                    } elseif ($isAn) {
                        $prefix = 'a.n.' . html_entity_decode('&nbsp;');
                        $indent = true;
                        $showTugas = true;
                        $unitKerja = $unitKerjaAtasan;
                    } else {
                        $prefix = '';
                        $indent = false;
                        $showTugas = false;
                        $unitKerja = $unitKerjaAtasan;
                    }

                    $indentChar = html_entity_decode('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
                @endphp

                {{ $prefix . $jabatanAtasan }}
                <br>
                {{ $indent ? $indentChar . $unitKerja : $unitKerja }}
                <br><br><br>

                @if($showTugas && $jabatanTugas)
                    <br>
                    {{ $indentChar . $jabatanTugas }}
                @endif

                <br>
                {{ $indent ? $indentChar . $nama : $nama }}

                @if($pangkat && $pangkat != '-')
                    <br>
                    {{ $indent ? $indentChar . $pangkat : $pangkat }}
                @endif

                <br>
                {{ $indent ? $indentChar . 'NIP. ' . $nip : 'NIP. ' . $nip }}
            </div>
        </div>
    </div>

</div>

{{-- =========================================================
     TOMBOL
     ========================================================= --}}
<div class="no-print">
    <a href="{{ route('surat-nodins.print', $suratNodin) }}" style="display:inline-block; margin-right:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">
        Kembali
    </a>
    <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">
        Cetak
    </button>
</div>

</body>
</html>
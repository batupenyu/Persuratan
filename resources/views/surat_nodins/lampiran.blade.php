<!doctype html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <title>
        Lampiran Surat Nodin -
        {{ $suratNodin->hal }}
    </title>


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

            text-align: center;

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


        .signature-name {

            font-weight: bold;

            font-size: 11pt;

        }


        .clearfix::after {

            content: "";

            clear: both;

            display: table;

        }


        .signature-unit {

            padding-left: 26px;

            display: block;

            font-size: 11pt;

        }


        .signature-body {

            font-weight: bold;

            font-size: 11pt;

        }


        .signature-nip {

            margin-top: 5px;

            font-size: 11pt;

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

                <td class="label">
                    LAMPIRAN I
                </td>

                <td class="colon">
                    :
                </td>

                <td>
                    Kepala Dinas Pendidikan
                    Prov. Kepulauan Bangka Belitung
                </td>

            </tr>


            <tr>

                <td class="label">
                    NOMOR
                </td>

                <td class="colon">
                    :
                </td>

                <td>
                    {{ $suratNodin->nomor ?: '-' }}
                </td>

            </tr>


            <tr>

                <td class="label">
                    TANGGAL
                </td>

                <td class="colon">
                    :
                </td>

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
        {{ strtoupper($suratNodin->hal ?: 'UNDANGAN') }}

    </div>


    {{-- =====================================================
         TABEL PESERTA
         ===================================================== --}}

    <table>

        <thead>

            <tr>

                <th style="width:5%;">
                    No
                </th>

                <th style="width:22%;">
                    Nama
                </th>

                <th style="width:18%;">
                    NIP/NIS
                </th>

                <th style="width:15%;">
                    Pangkat / Gol
                </th>

                <th style="width:15%;">
                    Jabatan
                </th>

                <th style="width:12%;">
                    Tanggal Kegiatan
                </th>

                <th style="width:13%;">
                    Tempat Kegiatan
                </th>

            </tr>

        </thead>


        <tbody>

        @php

            $pesertaList =
                $suratNodin->pesertaSuratUsulans
                ->values();


            /*
             * Kelompok berdasarkan pegawai.
             */

            $kelompokPegawai =
                $pesertaList->groupBy(
                    function ($peserta) {

                        return
                            $peserta->pegawai_id
                            ?: 'tanpa_pegawai';

                    }
                );


            $nomorPeserta = 1;

        @endphp


        @forelse($kelompokPegawai as $anggota)

            @php

                /*
                 * Pegawai satu kali.
                 */

                $pegawai =
                    $anggota
                        ->firstWhere(
                            'pegawai_id',
                            '!=',
                            null
                        )
                        ?->pegawai;


                /*
                 * Siswa unik.
                 */

                $siswaList =
                    $anggota

                        ->filter(
                            function ($peserta) {

                                return
                                    !empty(
                                        $peserta->siswa_id
                                    )
                                    &&
                                    !empty(
                                        $peserta->siswa
                                    );

                            }
                        )

                        ->unique('siswa_id')

                        ->values();


                /*
                 * Kegiatan.
                 */

                $kegiatan =
                    $anggota->first();


                $awal =
                    $kegiatan->tgl_awal_kegiatan
                        ? \Carbon\Carbon::parse(
                            $kegiatan->tgl_awal_kegiatan
                        )
                        : null;


                $akhir =
                    $kegiatan->tgl_akhir_kegiatan
                        ? \Carbon\Carbon::parse(
                            $kegiatan->tgl_akhir_kegiatan
                        )
                        : null;


                $tempat =
                    trim(
                        (string)
                        (
                            $kegiatan->tempat_kegiatan
                            ?? ''
                        )
                    );


                /*
                 * Format tanggal.
                 */

                if (
                    $awal &&
                    $akhir &&
                    $awal->isSameDay($akhir)
                ) {

                    $tanggalText =
                        \App\Http\Controllers\SuratNodinController::formatTanggal(
                            $awal,
                            '%d %B %Y'
                        );

                } elseif ($awal && $akhir) {

                    $tanggalText =
                        \App\Http\Controllers\SuratNodinController::formatTanggal(
                            $awal,
                            '%d %B %Y'
                        )
                        .
                        ' s.d. '
                        .
                        \App\Http\Controllers\SuratNodinController::formatTanggal(
                            $akhir,
                            '%d %B %Y'
                        );

                } elseif ($awal) {

                    $tanggalText =
                        \App\Http\Controllers\SuratNodinController::formatTanggal(
                            $awal,
                            '%d %B %Y'
                        );

                } else {

                    $tanggalText = '-';

                }

            @endphp


            {{-- =================================================
                 PEGAWAI
                 ================================================= --}}

            @if($pegawai)

                <tr>

                    <td class="text-center">

                        {{ $nomorPeserta++ }}

                    </td>


                    <td>

                        {{ $pegawai->nama ?: '-' }}

                    </td>


                    <td>

                        {{ $pegawai->nip ?: '-' }}

                    </td>


                    <td>

                        @php

                            $pangkat =
                                $pegawai->pangkat
                                ?? '';

                            $golongan =
                                $pegawai->golongan
                                ?? '';


                            $pangkatGolongan =
                                $pangkat || $golongan

                                    ? trim(
                                        ($pangkat ?: '') .
                                        (
                                            $golongan
                                                ? ', ' . $golongan
                                                : ''
                                        ),
                                        ', '
                                    )

                                    : '-';

                        @endphp


                        {{ $pangkatGolongan }}

                    </td>


                    <td>

                        {{ $pegawai->jabatan ?: '-' }}

                    </td>


                    <td>

                        {{ $tanggalText }}

                    </td>


                    <td>

                        {{ $tempat ?: '-' }}

                    </td>

                </tr>

            @endif


            {{-- =================================================
                 SISWA
                 ================================================= --}}

            @foreach($siswaList as $siswa)

                <tr>

                    <td class="text-center">

                        {{ $nomorPeserta++ }}

                    </td>


                    <td>

                        {{ strtoupper(
                            $siswa->nama ?: '-'
                        ) }}

                    </td>


                    <td>

                        {{ $siswa->nis ?: '-' }}

                    </td>


                    <td>

                        {{ $siswa->kelas ?: '-' }}

                    </td>


                    <td>

                        Siswa

                    </td>


                    <td>

                        {{ $tanggalText }}

                    </td>


                    <td>

                        {{ $tempat ?: '-' }}

                    </td>

                </tr>

            @endforeach


        @empty

            <tr>

                <td
                    colspan="7"
                    class="text-center"
                >

                    Tidak ada data peserta.

                </td>

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

                    $atasan =
                        $suratNodin->penandatangan;

                    $pegawaiTugas =
                        $suratNodin->pegawaiTugas;


                    $jabatanAtasan =
                        $atasan->jabatan ?? '';

                    $unitKerjaAtasan =
                        $atasan->unit_kerja ?? '';

                    $nama =
                        $atasan->nama ?? '';

                    $pangkat =
                        $atasan->pangkat_golongan ?? '';

                    $nip =
                        $atasan->nip ?? '';


                    $jabatanTugas =
                        $pegawaiTugas->jabatan ?? '';

                    $unitKerjaTugas =
                        $pegawaiTugas->unit_kerja ?? '';


                    $isPlt =
                        $suratNodin->penandatangan_plt
                        ?? false;

                    $isPlh =
                        $suratNodin->penandatangan_plh
                        ?? false;

                    $isAn =
                        $suratNodin->penandatangan_an
                        ?? false;


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

                        $prefix =
                            'Plt.' .
                            html_entity_decode('&nbsp;');

                        $indent = true;

                        $showTugas = true;

                        $unitKerja =
                            $unitKerjaTugas
                            ?: $unitKerjaAtasan;

                    } elseif ($isPlh) {

                        $prefix =
                            'Plh.' .
                            html_entity_decode('&nbsp;');

                        $indent = true;

                        $showTugas = true;

                        $unitKerja =
                            $unitKerjaTugas
                            ?: $unitKerjaAtasan;

                    } elseif ($isAn) {

                        $prefix =
                            'a.n.' .
                            html_entity_decode('&nbsp;');

                        $indent = true;

                        $showTugas = true;

                        $unitKerja =
                            $unitKerjaAtasan;

                    } else {

                        $prefix = '';

                        $indent = false;

                        $showTugas = false;

                        $unitKerja =
                            $unitKerjaAtasan;

                    }


                    $indentChar =
                        html_entity_decode(
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                        );

                @endphp


                {{ $prefix . $jabatanAtasan }}


                <br>


                {{
                    $indent
                        ? $indentChar . $unitKerja
                        : $unitKerja
                }}


                <br><br><br>


                @if($showTugas && $jabatanTugas)

                    <br>

                    {{ $indentChar . $jabatanTugas }}

                @endif


                <br>


                {{
                    $indent
                        ? $indentChar . $nama
                        : $nama
                }}


                @if($pangkat && $pangkat != '-')

                    <br>

                    {{
                        $indent
                            ? $indentChar . $pangkat
                            : $pangkat
                    }}

                @endif


                <br>


                {{
                    $indent
                        ? $indentChar . 'NIP. ' . $nip
                        : 'NIP. ' . $nip
                }}

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     TOMBOL
     ========================================================= --}}

<div class="no-print">

    <a
        href="{{ route(
            'surat-nodins.print',
            $suratNodin
        ) }}"
        style="
            display:inline-block;
            margin-right:0.5rem;
            background:#6b7280;
            color:#fff;
            text-decoration:none;
            padding:0.6rem 1.4rem;
            border-radius:4px;
            font-size:0.95rem;
        "
    >
        Kembali
    </a>


    <button
        onclick="window.print()"
        style="
            background:#2563eb;
            color:#fff;
            border:none;
            padding:0.6rem 1.4rem;
            border-radius:4px;
            font-size:0.95rem;
            cursor:pointer;
        "
    >
        Cetak
    </button>

</div>


</body>
</html>
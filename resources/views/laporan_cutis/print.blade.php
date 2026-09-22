<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Kendali Cuti - {{ $laporanCuti->asn->nama ?? 'Pegawai' }}</title>
    <style>
        @page {
            size: 297mm 210mm;
            margin: 0.5cm 2cm 1cm 2cm;
        }
        body {
            background-color: #525659;
            font-family: "Helvetica", sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
        }
        .page {
            width: 297mm;
            min-height: 210mm;
            padding: 10mm 18mm;
            margin: 20px auto;
            background: white;
            position: relative;
            box-shadow: 0 0 6px rgba(0,0,0,0.3);
            box-sizing: border-box;
        }
        .no-print { margin-top: 20px; text-align: center; }
        @media print {
            body { background: white; }
            .page {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }
            .no-print { display: none !important; }
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h4 {
            font-weight: bold;
            margin: 0;
            padding: 0;
        }
        .employee-details {
            margin-bottom: 20px;
        }
        .employee-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .employee-details td {
            padding: 0px 0;
            vertical-align: top;
        }
        .employee-details .label-col {
            width: 150pt;
            white-space: nowrap;
        }
        .employee-details .colon-col {
            width: 10pt;
            text-align: center;
        }
        .employee-details .value-col {
            width: auto;
            white-space: nowrap;
        }
        .leave-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .leave-table th,
        .leave-table td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            vertical-align: middle;
        }
        .report-page {
            break-before: page;
            page-break-before: always;
        }
        .report-page:first-child,
        .report-page-first {
            break-before: auto;
            page-break-before: auto;
        }
        .leave-table th {
            background-color: #f2f2f2;
        }
        .left-section {
            width: 48%;
            float: left;
            text-align: left;
        }
        .right-section {
            width: 48%;
            float: right;
            text-align: left;
        }
        .clear-float {
            clear: both;
        }
        .keterangan {
            margin-top: 20px;
            font-size: 9pt;
        }
        .keterangan p {
            margin: 0;
            padding: 0;
        }
        .signature-block {
            margin-top: 20px;
        }
        .signature-block p {
            margin: 0;
            padding: 0;
        }
        .signature-block .jabatan {
            margin-bottom: 50px;
        }
        .signature-block .nama {
            text-decoration: underline;
            font-weight: bold;
        }
        .signature-block .nip {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="page container">
        <div class="header">
            <h4>KARTU KENDALI CUTI PEGAWAI NEGERI SIPIL</h4>
            <h4>SMK NEGERI 1 KOBA</h4>
            <h4>CABANG DINAS PENDIDIKAN WILAYAH I</h4>
            <h4>DINAS PENDIDIKAN PROVINSI KEPULAUAN BANGKA BELITUNG</h4>
            <h4 style="margin-top: 10px">
                TAHUN {{ $laporanCuti->tahun ?: date('Y') }}
            </h4>
        </div>
        <br />
        <div class="employee-details">
            <table>
                <tr>
                    <td class="label-col">Nama</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $laporanCuti->asn->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">NIP</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $laporanCuti->asn->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Pangkat/Gol. Ruang</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $laporanCuti->asn->pangkat ?? '' }},{{ $laporanCuti->asn->golongan ?? '' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Jabatan</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $laporanCuti->asn->jabatan ?? ($laporanCuti->asn->jabatan ?? ($laporanCuti->asn->jenis_ptk ?? '-')) }}</td>
                </tr>
                <tr>
                    <td class="label-col">Unit Kerja</td>
                    <td class="colon-col">:</td>
                    <td class="value-col">{{ $laporanCuti->asn->unit_kerja ?? '-' }}</td>
                </tr>
            </table>
        </div>

        @if($pages)
            @foreach($pages as $page)
            <table class="leave-table">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th colspan="2">Surat Izin/Surat Keputusan</th>
                        <th colspan="2">Lamanya</th>
                        <th rowspan="2">Jenis Cuti</th>
                        <th rowspan="2">Paraf Pegawai Kepegawaian</th>
                        <th colspan="3">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Dari Tanggal</th>
                        <th>Sampai Tanggal</th>
                        <th>ATB</th>
                        <th>LHC</th>
                        <th>STB</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($page['rows'] as $item)
                    <tr>
                        <td>{{ $item['number'] }}</td>
                        {{-- <td>{{ $item['surat_cuti']->nomor_surat ?: '-' }}</td> --}}
                        <td>-</td>
                        <td>{{ $item['surat_cuti']->tanggal_surat ? \App\Http\Controllers\SuratCutiController::formatTanggal($item['surat_cuti']->tanggal_surat, '%d-%m-%Y') : '-' }}</td>
                        <td>{{ $item['surat_cuti']->tanggal_mulai_cuti ? \App\Http\Controllers\SuratCutiController::formatTanggal($item['surat_cuti']->tanggal_mulai_cuti, '%d-%m-%Y') : '-' }}</td>
                        <td>{{ $item['surat_cuti']->tanggal_selesai_cuti ? \App\Http\Controllers\SuratCutiController::formatTanggal($item['surat_cuti']->tanggal_selesai_cuti, '%d-%m-%Y') : '-' }}</td>
                        <td>{{ $item['surat_cuti']->jenis_cuti ?: '-' }}</td>
                        <td></td>
                        <td>{{ $item['atb_for_row'] }}</td>
                        <td>{{ $item['lhc'] }}</td>
                        <td>{{ $item['stb_for_row'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if(!$loop->last)
            <div class="report-page"></div>
            @endif
            @endforeach
        @else
            <table class="leave-table">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th colspan="2">Surat Izin/Surat Keputusan</th>
                        <th colspan="2">Lamanya</th>
                        <th rowspan="2">Jenis Cuti</th>
                        <th rowspan="2">Paraf Pegawai Kepegawaian</th>
                        <th colspan="3">Keterangan</th>
                    </tr>
                    <tr>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Dari Tanggal</th>
                        <th>Sampai Tanggal</th>
                        <th>ATB</th>
                        <th>LHC</th>
                        <th>STB</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">Tidak ada data cuti.</td>
                    </tr>
                </tbody>
            </table>
        @endif

        <div style="width: 100%">
            <div class="left-section">
                <div class="keterangan">
                    <p>
                        ATB : Akumulasi Sisa Tahun Berjalan <br />
                        LHC : Lama Hari Cuti <br />
                        STB : Sisa Cuti Tahun Berjalan <br />
                        Alokasi Awal Tahun N: {{ $laporanCuti->alokasi_awal_tahun_n ?? 0 }} <br />
                        Alokasi Awal Tahun N-1: {{ $laporanCuti->alokasi_awal_tahun_n_1 ?? 0 }} <br />
                        Alokasi Awal Tahun N-2: {{ $laporanCuti->alokasi_awal_tahun_n_2 ?? 0 }} <br />
                        Total Alokasi Awal: {{ $laporanCuti->total_alokasi_awal ?? 0 }}
                    </p>
                </div>
            </div>
            <div class="right-section">
                <div class="signature-block" style="width: 250pt; margin-left: auto; text-align: center">
                    <p>Mengetahui,</p>
                    <p class="penandatangan">
                        {{ $laporanCuti->penandatangan->tugas_tambahan ?? ($laporanCuti->penandatangan->jabatan ?? ($laporanCuti->penandatangan->pangkat_golongan ?? '')) }} {{$laporanCuti->penandatangan->unit_kerja  ?? ''}}<br /><br /><br />
                        {{ $laporanCuti->penandatangan->nama ?? '' }}<br />
                        {{ $laporanCuti->penandatangan->pangkat_golongan ?? '' }}<br />
                        NIP. {{ $laporanCuti->penandatangan->nip ?? '' }}
                    </p>
                </div>
            </div>
            <div class="clear-float"></div>
        </div>
    </div>

    <div class="no-print" style="text-align:center; margin-top:20px;padding-left:15px">
    <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
    <a href="{{ route('laporan-cutis.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Kembali</a>
</div>
</body>
</html>

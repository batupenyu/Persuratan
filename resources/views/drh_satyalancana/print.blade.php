<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Riwayat Hidup - Usul Tanda Kehormatan</title>
    <style>
        body {
                    background-color: #525659;
            font-family: Arial, sans-serif;
            line-height: 1.4;
            margin: 40px;
            color: #000;
        }
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 10px;
            vertical-align: top;
            font-size: 13px;
        }
        .center-text {
            text-align: center;
        }
        .strike {
            text-decoration: line-through;
        }
        .signature-container {
            width: 100%;
            margin-top: 20px;
            font-size: 13px;
            padding-left: 50px;
        }
        .signature-table {
            width: 100%;
            border: none;
        }
        .signature-table td {
            border: none;
            padding: 0;
            width: 50%;
        }
        .space-tg {
            height: 15px;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 18mm;
            margin: 20px auto;
            background: white;
            position: relative;
            box-shadow: 0 0 6px rgba(0,0,0,0.3);
        }
        .st-page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 18mm;
            margin: 20px auto;
            background: white;
            position: relative;
            box-shadow: 0 0 6px rgba(0,0,0,0.3);
        }
        .st-page .header-table,
        .st-page table,
        .st-page th,
        .st-page td {
            border: none !important;
        }
        .st-page .header-table {
            border-bottom: 4px double #000;
        }
        body, .page, .st-page {
            font-size: 14pt;
        }
        body *, .page *, .st-page * {
            font-size: 14pt !important;
        }
        .st-page .kop-surat-container {
            text-align: center;
            margin-bottom: 20px;
            margin-left: 0;
            margin-right: 0;
        }
        .st-page .kop-surat-image {
            width: 100%;
            max-width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: contain;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 4px double #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        .logo-cell {
            width: 12%;
            vertical-align: middle;
            text-align: center;
        }
        .logo-img {
            width: 70px;
            height: auto;
        }
        .text-cell {
            width: 88%;
            text-align: center;
        }
        .instansi-prov {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .nama-dinas {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .alamat-dinas {
            font-size: 11px;
            margin: 3px 0 0 0;
        }
        .judul-surat {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 16px;
            margin-top: 25px;
            margin-bottom: 0;
            text-decoration: underline;
        }
        .nomor-surat {
            text-align: center;
            font-size: 14px;
            margin-top: 2px;
            margin-bottom: 30px;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .content-table td {
            vertical-align: top;
            padding: 4px 0;
        }
        .label-col {
            width: 15%;
            font-weight: bold;
        }
        .titik-dua-col {
            width: 3%;
            text-align: center;
        }
        .isi-col {
            width: 82%;
        }
        .sub-content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .sub-label-col {
            width: 20%;
        }
        .sub-titik-dua-col {
            width: 4%;
            text-align: center;
        }
        .sub-isi-col {
            width: 76%;
        }
        .menugaskan {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            margin: 25px 0;
            letter-spacing: 1px;
        }
        .ordered-list {
            margin: 0;
            padding-left: 20px;
        }
        .ordered-list li {
            padding-bottom: 5px;
        }
        .ttd-container {
            float: right;
            width: 45%;
            margin-top: 40px;
            font-size: 14px;
        }
        .ttd-tempat-tanggal {
            margin-bottom: 20px;
        }
        .ttd-jabatan {
            font-weight: bold;
            /* text-transform: uppercase; */
            line-height: 1.3;
        }
        .ttd-spasi {
            height: 80px;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        .ttd-nip {
            margin: 0;
        }
        @media print {
            body { background: white; }
            .page, .st-page {
                box-shadow: none;
                margin: 0;
                page-break-after: always;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    @php
        $asn = $drh->asn ?? new class {
            public function __get($key) { return null; }
        };
        $fmt = [\App\Http\Controllers\DrhSatyalancanaController::class, 'formatTanggal'];
    @endphp

    <div class="page">
    <!-- <div class="kop-surat-container">
        @if($kopSuratBase64)
            <img src="{{ $kopSuratBase64 }}" alt="Kop Surat" class="kop-surat-image">
        @endif
    </div> -->

    <div class="header">
        DAFTAR RIWAYAT HIDUP<br>
        USUL TANDA KEHORMATAN SATYALANCANA KARYA SATYA
    </div>

    <table>
        <tbody>
            <tr>
                <td class="center-text" style="width: 5%;">1.</td>
                <td style="width: 45%;">Nama Lengkap</td>
                <td style="width: 50%; font-weight: bold;">{{ $asn->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td class="center-text">2.</td>
                <td>Tempat, Tanggal Lahir</td>
                <td>{{ $asn->tempat_lahir ?? '-' }}, {{ $fmt($asn->tanggal_lahir, '%d-%m-%Y') }}</td>
            </tr>
            <tr>
                <td class="center-text">3.</td>
                <td>NIP</td>
                <td>{{ $asn->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="center-text">4.</td>
                <td>NIP Lama (jika ada)</td>
                <td>{{ $drh->nip_lama ?: '-' }}</td>
            </tr>
            <tr>
                <td class="center-text">5.</td>
                <td>Pendidikan Terakhir</td>
                <td>{{ $drh->pendidikan_terakhir ?: '-' }}</td>
            </tr>
            <tr>
                <td class="center-text">6.</td>
                <td>Pangkat, Gol. Ruang Terakhir (TMT)</td>
                <td>{{ $drh->pangkat ?: '-' }} ({{ $fmt($drh->tmt_pangkat, '%d-%m-%Y') }})</td>
            </tr>
            <tr>
                <td class="center-text">7.</td>
                <td>No. SK CPNS (Tanggal SK / TMT)</td>
                <td>No. {{ $drh->no_sk_cpns ?: '-' }} ( {{ $fmt($drh->tgl_sk_cpns, '%d-%m-%Y') }} / {{ $fmt($drh->tmt_cpns, '%d-%m-%Y') }})</td>
            </tr>
            <tr>
                <td class="center-text">8.</td>
                <td>Jabatan Terakhir (TMT)</td>
                <td>{{ $drh->jabatan_terakhir ?: '-' }} ({{ $fmt($drh->tmt_jabatan, '%d-%m-%Y') }})</td>
            </tr>
            <tr>
                <td class="center-text">9.</td>
                <td>Jenis Kelamin</td>
                <td>(<span class="{{ ($asn->jk ?? '') == 'P' ? 'strike' : '' }}">Pria</span> / <span class="{{ ($asn->jk ?? '') == 'L' ? 'strike' : '' }}">Wanita</span>, coret yang tidak perlu)</td>
            </tr>
            <tr>
                <td class="center-text">10.</td>
                <td>Tanda Kehormatan yang sudah dimiliki (Nomor dan tanggal Keppres)</td>
                <td>{{ $drh->tanda_kehormatan ? ('Sudah mendapatkan SLKS yang ke ' . $drh->tanda_kehormatan . ' ( Nomor :' . ($drh->no_kepres ?: '-') . ' tanggal ' . $fmt($drh->tgl_kepres, '%d-%m-%Y') . ')') : '-' }}</td>
            </tr>
            <tr>
                <td class="center-text">11.</td>
                <td>Hukuman Disiplin (Jenis, Nomor dan TMT dijatuhi hukuman s.d selesai)</td>
                <td style="font-style: italic;">{{ $drh->hukuman_disiplin ?: '"tidak pernah dijatuhi hukuman disiplin tingkat sedang/berat"' }}</td>
            </tr>
            <tr>
                <td class="center-text">12.</td>
                <td>CLTN (Nomor, dan TMT CLTN s.d selesai)</td>
                <td style="font-style: italic;">{{ $drh->cltn ?: '"tidak pernah mengambil cuti di luar tanggungan negara (CLTN)"' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="signature-container">
        <table class="signature-table">
            <tr>
                <td><br>Jabatan Atasan Langsung,
                <td>
                    Ditetapkan di Pangkalpinang<br>
                    Tanggal : {{ $fmt($drh->tgl_ditetapkan, '%d %B %Y') }}
                </td>
            </tr>
            <tr class="space-tg">
                <td></td>
                <td></td>
            </tr>
            <tr class="space-ttd">
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="font-weight: bold; text-decoration: underline;">
                    <br>
                    <br>
                    <br>
                    {{ $drh->atasan_nama ?: 'SAIPUL BAKHRI, S.Pd., M.M.' }}
                </td>
                <td style="font-weight: bold; text-decoration: underline;">
                    <br>
                    <br>
                    <br>
                    {{ $asn->nama ?? '' }}</td>
            </tr>
            <tr>
                <td>
                    NIP. {{ $drh->atasan_nip ?: '19740304 200501 1 013' }}
                </td>
                <td>NIP. {{ $asn->nip ?? '' }}</td>
            </tr>
        </table>
    </div>


    </div>

    <div class="page st-page">
        <div class="container">
            <div class="kop-surat-container">
                @if($kopSuratBase64)
                    <img src="{{ $kopSuratBase64 }}" alt="Kop Surat" class="kop-surat-image">
                @endif
            </div>

            <h1 class="judul-surat">Surat Tugas</h1>
            <div class="nomor-surat">NOMOR : 800 / 498 / DISDIK / 2026</div>

            <table class="content-table">
                <tr>
                    <td class="label-col">DASAR</td>
                    <td class="titik-dua-col">:</td>
                    <td class="isi-col" style="text-align: justify;">
                        {{ $drh->dasar ?: 'Surat Sekretariat Daerah Provinsi Kepulauan Bangka Belitung Nomor: 800/143/BKPSDM/2026 tanggal 20 Juli 2026 tentang Persyaratan Pengajuan Satyalancana Karya Satya, Kepala Dinas Pendidikan Provinsi Bangka Belitung.' }}
                    </td>
                </tr>
            </table>

            <div class="menugaskan">Menugaskan:</div>

            <table class="content-table">
                <tr>
                    <td class="label-col">KEPADA</td>
                    <td class="titik-dua-col">:</td>
                    <td class="isi-col">
                        <table class="sub-content-table">
                            <tr>
                                <td class="sub-label-col">Nama</td>
                                <td class="sub-titik-dua-col">:</td>
                                <td class="sub-isi-col"><strong>{{ ucwords($asn->nama ?? '-') }}</strong></td>
                            </tr>
                            <tr>
                                <td class="sub-label-col">NIP</td>
                                <td class="sub-titik-dua-col">:</td>
                                <td class="sub-isi-col">{{ $asn->nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="sub-label-col">Pangkat / Gol</td>
                                <td class="sub-titik-dua-col">:</td>
                                <td class="sub-isi-col">{{ $asn->pangkat ?? '-' }} / {{ $asn->golongan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="sub-label-col">Jabatan</td>
                                <td class="sub-titik-dua-col">:</td>
                                <td class="sub-isi-col">{{ $asn->jabatan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="sub-label-col">Unit Kerja</td>
                                <td class="sub-titik-dua-col">:</td>
                                <td class="sub-isi-col">{{ $asn->unit_kerja ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="content-table" style="margin-top: 15px;">
                <tr>
                    <td class="label-col">UNTUK</td>
                    <td class="titik-dua-col">:</td>
                    <td class="isi-col">
                        <ol class="ordered-list">
                            @foreach(explode("\n", $drh->untuk ?: "Pengajuan Tanda Kehormatan Satyalancana Karya Satya " . ($drh->tanda_kehormatan ? '20' : '10') . " Tahun\nDilaksanakan dengan sebaik-baik dan penuh tanggung jawab.") as $item)
                                @if(trim($item))
                                    <li>{{ trim($item) }}</li>
                                @endif
                            @endforeach
                        </ol>
                    </td>
                </tr>
            </table>

            <div class="ttd-container">
                <div class="ttd-tempat-tanggal">
                    Dikeluarkan di : Pangkalpinang<br>
                    Pada Tanggal : {{ $fmt($drh->tgl_ditetapkan, '%d %B %Y') }}
                </div>
<div class="ttd-jabatan">
    Plt. KEPALA DINAS PENDIDIKAN<br>
    PROVINSI KEP. BANGKA BELITUNG
</div>
<div class="ttd-spasi"></div>
<div class="ttd-nama">
    @php
        $nama = $drh->atasan_nama ?: 'SAIPUL BAKHRI, S.Pd, M.M.';
        $nama = str_replace('PLT.', 'Plt.', $nama);
        $nama = str_replace('plt.', 'Plt.', $nama);
        $nama = strtoupper($nama);
        $nama = str_ireplace('S.PD', 'S.Pd', $nama);
        $nama = str_ireplace('M.M.', 'M.M.', $nama);
        $nama = str_ireplace('S.SOS', 'S.Sos', $nama);
    @endphp
    {{ $nama }}
</div>
                <div class="ttd-nip">NIP. {{ $drh->atasan_nip ?: '19740430 200501 1 013' }}</div>
            </div>
        </div>
    </div>
    <div class="no-print" style="text-align:center; margin-top:20px;padding-left:15px">
        <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
        <a href="{{ route('drh-satyalancana.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Kembali</a>
    </div>
</body>
</html>

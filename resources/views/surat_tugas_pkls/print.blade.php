<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tugas PKL SMKN 1 Koba</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 1.5cm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        .kop-surat-container { text-align: center; margin-bottom: 8px; }
        .kop-surat-image { max-width: 100%; max-height: 300px; height: auto; display: inline-block; }
        .judul-surat {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 20px;
        }
        .judul-surat h4 {
            font-size: 16px;
            text-decoration: underline;
            margin: 0;
            letter-spacing: 1px;
        }
        .judul-surat p {
            font-size: 14px;
            margin: 5px 0 0 0;
        }
        .section {
            display: flex;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .section-label {
            width: 100px;
            flex-shrink: 0;
        }
        .section-separator {
            width: 20px;
            flex-shrink: 0;
        }
        .section-content {
            flex-grow: 1;
        }
        .perintah {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            font-size: 14px;
            letter-spacing: 1px;
        }
        .tabel-petugas {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 14px;
        }
        .tabel-petugas th, .tabel-petugas td {
            border: 1px solid #000;
            /* padding: 6px 10px; */
            text-align: left;
        }
        .tabel-petugas th {
            text-align: center;
            font-weight: bold;
        }
        .tabel-petugas td:nth-child(1) {
            text-align: center;
            width: 40px;
        }
        .list-untuk {
            padding-left: 0;
            margin: 0;
            list-style: none;
        }
        .list-untuk li {
            display: flex;
            margin-bottom: 0;
            text-align: justify;
        }
        .list-untuk .num {
            width: 25px;
            flex-shrink: 0;
        }
        .ttd-container {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
            font-size: 14px;
        }
        .ttd-box {
            width: 300px;
        }
        .ttd-space {
            height: 60px;
        }
        @media print {
            body {
                background-color: #fff;
                padding: 0;
                width: auto;
            }
            .container {
                box-shadow: none;
                padding: 0;
                width: 100%;
                min-height: 0;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Kop Surat -->
  <div class="kop-surat-container">
    @if ($kopSuratBase64)
      <img src="{{ $kopSuratBase64 }}" alt="Kop Surat" class="kop-surat-image">
    @endif
  </div>

    <!-- Judul Dokumen -->
    <div class="judul-surat">
        <h4>SURAT TUGAS</h4>
        <p>NOMOR : {{ $suratTugasPkl->nomor ?: ' ' }}</p>
    </div>

    <!-- Dasar -->
    <div class="section">
        <div class="section-label">Dasar</div>
        <div class="section-separator">:</div>
        <div class="section-content">
            @php
                $dasarItems = array_filter(array_map('trim', explode("\n", $suratTugasPkl->dasar ?: '')), fn ($l) => $l !== '');
            @endphp
            @if (count($dasarItems))
                @if (count($dasarItems) === 1)
                    {{ $dasarItems[0] }}
                @else
                    <ol style="padding-left: 22px; margin: 2px 0;">
                        @foreach ($dasarItems as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ol>
                @endif
            @else
                -
            @endif
        </div>
    </div>

    <div class="perintah">MEMERINTAHKAN:</div>

    <!-- Kepada -->
    <div class="section">
        <div class="section-label">Kepada</div>
        <div class="section-separator">:</div>
        <div class="section-content">
            <table class="tabel-petugas">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>NIP/Kelas</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($pegawai as $p)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td style="padding-left: 4px">{{ $p->nama ?: ' ' }}</td>
                            <td style="padding-left: 4px">{{ $p->nip ?: ' ' }}</td>
                            <td style="padding-left: 4px">Guru Pendamping</td>
                        </tr>
                    @endforeach
                    @foreach ($siswa as $s)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td style="padding-left: 4px">{{ ucwords(strtolower($s->nama ?: ' ')) }}</td>
                            <td style="padding-left: 4px">{{ $s->kelas ?: ' ' }}</td>
                            <td style="padding-left: 4px">Siswa</td>
                        </tr>
                    @endforeach
                    @if ($no === 1)
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Untuk -->
    <div class="section" style="margin-top: 15px;">
        <div class="section-label">Untuk</div>
        <div class="section-separator">:</div>
        <div class="section-content">
            <ul class="list-untuk">
                <li>
                    <span class="num">1.</span>
                    <span>{{ $suratTugasPkl->kegiatan ?: ' ' }}.</span>
                </li>
                <li>
                    <span class="num">2.</span>
                    <span>Perjalanan dinas dilaksanakan selama {{ $selamaHuruf ?: ' ' }} pada hari/tanggal {{ $tanggalText ?: 'Tanggal' }} pukul : {{ $suratTugasPkl->pukul ?: 'Pukul' }} ke {{ $suratTugasPkl->tempat ?: 'Tempat' }}</span>
                </li>
                <li>
                    <span class="num">3.</span>
                    <span>Membuat laporan hasil pelaksanaan perintah tugas kepada Kepala SMK Negeri 1 Koba</span>
                </li>
                <li>
                    <span class="num">4.</span>
                    <span>Perjalanan dinas ini dibiayai dari {{ $suratTugasPkl->sumber_dana ?: 'dana APBD' }} Tahun Anggaran {{ $suratTugasPkl->tahun_anggaran ?: date('Y') }}</span>
                </li>
                <li>
                    <span class="num">5.</span>
                    <span>Dilaksanakan sebaik-baiknya dengan penuh tanggungjawab</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tanda Tangan -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p>{{ $suratTugasPkl->dikeluarkan_di ?: 'Nama Tempat' }}, {{ $tanggalDikeluarkan ?: 'Tanggal' }}<br>Kepala SMK Negeri 1 Koba</p>
            <div class="ttd-space"></div>
            <p>
                @php
                    $atasan = $suratTugasPkl->penandatangan;
                    $nama = $atasan->nama ?? ($suratTugasPkl->nama_penandatangan ?: 'Nama');
                    $pangkat = $atasan->pangkat_golongan ?? '';
                    $nip = $atasan->nip ?? ($suratTugasPkl->nip_penandatangan ?: '');
                @endphp
                <strong>{{ $nama }}</strong><br>
                @if ($pangkat)
                    {{ $pangkat }}<br>
                @endif
                @if ($nip)
                    NIP. {{ $nip }}
                @endif
            </p>
        </div>
    </div>

    <div class="no-print" style="text-align:center; margin-top:20px;padding-left:15px">
        <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
        <a href="{{ route('surat-tugas-pkls.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Home</a>
    </div>
  </div>

</body>
</html>

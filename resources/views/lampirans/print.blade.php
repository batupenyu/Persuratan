<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampiran - {{ $lampiran->judul ?: 'Tanpa Judul' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #000;
            line-height: 1.5;
        }
        .header-doc {
            float: right;
            margin-bottom: 30px;
            font-weight: bold;
            font-size: 14px;
        }
        .clear {
            clear: both;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #e0e0e0;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .footer-doc {
            float: right;
            margin-top: 50px;
            text-align: left;
            font-size: 14px;
            padding-right: 50px;
        }
        .signature-space {
            height: 60px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }

        .indent {
            padding-left: 1.8em; /* Menjorok ke dalam sejajar teks "Kepala..." */
        }
        .indent p {
            margin: 0;
        }
    </style>
</head>
<body>

    <div class="header-doc">
        <i>
        <table>
            <tr style="border: none;">
                <td style="border: none; padding: 0px;">{{ ($lampiran->judul ?: 'Lampiran') }}</td>
                <td style="border: none; padding: 0px;">: {{ $lampiran->keterangan ?: 'Surat Kepala SMK Negeri 1 Koba' }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 0px;">Nomor</td>
                <td style="border: none; padding: 0px;">: {{ $lampiran->nomor ?: '.........................' }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 0px;">Tanggal</td>
                <td style="border: none; padding: 0px;">: {{ $lampiran->tanggal ? \App\Http\Controllers\LampiranController::formatTanggal($lampiran->tanggal, '%d %B %Y') : \Carbon\Carbon::now()->format('d F Y') }}</td>
            </tr>
        </table>
        </i>
    </div>

    <div class="clear"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama</th>
                <th style="width: 30%;">NIP / KLS</th>
                <th style="width: 30%;">Jabatan</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($lampiran->getPegawaiList() as $p)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->nip ?: '-' }}</td>
                <td>{{ $p->jabatan ?: '-' }}</td>
            </tr>
            @endforeach
            @foreach($lampiran->getSiswaList() as $s)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ ucwords(strtolower($s->nama ?: ' ')) }}</td>
                <td>{{ $s->kelas ?: ($s->nis ?: '-') }}</td>
                <td>Siswa</td>
            </tr>
            @endforeach
            @if($no === 1)
            <tr>
                <td class="text-center" colspan="4">Tidak ada data peserta.</td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-doc">
        @php
            $penandatangan = $lampiran->penandatangan;
            $isAn = $lampiran->penandatangan_an ?? false;
            $jabatanTertanda = $penandatangan->jabatan ?? 'Kepala SMKN 1 Koba';
            $namaTertanda = $penandatangan->nama ?? 'SYAHRYANTO, S.T., M.Pd';
            $pangkatTertanda = $penandatangan->pangkat_golongan ?? 'Pembina Tk. I, IV/b';
            $nipTertanda = $penandatangan->nip ?? '197708262006041005';
            $prefix = $isAn ? 'a.n. ' : '';
        @endphp
        @if($isAn)
            <p>an. KEPALA SMK NEGERI 1 KOBA <br>
                <span class="indent">
                {{ $penandatangan->jabatan ?? '' }}</span></p>
            <div class="indent">
                <div class="signature-space"></div>
                <p><strong>{{ $namaTertanda ?? '' }}</strong></p>
                <p>{{ $pangkatTertanda ?? '' }}</p>
                <p>NIP. {{ $nipTertanda ?? '' }}</p>
            </div>
        @else
            <p>KEPALA SMK NEGERI 1 KOBA</p>
            <div class="signature-space"></div>
            <p><strong>{{ $namaTertanda ?? '' }}</strong><br>
            {{ $pangkatTertanda ?? '' }}<br>
            NIP. {{ $nipTertanda ?? '' }}</p>
        @endif
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
        <a href="{{ route('lampirans.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Home</a>
    </div>

</body>
</html>

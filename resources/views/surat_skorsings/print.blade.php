<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Skorsing - {{ $suratSkorsing->nomor_surat }}</title>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: 'Times New Roman', Times, serif; background-color: #f0f0f0; margin: 0; padding: 20px; display: flex; justify-content: center; color: #333; }
        .kertas-surat { 
            background-color: white; 
            width: 210mm; 
            min-height: 297mm; 
            padding: 1cm 20mm 20mm 25mm; /* ← padding atas diubah jadi 1cm (rapat ke atas) */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            box-sizing: border-box; 
        }
        .kop-surat { 
            padding-bottom: 5px; /* ← dikurangi dari 10px */
            margin-bottom: 10px; /* ← dikurangi dari 25px biar lebih rapat */
            text-align: center; 
        }
        .kop-surat img { 
            max-width: 100%; 
            height: auto; 
            display: block; 
            margin: 0 auto; 
        }
        .judul-surat { text-align: center; margin-bottom: 25px; }
        .judul-surat h4 { font-size: 14pt; text-transform: uppercase; text-decoration: underline; margin: 0 0 5px; letter-spacing: 1px; }
        .judul-surat p, .pembuka-data, .paragraf-isi, .penutup-surat, .tabel-data td, .box-ttd { font-size: 11pt; }
        .judul-surat p { margin: 0; }
        .pembuka-data, .paragraf-isi, .penutup-surat { line-height: 1.5; text-align: justify; margin-bottom: 12px; }
        .tabel-data { width: 100%; margin-left: 20px; margin-bottom: 20px; border-collapse: collapse; }
        .tabel-data td { padding: 3px 0; vertical-align: top; }
        .tabel-data td.label { width: 160px; }
        .tabel-data td.titik-dua { width: 15px; text-align: center; }
        .ttd-container { margin-top: 40px; width: 100%; display: flex; justify-content: flex-end; }
        .box-ttd { width: 300px; text-align: left; line-height: 1.4; }
        .space-ttd { height: 80px; }
        .nama-pejabat { font-weight: bold; text-decoration: underline; }
        .no-print { text-align: center; margin-top: 20px; }
        .no-print button, .no-print a { display: inline-block; color: #fff; border: 0; padding: .6rem 1.4rem; border-radius: 4px; font-size: .95rem; text-decoration: none; cursor: pointer; }
        .no-print button { background: #2563eb; }
        .no-print a { background: #6b7280; margin-left: .5rem; }
        @media print { 
            body { background: #fff; padding: 0; } 
            .kertas-surat { box-shadow: none; } 
            .no-print { display: none; } 
        }
    </style>
</head>
<body>
<div class="kertas-surat">
    @php
        $fmt = fn ($date) => $date ? \App\Http\Controllers\SuratSkorsingController::formatTanggal($date) : '...................';
        $siswa = $suratSkorsing->siswa;
        $penandatangan = $suratSkorsing->penandatangan;
        $namaPenandatangan = $penandatangan?->nama ?? '';
        $nipPenandatangan = $penandatangan?->nip ?? '';
        $pangkatPenandatangan = $penandatangan?->pangkat_golongan ?? '';
        $jabatanPenandatangan = $penandatangan?->tugas_tambahan ?: ($penandatangan?->jabatan ?? 'Kepala Sekolah');
        $unitKerjaPenandatangan = $penandatangan?->unit_kerja ?: ($penandatangan?->lembaga_pengangkatan ?? 'SMK Negeri 1 Koba');
        $keteranganPelanggaran = $suratSkorsing->pelanggaran ? ' yaitu '.$suratSkorsing->pelanggaran : '';
        $tanggalMulai = $suratSkorsing->tanggal_mulai;
        $tanggalSelesai = $suratSkorsing->tanggal_selesai;
        if ($tanggalMulai && $tanggalSelesai && $tanggalMulai->format('m/Y') === $tanggalSelesai->format('m/Y')) {
            $periodeTanggal = $tanggalMulai->format('d').'  s.d. '.$tanggalSelesai->format('d').' '.$tanggalMulai->format('F Y');
        } else {
            $periodeTanggal = $fmt($tanggalMulai).' sampai dengan '.$fmt($tanggalSelesai);
        }
        $periode = $tanggalMulai || $tanggalSelesai
            ? 'terhitung mulai '.$periodeTanggal
            : 'terhitung mulai ................... sampai dengan ...................';
    @endphp

    <div class="kop-surat">
        @if($kopSuratBase64)
            <img src="{{ $kopSuratBase64 }}" alt="Kop surat">
        @endif
    </div>

    <div class="judul-surat">
        <h4>Surat Skorsing</h4>
        <p>Nomor : {{ $suratSkorsing->nomor_surat ?: '...................' }}</p>
    </div>

    <p class="pembuka-data">Yang bertanda tangan di bawah ini :</p>
    <table class="tabel-data">
        <tr><td class="label">Nama</td><td class="titik-dua">:</td><td><strong>{{ $namaPenandatangan }}</strong></td></tr>
        <tr><td class="label">NIP</td><td class="titik-dua">:</td><td>{{ $nipPenandatangan }}</td></tr>
        <tr><td class="label">Pangkat, Gol/Ruang</td><td class="titik-dua">:</td><td>{{ $pangkatPenandatangan }}</td></tr>
        <tr><td class="label">Jabatan</td><td class="titik-dua">:</td><td>{{ $jabatanPenandatangan }} {{$unitKerjaPenandatangan}}</td></tr>
        <tr><td class="label">Unit Kerja</td><td class="titik-dua">:</td><td>{{ $unitKerjaPenandatangan }}</td></tr>
    </table>

    <p class="pembuka-data">Dengan ini menerangkan bahwa :</p>
    <table class="tabel-data">
        <tr><td class="label">Nama</td><td class="titik-dua">:</td><td>{{ $siswa->nama ?? '' }}</td></tr>
        <tr><td class="label">Kelas</td><td class="titik-dua">:</td><td>{{ $siswa->kelas ?? '' }}</td></tr>
        <tr><td class="label">NIS</td><td class="titik-dua">:</td><td>{{ $siswa->nis ?? '' }}</td></tr>
    </table>

    <p class="paragraf-isi">Maka dengan ini kami dari pihak sekolah dengan sangat terpaksa memberikan skorsing kepada nama siswa tersebut di atas untuk tidak mengikuti kegiatan belajar mengajar di sekolah selama {{ $suratSkorsing->durasi_skorsing ?: '...................' }} {{ $periode }}, dengan tujuan memberi sanksi terhadap siswa atas pelanggaran yang dilakukan{{ $keteranganPelanggaran }} dan sekaligus memberikan kesempatan kepada orang tua siswa untuk lebih dekat dengan putranya dan memberikan pembinaan di rumah.</p>

    <p class="penutup-surat">Demikian Surat Skorsing ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <div class="ttd-container"><div class="box-ttd">
        {{ $suratSkorsing->tempat_ditetapkan ?: 'Koba' }}, {{ $fmt($suratSkorsing->tanggal_ditetapkan) }}<br>
        {{ $jabatanPenandatangan }} {{ $unitKerjaPenandatangan }},<br>
        <div class="space-ttd"></div>
        <span class="nama-pejabat">{{ $namaPenandatangan }}</span><br>
        {{ $pangkatPenandatangan }}<br>
        NIP. {{ $nipPenandatangan }}
    </div></div>

    <div class="no-print"><button onclick="window.print()">Cetak</button><a href="{{ route('surat-skorsings.index') }}">Kembali</a></div>
</div>
</body>
</html>

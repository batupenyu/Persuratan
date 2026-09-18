<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Surat PKL {{ $suratPkl->nomor }}</title>
    <style>
      @page {
        size: A4;
        margin: 1cm 1.5cm 0.5cm 1.5cm;
      }
      body {
        background-color: #525659;
        font-family: "Helvetica", sans-serif;
        font-size: 14pt;
        line-height: 20pt;
        color: #000;
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
      .tempat-tanggal {
        text-align: right;
        margin-bottom: 15pt;
      }
      table {
        width: 100%;
        border-collapse: collapse;
      }
      td.label {
        width: 120px;
        white-space: nowrap;
        vertical-align: top;
      }
      td.colon {
        width: 15px;
      }
      .content p {
        margin-top: 0;
        margin-bottom: 0;
        line-height: 1.5;
        text-align: justify;
      }
      .isi-paragraf {
        text-indent: 1cm;
        margin-bottom: 10pt;
        line-height: 1.5;
        text-align: justify;
      }
      .signature {
        padding-left: 430px;
        margin-top: 20px;
      }
      .no-print {
        margin-top: 20px;
        text-align: center;
      }
      @media print {
        body { background: white; }
        .page {
          box-shadow: none;
          margin: 0;
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
      @php
        $fmt = fn ($d, $f = '%d %B %Y') => $d ? \App\Http\Controllers\SuratPklController::formatTanggal($d, $f) : '...................';
        $penandatangan = $suratPkl->penandatangan;

        $angkaTerbilang = function ($n) {
            $angka = [
                0 => 'nol', 1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat',
                5 => 'lima', 6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan',
                10 => 'sepuluh', 11 => 'sebelas',
            ];

            if ($n < 12) {
                return $angka[$n];
            }

            if ($n < 20) {
                return $angka[$n - 10] . ' belas';
            }

            if ($n < 100) {
                $puluhan = intdiv($n, 10);
                $satuan = $n % 10;
                $kataPuluhan = [
                    2 => 'dua puluh', 3 => 'tiga puluh', 4 => 'empat puluh',
                    5 => 'lima puluh', 6 => 'enam puluh', 7 => 'tujuh puluh',
                    8 => 'delapan puluh', 9 => 'sembilan puluh',
                ];

                return ($satuan ? $kataPuluhan[$puluhan] . ' ' . $angka[$satuan] : $kataPuluhan[$puluhan]);
            }

            return (string) $n;
        };

        $jumlahSiswa = (int) ($suratPkl->jumlah_siswa ?? 2);
        $namaKelas = $suratPkl->nama_kelas ?: 'XII';
        $tahunAjaran = $suratPkl->tahun_ajaran ?: '2026/2027';
        $namaJurusan = $suratPkl->nama_jurusan ?: 'Teknik Pengelasan (TP)';
        $jenisInstansi = $suratPkl->jenis_instansi_yang_dituju ?: 'Instansi';
        $lamaMagang = (int) ($suratPkl->lama_magang ?? 4);
        $lamaMagangTerbilang = $angkaTerbilang($lamaMagang);
        $tglAwal = $suratPkl->tgl_awal_magang ? \Carbon\Carbon::parse($suratPkl->tgl_awal_magang) : \Carbon\Carbon::parse('2026-10-06');
        $tglAkhir = $suratPkl->tgl_akhir_magang ? \Carbon\Carbon::parse($suratPkl->tgl_akhir_magang) : \Carbon\Carbon::parse('2027-01-28');
        $noContact = $suratPkl->no_contact ?: '087819754201';

        $defaultIsi = "Sehubungan dengan program kerja/kegiatan SMK Negeri 1 Koba TA {$tahunAjaran}, kami memohon kesediaan {$jenisInstansi} Bapak/Ibu untuk menerima siswa kelas {$namaKelas} jurusan {$namaJurusan} sejumlah {$jumlahSiswa} ({$angkaTerbilang($jumlahSiswa)}) murid dalam kegiatan Praktek Kerja Lapangan (PKL). Kegiatan ini direncanakan berlangsung selama {$lamaMagang} ({$lamaMagangTerbilang}) bulan, terhitung mulai tanggal {$fmt($tglAwal, '%d %B %Y')} hingga {$fmt($tglAkhir, '%d %B %Y')}. 

Adapun data siswa yang bersangkutan akan segera kami kirimkan setelah menerima surat balasan kesediaan dari {$jenisInstansi} Bapak/Ibu. Untuk konfirmasi dan informasi lebih lanjut, silakan menghubungi Humas SMK Negeri 1 Koba di nomor HP/WA {$noContact}.

Demikian permohonan kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih";

        $isiSurat = $defaultIsi;
      @endphp

      @if($kopSuratBase64)
      <img src="{{ $kopSuratBase64 }}" style="max-width: 100%; height: auto; display: block; margin-bottom: 20px" />
      @endif

      <div class="tempat-tanggal">
        {{ $suratPkl->tempat_ditetapkan ?: 'Koba' }}, {{ $fmt($suratPkl->tanggal_ditetapkan) }}
      </div>

      <div class="content">
        <table>
          <tr>
            <td class="label">Sifat</td>
            <td class="colon">:</td>
            <td>{{ $suratPkl->sifat ?: 'Penting' }}</td>
          </tr>
          <tr>
            <td class="label">Lampiran</td>
            <td class="colon">:</td>
            <td>{{ $suratPkl->lampiran ?: '-' }}</td>
          </tr>
          <tr>
            <td class="label">Perihal</td>
            <td class="colon">:</td>
            <td><strong>{{ $suratPkl->perihal ?: 'Permohonan PKL' }}</strong></td>
          </tr>
        </table>
        <br />

        <p>
          Kepada Yth.<br />
          {{ $suratPkl->pejabat_tujuan_surat ?: 'Pimpinan Instansi/Perusahaan' }}<br />
          di<br />
          <strong>{{ $suratPkl->kota_tujuan_surat ?: 'Tempat' }}</strong>
        </p>
        <br />

        @php
          $paragraphs = preg_split('/\n\s*\n/', trim($isiSurat));
        @endphp

        @foreach ($paragraphs as $paragraph)
          <div class="isi-paragraf">
            <p>{!! nl2br(e(trim($paragraph))) !!}</p>
          </div>
        @endforeach

      </div>

      @if($penandatangan)
      <div class="signature">
        <p>
          @php
            $atasan = $penandatangan;
            $nama = $atasan->nama ?? '';
            $normalizeGelar = function (string $value): string {
                $value = trim($value);
                $value = preg_replace_callback('/\b([A-Z])\.([A-Z]{2,})(?=\.|\b)/', function ($m) {
                    return $m[1] . '.' . ucfirst(strtolower($m[2]));
                }, mb_strtoupper($value));

                foreach (['M.PD' => 'M.Pd', 'S.PD' => 'S.Pd', 'S.PSI' => 'S.Psi', 'S.SOS' => 'S.Sos', 'S.AG' => 'S.Ag'] as $from => $to) {
                    $value = str_ireplace($from, $to, $value);
                }

                return $value;
            };
            $nama = $normalizeGelar($nama);
            $pangkat = $atasan->pangkat_golongan ?? '';
            $nip = $atasan->nip ?? '';
            $jabatan = $atasan->jabatan ?? '';
            $unitKerja = $atasan->unit_kerja ?? '';
          @endphp

          {{ strtoupper($jabatan) }}{{ $unitKerja ? ' ' . strtoupper($unitKerja) : '' }}
          <br><br><br><br><br>
          {{ $nama }}
          @if($pangkat && $pangkat != '-')
              <br>{{ $pangkat }}
          @endif
          <br>NIP. {{ $nip }}
        </p>
      </div>
      @endif

      <div class="no-print" style="text-align:center; margin-top:20px;padding-left:15px">
        <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
        <a href="{{ route('surat-pkls.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Kembali</a>
      </div>
    </div>
  </body>
</html>

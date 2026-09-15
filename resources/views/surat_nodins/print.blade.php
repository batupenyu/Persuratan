<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <title>Nota Dinas - {{ $suratNodin->nomor ?? 'Draft' }}</title>
    <style>
      @page {
        size: A4;
        margin: 1cm 1cm;
      }
      body {
        background-color: #525659;
        font-family: Arial, sans-serif;
        font-size: 13pt;
        line-height: 1.3;
        color: #000;
        margin: 0;
        background: #fff;
      }
      * {
        font-size: inherit;
      }
      .kop-surat-container {
        text-align: center;
        margin-bottom: 10px;
        margin-left: -2cm;
        margin-right: -2cm;
      }
      .kop-surat-image {
        width: 80%;
        max-width: 21cm;
        height: auto;
        max-height: 200px;
        object-fit: contain;
      }
      .nota-title {
        text-align: center;
        font-size: 13pt;
        font-weight: bold;
        text-decoration: underline;
        margin-bottom: 20px;
        text-transform: uppercase;
      }
      .meta-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 13pt;
        page-break-inside: avoid;
      }
      .meta-table td {
        vertical-align: top;
        font-size: 13pt;
      }
      .meta-table td:nth-child(1) { width: 12%; }
      .meta-table td:nth-child(2) { width: 3%; text-align: center; }
      .meta-table td:nth-child(3) { width: 85%; }
      .peserta-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 11pt;
        page-break-inside: auto;
      }
      .peserta-table thead {
        display: table-header-group;
      }
      .peserta-table tbody {
        display: table-row-group;
      }
      .peserta-table tr {
        page-break-inside: avoid;
      }
      .peserta-table th,
      .peserta-table td {
        border: 1px solid #000;
        padding: 6px;
        font-size: 11pt;
        text-align: left;
        vertical-align: middle;
      }
      .peserta-table th {
        font-weight: bold;
        background-color: #e6e6e6;
        text-align: center;
      }
      .content {
        text-align: justify;
        text-indent: 40px;
        margin-bottom: 15px;
        font-size: 13pt;
        page-break-inside: avoid;
      }
      .content p {
        margin: 0 0 15px 0;
        font-size: 13pt;
      }
      .signature-container {
        margin-top: 40px;
        float: right;
        width: 50%;
        text-align: left;
        font-size: 13pt;
        page-break-inside: avoid;
        page-break-after: avoid;
      }
      .signature-title {
        font-weight: bold;
        margin-bottom: 10px;
        font-size: 13pt;
      }
      .clearfix::after {
        content: "";
        clear: both;
        display: table;
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
      .no-print {
        margin-top: 20px;
        text-align: center;
      }
      @media print {
        body { margin: 0; padding: 0; }
        .page {
          box-shadow: none;
          margin: 0;
          padding-top: 0;
          page-break-after: auto;
          page-break-inside: auto;
        }
        .no-print {
          display: none !important;
        }
        .signature-container {
          break-inside: avoid;
        }
      }
    </style>
  </head>
  <body>
    <div class="page">
      <div class="kop-surat-container">
        @if(!empty($kopSuratBase64))
        <img src="{{ $kopSuratBase64 }}" alt="Kop Surat" class="kop-surat-image" />
        @endif
      </div>

      <div class="nota-title">Nota Dinas</div>

      <table class="meta-table">
        <tr>
          <td>Yth.</td>
          <td>:</td>
          <td>{{ $suratNodin->kepada ?: '-' }}</td>
        </tr>
        <tr>
          <td>Dari</td>
          <td>:</td>
          <td>
            @php
              $dari = $suratNodin->dari;
              $prefix = '';
              if (($suratNodin->dari_plt ?? false)) $prefix .= 'Plt. ';
              if (($suratNodin->dari_an ?? false)) $prefix .= 'a.n. ';
              echo $prefix . ($dari ?: '-');
            @endphp
          </td>
        </tr>
        <tr>
          <td>Tanggal</td>
          <td>:</td>
          <td>{{ $suratNodin->tanggal ? \App\Http\Controllers\SuratNodinController::formatTanggal($suratNodin->tanggal, '%d %B %Y') : '-' }}</td>
        </tr>
        <tr>
          <td>Nomor</td>
          <td>:</td>
          <td>{{ $suratNodin->nomor ?: '-' }}</td>
        </tr>
        <tr>
          <td>Sifat</td>
          <td>:</td>
          <td>{{ $suratNodin->sifat ?: '-' }}</td>
        </tr>
        <tr>
          <td>Lampiran</td>
          <td>:</td>
          <td>{{ $suratNodin->lampiran ?: '-' }}</td>
        </tr>
        <tr>
          <td>Hal</td>
          <td>:</td>
          <td>{{ $suratNodin->hal ?: '-' }}</td>
        </tr>
      </table>

      <hr style="border: 0; border-top: 1px solid #000; margin-bottom: 20px;">

      @if($suratNodin->dasar_surat)
      <div class="content">
        <p>{{ $suratNodin->dasar_surat }}</p>
      </div>
      @endif

      @if($suratNodin->isi_surat)
      <div class="content">
        <p>{{ $suratNodin->isi_surat }}</p>
      </div>
      @endif

      @php
          $pesertaList = $suratNodin->pesertaSuratUsulans ?? collect();
          $pesertaCount = $pesertaList->count();
          $showPesertaTable = $pesertaCount <= 15;
      @endphp

      @if($showPesertaTable && $pesertaCount > 0)
      <table class="peserta-table">
        <thead>
          <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 22%;">Nama</th>
            <th style="width: 18%;">NIP/NIS</th>
            <th style="width: 15%;">Pangkat / Gol / Kelas</th>
            <th style="width: 15%;">Jabatan</th>
            <th style="width: 25%;">Tanggal/Tempat Kegiatan</th>
          </tr>
        </thead>
        <tbody>
          @php
              // 0. Peserta ke-2 dst yang tidak mengisi tgl_awal_kegiatan, tgl_akhir_kegiatan,
              //    dan/atau tempat_kegiatan (kosong) mewarisi nilai tsb dari peserta
              //    sebelumnya (urutan input asli) per-field secara independen,
              //    supaya nanti bisa digabung (rowspan) dengan baris di atasnya.
              $normalizeDate = function ($val) {
                  if ($val instanceof \Carbon\CarbonInterface) {
                      return $val->format('Y-m-d');
                  }
                  return $val ? (string) $val : '';
              };

              $lastAwal = '';
              $lastAkhir = '';
              $lastTempat = '';
              $pesertaList = $pesertaList->values()->map(function ($peserta, $idx) use (&$lastAwal, &$lastAkhir, &$lastTempat, $normalizeDate) {
                  $awalAsli = $normalizeDate($peserta->tgl_awal_kegiatan ?? '');
                  $akhirAsli = $normalizeDate($peserta->tgl_akhir_kegiatan ?? '');
                  $tempatAsli = trim((string) ($peserta->tempat_kegiatan ?? ''));

                  if ($awalAsli === '' && $idx > 0) {
                      $peserta->tgl_awal_efektif = $lastAwal;
                  } else {
                      $peserta->tgl_awal_efektif = $awalAsli;
                      $lastAwal = $awalAsli;
                  }

                  if ($akhirAsli === '' && $idx > 0) {
                      $peserta->tgl_akhir_efektif = $lastAkhir;
                  } else {
                      $peserta->tgl_akhir_efektif = $akhirAsli;
                      $lastAkhir = $akhirAsli;
                  }

                  if ($tempatAsli === '' && $idx > 0) {
                      $peserta->tempat_efektif = $lastTempat;
                  } else {
                      $peserta->tempat_efektif = $tempatAsli;
                      $lastTempat = $tempatAsli;
                  }

                  return $peserta;
              });

              // 1. Urutkan berdasarkan tanggal awal, tanggal akhir, dan tempat efektif agar mengelompok
              $sorted = $pesertaList->sortBy(function ($peserta) {
                  return $peserta->tgl_awal_efektif . '|' . $peserta->tgl_akhir_efektif . '|' . $peserta->tempat_efektif;
              })->values();

              // 2. Hitung jumlah baris (rowspan) untuk setiap kombinasi tanggal dan tempat efektif
              $counts = [];
              foreach ($sorted as $peserta) {
                  $key = $peserta->tgl_awal_efektif . '|' . $peserta->tgl_akhir_efektif . '|' . $peserta->tempat_efektif;
                  $counts[$key] = ($counts[$key] ?? 0) + 1;
              }

              $renderedKeys = [];
          @endphp

          @foreach($sorted as $index => $peserta)
              @php
                  $awal = $peserta->tgl_awal_efektif !== '' ? \Carbon\Carbon::parse($peserta->tgl_awal_efektif) : null;
                  $akhir = $peserta->tgl_akhir_efektif !== '' ? \Carbon\Carbon::parse($peserta->tgl_akhir_efektif) : null;
                  $tempat = $peserta->tempat_efektif ?? '';

                  $key = $peserta->tgl_awal_efektif . '|' . $peserta->tgl_akhir_efektif . '|' . $tempat;

                  if ($awal && $akhir && $awal->isSameDay($akhir)) {
                      $tanggalText = \App\Http\Controllers\SuratNodinController::formatTanggal($awal, '%d %B %Y');
                  } elseif ($awal && $akhir && $awal->format('n') === $akhir->format('n') && $awal->format('Y') === $akhir->format('Y')) {
                      $tanggalText = \App\Http\Controllers\SuratNodinController::formatTanggal($awal, '%d') . ' s.d. ' . \App\Http\Controllers\SuratNodinController::formatTanggal($akhir, '%d %B %Y');
                  } elseif ($awal && $akhir) {
                      $tanggalText = \App\Http\Controllers\SuratNodinController::formatTanggal($awal, '%d %B %Y') . ' s.d. ' . \App\Http\Controllers\SuratNodinController::formatTanggal($akhir, '%d %B %Y');
                  } elseif ($awal) {
                      $tanggalText = \App\Http\Controllers\SuratNodinController::formatTanggal($awal, '%d %B %Y');
                  } else {
                      $tanggalText = '';
                  }
              @endphp
              <tr>
                <td style="text-align: center;">{{ $index + 1 }}.</td>
                <td>
                  @if(!empty($peserta->pegawai))
                    {{ $peserta->pegawai->nama }}
                  @elseif(!empty($peserta->siswa))
                    {{ ucwords(strtolower($peserta->siswa->nama)) }}
                  @else
                    -
                  @endif
                </td>
                <td>
                  @if(!empty($peserta->pegawai))
                    {{ $peserta->pegawai->nip ?: '-' }}
                  @elseif(!empty($peserta->siswa))
                    {{ $peserta->siswa->nis ?: '-' }}
                  @else
                    -
                  @endif
                </td>
                <td style="text-align: {{ !empty($peserta->siswa) ? 'center' : 'left' }};">
                  @if(!empty($peserta->pegawai))
                    @php
                        $pangkat = $peserta->pegawai->pangkat ?? '';
                        $golongan = $peserta->pegawai->golongan ?? '';
                        $pangkatGolongan = $pangkat || $golongan
                            ? trim(($pangkat ?: '') . ($golongan ? ', ' . $golongan : ''), ', ')
                            : '-';
                    @endphp
                    {{ $pangkatGolongan }}
                  @elseif(!empty($peserta->siswa))
                    {{ $peserta->siswa->kelas ?: '-' }}
                  @else
                    -
                  @endif
                </td>
                <td>
                  @if(!empty($peserta->pegawai))
                    {{ $peserta->pegawai->jabatan ?: '-' }}
                  @elseif(!empty($peserta->siswa))
                    Siswa
                  @else
                    -
                  @endif
                </td>

                {{-- Kolom tanggal & tempat dengan rowspan.
                     Peserta yg tgl_awal_kegiatan/tgl_akhir_kegiatan/tempat_kegiatan-nya
                     kosong sudah diberi nilai efektif warisan (dari peserta sebelumnya)
                     di atas, sehingga key-nya sama dgn peserta sebelumnya dan otomatis
                     ikut tergabung dalam rowspan yg sama. --}}
                @if(!in_array($key, $renderedKeys))
                    <td rowspan="{{ $counts[$key] }}">
                      {{ $tanggalText }}{{ $tanggalText && $tempat ? ' di ' : '' }}{{ $tempat ?: '-' }}
                    </td>
                    @php $renderedKeys[] = $key; @endphp
                @endif
              </tr>
          @endforeach
        </tbody>
      </table>
      @endif

      <div class="content">
        <p>Demikian surat permohonan ini kami sampaikan atas perhatian Bapak, Kami ucapkan terima kasih.</p>
      </div>

      <div class="clearfix">
        <div class="signature-container">
          <div class="signature-title">
            @php
                $atasan = $suratNodin->penandatangan ?? null;
                $pegawaiTugas = $suratNodin->pegawaiTugas ?? null;

                $jabatanAtasan   = $atasan->jabatan ?? '';
                $unitKerjaAtasan = $atasan->unit_kerja ?? '';
                $nama            = $atasan->nama ?? '';
                $pangkat         = $atasan->pangkat_golongan ?? '';
                $nip             = $atasan->nip ?? '';
                $opdInduk        = $atasan->nama_opd_indu ?? '';

                $jabatanTugas    = $pegawaiTugas->jabatan ?? '';
                $unitKerjaTugas  = $pegawaiTugas->unit_kerja ?? '';

                $isPlt = $suratNodin->penandatangan_plt ?? false;
                $isPlh = $suratNodin->penandatangan_plh ?? false;  // tambahan
                $isAn  = $suratNodin->penandatangan_an ?? false;

                // Prioritas: Plt > Plh > a.n. (hanya satu yang aktif)
                if ($isPlt) {
                    $isPlh = false;
                    $isAn  = false;
                } elseif ($isPlh) {
                    $isAn = false;
                }

                $prefix    = '';
                $indent    = false;
                $showTugas = false;
                $unitKerja = '';

                if ($isPlt) {
                    $prefix    = 'Plt.&nbsp;';
                    $indent    = true;
                    $showTugas = true;
                    $unitKerja = $unitKerjaTugas ?: $unitKerjaAtasan;
                } elseif ($isPlh) {
                    $prefix    = 'Plh.&nbsp;';
                    $indent    = true;
                    $showTugas = true;
                    $unitKerja = $unitKerjaTugas ?: $unitKerjaAtasan;
                } elseif ($isAn) {
                    $prefix    = 'a.n.&nbsp;';
                    $indent    = true;
                    $showTugas = true;
                    $unitKerja = $unitKerjaAtasan;
                } else {
                    $prefix    = '';
                    $indent    = false;
                    $showTugas = false;
                    $unitKerja = $unitKerjaAtasan;
                }

                $indentChar = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';

                // Helper: UPPERCASE nama, tapi gelar (setelah koma) tetap title case
                $upperNamaKeepGelar = function ($nama) {
                    if (blank($nama)) return '';

                    $parts     = explode(',', $nama, 2);
                    $namaDepan = trim($parts[0]);
                    $gelar     = isset($parts[1]) ? trim($parts[1]) : '';

                    $namaUpper = mb_strtoupper($namaDepan);

                    if ($gelar === '') {
                        return $namaUpper;
                    }

                    $gelarFormatted = collect(explode(',', $gelar))
                      ->map(function ($gelarItem) {
                        $gelarItem = rtrim(trim($gelarItem), '.');

                        return ucwords(strtolower($gelarItem), '.');
                      })
                      ->implode(',');

                    return $namaUpper . ', ' . $gelarFormatted;
                };

                // Helper title case gelar (untuk pangkat, dll)
                $titleCaseGelar = function ($text) {
                    if (blank($text)) return '';

                  $parts = array_map('trim', explode(',', $text, 2));
                    $pangkat = preg_replace_callback('/\bTk\s*\.\s*([ivxlcdm]+)/i', function ($m) {
                    return 'Tk. ' . mb_strtoupper($m[1]);
                  }, $parts[0]);

                  if (! isset($parts[1])) {
                    return $pangkat;
                  }

                  $golongan = preg_replace_callback('/^([ivxlcdm]+)\s*\/\s*([a-z])$/i', function ($m) {
                    return mb_strtoupper($m[1]) . '/' . mb_strtolower($m[2]);
                  }, $parts[1]);

                  return $pangkat . ', ' . $golongan;
                };

                // Jabatan & OPD -> UPPERCASE penuh
                $jabatanAtasanUpper = mb_strtoupper($jabatanAtasan ?? '');
                $jabatanTugasUpper  = mb_strtoupper($jabatanTugas ?? '');
                $unitKerjaUpper     = mb_strtoupper($unitKerja ?? '');
                $opdIndukUpper      = mb_strtoupper($opdInduk ?? '');

                // Nama pegawai -> UPPERCASE, gelar tetap (S.Sos, S.Pd, M.Pd, S.Ag, S.T)
                $namaFormatted      = $upperNamaKeepGelar($nama);

                // Pangkat/golongan -> title case gelar
                $pangkatFormatted   = $titleCaseGelar($pangkat);
            @endphp

            {!! $prefix . e($jabatanAtasanUpper) !!}{{ $unitKerjaUpper ? ' ' . $unitKerjaUpper : '' }}
            <br>{!! $indent ? $indentChar : '' !!}{{ $opdIndukUpper }}
            <br><br><br>
            @if($showTugas && $jabatanTugas)
                <br>{!! $indent ? $indentChar : '' !!}{{ $jabatanTugasUpper }}
            @endif
            <br>{!! $indent ? $indentChar : '' !!}{{ $namaFormatted }}
            @if($pangkat && $pangkat != '-')
                <br>{!! $indent ? $indentChar : '' !!}{{ $pangkatFormatted }}
            @endif
            <br>{!! $indent ? $indentChar : '' !!}NIP. {{ $nip }}
          </div>
        </div>
      </div>
    </div>

    <div class="no-print" style="text-align:center; margin-top:20px; padding-left:15px">
      @if(!$showPesertaTable && $pesertaCount > 15)
      <a href="{{ route('surat-nodins.lampiran-tabel-peserta', $suratNodin) }}" target="_blank" style="display:inline-block; margin-right:0.5rem; background:#16a34a; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; font-weight:bold;">Lampiran Tabel Peserta</a>
      @endif
      <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
      <a href="{{ route('surat-nodins.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Kembali</a>
    </div>
  </body>
</html>
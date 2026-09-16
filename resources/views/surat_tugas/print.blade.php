<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Surat Tugas</title>
<style>
  @page { size: A4; margin: 0.5cm 1cm; }
  body {
              background-color: #525659;
    font-family: "arial", sans-serif;
    font-size: 15pt;
    color: #000;
    max-width: 750px;
    margin: 1cm auto;
    padding: 20px 20px;
    line-height: 18pt;
    text-align: justify;
  }
  .title { text-align: center; margin-bottom: 4px; }
  .title h1 { font-size: 18pt; letter-spacing: 1px; margin: 0; }
  .title .nomor { font-size: 16pt; margin-top: 4px; }
  /* .dots {
    border-bottom: 1px dotted #000;
    display: inline-block;
    min-width: 20px;
  } */
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  td { vertical-align: top; padding: 3px 0; }
  .label { width: 90px; white-space: nowrap; }
  .colon { width: 15px; }
  .field-label { width: 110px; white-space: nowrap; }
  .no-col { width: 25px; vertical-align: top; }
  .memerintahkan { text-align: center; margin: 25px 0; font-weight: normal; }
  /* .fill { border-bottom: 1px dotted #000; display: block; min-height: 20px; } */
  .sub-table td { padding: 2px 0; }
  .peserta-sep { height: 12px; }
  .dasar-list { margin: 2px 0; padding-left: 22px; }
  .dasar-list li { margin: 2px 0; }
  .signature { margin-top: 60px; margin-left: 400px; }
  .signature .line { margin-bottom: 4px; }
  .signature .spacer { height: 60px; }
  .spacer-1pt { height: 1pt; }
  .no-print { margin-top: 20px; text-align: center; }
  .kop-surat-container { text-align: center; margin-bottom: 8px; }
  .kop-surat-image { max-width: 100%; max-height: 300px; height: auto; display: inline-block; }
  .doc-meta { margin: 10px 0; }
  .doc-meta table td { padding: 2px 0; }
  .doc-meta .label { width: 75px; white-space: nowrap; }
  .page {
    width: 230mm;
    min-height: 297mm;
    padding: 15mm 18mm;
    margin: 20px auto;
    background: white;
    position: relative;
    box-shadow: 0 0 6px rgba(0,0,0,0.3);
  }
  @media print {
    body { margin: 0; padding: 1cm 40px; background: white; }
    .page {
      box-shadow: none;
      margin: 0;
      page-break-after: always;
    }
    .no-print { display: none !important; }
  }
</style>
</head>
<body>

  <div class="page">
  <div class="kop-surat-container">
    @if ($kopSuratBase64)
      <img src="{{ $kopSuratBase64 }}" alt="Kop Surat" class="kop-surat-image">
    @endif
  </div>
  <br>
  <div class="title">
    <h1><u>SURAT TUGAS</u></h1>
    <div class="nomor">NOMOR : {{ $suratTugas->nomor ?: ' ' }}</div>
  </div>
  <br>
  <br>
  <div class="spacer-1pt"></div>
  <table>
    <tr>
      <td class="label" style="vertical-align: top; padding-top: 3px;">Dasar</td>
      <td class="colon" style="vertical-align: top; padding-top: 3px;">:</td>
      <td style="vertical-align: top; padding-top: 3px; text-align: justify;">
        @php
          $dasarItems = array_filter(array_map('trim', explode("\n", $suratTugas->dasar ?: '')), fn ($l) => $l !== '');
        @endphp
        @if (count($dasarItems))
          @if (count($dasarItems) === 1)
            {{ $dasarItems[0] }}
          @else
            <ol class="dasar-list">
              @foreach ($dasarItems as $item)
                <li>{{ $item }}</li>
              @endforeach
            </ol>
          @endif
        @else
          <span class="fill">&nbsp;</span>
        @endif
      </td>
    </tr>
  </table>
  <br>
  <div class="memerintahkan">MEMERINTAHKAN:</div>

  <table>
    <tr>
      <td class="label" style="vertical-align:top; width:80px;padding: 14px 0;">Kepada</td>
      <td class="colon" style="vertical-align:top;padding-top :14px;">:</td>
      <td>
        <table class="sub-table">
          @forelse ($peserta as $i => $p)
            <tr>
              @if (count($peserta) > 1)
                <td class="no-col">{{ $i + 1 }}.</td>
              @endif
              <td class="field-label" @if (count($peserta) === 1) style="padding-left: 20px;" @endif>Nama</td>
              <td class="colon">:</td>
              <td><span class="fill">{{ $p->nama ?: ' ' }}</span></td>
            </tr>
            <tr>
              @if (count($peserta) > 1)
                <td></td>
              @endif
              <td class="field-label" @if (count($peserta) === 1) style="padding-left: 20px;" @endif>Pangkat/gol</td>
              <td class="colon">:</td>
              <td><span class="fill">{{ $p->pangkat_golongan ?: ' ' }}</span></td>
            </tr>
            <tr>
              @if (count($peserta) > 1)
                <td></td>
              @endif
              <td class="field-label" @if (count($peserta) === 1) style="padding-left: 20px;" @endif>NIP</td>
              <td class="colon">:</td>
              <td><span class="fill">{{ $p->nip ?: ' ' }}</span></td>
            </tr>
            <tr>
              @if (count($peserta) > 1)
                <td></td>
              @endif
              <td class="field-label" @if (count($peserta) === 1) style="padding-left: 20px;" @endif>Jabatan</td>
              <td class="colon">:</td>
              <td><span class="fill">{{ $p->jabatan ?: ' ' }}</span></td>
            </tr>
            @if (!$loop->last && count($peserta) > 1)
              <tr><td colspan="4" class="peserta-sep"></td></tr>
            @endif
          @empty
            <tr>
              <td class="no-col">1.</td>
              <td class="field-label">Nama</td>
              <td class="colon">:</td>
              <td><span class="fill">&nbsp;</span></td>
            </tr>
            <tr>
              <td></td>
              <td class="field-label">Pangkat/gol</td>
              <td class="colon">:</td>
              <td><span class="fill">&nbsp;</span></td>
            </tr>
            <tr>
              <td></td>
              <td class="field-label">NIP</td>
              <td class="colon">:</td>
              <td><span class="fill">&nbsp;</span></td>
            </tr>
            <tr>
              <td></td>
              <td class="field-label">Jabatan</td>
              <td class="colon">:</td>
              <td><span class="fill">&nbsp;</span></td>
            </tr>
          @endforelse
        </table>
      </td>
    </tr>
  </table>

  <table style="margin-top:25px;">
    <tr class="untuk-row">
      <td class="label" style="vertical-align:top; padding-top: 14px;">Untuk</td>
      <td class="colon" style="vertical-align:top; padding-top: 14px;">:</td>
      <td>
        <table class="sub-table">
          <tr>
            <td class="no-col">1.</td>
            <td>
              Dalam rangka {{ $suratTugas->kegiatan ?: ' ' }} <br>
            </td>
          </tr>
          <tr>
            <td class="no-col">2.</td>
            <td>
              Perjalanan dinas dilaksanakan selama {{ $selamaHuruf ?: ' ' }} pada hari/tanggal {{ $tanggalText ?: 'Tanggal' }} pukul : {{ $suratTugas->pukul ?: 'Pukul' }} bertempat di {{ $suratTugas->tempat ?: 'Tempat' }}<br>
            </td>
          </tr>
          <tr>
            <td class="no-col">3.</td>
            <td><span class="fill">Membuat laporan hasil pelaksanaan perintah tugas kepada {{$suratTugas->penandatangan?->jabatan ?: 'Jabatan' }}</span></td>
          </tr>
          <tr>
            <td class="no-col">4.</td>
            <td><span class="fill">Perjalanan dinas ini dibiayai dari {{ $suratTugas->sumber_dana ?: 'dana APBD' }} Tahun Anggaran {{ $suratTugas->tahun_anggaran ?: date('Y') }}</span></td>
          </tr>
          <tr>
            <td class="no-col">5.</td>
            <td><span class="fill">Dilaksanakan sebaik-baiknya dengan penuh tanggungjawab</span></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <div class="signature">
    <div class="line">
      {{ $suratTugas->dikeluarkan_di ?: 'Nama Tempat' }}, {{ $tanggalDikeluarkan ?: 'Tanggal' }}
    </div>
    <!-- <div class="spacer"></div> -->
    <div class="line">
            @php
          $atasan = $suratTugas->penandatangan ?? null;
          $pegawaiTugas = $suratTugas->pegawaiTugas ?? null;

          $jabatanAtasan   = $atasan->jabatan ?? '';
          $unitKerjaAtasan = $atasan->unit_kerja ?? '';
          $nama            = $atasan->nama ?? '';
          $pangkat         = $atasan->pangkat_golongan ?? '';
          $nip             = $atasan->nip ?? '';
          $opdInduk        = $atasan->nama_opd_indu ?? '';

          $jabatanTugas    = $pegawaiTugas->jabatan ?? '';
          $unitKerjaTugas  = $pegawaiTugas->unit_kerja ?? '';

          $isPlt = $suratTugas->penandatangan_plt ?? false;
          $isPlh = false;
          $isAn  = $suratTugas->penandatangan_an ?? false;

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

  <div class="no-print" style="text-align:center; margin-top:20px;padding-left:15px">
    <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
    <a href="{{ route('surat-tugas.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Kembali</a>
</div>
  </div>

</body>
</html>

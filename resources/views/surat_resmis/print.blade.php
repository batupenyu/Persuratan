<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <title>Surat Resmi {{ $suratResmi->nomor }}</title>
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
      td.label {
        width: 100px;
        white-space: nowrap;
      }
      td.value {
        white-space: nowrap;
      }
      td.colon {
        width: 15px;
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
      .header {
        text-align: center;
      }
      .tempat-tanggal {
        text-align: right;
        margin-bottom: 15pt;
      }
      .content p {
        margin-top: 0;
        margin-bottom: 0;
        line-height: 1.5;
        text-align: justify;
      }
      .pembuka-surat, .isi-surat, .penutup-surat {
        text-align: justify;
        text-indent: 1cm;
        margin-bottom: 10pt;
        line-height: 1.5;
      }
      .data-pegawai .label {
        width: 150px;
        white-space: nowrap;
      }
      .data-pegawai .colon {
        width: 15px;
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
      $fmt = fn ($d, $f = '%d %B %Y') => $d ? \App\Http\Controllers\SuratResmiController::formatTanggal($d, $f) : '...................';
      $penandatangan = $suratResmi->penandatangan;
    @endphp

    @if($kopSuratBase64)
    <img
      src="{{ $kopSuratBase64 }}"
      style="max-width: 100%; height: auto; display: block; margin-bottom: 20px"
    />
    @endif

    <div class="tempat-tanggal">
      {{ $suratResmi->tempat_ditetapkan }}, {{ $fmt($suratResmi->tanggal_ditetapkan) }}
    </div>

    <!-- <div class="header">
      <p>
        <strong>SURAT RESMI</strong><br />
        <strong>Nomor : {{ $suratResmi->nomor }}</strong>
      </p>
    </div>
    <br /> -->

    <div class="content">
      <table>
        <tr>
          <td class="label">Sifat</td>
          <td class="colon">:</td>
          <td>{{ $suratResmi->sifat }}</td>
        </tr>
        <tr>
          <td class="label">Lampiran</td>
          <td class="colon">:</td>
          <td>{{ $suratResmi->lampiran }}</td>
        </tr>
        <tr>
          <td class="label">Perihal</td>
          <td class="colon">:</td>
          <td><strong>{{ $suratResmi->perihal }}</strong></td>
        </tr>
      </table>
      <br />

      <p>
        Kepada Yth.<br />
        {{ $suratResmi->pejabat_tujuan_surat }}<br />
        di<br />
        <strong>{{ $suratResmi->kota_tujuan_surat }}</strong>
      </p>
      <br />

      <div class="pembuka-surat">
        <p>{!! nl2br(e($suratResmi->pembuka_surat)) !!}</p>
      </div>

      @if($suratResmi->tanggal_kegiatan || $suratResmi->waktu_kegiatan || $suratResmi->tempat_kegiatan)
      <table>
        @if($suratResmi->tanggal_kegiatan)
        <tr>
          <td class="label">Hari/Tanggal</td>
          <td class="colon">:</td>
          <td>{{ $fmt($suratResmi->tanggal_kegiatan, '%A, %d %B %Y') }}</td>
        </tr>
        @endif
        @if($suratResmi->waktu_kegiatan)
        <tr>
          <td class="label">Waktu</td>
          <td class="colon">:</td>
          <td>{{ $suratResmi->waktu_kegiatan }}</td>
        </tr>
        @endif
        @if($suratResmi->tempat_kegiatan)
        <tr>
          <td class="label">Tempat</td>
          <td class="colon">:</td>
          <td>{{ $suratResmi->tempat_kegiatan }}</td>
        </tr>
        @endif
      </table>
      <br />
      @endif

      @if($suratResmi->pegawai->isNotEmpty())
      <p>Adapun nama pegawai yang dimaksud:</p>
      <table class="data-pegawai">
        @foreach($suratResmi->pegawai as $pegawai)
        <tr>
          <td class="label">Nama</td>
          <td class="colon">:</td>
          <td>{{ $pegawai->nama }}</td>
        </tr>
        <tr>
          <td class="label">NIP</td>
          <td class="colon">:</td>
          <td>{{ $pegawai->nip }}</td>
        </tr>
        @if($pegawai->pangkat !== '' && $pegawai->pangkat !== 'IX')
        <tr>
          <td class="label">Pangkat/Gol. Ruang</td>
          <td class="colon">:</td>
          <td>{{ $pegawai->pangkat }}/{{ $pegawai->golongan }}</td>
        </tr>
        @endif
        <tr>
          <td class="label">Jabatan</td>
          <td class="colon">:</td>
          <td>{{ $pegawai->jabatan ?? ($pegawai->tugas_tambahan ?: ($pegawai->jenis_ptk ?: '')) }}</td>
        </tr>
        <tr>
          <td class="label">Unit Kerja</td>
          <td class="colon">:</td>
          <td>{{ $pegawai->lembaga_pengangkatan }}</td>
        </tr>
        @if(!$loop->last)
        <tr><td colspan="3"><hr style="border-top: 1px dashed #ccc;"></td></tr>
        @endif
        @endforeach
      </table>
      <br />
      @endif

      <div class="isi-surat">
        <p>{!! nl2br(e($suratResmi->isi_surat)) !!}</p>
      </div>

      <div class="penutup-surat">
        <p>{!! nl2br(e($suratResmi->penutup_surat)) !!}</p>
      </div>
    </div>

    @if($penandatangan)
    <div class="signature" style="padding-left: 430px">
      <p>
        @php
            $atasan = $penandatangan;
            $jabatan = $atasan?->jabatan ?? '';
            $unitKerja = $atasan?->unit_kerja ?? '';
            $nama = $atasan?->nama ?? '';
            $pangkat = $atasan?->pangkat_golongan ?? '';
            $nip = $atasan?->nip ?? '';

            $upperNamaKeepGelar = function ($nama) {
                if (blank($nama)) return '';

                $parts = explode(',', $nama, 2);
                $namaDepan = trim($parts[0]);
                $gelar = isset($parts[1]) ? trim($parts[1]) : '';
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

            $jabatanUpper = mb_strtoupper($jabatan);
            $unitKerjaUpper = mb_strtoupper($unitKerja);
            $namaFormatted = $upperNamaKeepGelar($nama);
            $pangkatFormatted = $titleCaseGelar($pangkat);
        @endphp

        {!! e($jabatanUpper) !!}{{ $unitKerjaUpper ? ' ' . $unitKerjaUpper : '' }}
        <br><br><br><br>
        {!! e($namaFormatted) !!}
        @if($pangkat && $pangkat != '-')
            <br>{!! e($pangkatFormatted) !!}
        @endif
        <br>NIP. {{ $nip }}
      </p>
    </div>
    @endif

    <div class="no-print" style="text-align:center; margin-top:20px;padding-left:15px">
    <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
    <a href="{{ route('surat-resmis.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Kembali</a>
</div>
    </div>
  </body>
</html>

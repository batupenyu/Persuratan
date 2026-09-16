<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <title>Surat Santunan Korpri - {{ $suratSantunan->nomor_surat }}</title>
    <style>
      @page {
        size: A4;
        margin: 1cm 1cm;
      }
      body {
        background-color: #525659;
        font-family: Arial, sans-serif;
        font-size: 14pt;
        line-height: 1.2;
        color: #333;
      }
      .kop-surat-container {
        text-align: center;
        margin-bottom: 20px;
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
      .address-block {
        text-align: left;
        margin-bottom: 20px;
      }
      .content-block {
        text-align: justify;
      }
      .content-block p {
        text-indent: 50px;
      }
      .signature-block {
        padding-left: 250pt;
        text-align: center;
        margin-top: 40px;
      }
      .info-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
      }
      .info-table td {
        padding: 2px 0;
        vertical-align: top;
      }
      .info-table .label {
        width: 70px;
        white-space: nowrap;
      }
      .info-table .colon {
        width: 20px;
        text-align: center;
      }
      .info-table .value {
        white-space: nowrap;
      }
      .no-border {
        border: none;
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
    <div class="kop-surat-container">
      @if($kopSuratBase64)
      <img
        src="{{ $kopSuratBase64 }}"
        alt="Kop Surat"
        class="kop-surat-image"
      />
      @endif
    </div>

    <div style="text-align: right">
        {{ $suratSantunan->tempat_ditetapkan }}, {{ \App\Http\Controllers\SuratSantunanController::formatTanggal($suratSantunan->tanggal_ditetapkan, '%d %B %Y') }}
    </div>
    <table class="info-table">
        <tr>
            <td class="label">Nomor</td>
            <td class="colon">:</td>
            <td>{{ $suratSantunan->nomor_surat }}</td>
        </tr>
        <tr>
            <td class="label">Sifat</td>
            <td class="colon">:</td>
            <td>{{ $suratSantunan->sifat }}</td>
        </tr>
        <tr>
            <td class="label">Lampiran</td>
            <td class="colon">:</td>
            <td>{{ $suratSantunan->lampiran }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td class="colon">:</td>
            <td>{{ $suratSantunan->perihal }}</td>
        </tr>
    </table>
    <br /><br />
    <div class="address-block">
      Kepada Yth.<br />
      {{ $suratSantunan->instansi_tujuan_surat }}<br />
      di<br />
      {{ $suratSantunan->kota_tujuan_surat }}
    </div>
    <br />
    <div class="content-block">
        @if($suratSantunan->isi_surat_pertama)
        <p>{{ $suratSantunan->isi_surat_pertama }}</p>
        @endif
    </div>
    <div class="content-block">
      <p>Yang bertanda tangan dibawah ini :</p>
    </div>

    <table class="info-table">
      <tr>
        <td class="label">Nama</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->penandatangan->nama ?? '' }}</td>
      </tr>
      <tr>
        <td class="label">Pangkat/Gol.</td>
        <td class="colon">:</td>
        <td>
          {{ $suratSantunan->penandatangan->pangkat ?? '' }},{{ $suratSantunan->penandatangan->golongan ?? '' }}
        </td>
      </tr>
      <tr>
        <td class="label">Jabatan</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->penandatangan->jabatan ?? '' }}</td>
      </tr>
    </table>
    <div class="content-block">
      <p>
        Dengan ini menyampaikan permohonan usulan bantuan korpri/tali asih
        pegawai berikut :
      </p>
    </div>

    <table class="info-table">
      <tr>
        <td class="label">Nama</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->pegawai->nama ?? '' }}</td>
      </tr>
      <tr>
        <td class="label">NIP</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->pegawai->nip ?? '' }}</td>
      </tr>
      <tr>
        <td class="label">Pangkat/Gol.</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->pegawai->pangkat ?? '' }},{{ $suratSantunan->pegawai->golongan ?? '' }}</td>
      </tr>
      <tr>
        <td class="label">Tempat/Tgl. Lahir</td>
        <td class="colon">:</td>
        <td>
          {{ $suratSantunan->pegawai->tempat_lahir ?? '' }} /
          {{ \App\Http\Controllers\SuratSantunanController::formatTanggal($suratSantunan->pegawai->tanggal_lahir ?? null, '%d %B %Y') }}
        </td>
      </tr>
      <tr>
        <td class="label">Alamat</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->pegawai->alamat_jalan ?? '' }}</td>
      </tr>
      <tr>
        <td class="label">Unit Kerja</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->pegawai->lembaga_pengangkatan ?? '' }}</td>
      </tr>
      <tr>
        <td class="label">Nomor HP</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->pegawai->hp ?? '' }}</td>
      </tr>
      <tr>
        <td class="label">Nama Istri/Suami</td>
        <td class="colon">:</td>
        <td>{{ $suratSantunan->pegawai->nama_suami_istri ?? '' }}</td>
      </tr>
    </table>

    <div class="content-block">
      <p>
        Demikian surat permohonan ini kami sampaikan, atas perhatian dan
        kerjasama yang diberikan kami ucapkan terima kasih.
      </p>
    </div>

    <div class="signature-block">
      <div>
        {{ $suratSantunan->penandatangan->jabatan ?? '' }}
        {{ $suratSantunan->penandatangan->unit_kerja ?? '' }}
      </div>
      <br /><br /><br />
      <div>
        <u>{{ $suratSantunan->penandatangan->nama ?? '' }}</u><br />
        {{ $suratSantunan->penandatangan->pangkat ?? '' }},{{ $suratSantunan->penandatangan->golongan ?? '' }}<br />
        NIP. {{ $suratSantunan->penandatangan->nip ?? '' }}
      </div>
    </div>

    </div>

    <div class="no-print" style="text-align:center; margin-top:20px;padding-left:15px">
    <button onclick="window.print()" style="background:#2563eb; color:#fff; border:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem; cursor:pointer;">Cetak</button>
    <a href="{{ route('surat-santunans.index') }}" style="display:inline-block; margin-left:0.5rem; background:#6b7280; color:#fff; text-decoration:none; padding:0.6rem 1.4rem; border-radius:4px; font-size:0.95rem;">Kembali</a>
</div>
  </body>
</html>

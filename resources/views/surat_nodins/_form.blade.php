@php
    $suratNodin = $suratNodin ?? null;

    $pesertaIndex = 0;

    $asns    = $asns    ?? [];
    $siswas  = $siswas  ?? [];
    $dudikas = $dudikas ?? [];
    $logos   = $logos   ?? [];

    /*
    |--------------------------------------------------------------------------
    | Grouping peserta lama
    |--------------------------------------------------------------------------
    | Berdasarkan rentang tanggal (tgl_awal, tgl_akhir)
    | dan kelompok pegawai/siswa yang saling terhubung,
    | sehingga kartu peserta pada saat create tidak
    | menggabung maupun terpecah saat edit.
    |--------------------------------------------------------------------------
    */

    $pesertaData = [];

    /*
     * Nama DUDIKA tidak boleh muncul pada field
     * Tempat Kegiatan.
     *
     * Saat penyimpanan, nama DUDIKA ikut disimpan
     * pada kolom tempat_kegiatan (SuratNodinController
     * syncPeserta) dengan penanda dudika_id, sehingga
     * saat form di edit, baris bertanda dudika_id
     * harus dikecualikan dari daftar tempat.
     */
    $dudikaNameMap = [];

    foreach ($dudikas as $dudika) {
        $namaDudika = trim((string) ($dudika->nama_dudika ?? ''));
        if ($namaDudika !== '') {
            $dudikaNameMap[$namaDudika] = $dudika->id;
        }
    }

    if (isset($suratNodin) && $suratNodin->pesertaSuratUsulans->count() > 0) {

        /*
         * Tahap 1: kelompokkan baris berdasarkan
         * rentang tanggal (tgl_awal + tgl_akhir).
         */
        $rowsByTanggal = [];

        foreach ($suratNodin->pesertaSuratUsulans as $peserta) {

            $tglAwal = $peserta->tgl_awal_kegiatan
                ? \Carbon\Carbon::parse($peserta->tgl_awal_kegiatan)->format('Y-m-d')
                : '';

            $tglAkhir = $peserta->tgl_akhir_kegiatan
                ? \Carbon\Carbon::parse($peserta->tgl_akhir_kegiatan)->format('Y-m-d')
                : '';

            $rowsByTanggal[$tglAwal . '_' . $tglAkhir][] = [
                'tgl_awal' => $tglAwal,
                'tgl_akhir' => $tglAkhir,
                'peserta' => $peserta,
            ];
        }

        $groupedPeserta = [];

        foreach ($rowsByTanggal as $tglKey => $rows) {

            /*
             * Tahap 2: pisahkan kartu yang memang
             * berbeda pada saat create.
             *
             * Penyimpanan membuat kombinasi
             * (pegawai x siswa x tempat), sehingga
             * kelompok hanya berdasarkan tanggal
             * akan menggabungkan beberapa kartu.
             */
            $groupedRows = \App\Support\PesertaGrouping::group(
                array_map(
                    fn($row) => [
                        'pegawai_id' => $row['peserta']->pegawai_id,
                        'siswa_id' => $row['peserta']->siswa_id,
                        'tempat' => $row['peserta']->tempat_kegiatan,
                        'row' => $row,
                    ],
                    $rows
                )
            );

            foreach ($groupedRows as $groupIndex => $groupRows) {

                $key = $tglKey . '#' . $groupIndex;

                $groupedPeserta[$key] = [
                    'pegawai_ids'      => [],
                    'siswa_ids'        => [],
                    'tgl_awal'         => $groupRows[0]['row']['tgl_awal'],
                    'tgl_akhir'        => $groupRows[0]['row']['tgl_akhir'],
                    'tempat_kegiatan'  => [],
                    'dudika_ids'       => [],
                ];

                foreach ($groupRows as $item) {

                    $peserta = $item['row']['peserta'];

                    if ($peserta->pegawai_id
                        && !in_array($peserta->pegawai_id, $groupedPeserta[$key]['pegawai_ids'])) {
                        $groupedPeserta[$key]['pegawai_ids'][] = $peserta->pegawai_id;
                    }

                    if ($peserta->siswa_id
                        && !in_array($peserta->siswa_id, $groupedPeserta[$key]['siswa_ids'])) {
                        $groupedPeserta[$key]['siswa_ids'][] = $peserta->siswa_id;
                    }

                    /*
                     * Tempat kegiatan.
                     *
                     * Nama DUDIKA tidak boleh muncul pada
                     * field ini.
                     *
                     * Data lama dapat menyimpan nama DUDIKA
                     * pada tempat_kegiatan tanpa dudika_id
                     * (bahkan beberapa nama dalam satu baris
                     * dipisahkan baris baru), sehingga setiap
                     * baris dicocokkan dengan daftar DUDIKA.
                     */
                    $tempatParts = preg_split(
                        '/\r\n|\r|\n/',
                        (string) ($peserta->tempat_kegiatan ?? '')
                    );

                    foreach ($tempatParts as $tempatPart) {

                        $tempat = rtrim(trim($tempatPart), ', ');

                        if ($tempat === '') {
                            continue;
                        }

                        $dudikaIdTempat = $dudikaNameMap[$tempat] ?? null;

                        if ($dudikaIdTempat) {

                            if (!in_array(
                                $dudikaIdTempat,
                                $groupedPeserta[$key]['dudika_ids']
                            )) {
                                $groupedPeserta[$key]['dudika_ids'][] = $dudikaIdTempat;
                            }

                            continue;
                        }

                        if ($peserta->dudika_id) {
                            continue;
                        }

                        if (!in_array(
                            $tempat,
                            $groupedPeserta[$key]['tempat_kegiatan']
                        )) {
                            $groupedPeserta[$key]['tempat_kegiatan'][] = $tempat;
                        }
                    }

                    if ($peserta->dudika_id
                        && !in_array($peserta->dudika_id, $groupedPeserta[$key]['dudika_ids'])) {
                        $groupedPeserta[$key]['dudika_ids'][] = $peserta->dudika_id;
                    }
                }
            }
        }

        foreach ($groupedPeserta as &$group) {
            if (empty($group['tempat_kegiatan'])) {
                $group['tempat_kegiatan'] = [''];
            }
        }
        unset($group);

        $pesertaData = array_values($groupedPeserta);
    }

    if (empty($pesertaData)) {
        $pesertaData = [[
            'pegawai_ids'     => [],
            'siswa_ids'       => [],
            'tgl_awal'        => '',
            'tgl_akhir'       => '',
            'tempat_kegiatan' => [''],
            'dudika_ids'      => [],
        ]];
    }
@endphp


{{-- ========================================================= --}}
{{-- CSS                                                       --}}
{{-- ========================================================= --}}
<style>
    .peserta-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
    }

    .dark .peserta-card {
        background: #1f2937;
        border-color: #374151;
    }

    .peserta-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .peserta-card-header {
        border-color: #374151;
    }

    .peserta-number {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #2563eb;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
        flex-shrink: 0;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 7px;
        color: inherit;
    }

    .form-help {
        font-size: 12px;
        color: #6b7280;
        margin-top: 5px;
    }

    .dark .form-help {
        color: #9ca3af;
    }

    /* =========================================================
       TEMPAT KEGIATAN — input text satu baris + tombol X
       ========================================================= */
    .tempat-kegiatan-list {
        display: block;
    }

    .tempat-item {
        display: flex !important;
        align-items: flex-start !important;
        gap: 8px !important;
    }

    .tempat-item input[type="text"] {
        flex: 1 1 auto !important;
        display: block !important;
        width: 100% !important;
        height: 42px !important;
        min-height: 42px !important;
        max-height: 42px !important;
        padding: 8px 12px !important;
        line-height: 1.4 !important;
        font-size: 14px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        color: #111827 !important;
        box-sizing: border-box !important;
        resize: none !important;
        overflow: hidden !important;
        appearance: none !important;
        -webkit-appearance: none !important;
        outline: none !important;
    }

    .tempat-item input[type="text"]:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, .25) !important;
    }

    .dark .tempat-item input[type="text"] {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
        color: #f3f4f6 !important;
    }

    .tempat-item .tempat-remove {
        flex: 0 0 42px !important;
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
        min-height: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        margin: 0 !important;
        line-height: 1 !important;
        font-size: 20px !important;
        font-weight: bold !important;
        color: #ffffff !important;
        background-color: #ef4444 !important;
        border: none !important;
        border-radius: 8px !important;
        cursor: pointer !important;
        transition: background-color .15s ease;
    }

    .tempat-item .tempat-remove:hover {
        background-color: #dc2626 !important;
    }

    /* =========================================================
       SELECT2 — Pegawai / Siswa / DUDIKA
       ========================================================= */
    .select2-container {
        width: 100% !important;
        display: block !important;
    }

    .select2-container--default .select2-selection--multiple {
        min-height: 42px !important;
        padding: 4px 6px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
        background: #ffffff !important;
    }

    .dark .select2-container--default .select2-selection--multiple {
        background-color: #374151 !important;
        border-color: #4b5563 !important;
        color: #f3f4f6 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 4px !important;
        padding: 2px !important;
        list-style: none !important;
        margin: 0 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #2563eb !important;
        border: none !important;
        color: #ffffff !important;
        padding: 3px 8px !important;
        border-radius: 4px !important;
        margin: 0 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ffffff !important;
        margin-right: 4px !important;
        border: none !important;
    }

    .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 6px !important;
        font-size: 14px !important;
        height: 26px !important;
        border: none !important;
        outline: none !important;
        background: transparent !important;
        color: inherit !important;
    }

    /* =========================================================
       TAB
       ========================================================= */
    .tab-btn {
        transition: background-color .15s ease, color .15s ease;
    }

    .tab-btn.is-active {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }

    @media (max-width: 768px) {
        .peserta-card {
            padding: 15px;
        }
    }
</style>


{{-- ========================================================= --}}
{{-- FORM TABS                                                 --}}
{{-- ========================================================= --}}
<div id="form-tabs">

    <div class="sticky top-0 z-20 -mx-6 px-6 py-2 mb-6
                bg-white/90 dark:bg-gray-800/90
                backdrop-blur border-b dark:border-gray-700">
        <nav id="tab-nav" class="flex flex-wrap gap-2">
            <button type="button" data-tab="data-umum"     class="tab-btn is-active px-4 py-2 rounded-full font-medium">Data Umum</button>
            <button type="button" data-tab="isi-surat"     class="tab-btn px-4 py-2 rounded-full font-medium text-gray-600 hover:bg-gray-100">Isi Surat</button>
            <button type="button" data-tab="peserta"       class="tab-btn px-4 py-2 rounded-full font-medium text-gray-600 hover:bg-gray-100">Peserta</button>
            <button type="button" data-tab="penandatangan" class="tab-btn px-4 py-2 rounded-full font-medium text-gray-600 hover:bg-gray-100">Penandatangan</button>
        </nav>
    </div>


    {{-- ===================================================== --}}
    {{-- DATA UMUM                                            --}}
    {{-- ===================================================== --}}
    <div id="tab-data-umum" class="tab-content grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="md:col-span-2">
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Data Umum</h2>
        </div>

        <div>
            <label class="form-label">Nomor</label>
            <input type="text" name="nomor"
                   value="{{ old('nomor', $suratNodin->nomor ?? '................................................................') }}"
                   class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <div>
            <label class="form-label">Sifat</label>
            <input type="text" name="sifat"
                   value="{{ old('sifat', $suratNodin->sifat ?? 'Penting') }}"
                   class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <div>
            <label class="form-label">Lampiran</label>
            <input type="text" name="lampiran"
                   value="{{ old('lampiran', $suratNodin->lampiran ?? '1 (satu) berkas') }}"
                   class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <div>
            <label class="form-label">Hal</label>
            <input type="text" name="hal"
                   value="{{ old('hal', $suratNodin->hal ?? 'Permohonan Izin Perjalanan Dinas') }}"
                   class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <div class="md:col-span-2">
            <label class="form-label">Yth.</label>
            <input type="text" name="kepada"
                   value="{{ old('kepada', $suratNodin->kepada ?? 'Yth. Gubernur Kepulauan Bangka Belitung') }}"
                   class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>

        <div class="md:col-span-2">
            <label class="form-label">Dari</label>
            <select name="dari" id="select-dari"
                    class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Pilih Dari --</option>
                <option value="Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung"
                    {{ old('dari', $suratNodin->dari ?? '') == 'Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung' ? 'selected' : '' }}>
                    Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung
                </option>
                <option value="Kepala SMK Negeri 1 Koba"
                    {{ old('dari', $suratNodin->dari ?? '') == 'Kepala SMK Negeri 1 Koba' ? 'selected' : '' }}>
                    Kepala SMK Negeri 1 Koba
                </option>
            </select>

            <div class="mt-3">
                <label class="inline-flex items-center">
                    <input type="hidden" name="dari_plt" value="0">
                    <input type="checkbox" name="dari_plt" value="1"
                        {{ old('dari_plt', $suratNodin->dari_plt ?? false) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm">Plt pada pengirim</span>
                </label>

                <label class="inline-flex items-center ml-6">
                    <input type="checkbox" name="dari_an" value="1"
                        {{ old('dari_an', $suratNodin->dari_an ?? false) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm">a.n (Atas Nama)</span>
                </label>
            </div>
        </div>

        <div>
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal"
                   value="{{ old('tanggal', optional($suratNodin->tanggal ?? null)->format('Y-m-d')) }}"
                   class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- ISI SURAT                                            --}}
    {{-- ===================================================== --}}
    <div id="tab-isi-surat" class="tab-content hidden grid grid-cols-1 gap-6">

        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Isi Surat</h2>
        </div>

        <div>
            <label class="form-label">Dasar Surat</label>
            <textarea name="dasar_surat" rows="5"
                      class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('dasar_surat', $suratNodin->dasar_surat ?? '') }}</textarea>
        </div>

        <div>
            <label class="form-label">Isi Surat</label>
            <textarea name="isi_surat" rows="7"
                      class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('isi_surat', $suratNodin->isi_surat ?? '') }}</textarea>
        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- PESERTA                                               --}}
    {{-- ===================================================== --}}
    <div id="tab-peserta" class="tab-content hidden">

        <div class="mb-7">
            <label class="form-label">Kop Surat</label>
            <select name="kop_surat" id="select-kop"
                    class="w-full border rounded-lg px-3 py-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Pilih Kop Surat --</option>
                @foreach($logos as $logo)
                    <option value="{{ $logo->name }}"
                        {{ old('kop_surat', $suratNodin->kop_surat ?? '') == $logo->name ? 'selected' : '' }}>
                        {{ $logo->name ?: 'Tanpa Nama' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div>
                <h2 class="text-xl font-bold">Daftar Peserta</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Satu kelompok dapat terdiri dari banyak pegawai,
                    banyak siswa, dan banyak tempat kegiatan.
                </p>
            </div>

            <button type="button" id="tambah-peserta"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow">
                + Tambah Peserta
            </button>
        </div>

        <div id="peserta-list" class="space-y-5">
            @foreach($pesertaData as $group)
                @php
                    $currentIndex      = $pesertaIndex++;
                    $selectedPegawai   = $group['pegawai_ids']     ?? [];
                    $selectedSiswa     = $group['siswa_ids']       ?? [];
                    $selectedDudikaIds = $group['dudika_ids']      ?? [];
                    $tempatList        = $group['tempat_kegiatan'] ?? [''];
                    if (empty($tempatList)) { $tempatList = ['']; }
                @endphp

                <div class="peserta-card" data-index="{{ $currentIndex }}">

                    <div class="peserta-card-header">
                        <div class="flex items-center">
                            <span class="peserta-number">{{ $currentIndex + 1 }}</span>
                            <div>
                                <div class="font-bold text-base">Peserta / Kelompok</div>
                                <div class="text-xs text-gray-500">Pilih pegawai, siswa, tanggal dan tempat</div>
                            </div>
                        </div>
                        <button type="button"
                                class="hapus-peserta bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm">
                            Hapus
                        </button>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label">Pegawai yang Ikut</label>
                            <select name="peserta[{{ $currentIndex }}][pegawai_id][]" multiple class="pegawai-select2">
                                @foreach($asns as $asn)
                                    <option value="{{ $asn->id }}"
                                        {{ in_array($asn->id, $selectedPegawai) ? 'selected' : '' }}>
                                        {{ $asn->nama }}@if($asn->nip) ({{ $asn->nip }})@endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-help">Ketik nama untuk mencari. Dapat memilih beberapa pegawai.</div>
                        </div>

                        <div>
                            <label class="form-label">Siswa yang Ikut</label>
                            <select name="peserta[{{ $currentIndex }}][siswa_id][]" multiple class="siswa-select2">
                                @foreach($siswas as $siswa)
                                    <option value="{{ $siswa->id }}"
                                        {{ in_array($siswa->id, $selectedSiswa) ? 'selected' : '' }}>
                                        {{ $siswa->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-help">Ketik nama untuk mencari. Dapat memilih beberapa siswa.</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="form-label">Tanggal Mulai Kegiatan</label>
                            <input type="date"
                                   name="peserta[{{ $currentIndex }}][tgl_awal_kegiatan]"
                                   value="{{ $group['tgl_awal'] ?? '' }}"
                                   class="w-full border rounded-lg px-4 py-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="form-label">Tanggal Selesai Kegiatan</label>
                            <input type="date"
                                   name="peserta[{{ $currentIndex }}][tgl_akhir_kegiatan]"
                                   value="{{ $group['tgl_akhir'] ?? '' }}"
                                   class="w-full border rounded-lg px-4 py-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="form-label">DUDIKA <span class="text-gray-400">(opsional)</span></label>
                        <select name="peserta[{{ $currentIndex }}][dudika_id][]" multiple
                                class="dudika-select2 w-full">
                            @foreach($dudikas as $dudika)
                                <option value="{{ $dudika->id }}"
                                    {{ in_array($dudika->id, $selectedDudikaIds) ? 'selected' : '' }}>
                                    {{ $dudika->nama_dudika }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-help">Ketik nama DUDIKA untuk mencari. Dapat memilih beberapa DUDIKA.</div>
                    </div>

                    <div class="mt-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <div>
                                <label class="form-label mb-0">Tempat Kegiatan</label>
                                <div class="form-help">Satu kelompok dapat memiliki beberapa tempat kegiatan.</div>
                            </div>
                            <button type="button"
                                    class="tambah-tempat bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                                + Tambah Tempat
                            </button>
                        </div>

                        <div class="tempat-kegiatan-list space-y-3">
                            @foreach($tempatList as $tempat)
                                <div class="tempat-item">
                                    <input type="text"
                                           name="peserta[{{ $currentIndex }}][tempat_kegiatan][]"
                                           value="{{ trim($tempat) }}"
                                           placeholder="Masukkan tempat kegiatan...">
                                    <button type="button"
                                            class="hapus-tempat tempat-remove"
                                            title="Hapus tempat">×</button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- TEMPLATE PESERTA BARU                                 --}}
    {{-- ===================================================== --}}
    <div id="peserta-template" class="hidden">
        <div class="peserta-card" data-index="__INDEX__">

            <div class="peserta-card-header">
                <div class="flex items-center">
                    <span class="peserta-number">#</span>
                    <div>
                        <div class="font-bold">Peserta / Kelompok</div>
                        <div class="text-xs text-gray-500">Peserta baru</div>
                    </div>
                </div>
                <button type="button"
                        class="hapus-peserta bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm">
                    Hapus
                </button>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">Pegawai yang Ikut</label>
                    <select name="peserta[__INDEX__][pegawai_id][]" multiple class="pegawai-select2">
                        @foreach($asns as $asn)
                            <option value="{{ $asn->id }}">
                                {{ $asn->nama }}@if($asn->nip) ({{ $asn->nip }})@endif
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">Bisa memilih beberapa pegawai.</div>
                </div>

                <div>
                    <label class="form-label">Siswa yang Ikut</label>
                    <select name="peserta[__INDEX__][siswa_id][]" multiple class="siswa-select2">
                        @foreach($siswas as $siswa)
                            <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
                        @endforeach
                    </select>
                    <div class="form-help">Bisa memilih beberapa siswa.</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="form-label">Tanggal Mulai Kegiatan</label>
                    <input type="date" name="peserta[__INDEX__][tgl_awal_kegiatan]"
                           class="w-full border rounded-lg px-4 py-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="form-label">Tanggal Selesai Kegiatan</label>
                    <input type="date" name="peserta[__INDEX__][tgl_akhir_kegiatan]"
                           class="w-full border rounded-lg px-4 py-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="mt-6">
                <label class="form-label">DUDIKA <span class="text-gray-400">(opsional)</span></label>
                <select name="peserta[__INDEX__][dudika_id][]" multiple class="dudika-select2 w-full">
                    @foreach($dudikas as $dudika)
                        <option value="{{ $dudika->id }}">{{ $dudika->nama_dudika }}</option>
                    @endforeach
                </select>
                <div class="form-help">Ketik nama DUDIKA untuk mencari. Dapat memilih beberapa DUDIKA.</div>
            </div>

            <div class="mt-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                    <div>
                        <label class="form-label mb-0">Tempat Kegiatan</label>
                        <div class="form-help">Dapat menambahkan banyak tempat.</div>
                    </div>
                    <button type="button"
                            class="tambah-tempat bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg">
                        + Tambah Tempat
                    </button>
                </div>

                <div class="tempat-kegiatan-list space-y-3">
                    <div class="tempat-item">
                        <input type="text"
                               name="peserta[__INDEX__][tempat_kegiatan][]"
                               placeholder="Masukkan tempat kegiatan...">
                        <button type="button"
                                class="hapus-tempat tempat-remove"
                                title="Hapus tempat">×</button>
                    </div>
                </div>
            </div>

        </div>
    </div>


    {{-- ===================================================== --}}
    {{-- PENANDATANGAN                                         --}}
    {{-- ===================================================== --}}
    <div id="tab-penandatangan" class="tab-content hidden grid grid-cols-1 gap-6">

        <div>
            <h2 class="text-lg font-semibold mb-4 border-b pb-2">Penandatangan</h2>
        </div>

        <div>
            <label class="form-label">Pilih Penandatangan</label>
            <select name="penandatangan_id"
                    class="w-full border rounded-lg px-3 py-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Pilih Penandatangan --</option>
                @foreach($asns as $asn)
                    <option value="{{ $asn->id }}"
                        {{ old('penandatangan_id', $suratNodin->penandatangan_id ?? $defaultPenandatanganId ?? '') == $asn->id ? 'selected' : '' }}>
                        {{ $asn->nama }}@if($asn->nip) ({{ $asn->nip }})@endif
                    </option>
                @endforeach
            </select>

            <div class="mt-3">
                <label class="inline-flex items-center">
                    <input type="hidden" name="penandatangan_plt" value="0">
                    <input type="checkbox" name="penandatangan_plt" value="1"
                        {{ old('penandatangan_plt', $suratNodin->penandatangan_plt ?? false) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm">Plt pada penandatangan</span>
                </label>
                <label class="inline-flex items-center ml-6">
                    <input type="checkbox" name="penandatangan_an" value="1"
                        {{ old('penandatangan_an', $suratNodin->penandatangan_an ?? false) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm">a.n (Atas Nama)</span>
                </label>
            </div>
        </div>

        <div>
            <label class="form-label">Pilih Pegawai Yang Diberi Tugas</label>
            <select name="pegawai_tugas_id"
                    class="w-full border rounded-lg px-3 py-3 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">-- Pilih Pegawai Tugas --</option>
                @foreach($asns as $asn)
                    <option value="{{ $asn->id }}"
                        {{ old('pegawai_tugas_id', $suratNodin->pegawai_tugas_id ?? '') == $asn->id ? 'selected' : '' }}>
                        {{ $asn->nama }}@if($asn->nip) ({{ $asn->nip }})@endif
                    </option>
                @endforeach
            </select>
        </div>
    </div>

</div>


{{-- ========================================================= --}}
{{-- SELECT2 + JQUERY                                          --}}
{{-- ========================================================= --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


{{-- ========================================================= --}}
{{-- JAVASCRIPT                                                --}}
{{-- ========================================================= --}}
<script>
(function () {
    'use strict';

    // =========================================================
    // TAB SWITCH
    // =========================================================
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(function (el) {
            el.classList.add('hidden');
        });

        var target = document.getElementById('tab-' + tabId);
        if (target) target.classList.remove('hidden');

        document.querySelectorAll('.tab-btn').forEach(function (btn) {
            var isActive = btn.dataset.tab === tabId;
            btn.classList.toggle('is-active', isActive);
            btn.classList.toggle('bg-blue-600', isActive);
            btn.classList.toggle('text-white', isActive);
            btn.classList.toggle('text-gray-600', !isActive);
            btn.classList.toggle('hover:bg-gray-100', !isActive);
        });
    }

    window.switchTab = switchTab; // fallback kalau masih ada onclick lama

    // =========================================================
    // DOM READY
    // =========================================================
    document.addEventListener('DOMContentLoaded', function () {

        // ---------- Tab nav ----------
        var tabNav = document.getElementById('tab-nav');
        if (tabNav) {
            tabNav.addEventListener('click', function (e) {
                var btn = e.target.closest('.tab-btn');
                if (!btn) return;
                e.preventDefault();
                switchTab(btn.dataset.tab);

                if (btn.dataset.tab === 'peserta') {
                    // re-init Select2 setelah tab terlihat
                    setTimeout(initAllSelect2, 60);
                }
            });
        }

        // ---------- Elemen utama ----------
        var pesertaList      = document.getElementById('peserta-list');
        var pesertaTemplate  = document.getElementById('peserta-template');
        var btnTambahPeserta = document.getElementById('tambah-peserta');

        if (!pesertaList || !pesertaTemplate || !btnTambahPeserta) {
            console.warn('[Form] Elemen peserta tidak ditemukan.');
            return;
        }

        var templateCard = pesertaTemplate.querySelector('.peserta-card');

        // ---------- Select2 helpers ----------
        function safeInitSelect2(select, placeholder) {
            if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) return;
            if (!document.body.contains(select)) return;

            var $el = window.jQuery(select);
            if ($el.hasClass('select2-hidden-accessible')) return;

            $el.select2({
                placeholder: placeholder,
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });
        }

        function initAllSelect2() {
            if (typeof window.jQuery === 'undefined' || !window.jQuery.fn.select2) return;

            pesertaList.querySelectorAll('.pegawai-select2').forEach(function (el) {
                safeInitSelect2(el, 'Ketik nama pegawai...');
            });
            pesertaList.querySelectorAll('.siswa-select2').forEach(function (el) {
                safeInitSelect2(el, 'Ketik nama siswa...');
            });
            pesertaList.querySelectorAll('.dudika-select2').forEach(function (el) {
                safeInitSelect2(el, 'Ketik nama DUDIKA...');
            });
        }

        // Tunggu jQuery + Select2 siap baru init
        (function waitForJQuery(tries) {
            tries = tries || 0;
            if (typeof window.jQuery !== 'undefined' && window.jQuery.fn && window.jQuery.fn.select2) {
                initAllSelect2();
            } else if (tries < 50) {
                setTimeout(function () { waitForJQuery(tries + 1); }, 100);
            } else {
                console.error('[Form] jQuery/Select2 gagal dimuat.');
            }
        })();

        // ---------- Update nomor peserta ----------
        function updateNomorPeserta() {
            pesertaList.querySelectorAll('.peserta-card').forEach(function (card, index) {
                var nomor = card.querySelector('.peserta-number');
                if (nomor) nomor.textContent = index + 1;
            });
        }

        // ---------- TAMBAH PESERTA ----------
        btnTambahPeserta.addEventListener('click', function () {
            var index = pesertaList.querySelectorAll('.peserta-card').length;

            var clone = templateCard.cloneNode(true);
            clone.innerHTML = clone.innerHTML.replace(/__INDEX__/g, index);

            var nomor = clone.querySelector('.peserta-number');
            if (nomor) nomor.textContent = index + 1;

            pesertaList.appendChild(clone);

            clone.querySelectorAll('.pegawai-select2').forEach(function (el) {
                safeInitSelect2(el, 'Ketik nama pegawai...');
            });
            clone.querySelectorAll('.siswa-select2').forEach(function (el) {
                safeInitSelect2(el, 'Ketik nama siswa...');
            });
            clone.querySelectorAll('.dudika-select2').forEach(function (el) {
                safeInitSelect2(el, 'Ketik nama DUDIKA...');
            });

            updateNomorPeserta();
        });

        // ---------- EVENT DELEGATION ----------
        pesertaList.addEventListener('click', function (e) {

            // HAPUS PESERTA
            var btnHapusPeserta = e.target.closest('.hapus-peserta');
            if (btnHapusPeserta) {
                var card = btnHapusPeserta.closest('.peserta-card');
                if (!card) return;

                if (window.jQuery && window.jQuery.fn.select2) {
                    card.querySelectorAll('select').forEach(function (sel) {
                        if (window.jQuery(sel).hasClass('select2-hidden-accessible')) {
                            window.jQuery(sel).select2('destroy');
                        }
                    });
                }
                card.remove();
                updateNomorPeserta();
                return;
            }

            // TAMBAH TEMPAT
            var btnTambahTempat = e.target.closest('.tambah-tempat');
            if (btnTambahTempat) {
                var card2 = btnTambahTempat.closest('.peserta-card');
                var tempatList = card2.querySelector('.tempat-kegiatan-list');
                var templateItem = tempatList.querySelector('.tempat-item');
                if (!templateItem) return;

                var item = templateItem.cloneNode(true);
                var input = item.querySelector('input[type="text"]');
                if (input) {
                    input.value = '';
                    input.setAttribute('type', 'text');
                }
                tempatList.appendChild(item);
                if (input) input.focus();
                return;
            }

            // HAPUS TEMPAT
            var btnHapusTempat = e.target.closest('.hapus-tempat');
            if (btnHapusTempat) {
                var item2 = btnHapusTempat.closest('.tempat-item');
                var tempatList2 = item2.closest('.tempat-kegiatan-list');
                var jumlah = tempatList2.querySelectorAll('.tempat-item').length;

                if (jumlah > 1) {
                    item2.remove();
                } else {
                    var inputField = item2.querySelector('input[type="text"]');
                    if (inputField) inputField.value = '';
                }
            }
        });

        // ---------- AUTO KOP SURAT ----------
        var selectDari = document.getElementById('select-dari');
        var selectKop  = document.getElementById('select-kop');

        if (selectDari && selectKop) {
            selectDari.addEventListener('change', function () {
                if (this.value === 'Kepala SMK Negeri 1 Koba') {
                    selectKop.value = 'kop_smk';
                } else if (this.value === 'Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung') {
                    selectKop.value = 'kop_dinas';
                } else {
                    selectKop.value = '';
                }
            });
        }

    }); // end DOMContentLoaded

})();
</script>
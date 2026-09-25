@php
    $suratNodin = $suratNodin ?? null;

    $pesertaIndex = 0;

    $asns = $asns ?? [];
    $siswas = $siswas ?? [];
    $logos = $logos ?? [];

    /*
    |--------------------------------------------------------------------------
    | Fungsi untuk mendapatkan nilai array peserta lama
    |--------------------------------------------------------------------------
    */

    $pesertaData = [];

    if (isset($suratNodin) && $suratNodin->pesertaSuratUsulans->count() > 0) {

        $groupedPeserta = [];

        foreach ($suratNodin->pesertaSuratUsulans as $peserta) {

            $pegawaiKey = $peserta->pegawai_id ?? 'no-pegawai';

            $key =
                $pegawaiKey . '|' .
                ($peserta->siswa_id ?? '') . '|' .
                ($peserta->tgl_awal_kegiatan ?? '') . '|' .
                ($peserta->tgl_akhir_kegiatan ?? '');

            if (!isset($groupedPeserta[$key])) {

                $groupedPeserta[$key] = [
                    'pegawai_ids' => [],
                    'siswa_ids' => [],
                    'tgl_awal' => $peserta->tgl_awal_kegiatan
                        ? \Carbon\Carbon::parse($peserta->tgl_awal_kegiatan)->format('Y-m-d')
                        : '',
                    'tgl_akhir' => $peserta->tgl_akhir_kegiatan
                        ? \Carbon\Carbon::parse($peserta->tgl_akhir_kegiatan)->format('Y-m-d')
                        : '',
                    'tempat_kegiatan' => [],
                ];

            }

            /*
            |--------------------------------------------------------------------------
            | Pegawai
            |--------------------------------------------------------------------------
            */

            if (
                $peserta->pegawai_id &&
                !in_array(
                    $peserta->pegawai_id,
                    $groupedPeserta[$key]['pegawai_ids']
                )
            ) {

                $groupedPeserta[$key]['pegawai_ids'][] =
                    $peserta->pegawai_id;

            }


            /*
            |--------------------------------------------------------------------------
            | Siswa
            |--------------------------------------------------------------------------
            */

            if (
                $peserta->siswa_id &&
                !in_array(
                    $peserta->siswa_id,
                    $groupedPeserta[$key]['siswa_ids']
                )
            ) {

                $groupedPeserta[$key]['siswa_ids'][] =
                    $peserta->siswa_id;

            }


            /*
            |--------------------------------------------------------------------------
            | Tempat
            |--------------------------------------------------------------------------
            */

            if (
                $peserta->tempat_kegiatan &&
                !in_array(
                    $peserta->tempat_kegiatan,
                    $groupedPeserta[$key]['tempat_kegiatan']
                )
            ) {

                $groupedPeserta[$key]['tempat_kegiatan'][] =
                    $peserta->tempat_kegiatan;

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


    /*
    |--------------------------------------------------------------------------
    | Jika tidak ada peserta lama
    |--------------------------------------------------------------------------
    */

    if (empty($pesertaData)) {

        $pesertaData = [
            [
                'pegawai_ids' => [],
                'siswa_ids' => [],
                'tgl_awal' => '',
                'tgl_akhir' => '',
                'tempat_kegiatan' => [''],
            ]
        ];

    }

@endphp


{{-- ========================================================= --}}
{{-- CSS                                                       --}}
{{-- ========================================================= --}}

<style>

    .peserta-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 2px 6px rgba(0,0,0,.05);
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
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 10px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .form-help {
        font-size: 12px;
        color: #6b7280;
        margin-top: 5px;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container
    .select2-selection--multiple {
        min-height: 52px !important;
        padding: 6px 8px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 8px !important;
    }

    .select2-container
    .select2-selection--multiple
    .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap;
        gap: 4px;
    }

    .select2-container
    .select2-search--inline
    .select2-search__field {
        min-height: 30px;
        font-size: 14px;
    }

    .tempat-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
    }

    .tempat-item textarea {
        flex: 1;
        min-height: 58px;
        resize: vertical;
    }

    .tempat-remove {
        flex-shrink: 0;
        margin-top: 4px;
    }

    @media (max-width: 768px) {

        .peserta-card {
            padding: 15px;
        }

    }

</style>


{{-- ========================================================= --}}
{{-- TAB NAVIGATION                                            --}}
{{-- ========================================================= --}}

<div id="form-tabs">

    <div class="sticky top-0 z-20 -mx-6 px-6 py-2 mb-6
                bg-white/90 dark:bg-gray-800/90
                backdrop-blur border-b dark:border-gray-700">

        <nav class="flex flex-wrap gap-2">

            <button
                type="button"
                data-tab="data-umum"
                onclick="switchTab('data-umum')"
                class="tab-btn px-4 py-2 rounded-full font-medium
                       bg-blue-600 text-white">

                Data Umum

            </button>


            <button
                type="button"
                data-tab="isi-surat"
                onclick="switchTab('isi-surat')"
                class="tab-btn px-4 py-2 rounded-full font-medium
                       text-gray-600 hover:bg-gray-100">

                Isi Surat

            </button>


            <button
                type="button"
                data-tab="peserta"
                onclick="switchTab('peserta')"
                class="tab-btn px-4 py-2 rounded-full font-medium
                       text-gray-600 hover:bg-gray-100">

                Peserta

            </button>


            <button
                type="button"
                data-tab="penandatangan"
                onclick="switchTab('penandatangan')"
                class="tab-btn px-4 py-2 rounded-full font-medium
                       text-gray-600 hover:bg-gray-100">

                Penandatangan

            </button>

        </nav>

    </div>


    {{-- ===================================================== --}}
    {{-- DATA UMUM                                            --}}
    {{-- ===================================================== --}}

    <div
        id="tab-data-umum"
        class="tab-content grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="md:col-span-2">

            <h2 class="text-lg font-semibold mb-4 border-b pb-2">
                Data Umum
            </h2>

        </div>


        <div>

            <label class="form-label">
                Nomor
            </label>

            <input
                type="text"
                name="nomor"
                value="{{ old('nomor', $suratNodin->nomor ?? '................................................................') }}"
                class="w-full border rounded-lg px-3 py-2">

        </div>


        <div>

            <label class="form-label">
                Sifat
            </label>

            <input
                type="text"
                name="sifat"
                value="{{ old('sifat', $suratNodin->sifat ?? 'Penting') }}"
                class="w-full border rounded-lg px-3 py-2">

        </div>


        <div>

            <label class="form-label">
                Lampiran
            </label>

            <input
                type="text"
                name="lampiran"
                value="{{ old('lampiran', $suratNodin->lampiran ?? '1 (satu) berkas') }}"
                class="w-full border rounded-lg px-3 py-2">

        </div>


        <div>

            <label class="form-label">
                Hal
            </label>

            <input
                type="text"
                name="hal"
                value="{{ old('hal', $suratNodin->hal ?? 'Permohonan Izin Perjalanan Dinas') }}"
                class="w-full border rounded-lg px-3 py-2">

        </div>


        <div class="md:col-span-2">

            <label class="form-label">
                Yth.
            </label>

            <input
                type="text"
                name="kepada"
                value="{{ old('kepada', $suratNodin->kepada ?? 'Yth. Gubernur Kepulauan Bangka Belitung') }}"
                class="w-full border rounded-lg px-3 py-2">

        </div>


        <div class="md:col-span-2">

            <label class="form-label">
                Dari
            </label>

            <select
                name="dari"
                id="select-dari"
                class="w-full border rounded-lg px-3 py-2">

                <option value="">
                    -- Pilih Dari --
                </option>

                <option
                    value="Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung"
                    {{ old('dari', $suratNodin->dari ?? '') ==
                       'Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung'
                       ? 'selected' : '' }}>

                    Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung

                </option>

                <option
                    value="Kepala SMK Negeri 1 Koba"
                    {{ old('dari', $suratNodin->dari ?? '') ==
                       'Kepala SMK Negeri 1 Koba'
                       ? 'selected' : '' }}>

                    Kepala SMK Negeri 1 Koba

                </option>

            </select>


            <div class="mt-3">

                <label class="inline-flex items-center">

                    <input
                        type="hidden"
                        name="dari_plt"
                        value="0">

                    <input
                        type="checkbox"
                        name="dari_plt"
                        value="1"
                        {{ old('dari_plt', $suratNodin->dari_plt ?? false)
                           ? 'checked' : '' }}>

                    <span class="ml-2 text-sm">
                        Plt pada pengirim
                    </span>

                </label>


                <label class="inline-flex items-center ml-6">

                    <input
                        type="checkbox"
                        name="dari_an"
                        value="1"
                        {{ old('dari_an', $suratNodin->dari_an ?? false)
                           ? 'checked' : '' }}>

                    <span class="ml-2 text-sm">
                        a.n (Atas Nama)
                    </span>

                </label>

            </div>

        </div>


        <div>

            <label class="form-label">
                Tanggal
            </label>

            <input
                type="date"
                name="tanggal"
                value="{{ old(
                    'tanggal',
                    optional($suratNodin->tanggal ?? null)->format('Y-m-d')
                ) }}"
                class="w-full border rounded-lg px-3 py-2">

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- ISI SURAT                                            --}}
    {{-- ===================================================== --}}

    <div
        id="tab-isi-surat"
        class="tab-content hidden grid grid-cols-1 gap-6">

        <div>

            <h2 class="text-lg font-semibold mb-4 border-b pb-2">
                Isi Surat
            </h2>

        </div>


        <div>

            <label class="form-label">
                Dasar Surat
            </label>

            <textarea
                name="dasar_surat"
                rows="5"
                class="w-full border rounded-lg px-3 py-2">{{ old(
                    'dasar_surat',
                    $suratNodin->dasar_surat ?? ''
                ) }}</textarea>

        </div>


        <div>

            <label class="form-label">
                Isi Surat
            </label>

            <textarea
                name="isi_surat"
                rows="7"
                class="w-full border rounded-lg px-3 py-2">{{ old(
                    'isi_surat',
                    $suratNodin->isi_surat ?? ''
                ) }}</textarea>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- PESERTA                                               --}}
    {{-- ===================================================== --}}

    <div
        id="tab-peserta"
        class="tab-content hidden">

        {{-- KOP SURAT --}}

        <div class="mb-7">

            <label class="form-label">
                Kop Surat
            </label>

            <select
                name="kop_surat"
                id="select-kop"
                class="w-full border rounded-lg px-3 py-3">

                <option value="">
                    -- Pilih Kop Surat --
                </option>

                @foreach($logos as $logo)

                    <option
                        value="{{ $logo->name }}"
                        {{ old(
                            'kop_surat',
                            $suratNodin->kop_surat ?? ''
                        ) == $logo->name ? 'selected' : '' }}>

                        {{ $logo->name ?: 'Tanpa Nama' }}

                    </option>

                @endforeach

            </select>

        </div>


        {{-- HEADER PESERTA --}}

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between
                    gap-3 mb-5">

            <div>

                <h2 class="text-xl font-bold">
                    Daftar Peserta
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Satu kelompok dapat terdiri dari banyak pegawai,
                    banyak siswa, dan banyak tempat kegiatan.
                </p>

            </div>


            <button
                type="button"
                id="tambah-peserta"
                class="bg-blue-600 hover:bg-blue-700
                       text-white font-semibold
                       px-5 py-2.5 rounded-lg shadow">

                + Tambah Peserta

            </button>

        </div>


        {{-- DAFTAR KARTU --}}

        <div
            id="peserta-list"
            class="space-y-5">

            @foreach($pesertaData as $group)

                @php

                    $currentIndex = $pesertaIndex++;

                    $selectedPegawai =
                        $group['pegawai_ids'] ?? [];

                    $selectedSiswa =
                        $group['siswa_ids'] ?? [];

                    $tempatList =
                        $group['tempat_kegiatan'] ?? [''];

                    if (empty($tempatList)) {
                        $tempatList = [''];
                    }

                @endphp


                <div
                    class="peserta-card"
                    data-index="{{ $currentIndex }}">


                    {{-- HEADER KARTU --}}

                    <div class="peserta-card-header">

                        <div class="flex items-center">

                            <span class="peserta-number">
                                {{ $currentIndex + 1 }}
                            </span>

                            <div>

                                <div class="font-bold text-base">
                                    Peserta / Kelompok
                                </div>

                                <div class="text-xs text-gray-500">
                                    Pilih pegawai, siswa, tanggal dan tempat
                                </div>

                            </div>

                        </div>


                        <button
                            type="button"
                            class="hapus-peserta
                                   bg-red-500 hover:bg-red-600
                                   text-white px-3 py-2
                                   rounded-lg text-sm">

                            Hapus

                        </button>

                    </div>


                    {{-- PEGAWAI + SISWA --}}

                    <div
                        class="grid grid-cols-1 xl:grid-cols-2
                               gap-6">


                        {{-- PEGAWAI --}}

                        <div>

                            <label class="form-label">
                                Pegawai yang Ikut
                            </label>

                            <select
                                name="peserta[{{ $currentIndex }}][pegawai_id][]"
                                multiple
                                class="pegawai-select2">

                                @foreach($asns as $asn)

                                    <option
                                        value="{{ $asn->id }}"
                                        {{ in_array(
                                            $asn->id,
                                            $selectedPegawai
                                        ) ? 'selected' : '' }}>

                                        {{ $asn->nama }}

                                        @if($asn->nip)
                                            ({{ $asn->nip }})
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            <div class="form-help">
                                Ketik nama untuk mencari.
                                Dapat memilih beberapa pegawai.
                            </div>

                        </div>


                        {{-- SISWA --}}

                        <div>

                            <label class="form-label">
                                Siswa yang Ikut
                            </label>

                            <select
                                name="peserta[{{ $currentIndex }}][siswa_id][]"
                                multiple
                                class="siswa-select2">

                                @foreach($siswas as $siswa)

                                    <option
                                        value="{{ $siswa->id }}"
                                        {{ in_array(
                                            $siswa->id,
                                            $selectedSiswa
                                        ) ? 'selected' : '' }}>

                                        {{ $siswa->nama }}

                                    </option>

                                @endforeach

                            </select>

                            <div class="form-help">
                                Ketik nama untuk mencari.
                                Dapat memilih beberapa siswa.
                            </div>

                        </div>

                    </div>


                    {{-- TANGGAL --}}

                    <div
                        class="grid grid-cols-1 md:grid-cols-2
                               gap-6 mt-6">


                        <div>

                            <label class="form-label">
                                Tanggal Mulai Kegiatan
                            </label>

                            <input
                                type="date"
                                name="peserta[{{ $currentIndex }}][tgl_awal_kegiatan]"
                                value="{{ $group['tgl_awal'] ?? '' }}"
                                class="w-full border rounded-lg
                                       px-4 py-3">

                        </div>


                        <div>

                            <label class="form-label">
                                Tanggal Selesai Kegiatan
                            </label>

                            <input
                                type="date"
                                name="peserta[{{ $currentIndex }}][tgl_akhir_kegiatan]"
                                value="{{ $group['tgl_akhir'] ?? '' }}"
                                class="w-full border rounded-lg
                                       px-4 py-3">

                        </div>

                    </div>


                    {{-- TEMPAT --}}

                    <div class="mt-6">

                        <div
                            class="flex flex-col sm:flex-row
                                   sm:items-center
                                   sm:justify-between
                                   gap-2 mb-3">

                            <div>

                                <label class="form-label mb-0">
                                    Tempat Kegiatan
                                </label>

                                <div class="form-help">
                                    Satu kelompok dapat memiliki
                                    beberapa tempat kegiatan.
                                </div>

                            </div>


                            <button
                                type="button"
                                class="tambah-tempat
                                       bg-green-600 hover:bg-green-700
                                       text-white text-sm font-semibold
                                       px-4 py-2 rounded-lg">

                                + Tambah Tempat

                            </button>

                        </div>


                        <div
                            class="tempat-kegiatan-list space-y-3">

                            @foreach($tempatList as $tempat)

                                <div class="tempat-item">

                                    <textarea
                                        name="peserta[{{ $currentIndex }}][tempat_kegiatan][]"
                                        rows="2"
                                        placeholder="Masukkan tempat kegiatan..."
                                        class="w-full border rounded-lg
                                               px-4 py-2.5
                                               focus:ring-2
                                               focus:ring-blue-500">{{ trim($tempat) }}</textarea>


                                    <button
                                        type="button"
                                        class="hapus-tempat
                                               tempat-remove
                                               bg-red-500
                                               hover:bg-red-600
                                               text-white px-3 py-2
                                               rounded-lg">

                                        ×

                                    </button>

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

        <div
            class="peserta-card"
            data-index="__INDEX__">


            <div class="peserta-card-header">

                <div class="flex items-center">

                    <span class="peserta-number">
                        #
                    </span>

                    <div>

                        <div class="font-bold">
                            Peserta / Kelompok
                        </div>

                        <div class="text-xs text-gray-500">
                            Peserta baru
                        </div>

                    </div>

                </div>


                <button
                    type="button"
                    class="hapus-peserta
                           bg-red-500 hover:bg-red-600
                           text-white px-3 py-2 rounded-lg text-sm">

                    Hapus

                </button>

            </div>


            <div
                class="grid grid-cols-1 xl:grid-cols-2
                       gap-6">


                {{-- PEGAWAI --}}

                <div>

                    <label class="form-label">
                        Pegawai yang Ikut
                    </label>

                    <select
                        name="peserta[__INDEX__][pegawai_id][]"
                        multiple
                        class="pegawai-select2">

                        @foreach($asns as $asn)

                            <option value="{{ $asn->id }}">

                                {{ $asn->nama }}

                                @if($asn->nip)
                                    ({{ $asn->nip }})
                                @endif

                            </option>

                        @endforeach

                    </select>

                    <div class="form-help">
                        Bisa memilih beberapa pegawai.
                    </div>

                </div>


                {{-- SISWA --}}

                <div>

                    <label class="form-label">
                        Siswa yang Ikut
                    </label>

                    <select
                        name="peserta[__INDEX__][siswa_id][]"
                        multiple
                        class="siswa-select2">

                        @foreach($siswas as $siswa)

                            <option value="{{ $siswa->id }}">
                                {{ $siswa->nama }}
                            </option>

                        @endforeach

                    </select>

                    <div class="form-help">
                        Bisa memilih beberapa siswa.
                    </div>

                </div>

            </div>


            {{-- TANGGAL --}}

            <div
                class="grid grid-cols-1 md:grid-cols-2
                       gap-6 mt-6">

                <div>

                    <label class="form-label">
                        Tanggal Mulai Kegiatan
                    </label>

                    <input
                        type="date"
                        name="peserta[__INDEX__][tgl_awal_kegiatan]"
                        class="w-full border rounded-lg
                               px-4 py-3">

                </div>


                <div>

                    <label class="form-label">
                        Tanggal Selesai Kegiatan
                    </label>

                    <input
                        type="date"
                        name="peserta[__INDEX__][tgl_akhir_kegiatan]"
                        class="w-full border rounded-lg
                               px-4 py-3">

                </div>

            </div>


            {{-- TEMPAT --}}

            <div class="mt-6">

                <div
                    class="flex flex-col sm:flex-row
                           sm:items-center
                           sm:justify-between
                           gap-2 mb-3">

                    <div>

                        <label class="form-label mb-0">
                            Tempat Kegiatan
                        </label>

                        <div class="form-help">
                            Dapat menambahkan banyak tempat.
                        </div>

                    </div>


                    <button
                        type="button"
                        class="tambah-tempat
                               bg-green-600 hover:bg-green-700
                               text-white text-sm font-semibold
                               px-4 py-2 rounded-lg">

                        + Tambah Tempat

                    </button>

                </div>


                <div
                    class="tempat-kegiatan-list space-y-3">

                    <div class="tempat-item">

                        <textarea
                            name="peserta[__INDEX__][tempat_kegiatan][]"
                            rows="2"
                            placeholder="Masukkan tempat kegiatan..."
                            class="w-full border rounded-lg
                                   px-4 py-2.5
                                   focus:ring-2
                                   focus:ring-blue-500"></textarea>


                        <button
                            type="button"
                            class="hapus-tempat
                                   tempat-remove
                                   bg-red-500 hover:bg-red-600
                                   text-white px-3 py-2
                                   rounded-lg">

                            ×

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ===================================================== --}}
    {{-- PENANDATANGAN                                         --}}
    {{-- ===================================================== --}}

    <div
        id="tab-penandatangan"
        class="tab-content hidden grid
               grid-cols-1 gap-6">

        <div>

            <h2 class="text-lg font-semibold mb-4 border-b pb-2">
                Penandatangan
            </h2>

        </div>


        <div>

            <label class="form-label">
                Pilih Penandatangan
            </label>

            <select
                name="penandatangan_id"
                class="w-full border rounded-lg px-3 py-3">

                <option value="">
                    -- Pilih Penandatangan --
                </option>

                @foreach($asns as $asn)

                    <option
                        value="{{ $asn->id }}"
                        {{ old(
                            'penandatangan_id',
                            $suratNodin->penandatangan_id
                            ?? $defaultPenandatanganId
                            ?? ''
                        ) == $asn->id
                        ? 'selected'
                        : '' }}>

                        {{ $asn->nama }}

                        @if($asn->nip)
                            ({{ $asn->nip }})
                        @endif

                    </option>

                @endforeach

            </select>


            <div class="mt-3">

                <label class="inline-flex items-center">

                    <input
                        type="hidden"
                        name="penandatangan_plt"
                        value="0">

                    <input
                        type="checkbox"
                        name="penandatangan_plt"
                        value="1"
                        {{ old(
                            'penandatangan_plt',
                            $suratNodin->penandatangan_plt ?? false
                        ) ? 'checked' : '' }}>

                    <span class="ml-2 text-sm">
                        Plt pada penandatangan
                    </span>

                </label>


                <label class="inline-flex items-center ml-6">

                    <input
                        type="checkbox"
                        name="penandatangan_an"
                        value="1"
                        {{ old(
                            'penandatangan_an',
                            $suratNodin->penandatangan_an ?? false
                        ) ? 'checked' : '' }}>

                    <span class="ml-2 text-sm">
                        a.n (Atas Nama)
                    </span>

                </label>

            </div>

        </div>


        <div>

            <label class="form-label">
                Pilih Pegawai Yang Diberi Tugas
            </label>

            <select
                name="pegawai_tugas_id"
                class="w-full border rounded-lg px-3 py-3">

                <option value="">
                    -- Pilih Pegawai Tugas --
                </option>

                @foreach($asns as $asn)

                    <option
                        value="{{ $asn->id }}"
                        {{ old(
                            'pegawai_tugas_id',
                            $suratNodin->pegawai_tugas_id ?? ''
                        ) == $asn->id
                        ? 'selected'
                        : '' }}>

                        {{ $asn->nama }}

                        @if($asn->nip)
                            ({{ $asn->nip }})
                        @endif

                    </option>

                @endforeach

            </select>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- SELECT2                                                   --}}
{{-- ========================================================= --}}

<link
    href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    rel="stylesheet">


<script
    src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js">
</script>


<script
    src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js">
</script>


{{-- ========================================================= --}}
{{-- JAVASCRIPT                                                --}}
{{-- ========================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {


    const pesertaList =
        document.getElementById('peserta-list');

    const pesertaTemplate =
        document.getElementById('peserta-template')
            .firstElementChild;

    const btnTambahPeserta =
        document.getElementById('tambah-peserta');


    // =====================================================
    // SELECT2 PEGAWAI
    // =====================================================

    function initPegawai(select) {

        if (
            typeof $ === 'undefined' ||
            $(select).hasClass('select2-hidden-accessible')
        ) {
            return;
        }

        $(select).select2({

            placeholder: 'Ketik nama pegawai...',

            allowClear: true,

            width: '100%'

        });

    }


    // =====================================================
    // SELECT2 SISWA
    // =====================================================

    function initSiswa(select) {

        if (
            typeof $ === 'undefined' ||
            $(select).hasClass('select2-hidden-accessible')
        ) {
            return;
        }

        $(select).select2({

            placeholder: 'Ketik nama siswa...',

            allowClear: true,

            width: '100%'

        });

    }


    // =====================================================
    // INISIALISASI AWAL
    // =====================================================

    pesertaList
        .querySelectorAll('.pegawai-select2')
        .forEach(initPegawai);


    pesertaList
        .querySelectorAll('.siswa-select2')
        .forEach(initSiswa);


    // =====================================================
    // NOMOR KARTU
    // =====================================================

    function updateNomorPeserta() {

        pesertaList
            .querySelectorAll('.peserta-card')
            .forEach(function(card, index) {

                const nomor =
                    card.querySelector('.peserta-number');

                if (nomor) {
                    nomor.textContent = index + 1;
                }

            });

    }


    // =====================================================
    // TAMBAH PESERTA
    // =====================================================

    btnTambahPeserta.addEventListener(
        'click',
        function () {

            const index =
                pesertaList.querySelectorAll(
                    '.peserta-card'
                ).length;


            const clone =
                pesertaTemplate.cloneNode(true);


            // ---------------------------------------------
            // Ganti __INDEX__
            // ---------------------------------------------

            clone.innerHTML =
                clone.innerHTML.replace(
                    /__INDEX__/g,
                    index
                );


            // ---------------------------------------------
            // Reset nomor
            // ---------------------------------------------

            clone
                .querySelector('.peserta-number')
                .textContent = index + 1;


            // ---------------------------------------------
            // Masukkan kartu
            // ---------------------------------------------

            pesertaList.appendChild(clone);


            // ---------------------------------------------
            // Aktifkan Select2
            // ---------------------------------------------

            clone
                .querySelectorAll('.pegawai-select2')
                .forEach(initPegawai);


            clone
                .querySelectorAll('.siswa-select2')
                .forEach(initSiswa);


            updateNomorPeserta();

        }
    );


    // =====================================================
    // EVENT DELEGATION
    // =====================================================

    pesertaList.addEventListener(
        'click',
        function (event) {


            // =============================================
            // HAPUS PESERTA
            // =============================================

            const btnHapusPeserta =
                event.target.closest('.hapus-peserta');


            if (btnHapusPeserta) {

                const card =
                    btnHapusPeserta.closest(
                        '.peserta-card'
                    );


                if (card) {

                    card
                        .querySelectorAll(
                            '.pegawai-select2, .siswa-select2'
                        )
                        .forEach(function(select) {

                            if (
                                typeof $ !== 'undefined' &&
                                $(select).hasClass(
                                    'select2-hidden-accessible'
                                )
                            ) {

                                $(select).select2(
                                    'destroy'
                                );

                            }

                        });


                    card.remove();

                    updateNomorPeserta();

                }

                return;
            }


            // =============================================
            // TAMBAH TEMPAT
            // =============================================

            const btnTambahTempat =
                event.target.closest('.tambah-tempat');


            if (btnTambahTempat) {

                const card =
                    btnTambahTempat.closest(
                        '.peserta-card'
                    );


                const tempatList =
                    card.querySelector(
                        '.tempat-kegiatan-list'
                    );


                const template =
                    tempatList.querySelector(
                        '.tempat-item'
                    );


                const item =
                    template.cloneNode(true);


                const textarea =
                    item.querySelector('textarea');


                if (textarea) {

                    textarea.value = '';

                }


                // Ambil name dari item pertama
                const textareaPertama =
                    template.querySelector('textarea');


                if (
                    textarea &&
                    textareaPertama
                ) {

                    textarea.name =
                        textareaPertama.name;

                }


                tempatList.appendChild(item);


                textarea.focus();

                return;
            }


            // =============================================
            // HAPUS TEMPAT
            // =============================================

            const btnHapusTempat =
                event.target.closest('.hapus-tempat');


            if (btnHapusTempat) {

                const item =
                    btnHapusTempat.closest(
                        '.tempat-item'
                    );


                const tempatList =
                    item.closest(
                        '.tempat-kegiatan-list'
                    );


                const jumlah =
                    tempatList.querySelectorAll(
                        '.tempat-item'
                    ).length;


                if (jumlah > 1) {

                    item.remove();

                } else {

                    const textarea =
                        item.querySelector('textarea');

                    if (textarea) {
                        textarea.value = '';
                    }

                }

            }

        }
    );


    // =====================================================
    // AUTO KOP SURAT
    // =====================================================

    const selectDari =
        document.getElementById('select-dari');

    const selectKop =
        document.getElementById('select-kop');


    if (selectDari && selectKop) {

        selectDari.addEventListener(
            'change',
            function () {

                if (
                    this.value ===
                    'Kepala SMK Negeri 1 Koba'
                ) {

                    selectKop.value =
                        'kop_smk';

                }
                else if (
                    this.value ===
                    'Kepala Dinas Pendidikan Provinsi Kepulauan Bangka Belitung'
                ) {

                    selectKop.value =
                        'kop_dinas';

                }
                else {

                    selectKop.value = '';

                }

            }
        );

    }

});


// =========================================================
// TAB
// =========================================================

function switchTab(tabId) {

    const contents =
        document.querySelectorAll('.tab-content');


    contents.forEach(function(el) {

        el.classList.add('hidden');

    });


    const target =
        document.getElementById(
            'tab-' + tabId
        );


    if (target) {

        target.classList.remove('hidden');

    }


    const buttons =
        document.querySelectorAll('.tab-btn');


    buttons.forEach(function(btn) {

        if (
            btn.dataset.tab === tabId
        ) {

            btn.classList.remove(
                'text-gray-600',
                'hover:bg-gray-100'
            );

            btn.classList.add(
                'bg-blue-600',
                'text-white'
            );

        }
        else {

            btn.classList.remove(
                'bg-blue-600',
                'text-white'
            );

            btn.classList.add(
                'text-gray-600',
                'hover:bg-gray-100'
            );

        }

    });

}

</script>

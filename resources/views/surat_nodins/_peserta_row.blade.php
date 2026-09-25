@php
    $item = $item ?? [
        'pegawai_id' => [],
        'siswa_id' => '',
        'tgl_awal_kegiatan' => '',
        'tgl_akhir_kegiatan' => '',
        'tempat_kegiatan' => [''],
    ];

    $asns = $asns ?? [];
    $siswas = $siswas ?? [];

    // ================================
    // PEGAWAI
    // ================================
    $selectedPegawai = $item['pegawai_id'] ?? [];

    if (!is_array($selectedPegawai)) {
        $selectedPegawai = [$selectedPegawai];
    }

    $selectedPegawai = array_filter($selectedPegawai);


    // ================================
    // TEMPAT KEGIATAN
    // ================================
    $tempatList = $item['tempat_kegiatan'] ?? [];

    if (is_string($tempatList)) {
        $tempatList = preg_split(
            '/\r\n|\r|\n/',
            $tempatList
        );
    }

    if (!is_array($tempatList)) {
        $tempatList = [];
    }

    $tempatList = array_values(
        array_filter(
            array_map('trim', $tempatList),
            fn($v) => $v !== ''
        )
    );

    // Minimal satu item
    if (empty($tempatList)) {
        $tempatList = [''];
    }
@endphp


<tr class="peserta-row border-b dark:border-gray-700">

    {{-- =====================================
         PEGAWAI
    ====================================== --}}
    <td class="px-2 py-2 align-top">

        <select
            name="peserta[{{ $index ?? '__INDEX__' }}][pegawai_id][]"
            multiple
            class="pegawai-select2 w-full border rounded px-2 py-1 text-sm
                   dark:bg-gray-700 dark:text-gray-100">

            @foreach($asns as $asn)

                <option
                    value="{{ $asn->id }}"
                    {{ in_array($asn->id, $selectedPegawai) ? 'selected' : '' }}>

                    {{ $asn->nama }}

                    @if($asn->nip)
                        ({{ $asn->nip }})
                    @endif

                </option>

            @endforeach

        </select>

        <div class="text-xs text-gray-400 mt-1">
            Bisa pilih beberapa pegawai
        </div>

    </td>


    {{-- =====================================
         SISWA
    ====================================== --}}
    <td class="px-2 py-2 align-top">

        <select
            name="peserta[{{ $index ?? '__INDEX__' }}][siswa_id]"
            class="w-full border rounded px-2 py-1 text-sm
                   dark:bg-gray-700 dark:text-gray-100">

            <option value="">
                -- Tidak Ada / Pilih Siswa --
            </option>

            @foreach($siswas as $siswa)

                <option
                    value="{{ $siswa->id }}"
                    {{ ($item['siswa_id'] ?? '') == $siswa->id ? 'selected' : '' }}>

                    {{ $siswa->nama }}

                </option>

            @endforeach

        </select>

    </td>


    {{-- =====================================
         TANGGAL AWAL
    ====================================== --}}
    <td class="px-2 py-2 align-top">

        <input
            type="date"
            name="peserta[{{ $index ?? '__INDEX__' }}][tgl_awal_kegiatan]"
            value="{{ $item['tgl_awal_kegiatan'] ?? '' }}"
            class="w-full border rounded px-2 py-1 text-sm
                   dark:bg-gray-700 dark:text-gray-100">

    </td>


    {{-- =====================================
         TANGGAL AKHIR
    ====================================== --}}
    <td class="px-2 py-2 align-top">

        <input
            type="date"
            name="peserta[{{ $index ?? '__INDEX__' }}][tgl_akhir_kegiatan]"
            value="{{ $item['tgl_akhir_kegiatan'] ?? '' }}"
            class="w-full border rounded px-2 py-1 text-sm
                   dark:bg-gray-700 dark:text-gray-100">

    </td>


    {{-- =====================================
         MULTI TEMPAT KEGIATAN
    ====================================== --}}
    <td
        class="px-2 py-2 align-top"
        style="min-width:320px; width:320px;">

        <div class="tempat-kegiatan-list space-y-2">

            @foreach($tempatList as $tempatVal)

                <div class="tempat-item flex items-start gap-2">

                    <div class="flex-1">

                        <textarea
                            name="peserta[{{ $index ?? '__INDEX__' }}][tempat_kegiatan][]"
                            rows="2"
                            placeholder="Masukkan tempat kegiatan..."
                            class="w-full border rounded px-2 py-1.5 text-sm
                                   dark:bg-gray-700 dark:text-gray-100
                                   focus:ring-2 focus:ring-blue-500
                                   focus:outline-none">{{ $tempatVal }}</textarea>

                    </div>

                    <button
                        type="button"
                        class="hapus-tempat bg-red-500 hover:bg-red-700
                               text-white px-2 py-1.5 rounded text-xs mt-1"
                        title="Hapus tempat">

                        ✕

                    </button>

                </div>

            @endforeach

        </div>


        {{-- Tambah tempat --}}
        <button
            type="button"
            class="tambah-tempat mt-2 bg-green-500 hover:bg-green-700
                   text-white text-xs font-bold py-1.5 px-3 rounded">

            + Tambah Tempat

        </button>

        <div class="text-xs text-gray-400 mt-1">
            Satu peserta dapat memiliki beberapa tempat kegiatan.
        </div>

    </td>


    {{-- =====================================
         HAPUS PESERTA
    ====================================== --}}
    <td class="px-2 py-2 text-center align-top">

        <button
            type="button"
            class="hapus-peserta bg-red-500 hover:bg-red-700
                   text-white text-xs font-bold py-1.5 px-3 rounded">

            Hapus

        </button>

    </td>

</tr>

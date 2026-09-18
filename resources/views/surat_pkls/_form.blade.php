<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label class="block font-medium mb-1">Nomor</label>
        <input type="text" name="nomor" value="{{ old('nomor', $suratPkl->nomor ?? '') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>
    <div>
        <label class="block font-medium mb-1">Sifat</label>
        <input type="text" name="sifat" value="{{ old('sifat', $suratPkl->sifat ?? 'Penting') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div>
        <label class="block font-medium mb-1">Lampiran</label>
        <input type="text" name="lampiran" value="{{ old('lampiran', $suratPkl->lampiran ?? '-') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>
    <div>
        <label class="block font-medium mb-1">Perihal</label>
        <input type="text" name="perihal" value="{{ old('perihal', $suratPkl->perihal ?? 'Permohonan PKL') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div class="md:col-span-2">
        <label class="block font-medium mb-1">Pejabat Tujuan Surat</label>
        <input type="text" name="pejabat_tujuan_surat" value="{{ old('pejabat_tujuan_surat', $suratPkl->pejabat_tujuan_surat ?? '') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div>
        <label class="block font-medium mb-1">Kota Tujuan Surat</label>
        <input type="text" name="kota_tujuan_surat" value="{{ old('kota_tujuan_surat', $suratPkl->kota_tujuan_surat ?? '') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>
    <div>
        <label class="block font-medium mb-1">Tempat Ditetapkan</label>
        <input type="text" name="tempat_ditetapkan" value="{{ old('tempat_ditetapkan', $suratPkl->tempat_ditetapkan ?? 'Koba') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div>
        <label class="block font-medium mb-1">Tanggal Ditetapkan</label>
        <input type="date" name="tanggal_ditetapkan" value="{{ old('tanggal_ditetapkan', optional($suratPkl->tanggal_ditetapkan ?? null)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>
    <div>
        <label class="block font-medium mb-1">Jumlah Siswa</label>
        <input type="number" min="0" name="jumlah_siswa" value="{{ old('jumlah_siswa', $suratPkl->jumlah_siswa ?? 2) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div>
        <label class="block font-medium mb-1">Nama Kelas</label>
        <input type="text" name="nama_kelas" value="{{ old('nama_kelas', $suratPkl->nama_kelas ?? 'XII') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div>
        <label class="block font-medium mb-1">Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $suratPkl->tahun_ajaran ?? '2026/2027') }}" placeholder="Contoh: 2026/2027" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div>
        <label class="block font-medium mb-1">Nama Jurusan</label>
        <select name="nama_jurusan" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
            <option value="">-- Pilih Jurusan --</option>
            @foreach([
                'Teknik Pengelasan (TP)',
                'Teknik Instalasi Tenaga Listrik (TITL)',
                'Teknik Sepeda Motor (TBSM)',
                'Teknik Kendaraan Ringan (TKR)',
                'Teknik Jaringan Komputer dan Telekomunikasi (TJKT)',
            ] as $jurusan)
                <option value="{{ $jurusan }}" {{ old('nama_jurusan', $suratPkl->nama_jurusan ?? 'Teknik Pengelasan (TP)') === $jurusan ? 'selected' : '' }}>{{ $jurusan }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block font-medium mb-1">Jenis Instansi yang Dituju</label>
        <select name="jenis_instansi_yang_dituju" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
            <option value="">-- Pilih --</option>
            <option value="Perusahaan" {{ old('jenis_instansi_yang_dituju', $suratPkl->jenis_instansi_yang_dituju ?? '') == 'Perusahaan' ? 'selected' : '' }}>Perusahaan</option>
            <option value="Instansi" {{ old('jenis_instansi_yang_dituju', $suratPkl->jenis_instansi_yang_dituju ?? '') == 'Instansi' ? 'selected' : '' }}>Instansi</option>
        </select>
    </div>
    <div>
        <label class="block font-medium mb-1">Lama Magang (bulan)</label>
        <input type="number" min="0" name="lama_magang" value="{{ old('lama_magang', $suratPkl->lama_magang ?? 0) }}" readonly class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-600 dark:text-gray-100">
    </div>

    <div>
        <label class="block font-medium mb-1">Tanggal Awal Magang</label>
        <input type="date" name="tgl_awal_magang" value="{{ old('tgl_awal_magang', optional($suratPkl->tgl_awal_magang ?? null)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>
    <div>
        <label class="block font-medium mb-1">Tanggal Akhir Magang</label>
        <input type="date" name="tgl_akhir_magang" value="{{ old('tgl_akhir_magang', optional($suratPkl->tgl_akhir_magang ?? null)->format('Y-m-d')) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div class="md:col-span-2">
        <label class="block font-medium mb-1">Nomor Kontak</label>
        <input type="text" name="no_contact" value="{{ old('no_contact', $suratPkl->no_contact ?? '087819754201') }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
    </div>

    <div class="md:col-span-2">
        <h2 class="text-lg font-semibold mb-4 border-b pb-2 mt-4">Penandatangan</h2>
    </div>

    <div class="md:col-span-2">
        <label class="block font-medium mb-1">Pilih Penandatangan</label>
        <select name="penandatangan_id" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
            <option value="">-- Pilih Penandatangan --</option>
            @foreach($asns as $asn)
                <option value="{{ $asn->id }}" {{ old('penandatangan_id', $suratPkl->penandatangan_id ?? $defaultPenandatanganId) == $asn->id ? 'selected' : '' }}>
                    {{ $asn->nama }} {{ $asn->nip ? '(' . $asn->nip . ')' : '' }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<script>
    (() => {
        const startInput = document.querySelector('[name="tgl_awal_magang"]');
        const endInput = document.querySelector('[name="tgl_akhir_magang"]');
        const durationInput = document.querySelector('[name="lama_magang"]');

        if (!startInput || !endInput || !durationInput) {
            return;
        }

        const updateDuration = () => {
            if (!startInput.value || !endInput.value) {
                durationInput.value = 0;
                return;
            }

            const [startYear, startMonth, startDay] = startInput.value.split('-').map(Number);
            const [endYear, endMonth, endDay] = endInput.value.split('-').map(Number);
            const start = new Date(Date.UTC(startYear, startMonth - 1, startDay));
            const end = new Date(Date.UTC(endYear, endMonth - 1, endDay));
            const monthCount = ((endYear - startYear) * 12) + (endMonth - startMonth) + 1;

            durationInput.value = end >= start ? monthCount : 0;
        };

        startInput.addEventListener('change', updateDuration);
        endInput.addEventListener('change', updateDuration);
        updateDuration();
    })();
</script>

<?php

namespace App\Http\Controllers;

use App\Models\Asn;
use App\Models\DataSiswa;
use App\Models\DaftarDudika;
use App\Models\LogoSetting;
use App\Models\PhotoNodin;
use App\Models\SuratNodin;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SuratNodinController extends Controller
{
    /**
     * Menampilkan daftar Surat Nodin.
     */
    public function index(): View
    {
        $suratNodins = SuratNodin::with(['penandatangan'])
            ->latest()
            ->paginate(5);

        return view('surat_nodins.index', compact('suratNodins'));
    }

    /**
     * Form tambah Surat Nodin.
     */
    public function create(): View
    {
        $asns = Asn::orderBy('nama')->get();
        $siswas = DataSiswa::orderBy('nama')->get();
        $dudikas = DaftarDudika::orderBy('nama_dudika')->get();
        $logos = LogoSetting::orderBy('name')->get();

        $defaultPenandatanganId = Asn::defaultPenandatanganId();

        return view(
            'surat_nodins.create',
            compact(
                'asns',
                'siswas',
                'dudikas',
                'logos',
                'defaultPenandatanganId'
            )
        );
    }

    /**
     * Menyimpan Surat Nodin baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        $suratNodin = DB::transaction(function () use ($validated, $request) {

            /*
             * peserta tidak disimpan ke tabel surat_nodins.
             * Peserta diproses oleh syncPeserta().
             */
            $suratData = $validated;
            unset($suratData['peserta']);

            $suratNodin = SuratNodin::create($suratData);

            $this->syncPeserta($suratNodin, $request);

            return $suratNodin;
        });

        return redirect()
            ->route('surat-nodins.print', $suratNodin)
            ->with('success', 'Surat Nodin berhasil disimpan.');
    }

    /**
     * Form edit Surat Nodin.
     */
    public function edit(SuratNodin $suratNodin): View
    {
        $asns = Asn::orderBy('nama')->get();
        $siswas = DataSiswa::orderBy('nama')->get();
        $dudikas = DaftarDudika::orderBy('nama_dudika')->get();
        $logos = LogoSetting::orderBy('name')->get();

        $suratNodin->load(
            'penandatangan',
            'pesertaSuratUsulans'
        );

        $defaultPenandatanganId = Asn::defaultPenandatanganId();

        return view(
            'surat_nodins.edit',
            compact(
                'asns',
                'siswas',
                'dudikas',
                'logos',
                'suratNodin',
                'defaultPenandatanganId'
            )
        );
    }

    /**
     * Memperbarui Surat Nodin.
     */
    public function update(
        Request $request,
        SuratNodin $suratNodin
    ): RedirectResponse {

        $validated = $this->validateData($request);

        DB::transaction(function () use (
            $validated,
            $request,
            $suratNodin
        ) {

            /*
             * Jangan ikutkan peserta ke update surat_nodins.
             */
            $suratData = $validated;
            unset($suratData['peserta']);

            $suratNodin->update($suratData);

            /*
             * syncPeserta() akan menghapus
             * peserta lama dan membuat ulang
             * sesuai data form terbaru.
             */
            $this->syncPeserta($suratNodin, $request);
        });

        return redirect()
            ->route('surat-nodins.print', $suratNodin)
            ->with('success', 'Surat Nodin berhasil diperbarui.');
    }

    /**
     * Menghapus Surat Nodin.
     */
    public function destroy(SuratNodin $suratNodin): RedirectResponse
    {
        DB::transaction(function () use ($suratNodin) {
            $suratNodin->pesertaSuratUsulans()->delete();
            $suratNodin->photos()->delete();
            $suratNodin->delete();
        });

        return redirect()
            ->route('surat-nodins.index')
            ->with('success', 'Surat Nodin berhasil dihapus.');
    }

    /**
     * Tampilan cetak Surat Nodin.
     */
    public function print(SuratNodin $suratNodin): View
    {
        $suratNodin->load(
            'penandatangan',
            'pegawaiTugas',
            'pesertaSuratUsulans.pegawai',
            'pesertaSuratUsulans.siswa',
            'pesertaSuratUsulans.dudika'
        );

        $kopSuratBase64 = null;

        $logoName = $suratNodin->kop_surat ?: 'kop_smk';

        $logo = LogoSetting::where('name', $logoName)->first()
            ?? LogoSetting::latest()->first();

        if ($logo && $logo->image) {
            $kopSuratBase64 =
                'data:' .
                ($logo->mime ?: 'image/png') .
                ';base64,' .
                base64_encode($logo->image);
        }

        return view(
            'surat_nodins.print',
            compact(
                'suratNodin',
                'kopSuratBase64'
            )
        );
    }

    /**
     * Lampiran Surat Nodin.
     */
    public function lampiran(SuratNodin $suratNodin): View
    {
        $suratNodin->load(
            'penandatangan',
            'pegawaiTugas',
            'pesertaSuratUsulans.pegawai',
            'pesertaSuratUsulans.siswa'
        );

        return view(
            'surat_nodins.lampiran',
            compact('suratNodin')
        );
    }

    /**
     * Lampiran foto.
     */
    public function photoLampiran(SuratNodin $suratNodin): View
    {
        $suratNodin->load(
            'penandatangan',
            'pegawaiTugas',
            'photos'
        );

        return view(
            'surat_nodins.photo_lampiran',
            compact('suratNodin')
        );
    }

    /**
     * Lampiran tabel peserta.
     */
    public function lampiranTabelPeserta(
        SuratNodin $suratNodin
    ): View {

        $suratNodin->load(
            'penandatangan',
            'pegawaiTugas',
            'pesertaSuratUsulans.pegawai',
            'pesertaSuratUsulans.siswa'
        );

        return view(
            'surat_nodins.lampiran_tabel_peserta',
            compact('suratNodin')
        );
    }

    /**
     * Daftar foto.
     */
    public function photos(SuratNodin $suratNodin): View
    {
        $suratNodin->load('photos');

        return view(
            'surat_nodins.photos',
            compact('suratNodin')
        );
    }

    /**
     * Menyimpan foto.
     */
    public function storePhoto(
        Request $request,
        SuratNodin $suratNodin
    ): RedirectResponse {

        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('photo')) {

            $file = $request->file('photo');

            PhotoNodin::create([
                'surat_nodin_id' => $suratNodin->id,
                'caption' => $request->input('caption'),
                'mime' => $file->getClientMimeType(),
                'image' => file_get_contents(
                    $file->getRealPath()
                ),
            ]);
        }

        return redirect()
            ->route('surat-nodins.photos', $suratNodin)
            ->with('success', 'Foto berhasil ditambahkan.');
    }

    /**
     * Form edit foto.
     */
    public function editPhoto(
        SuratNodin $suratNodin,
        PhotoNodin $photo
    ): View {

        $suratNodin->load('photos');

        return view(
            'surat_nodins.photos_edit',
            compact(
                'suratNodin',
                'photo'
            )
        );
    }

    /**
     * Memperbarui foto.
     */
    public function updatePhoto(
        Request $request,
        SuratNodin $suratNodin,
        PhotoNodin $photo
    ): RedirectResponse {

        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'caption' => 'nullable|string|max:255',
        ]);

        $photo->surat_nodin_id = $suratNodin->id;
        $photo->caption = $request->input('caption');

        if ($request->hasFile('photo')) {

            $file = $request->file('photo');

            $photo->mime = $file->getClientMimeType();

            $photo->image = file_get_contents(
                $file->getRealPath()
            );
        }

        $photo->save();

        return redirect()
            ->route('surat-nodins.photos', $suratNodin)
            ->with('success', 'Foto berhasil diperbarui.');
    }

    /**
     * Menghapus foto.
     */
    public function destroyPhoto(
        Request $request,
        SuratNodin $suratNodin,
        PhotoNodin $photo
    ): RedirectResponse {

        $photo->delete();

        return redirect()
            ->route('surat-nodins.photos', $suratNodin)
            ->with('success', 'Foto berhasil dihapus.');
    }

    /**
     * Validasi data Surat Nodin.
     */
    private function validateData(Request $request): array
    {
        $validated = $request->validate([

            /*
             * ==============================
             * DATA SURAT
             * ==============================
             */

            'nomor' => 'nullable|string|max:255',

            'sifat' => 'nullable|string|max:255',

            'lampiran' => 'nullable|string|max:255',

            'hal' => 'nullable|string|max:255',

            'kepada' => 'nullable|string|max:255',

            'dari' => 'nullable|string|max:255',

            'dari_plt' => 'nullable|boolean',

            'dari_an' => 'nullable|boolean',

            'tanggal' => 'nullable|date',

            'dasar_surat' => 'nullable|string',

            'isi_surat' => 'nullable|string',

            'penandatangan_id' =>
                'nullable|exists:asns,id',

            'penandatangan_plt' =>
                'nullable|boolean',

            'penandatangan_an' =>
                'nullable|boolean',

            'pegawai_tugas_id' =>
                'nullable|exists:asns,id',

            'kop_surat' =>
                'nullable|string|max:255',


    /*
              * ==============================
              * PESERTA
              * ==============================
              */

            'peserta' =>
                'nullable|array',

            /*
              * DUDIKA
              *
              * Opsional, boleh memilih lebih dari satu.
              * Bila dipilih, nama DUDIKA akan
              * ditampilkan pada kolom Tempat Kegiatan
              * di cetakan Surat Nodin.
              */
            'peserta.*.dudika_id' =>
                'nullable|array',

            'peserta.*.dudika_id.*' =>
                'nullable|integer|exists:daftar_dudika,id',


            /*
             * PEGAWAI
             *
             * Multi-select
             */
            'peserta.*.pegawai_id' =>
                'nullable|array',

            'peserta.*.pegawai_id.*' =>
                'nullable|integer|exists:asns,id',


            /*
             * SISWA
             *
             * Multi-select
             */
            'peserta.*.siswa_id' =>
                'nullable|array',

            'peserta.*.siswa_id.*' =>
                'nullable|integer|exists:data_siswa,id',


            /*
             * TANGGAL KEGIATAN
             */
            'peserta.*.tgl_awal_kegiatan' =>
                'nullable|date',

            'peserta.*.tgl_akhir_kegiatan' =>
                'nullable|date',


            /*
             * TEMPAT KEGIATAN
             *
             * Bisa lebih dari satu tempat.
             *
             * 5000 karakter untuk menghindari
             * masalah ketika uraian tempat panjang.
             */
            'peserta.*.tempat_kegiatan' =>
                'nullable|array',

            'peserta.*.tempat_kegiatan.*' =>
                'nullable|string|max:5000',
        ]);


        /*
         * Checkbox boolean.
         */
        $validated['penandatangan_plt'] =
            $request->boolean('penandatangan_plt');

        $validated['penandatangan_an'] =
            $request->boolean('penandatangan_an');

        $validated['dari_plt'] =
            $request->boolean('dari_plt');

        $validated['dari_an'] =
            $request->boolean('dari_an');


        return $validated;
    }

    /**
     * Sinkronisasi peserta Surat Nodin.
     *
     * Struktur form:
     *
     * peserta[0][pegawai_id][]
     * peserta[0][siswa_id][]
     * peserta[0][tgl_awal_kegiatan]
     * peserta[0][tgl_akhir_kegiatan]
     * peserta[0][tempat_kegiatan][]
     *
     * Semua ID disimpan sebagai nilai tunggal
     * pada setiap baris database.
     */
    private function syncPeserta(
        SuratNodin $suratNodin,
        Request $request
    ): void {

        /*
         * Hapus peserta lama.
         *
         * Penting untuk proses UPDATE.
         */
        $suratNodin
            ->pesertaSuratUsulans()
            ->delete();


        $pesertaList = $request->input(
            'peserta',
            []
        );

        if (!is_array($pesertaList)) {
            return;
        }


        /*
         * Peta (id => nama) Daftar DUDIKA.
         *
         * Dipakai untuk:
         * - menambahkan nama DUDIKA ke daftar
         *   tempat_kegiatan, sehingga nama DUDIKA
         *   ditampilkan pada kolom Tempat Kegiatan
         *   di cetakan Surat Nodin.
         */
        $dudikaMap =
            DaftarDudika::pluck('nama_dudika', 'id')
                ->toArray();


        foreach ($pesertaList as $peserta) {

            if (!is_array($peserta)) {
                continue;
            }


            /*
             * ==========================================
             * DUDIKA (opsional, multiple)
             * ==========================================
             *
             * Bisa memilih lebih dari satu DUDIKA.
             * Setiap nama DUDIKA yang dipilih akan
             * ditambahkan ke daftar tempat kegiatan
             * sehingga muncul pada kolom Tempat
             * Kegiatan di cetakan Surat Nodin, dan
             * dudika_id masing-masingnya tersimpan
             * pada tiap rekatan tempat.
             */

            $dudikaIds =
                $peserta['dudika_id'] ?? [];

            if (!is_array($dudikaIds)) {
                $dudikaIds =
                    $dudikaIds !== null &&
                    $dudikaIds !== ''
                        ? [$dudikaIds]
                        : [];
            }

            $dudikaIds = collect($dudikaIds)
                ->flatten()
                ->filter(
                    fn ($id) =>
                        $id !== null &&
                        $id !== ''
                )
                ->map(
                    fn ($id) => (int) $id
                )
                ->filter(
                    fn ($id) => $id > 0
                )
                ->unique()
                ->values()
                ->all();


            /*
             * ==========================================
             * PEGAWAI
             * ==========================================
             */

            $pegawaiIds =
                $peserta['pegawai_id'] ?? [];

            if (!is_array($pegawaiIds)) {
                $pegawaiIds =
                    $pegawaiIds !== null &&
                    $pegawaiIds !== ''
                        ? [$pegawaiIds]
                        : [];
            }

            $pegawaiIds = collect($pegawaiIds)
                ->flatten()
                ->filter(
                    fn ($id) =>
                        $id !== null &&
                        $id !== ''
                )
                ->map(
                    fn ($id) => (int) $id
                )
                ->filter(
                    fn ($id) => $id > 0
                )
                ->unique()
                ->values()
                ->all();


            /*
             * ==========================================
             * SISWA
             * ==========================================
             */

            $siswaIds =
                $peserta['siswa_id'] ?? [];

            if (!is_array($siswaIds)) {
                $siswaIds =
                    $siswaIds !== null &&
                    $siswaIds !== ''
                        ? [$siswaIds]
                        : [];
            }

            $siswaIds = collect($siswaIds)
                ->flatten()
                ->filter(
                    fn ($id) =>
                        $id !== null &&
                        $id !== ''
                )
                ->map(
                    fn ($id) => (int) $id
                )
                ->filter(
                    fn ($id) => $id > 0
                )
                ->unique()
                ->values()
                ->all();


            /*
             * ==========================================
             * TEMPAT KEGIATAN
             * ==========================================
             */

            $tempatList =
                $peserta['tempat_kegiatan'] ?? [];

            if (!is_array($tempatList)) {
                $tempatList = [$tempatList];
            }

            $tempatList = collect($tempatList)
                ->flatten()
                ->map(
                    fn ($tempat) =>
                        trim((string) $tempat)
                )
                ->filter(
                    fn ($tempat) =>
                        $tempat !== ''
                )
                ->unique()
                ->values()
                ->all();


            /*
             * Jika tidak ada tempat,
             * tetap buat record dengan NULL.
             */
            if (empty($tempatList)) {
                $tempatList = [null];
            }


            /*
             * ==========================================
             * SINKIGIT DUDIKA KE TEMPAT KEGIATAN
             * ==========================================
             *
             * Setiap DUDIKA yang dipilih:
             * - nama DUDIKA ditambahkan ke tempatList
             * - nama <=> dudika_id dicatat pada
             *   $tempatDudikaMap agar tiap rekatin
             *   tempat yang dihasilkan menyimpan
             *   dudika_id yang tepat.
             */

            $tempatDudikaMap = [];

            foreach ($dudikaIds as $dudikaId) {

                if (!isset($dudikaMap[$dudikaId])) {
                    continue;
                }

                $dudikaNama =
                    $dudikaMap[$dudikaId];

                if (
                    !in_array(
                        $dudikaNama,
                        $tempatList,
                        true
                    )
                ) {

                    if (
                        count($tempatList) === 1 &&
                        $tempatList[0] === null
                    ) {

                        $tempatList = [
                            $dudikaNama
                        ];

                    } else {

                        $tempatList[] =
                            $dudikaNama;

                        $tempatList =
                            array_values($tempatList);

                    }

                }

                $tempatDudikaMap[
                    $dudikaNama
                ] = $dudikaId;

            }


            /*
             * ==========================================
             * TANGGAL
             * ==========================================
             */

            $tglAwal =
                $peserta['tgl_awal_kegiatan']
                ?? null;

            $tglAkhir =
                $peserta['tgl_akhir_kegiatan']
                ?? null;


            /*
             * ==========================================
             * TIDAK ADA PEGAWAI DAN TIDAK ADA SISWA
             * ==========================================
             */

            if (
                empty($pegawaiIds) &&
                empty($siswaIds)
            ) {
                continue;
            }


            /*
             * ==========================================
             * PEGAWAI × SISWA × TEMPAT
             * ==========================================
             *
             * Contoh:
             *
             * 2 pegawai
             * 3 siswa
             * 2 tempat
             *
             * = 2 × 3 × 2
             * = 12 record database
             */

            if (
                !empty($pegawaiIds) &&
                !empty($siswaIds)
            ) {

                foreach ($pegawaiIds as $pegawaiId) {

                    foreach ($siswaIds as $siswaId) {

                        foreach ($tempatList as $tempat) {

                            $suratNodin
                                ->pesertaSuratUsulans()
                                ->create([
                                    'pegawai_id' =>
                                        $pegawaiId,

                                    'siswa_id' =>
                                        $siswaId,

                                    'tgl_awal_kegiatan' =>
                                        $tglAwal,

                                    'tgl_akhir_kegiatan' =>
                                        $tglAkhir,

                                    'tempat_kegiatan' =>
                                    $tempat,

                                'dudika_id' =>
                                    $tempatDudikaMap[$tempat] ?? null,
                            ]);
                        }
                    }
                }

                continue;
            }


            /*
             * ==========================================
             * HANYA PEGAWAI
             * ==========================================
             */

            if (!empty($pegawaiIds)) {

                foreach ($pegawaiIds as $pegawaiId) {

                    foreach ($tempatList as $tempat) {

                        $suratNodin
                            ->pesertaSuratUsulans()
                            ->create([
                                'pegawai_id' =>
                                    $pegawaiId,

                                'siswa_id' =>
                                    null,

                                'tgl_awal_kegiatan' =>
                                    $tglAwal,

                                'tgl_akhir_kegiatan' =>
                                    $tglAkhir,

                                'tempat_kegiatan' =>
                                    $tempat,

                                'dudika_id' =>
                                    $tempatDudikaMap[$tempat] ?? null,
                            ]);
                    }
                }

                continue;
            }


            /*
             * ==========================================
             * HANYA SISWA
             * ==========================================
             */

            if (!empty($siswaIds)) {

                foreach ($siswaIds as $siswaId) {

                    foreach ($tempatList as $tempat) {

                        $suratNodin
                            ->pesertaSuratUsulans()
                            ->create([
                                'pegawai_id' =>
                                    null,

                                'siswa_id' =>
                                    $siswaId,

                                'tgl_awal_kegiatan' =>
                                    $tglAwal,

                                'tgl_akhir_kegiatan' =>
                                    $tglAkhir,

                                'tempat_kegiatan' =>
                                    $tempat,

                                'dudika_id' =>
                                    $tempatDudikaMap[$tempat] ?? null,
                            ]);
                    }
                }
            }
        }
    }

    /**
     * Format tanggal Indonesia.
     *
     * Contoh:
     *
     * formatTanggal($tanggal)
     *     22 Mei 2026
     *
     * formatTanggal($tanggal, '%A, %d %B %Y')
     *     Jumat, 22 Mei 2026
     */
    public static function formatTanggal(
        $date,
        string $format = '%d %B %Y'
    ): string {

        if (empty($date)) {
            return '-';
        }

        $carbon = $date instanceof CarbonInterface
            ? $date
            : Carbon::parse($date);


        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];


        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];


        $out = $format;


        $out = str_replace(
            '%d',
            str_pad(
                $carbon->format('d'),
                2,
                '0',
                STR_PAD_LEFT
            ),
            $out
        );


        $out = str_replace(
            '%m',
            $carbon->format('m'),
            $out
        );


        $out = str_replace(
            '%Y',
            $carbon->format('Y'),
            $out
        );


        if (str_contains($format, '%B')) {

            $out = str_replace(
                '%B',
                $months[
                    (int) $carbon->format('n')
                ],
                $out
            );
        }


        if (str_contains($format, '%A')) {

            $out = str_replace(
                '%A',
                $days[
                    (int) $carbon->format('N')
                ],
                $out
            );
        }


        return $out;
    }
}
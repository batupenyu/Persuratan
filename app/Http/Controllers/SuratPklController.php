<?php

namespace App\Http\Controllers;

use App\Models\Asn;
use App\Models\LogoSetting;
use App\Models\SuratPkl;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratPklController extends Controller
{
    public function index(): View
    {
        $suratPkls = SuratPkl::with('penandatangan')->latest()->paginate(10);

        return view('surat_pkls.index', compact('suratPkls'));
    }

    public function create(): View
    {
        $asns = Asn::orderBy('nama')->get();
        $defaultPenandatanganId = Asn::defaultPenandatanganId();

        return view('surat_pkls.create', compact('asns', 'defaultPenandatanganId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        $suratPkl = SuratPkl::create($validated);

        return redirect()->route('surat-pkls.print', $suratPkl)
            ->with('success', 'Surat PKL berhasil disimpan.');
    }

    public function edit(SuratPkl $suratPkl): View
    {
        $asns = Asn::orderBy('nama')->get();
        $suratPkl->load('penandatangan');
        $defaultPenandatanganId = Asn::defaultPenandatanganId();

        return view('surat_pkls.edit', compact('asns', 'suratPkl', 'defaultPenandatanganId'));
    }

    public function update(Request $request, SuratPkl $suratPkl): RedirectResponse
    {
        $validated = $this->validateData($request);

        $suratPkl->update($validated);

        return redirect()->route('surat-pkls.print', $suratPkl)
            ->with('success', 'Surat PKL berhasil diperbarui.');
    }

    public function destroy(SuratPkl $suratPkl): RedirectResponse
    {
        $suratPkl->delete();

        return redirect()->route('surat-pkls.index')
            ->with('success', 'Surat PKL berhasil dihapus.');
    }

    public function print(SuratPkl $suratPkl): View
    {
        $suratPkl->load('penandatangan');

        $kopSuratBase64 = null;
        $logo = LogoSetting::where('name', 'kop_smk')->first() ?? LogoSetting::latest()->first();
        if ($logo && $logo->image) {
            $kopSuratBase64 = 'data:'.($logo->mime ?: 'image/png').';base64,'.base64_encode($logo->image);
        }

        return view('surat_pkls.print', compact('suratPkl', 'kopSuratBase64'));
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'nomor' => 'nullable|string|max:255',
            'sifat' => 'nullable|string|max:255',
            'lampiran' => 'nullable|string|max:255',
            'perihal' => 'nullable|string|max:255',
            'pejabat_tujuan_surat' => 'nullable|string|max:255',
            'kota_tujuan_surat' => 'nullable|string|max:255',
            'tempat_ditetapkan' => 'nullable|string|max:255',
            'tanggal_ditetapkan' => 'nullable|date',
            'penandatangan_id' => 'nullable|exists:asns,id',
            'jumlah_siswa' => 'nullable|integer|min:0',
            'nama_kelas' => 'nullable|string|max:100',
            'tahun_ajaran' => 'nullable|string|max:20',
            'nama_jurusan' => 'nullable|in:Teknik Pengelasan (TP),Teknik Instalasi Tenaga Listrik (TITL),Teknik Sepeda Motor (TBSM),Teknik Kendaraan Ringan (TKR),Teknik Komputer,Teknik Jaringan Komputer dan Telekomunikasi (TJKT)',
            'jenis_instansi_yang_dituju' => 'nullable|string|max:255',
            'lama_magang' => 'nullable|integer|min:0',
            'tgl_awal_magang' => 'nullable|date',
            'tgl_akhir_magang' => 'nullable|date',
            'no_contact' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['tgl_awal_magang']) && !empty($validated['tgl_akhir_magang'])) {
            $awal = Carbon::parse($validated['tgl_awal_magang']);
            $akhir = Carbon::parse($validated['tgl_akhir_magang']);
            $bulan = (($akhir->year - $awal->year) * 12) + ($akhir->month - $awal->month) + 1;

            $validated['lama_magang'] = $awal->lte($akhir) ? max(0, $bulan) : 0;
        }

        return $validated;
    }

    public static function formatTanggal($date, string $format = '%d %B %Y'): string
    {
        if (empty($date)) {
            return '-';
        }

        $carbon = $date instanceof CarbonInterface
            ? $date
            : Carbon::parse($date);

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $out = $format;
        $out = str_replace('%d', str_pad($carbon->format('d'), 2, '0', STR_PAD_LEFT), $out);
        $out = str_replace('%m', $carbon->format('m'), $out);
        $out = str_replace('%Y', $carbon->format('Y'), $out);

        if (str_contains($format, '%B')) {
            $out = str_replace('%B', $months[(int) $carbon->format('n')], $out);
        }

        return $out;
    }
}

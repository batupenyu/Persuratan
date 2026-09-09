<?php

namespace App\Http\Controllers;

use App\Models\Asn;
use App\Models\DataSiswa;
use App\Models\LogoSetting;
use App\Models\SuratSkorsing;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratSkorsingController extends Controller
{
    public function index(): View
    {
        $suratSkorsings = SuratSkorsing::with(['siswa', 'penandatangan'])
            ->latest()
            ->paginate(10);

        return view('surat_skorsings.index', compact('suratSkorsings'));
    }

    public function create(): View
    {
        $asns = Asn::orderBy('nama')->get();
        $siswas = DataSiswa::orderBy('nama')->get();
        $defaultPenandatanganId = Asn::defaultPenandatanganId();

        return view('surat_skorsings.create', compact('asns', 'siswas', 'defaultPenandatanganId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $suratSkorsing = SuratSkorsing::create($this->validateData($request));

        return redirect()->route('surat-skorsings.print', $suratSkorsing)
            ->with('success', 'Surat Skorsing berhasil disimpan.');
    }

    public function edit(SuratSkorsing $suratSkorsing): View
    {
        $asns = Asn::orderBy('nama')->get();
        $siswas = DataSiswa::orderBy('nama')->get();
        $suratSkorsing->load('siswa', 'penandatangan');
        $defaultPenandatanganId = Asn::defaultPenandatanganId();

        return view('surat_skorsings.edit', compact('asns', 'siswas', 'suratSkorsing', 'defaultPenandatanganId'));
    }

    public function update(Request $request, SuratSkorsing $suratSkorsing): RedirectResponse
    {
        $suratSkorsing->update($this->validateData($request));

        return redirect()->route('surat-skorsings.print', $suratSkorsing)
            ->with('success', 'Surat Skorsing berhasil diperbarui.');
    }

    public function destroy(SuratSkorsing $suratSkorsing): RedirectResponse
    {
        $suratSkorsing->delete();

        return redirect()->route('surat-skorsings.index')
            ->with('success', 'Surat Skorsing berhasil dihapus.');
    }

    public function print(SuratSkorsing $suratSkorsing): View
    {
        $suratSkorsing->load('siswa', 'penandatangan');
        $kopSuratBase64 = null;
        $logo = LogoSetting::where('name', 'kop_smk')->first() ?? LogoSetting::latest()->first();
        if ($logo && $logo->image) {
            $kopSuratBase64 = 'data:'.($logo->mime ?: 'image/png').';base64,'.base64_encode($logo->image);
        }

        return view('surat_skorsings.print', compact('suratSkorsing', 'kopSuratBase64'));
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nomor_surat' => 'nullable|string|max:255',
            'siswa_id' => 'nullable|exists:data_siswa,id',
            'penandatangan_id' => 'nullable|exists:asns,id',
            'pelanggaran' => 'nullable|string',
            'durasi_skorsing' => 'nullable|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tempat_ditetapkan' => 'nullable|string|max:255',
            'tanggal_ditetapkan' => 'nullable|date',
        ]);
    }

    public static function formatTanggal($date, string $format = '%d %B %Y'): string
    {
        if (empty($date)) {
            return '...................';
        }

        $carbon = $date instanceof CarbonInterface ? $date : Carbon::parse($date);
        $months = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $out = str_replace('%d', $carbon->format('d'), $format);
        $out = str_replace('%m', $carbon->format('m'), $out);
        $out = str_replace('%Y', $carbon->format('Y'), $out);

        return str_replace('%B', $months[(int) $carbon->format('n')], $out);
    }
}

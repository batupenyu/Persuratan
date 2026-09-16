<?php

namespace App\Http\Controllers;

use App\Models\Asn;
use App\Models\LogoSetting;
use App\Models\SuratPengantar;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuratPengantarController extends Controller
{
    public function index(): View
    {
        $suratPengantars = SuratPengantar::with('penandatangan')
            ->latest()
            ->paginate(5);

        return view('surat_pengantars.index', compact('suratPengantars'));
    }

    public function create(): View
    {
        $asns = Asn::orderBy('nama')->get();

        $defaultPenandatanganId = Asn::defaultPenandatanganId();

        return view('surat_pengantars.create', compact('asns', 'defaultPenandatanganId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateData($request);

        $suratPengantar = SuratPengantar::create($validated);

        return redirect()->route('surat-pengantars.print', $suratPengantar)
            ->with('success', 'Surat Pengantar berhasil disimpan.');
    }

    public function edit(SuratPengantar $suratPengantar): View
    {
        $asns = Asn::orderBy('nama')->get();
        $suratPengantar->load('penandatangan');

        $defaultPenandatanganId = Asn::defaultPenandatanganId();

        return view('surat_pengantars.edit', compact('asns', 'suratPengantar', 'defaultPenandatanganId'));
    }

    public function update(Request $request, SuratPengantar $suratPengantar): RedirectResponse
    {
        $validated = $this->validateData($request);

        $suratPengantar->update($validated);

        return redirect()->route('surat-pengantars.print', $suratPengantar)
            ->with('success', 'Surat Pengantar berhasil diperbarui.');
    }

    public function destroy(SuratPengantar $suratPengantar): RedirectResponse
    {
        $suratPengantar->delete();

        return redirect()->route('surat-pengantars.index')
            ->with('success', 'Surat Pengantar berhasil dihapus.');
    }

    public function print(SuratPengantar $suratPengantar): View
    {
        $suratPengantar->load('penandatangan');

        $kopSuratBase64 = null;
        $logo = LogoSetting::where('name', 'kop_smk')->first() ?? LogoSetting::latest()->first();
        if ($logo && $logo->image) {
            $kopSuratBase64 = 'data:'.($logo->mime ?: 'image/png').';base64,'.base64_encode($logo->image);
        }

        return view('surat_pengantars.print', compact('suratPengantar', 'kopSuratBase64'));
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nomor_surat' => 'nullable|string|max:255',
            'tujuan_surat' => 'nullable|string|max:255',
            'yth' => 'nullable|string|max:255',
            'di' => 'nullable|string|max:255',
            'isi_surat' => 'nullable|string',
            'banyaknya' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'tempat_ditetapkan' => 'nullable|string|max:255',
            'tanggal_ditetapkan' => 'nullable|date',
            'nomor_telepon' => 'nullable|string|max:255',
            'penandatangan_id' => 'nullable|exists:asns,id',
        ]);
    }

    public static function formatTanggal(CarbonInterface|string|null $date, string $format = '%d %B %Y'): string
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

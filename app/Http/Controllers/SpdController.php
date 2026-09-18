<?php

namespace App\Http\Controllers;

use App\Models\Asn;
use App\Models\LogoSetting;
use App\Models\Spd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpdController extends Controller
{
    public function index(): View
    {
        $spds = Spd::with('asn')->latest()->paginate(10);

        return view('spds.index', compact('spds'));
    }

    public function create(Request $request): View
    {
        $asns = Asn::orderBy('nama')->get();
        $selectedAsn = null;

        if ($request->filled('asn_id')) {
            $selectedAsn = Asn::find($request->input('asn_id'));
        }

        return view('spds.create', compact('asns', 'selectedAsn'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asn_id' => 'required|exists:asns,id',
            'nomor' => 'nullable|string|max:100',
            'lembar_ke' => 'nullable|string|max:50',
            'kode_no' => 'nullable|string|max:50',
            'pejabat_pemberi_tugas' => 'required|string|max:255',
            'atasan_pejabat' => 'nullable|string|max:255',
            'jenis_pembiayaan' => 'nullable|in:APBD,APBN',
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:100',
            'jabatan_instansi' => 'nullable|string|max:255',
            'tingkat_biaya' => 'nullable|string|max:100',
            'maksud_perjalanan' => 'required|string|max:255',
            'alat_angkut' => 'nullable|string|max:100',
            'tempat_berangkat' => 'required|string|max:255',
            'tempat_tujuan' => 'required|string|max:255',
            'lama_perjalanan' => 'nullable|integer|min:0',
            'tanggal_berangkat' => 'nullable|date',
            'tanggal_kembali' => 'nullable|date',
            'instansi' => 'nullable|string|max:255',
            'akun' => 'nullable|string|max:255',
            'keterangan_lain' => 'nullable|string',
            'dikeluarkan_di' => 'nullable|string|max:255',
            'tanggal_dikeluarkan' => 'nullable|date',
        ]);

        Spd::create($validated);

        return redirect()->route('spds.index')->with('success', 'SPD berhasil dibuat.');
    }

    public function edit(Spd $spd): View
    {
        $asns = Asn::orderBy('nama')->get();

        return view('spds.edit', compact('spd', 'asns'));
    }

    public function update(Request $request, Spd $spd): RedirectResponse
    {
        $validated = $request->validate([
            'asn_id' => 'required|exists:asns,id',
            'nomor' => 'nullable|string|max:100',
            'lembar_ke' => 'nullable|string|max:50',
            'kode_no' => 'nullable|string|max:50',
            'pejabat_pemberi_tugas' => 'required|string|max:255',
            'atasan_pejabat' => 'nullable|string|max:255',
            'jenis_pembiayaan' => 'nullable|in:APBD,APBN',
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'pangkat_golongan' => 'nullable|string|max:100',
            'jabatan_instansi' => 'nullable|string|max:255',
            'tingkat_biaya' => 'nullable|string|max:100',
            'maksud_perjalanan' => 'required|string|max:255',
            'alat_angkut' => 'nullable|string|max:100',
            'tempat_berangkat' => 'required|string|max:255',
            'tempat_tujuan' => 'required|string|max:255',
            'lama_perjalanan' => 'nullable|integer|min:0',
            'tanggal_berangkat' => 'nullable|date',
            'tanggal_kembali' => 'nullable|date',
            'instansi' => 'nullable|string|max:255',
            'akun' => 'nullable|string|max:255',
            'keterangan_lain' => 'nullable|string',
            'dikeluarkan_di' => 'nullable|string|max:255',
            'tanggal_dikeluarkan' => 'nullable|date',
        ]);

        $spd->update($validated);

        return redirect()->route('spds.index')->with('success', 'SPD berhasil diperbarui.');
    }

    public function destroy(Spd $spd): RedirectResponse
    {
        $spd->delete();

        return redirect()->route('spds.index')->with('success', 'SPD berhasil dihapus.');
    }

    public function print(Spd $spd): View
    {
        $spd->load('asn');

        $kopSuratBase64 = null;
        $logo = LogoSetting::where('name', 'kop_smk')->first() ?? LogoSetting::latest()->first();
        if ($logo && $logo->image) {
            $kopSuratBase64 = 'data:'.($logo->mime ?: 'image/png').';base64,'.base64_encode($logo->image);
        }

        $lamaHuruf = null;
        if ($spd->lama_perjalanan) {
            $lamaHuruf = $spd->lama_perjalanan.' ('.$this->terbilangHari($spd->lama_perjalanan).') hari';
        }

        $pejabatAsn = null;
        if ($spd->pejabat_pemberi_tugas) {
            $pejabatAsn = Asn::where('nama', $spd->pejabat_pemberi_tugas)->first();
        }

        $atasanAsn = null;
        if ($spd->atasan_pejabat) {
            $atasanAsn = Asn::where('nama', $spd->atasan_pejabat)->first();
        }

        return view('spds.print', compact('spd', 'kopSuratBase64', 'lamaHuruf', 'pejabatAsn', 'atasanAsn'));
    }

    private function terbilangHari(int $n): string
    {
        $ones = [
            0 => '', 1 => 'satu', 2 => 'dua', 3 => 'tiga', 4 => 'empat',
            5 => 'lima', 6 => 'enam', 7 => 'tujuh', 8 => 'delapan', 9 => 'sembilan',
            10 => 'sepuluh', 11 => 'sebelas',
        ];

        if ($n < 12) {
            return $ones[$n];
        }
        if ($n < 20) {
            return $ones[$n - 10].' belas';
        }
        if ($n < 100) {
            $tens = intdiv($n, 10);
            $unit = $n % 10;
            $tensWord = [
                2 => 'dua puluh', 3 => 'tiga puluh', 4 => 'empat puluh',
                5 => 'lima puluh', 6 => 'enam puluh', 7 => 'tujuh puluh',
                8 => 'delapan puluh', 9 => 'sembilan puluh',
            ];

            return $tensWord[$tens].($unit ? ' '.$ones[$unit] : '');
        }

        return (string) $n;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DaftarDudika;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DaftarDudikaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $dudikas = DaftarDudika::query()
            ->when($search, function ($query, $search) {
                return $query->where('nama_dudika', 'like', "%{$search}%");
            })
            ->orderBy('nama_dudika')
            ->paginate(10)
            ->withQueryString();

        return view('daftar_dudika.index', compact('dudikas'));
    }

    public function create(): View
    {
        return view('daftar_dudika.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_dudika' => 'required|string|max:255',
        ]);

        DaftarDudika::create($validated);

        return redirect()->route('daftar-dudika.index')->with('success', 'Daftar DUDIKA berhasil ditambahkan.');
    }

    public function show(DaftarDudika $dudika): View
    {
        return view('daftar_dudika.show', compact('dudika'));
    }

    public function edit(DaftarDudika $dudika): View
    {
        return view('daftar_dudika.edit', compact('dudika'));
    }

    public function update(Request $request, DaftarDudika $dudika): RedirectResponse
    {
        $validated = $request->validate([
            'nama_dudika' => 'required|string|max:255',
        ]);

        $dudika->update($validated);

        return redirect()->route('daftar-dudika.index')->with('success', 'Daftar DUDIKA berhasil diperbarui.');
    }

    public function destroy(DaftarDudika $dudika): RedirectResponse
    {
        $dudika->delete();

        return redirect()->route('daftar-dudika.index')->with('success', 'Daftar DUDIKA berhasil dihapus.');
    }

    public function destroyAll(): RedirectResponse
    {
        DaftarDudika::query()->delete();

        return redirect()->route('daftar-dudika.index')->with('success', 'Semua data DUDIKA berhasil dihapus.');
    }
}

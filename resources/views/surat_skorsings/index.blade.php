@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Surat Skorsing</h1>
                    <a href="{{ route('surat-skorsings.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Buat Surat Skorsing</a>
                </div>
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
                @endif
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-700"><tr>
                            <th class="px-4 py-2 text-left">Nomor Surat</th>
                            <th class="px-4 py-2 text-left">Nama Siswa</th>
                            <th class="px-4 py-2 text-left">Periode Skorsing</th>
                            <th class="px-4 py-2 text-left">Aksi</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @forelse($suratSkorsings as $suratSkorsing)
                            <tr>
                                <td class="px-4 py-2">{{ $suratSkorsing->nomor_surat ?: '-' }}</td>
                                <td class="px-4 py-2">{{ $suratSkorsing->siswa->nama ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $suratSkorsing->tanggal_mulai ? $suratSkorsing->tanggal_mulai->format('d-m-Y') : '-' }} s.d. {{ $suratSkorsing->tanggal_selesai ? $suratSkorsing->tanggal_selesai->format('d-m-Y') : '-' }}</td>
                                <td class="px-4 py-2"><div class="flex items-center gap-3">
                                    <a href="{{ route('surat-skorsings.print', $suratSkorsing) }}" class="text-blue-600 hover:text-blue-800" title="Cetak" aria-label="Cetak">Cetak</a>
                                    <a href="{{ route('surat-skorsings.edit', $suratSkorsing) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit" aria-label="Edit">Edit</a>
                                    <form action="{{ route('surat-skorsings.destroy', $suratSkorsing) }}" method="POST" class="inline" onsubmit="return confirm('Hapus Surat Skorsing ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus" aria-label="Hapus">Hapus</button>
                                    </form>
                                </div></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-2 text-center">Belum ada data Surat Skorsing.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $suratSkorsings->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

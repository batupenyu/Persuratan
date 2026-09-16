@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Surat Pengantar</h1>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Nomor Surat</th>
                                <th class="px-4 py-2 text-left">Tujuan</th>
                                <th class="px-4 py-2 text-left">Tanggal</th>
                                <th class="px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @forelse($suratPengantars as $suratPengantar)
                            <tr>
                                <td class="px-4 py-2" style="width: 300px">{{ $suratPengantar->isi_surat ?: '-' }}</td>
                                <td class="px-4 py-2">{{ $suratPengantar->tujuan_surat ?: '-' }}</td>
                                <td class="px-4 py-2">{{ $suratPengantar->tanggal_ditetapkan ? \Carbon\Carbon::parse($suratPengantar->tanggal_ditetapkan)->format('d-m-Y') : '-' }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('surat-pengantars.print', $suratPengantar) }}" class="text-blue-600 hover:text-blue-800" title="Cetak" aria-label="Cetak">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6v-8z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('surat-pengantars.edit', $suratPengantar) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit" aria-label="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('surat-pengantars.destroy', $suratPengantar) }}" method="POST" class="inline" onsubmit="return confirm('Hapus Surat Pengantar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus" aria-label="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m2 0v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6h14zM10 11v6M14 11v6"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center">Belum ada data Surat Pengantar.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <a href="{{ route('surat-pengantars.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Buat Surat Pengantar Baru
                    </a>
                </div>

                <div class="mt-4">
                    {{ $suratPengantars->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

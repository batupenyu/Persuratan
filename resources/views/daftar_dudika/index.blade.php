@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Daftar DUDIKA</h1>
                    <div class="flex gap-2">
                        <a href="{{ route('daftar-dudika.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tambah Data
                        </a>
                        <form action="{{ route('daftar-dudika.destroy.all') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SEMUA data DUDIKA? Aksi ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Hapus Semua
                            </button>
                        </form>
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {!! session('success') !!}
                    </div>
                @endif

                <form method="GET" action="{{ route('daftar-dudika.index') }}" class="mb-4">
                    <div class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama DUDIKA..." class="w-full max-w-md border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Cari</button>
                        @if(request('search'))
                        <a href="{{ route('daftar-dudika.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Nama DUDIKA</th>
                                <th class="px-4 py-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @forelse($dudikas as $dudika)
                            <tr>
                                <td class="px-4 py-2">{{ $dudika->nama_dudika }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('daftar-dudika.show', $dudika) }}" class="text-blue-600 hover:text-blue-800" title="Lihat">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('daftar-dudika.edit', $dudika) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('daftar-dudika.destroy', $dudika) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-4 py-2 text-center">Tidak ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $dudikas->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

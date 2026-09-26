@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold">Detail Daftar DUDIKA</h1>
                    <div>
                        <a href="{{ route('daftar-dudika.edit', $dudika) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">Edit</a>
                        <a href="{{ route('daftar-dudika.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="block text-sm font-medium text-gray-500">Nama DUDIKA</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $dudika->nama_dudika }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

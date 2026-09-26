@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-2xl font-bold mb-6">Edit Daftar DUDIKA</h1>

                <form action="{{ route('daftar-dudika.update', $dudika) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium mb-1">Nama DUDIKA *</label>
                        <input type="text" name="nama_dudika" value="{{ old('nama_dudika', $dudika->nama_dudika) }}" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:text-gray-100" required>
                        @error('nama_dudika')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update
                        </button>
                        <a href="{{ route('daftar-dudika.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

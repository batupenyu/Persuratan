<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - CRUD ASN</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @media (min-width: 1024px) {
                .main-wrapper {
                    margin-left: 256px;
                }
            }
        </style>
    </head>
    <body class="bg-gray-50 dark:bg-gray-900">
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col h-screen">
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                    <a href="{{ route('asns.index') }}" class="text-lg font-bold text-gray-800 dark:text-gray-100">Menu</a>
                    <button id="close-sidebar" class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <nav class="p-4 space-y-1 overflow-y-auto flex-1 min-h-0">
                    <a href="{{ route('asns.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('asns.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                        Data ASN
                    </a>
                    <a href="{{ route('data-siswa.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('data-siswa.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                        Data Siswa
                    </a>
                    <a href="{{ route('spds.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('spds.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                        SPD
                    </a>
                    <a href="{{ route('logos.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('logos.*') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                        Logo
                    </a>

                    @php
                        $categories = [
                            [
                                'label' => 'Perjalanan Dinas & Tugas',
                                'routes' => ['surat-tugas.*', 'surat-tugas-pkls.*', 'drh-satyalancana.*'],
                                'items' => [
                                    ['label' => 'Surat Tugas', 'route' => 'surat-tugas.index', 'routeName' => 'surat-tugas.*'],
                                    ['label' => 'Surat Tugas PKL', 'route' => 'surat-tugas-pkls.index', 'routeName' => 'surat-tugas-pkls.*'],
                                    ['label' => 'DRH Satyalancana', 'route' => 'drh-satyalancana.index', 'routeName' => 'drh-satyalancana.*'],
                                ],
                            ],
                            [
                                'label' => 'Cuti & Dispensasi',
                                'routes' => ['surat-cutis.*', 'form-cutis.*', 'laporan-cutis.*', 'surat-dispensasis.*', 'hari-liburs.*'],
                                'items' => [
                                    ['label' => 'Surat Cuti', 'route' => 'surat-cutis.index', 'routeName' => 'surat-cutis.*'],
                                    ['label' => 'Form Cuti', 'route' => 'form-cutis.index', 'routeName' => 'form-cutis.*'],
                                    ['label' => 'Laporan Cuti', 'route' => 'laporan-cutis.index', 'routeName' => 'laporan-cutis.*'],
                                    ['label' => 'Surat Dispensasi', 'route' => 'surat-dispensasis.index', 'routeName' => 'surat-dispensasis.*'],
                                    ['label' => 'Hari Libur', 'route' => 'hari-liburs.index', 'routeName' => 'hari-liburs.*'],
                                ],
                            ],
                            [
                                'label' => 'Peserta Didik',
                                'routes' => ['surat-keterangans.*', 'surat-kp4-olds.*', 'surat-panggilan-siswas.*', 'surat-skorsings.*', 'surat-penarikan-siswas.*'],
                                'items' => [
                                    ['label' => 'Surat Keterangan', 'route' => 'surat-keterangans.index', 'routeName' => 'surat-keterangans.*'],
                                    ['label' => 'Surat KP4', 'route' => 'surat-kp4-olds.index', 'routeName' => 'surat-kp4-olds.*'],
                                    ['label' => 'Surat Panggilan Siswa', 'route' => 'surat-panggilan-siswas.index', 'routeName' => 'surat-panggilan-siswas.*'],
                                    ['label' => 'Surat Skorsing', 'route' => 'surat-skorsings.index', 'routeName' => 'surat-skorsings.*'],
                                    ['label' => 'Surat Penarikan Siswa', 'route' => 'surat-penarikan-siswas.index', 'routeName' => 'surat-penarikan-siswas.*'],
                                ],
                            ],
                            [
                                'label' => 'Surat Khusus & Umum',
                                'routes' => ['surat-pengantars.*', 'surat-rekomendasis.*', 'surat-resmis.*', 'surat-undangans.*', 'surat-pernyataans.*', 'surat-santunans.*', 'surat-mewakili.*', 'surat-aktif-mengajars.*', 'persetujuan-magangs.*', 'surat-bersedias.*'],
                                'items' => [
                                    ['label' => 'Surat Pengantar', 'route' => 'surat-pengantars.index', 'routeName' => 'surat-pengantars.*'],
                                    ['label' => 'Surat Rekomendasi', 'route' => 'surat-rekomendasis.index', 'routeName' => 'surat-rekomendasis.*'],
                                    ['label' => 'Surat Resmi', 'route' => 'surat-resmis.index', 'routeName' => 'surat-resmis.*'],
                                    ['label' => 'Surat Undangan', 'route' => 'surat-undangans.index', 'routeName' => 'surat-undangans.*'],
                                    ['label' => 'Surat Pernyataan', 'route' => 'surat-pernyataans.index', 'routeName' => 'surat-pernyataans.*'],
                                    ['label' => 'Surat Santunan', 'route' => 'surat-santunans.index', 'routeName' => 'surat-santunans.*'],
                                    ['label' => 'Surat Mewakili', 'route' => 'surat-mewakili.index', 'routeName' => 'surat-mewakili.*'],
                                    ['label' => 'Surat Aktif Mengajar', 'route' => 'surat-aktif-mengajars.index', 'routeName' => 'surat-aktif-mengajars.*'],
                                    ['label' => 'Persetujuan Magang', 'route' => 'persetujuan-magangs.index', 'routeName' => 'persetujuan-magangs.*'],
                                    ['label' => 'Surat Bersedia/Tidak ', 'route' => 'surat-bersedias.index', 'routeName' => 'surat-bersedias.*'],
                                ],
                            ],
                            [
                                'label' => 'Laporan & Nota Dinas',
                                'routes' => ['sptjms.*', 'spmts.*', 'surat-nodins.*', 'laporan-nodins.*', 'lampirans.*'],
                                'items' => [
                                    ['label' => 'SPTJM', 'route' => 'sptjms.index', 'routeName' => 'sptjms.*'],
                                    ['label' => 'SPMT', 'route' => 'spmts.index', 'routeName' => 'spmts.*'],
                                    ['label' => 'Surat Nodin', 'route' => 'surat-nodins.index', 'routeName' => 'surat-nodins.*'],
                                    ['label' => 'Laporan Nodin', 'route' => 'laporan-nodins.index', 'routeName' => 'laporan-nodins.*'],
                                    ['label' => 'Lampiran', 'route' => 'lampirans.index', 'routeName' => 'lampirans.*'],
                                ],
                            ],
                        ];
                    @endphp

                    @foreach($categories as $category)
                        @php($isCategoryActive = collect($category['routes'])->contains(fn($r) => request()->routeIs($r)))
                        <div class="mb-1">
                            <button type="button" class="category-toggle w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $isCategoryActive ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                <span>{{ $category['label'] }}</span>
                                <svg class="category-chevron w-4 h-4 transition-transform {{ $isCategoryActive ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="category-submenu ml-3 space-y-1 {{ $isCategoryActive ? '' : 'hidden' }}">
                                @foreach($category['items'] as $item)
                                    <a href="{{ route($item['route']) }}" class="block px-3 py-2 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs($item['routeName']) ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : '' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </nav>
            </aside>

            <!-- Overlay -->
            <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-w-0 main-wrapper">
                <!-- Top Header -->
                <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-16 flex items-center justify-between px-4">
                    <button id="open-sidebar" class="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        @yield('header-title', '')
                    </div>
                </header>

                <main class="flex-1 p-6">
                    @yield('content')
                </main>
            </div>
        </div>

        <script>
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const openBtn = document.getElementById('open-sidebar');
            const closeBtn = document.getElementById('close-sidebar');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            openBtn.addEventListener('click', openSidebar);
            closeBtn.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeSidebar();
            });

            const categoryToggles = document.querySelectorAll('.category-toggle');
            categoryToggles.forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const submenu = toggle.nextElementSibling;
                    const chevron = toggle.querySelector('.category-chevron');
                    submenu.classList.toggle('hidden');
                    chevron.classList.toggle('rotate-180');
                });
            });
        </script>
    </body>
</html>

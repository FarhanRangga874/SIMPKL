<aside class="col-start-1 row-start-1 flex h-dvh min-h-0 w-64 flex-col border-r border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">

    {{-- Logo dan identitas aplikasi --}}
    <div class="h-16 flex shrink-0 items-center px-6 border-b border-gray-100 dark:border-gray-700">
        <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-3">
            <x-application-logo
                class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"
            />

            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    SIM PKL
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Panel Mahasiswa
                </p>
            </div>
        </a>
    </div>

    {{-- Menu navigasi --}}
    <nav class="flex-1 space-y-2 overflow-y-auto p-4">

        {{-- Dashboard --}}
        <x-nav-link
            :href="route('mahasiswa.dashboard')"
            :active="request()->routeIs('mahasiswa.dashboard')"
            class="w-full"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8"
                class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 10.5 12 3l9 7.5M5.25 9v11.25h13.5V9M9 20.25v-6h6v6"/>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        {{-- Pendaftaran PKL --}}
        <x-nav-link
            href="#"
            :active="request()->routeIs('mahasiswa.pendaftaran.*')"
            class="w-full"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8"
                class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 4.5h8M9 3h6v3H9zM6 5.25H4.5v15h15v-15H18M8 12h8M8 16h5"/>
            </svg>
            <span>Pendaftaran PKL</span>
        </x-nav-link>

        {{-- Pelaksanaan PKL --}}
        <x-nav-link
            href="#"
            :active="request()->routeIs('mahasiswa.pelaksanaan.*')"
            class="w-full"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8"
                class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v6l4 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            <span>Pelaksanaan PKL</span>
        </x-nav-link>

        {{-- Seminar PKL --}}
        <x-nav-link
            href="#"
            :active="request()->routeIs('mahasiswa.seminar.*')"
            class="w-full"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8"
                class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 5.25h16v11.5H4zM8 20.25h8M12 16.75v3.5M8 9h8M8 12h5"/>
            </svg>
            <span>Seminar PKL</span>
        </x-nav-link>

        {{-- Nilai Akhir --}}
        <x-nav-link
            href="#"
            :active="request()->routeIs('mahasiswa.nilai.*')"
            class="w-full"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8"
                class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 4.5h8M9 3h6v3H9zM6 5.25H4.5v15h15v-15H18M8 11l2.5 2.5L16 8M8 17h8"/>
            </svg>
            <span>Nilai Akhir</span>
        </x-nav-link>

        {{-- Dokumen --}}
        <x-nav-link
            href="#"
            :active="request()->routeIs('mahasiswa.dokumen.*')"
            class="w-full"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.8"
                class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 3.75h8l4 4v12.5H6zM14 3.75v4h4M9 12h6M9 15.5h6"/>
            </svg>
            <span>Dokumen</span>
        </x-nav-link>

    </nav>
</aside>
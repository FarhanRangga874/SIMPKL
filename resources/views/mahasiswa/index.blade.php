<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Mahasiswa
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold">
                    Selamat datang, {{ auth()->user()->name }}
                </h3>

                <p class="mt-2 text-gray-600">
                    Pantau pendaftaran PKL, pelaksanaan, logbook,
                    laporan, seminar, dan nilai akhir melalui dashboard ini.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>

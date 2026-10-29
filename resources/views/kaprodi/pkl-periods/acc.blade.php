<x-app-layout>
    <x-slot name="header">
        <div>
            <a
                href="{{ route('kaprodi.pkl-periods.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800"
            >
                &larr; Kembali ke Periode PKL
            </a>

            <h2 class="mt-2 text-xl font-semibold text-gray-800">
                Kelola ACC Pendaftaran PKL
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                {{ $pklPeriod->semester }}
                {{ $pklPeriod->tahun_akademik }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Informasi periode --}}
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <p class="text-sm text-gray-500">
                            Periode akademik
                        </p>

                        <h3 class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $pklPeriod->semester }}
                            {{ $pklPeriod->tahun_akademik }}
                        </h3>
                    </div>

                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-sm font-medium
                        {{ $pklPeriod->status === 'aktif'
                            ? 'bg-green-100 text-green-700'
                            : ($pklPeriod->status === 'selesai'
                                ? 'bg-gray-100 text-gray-700'
                                : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($pklPeriod->status) }}
                    </span>
                </div>
            </div>

            {{-- Navigasi batch --}}
            <div
                x-data="{
                    selectedBatch: {{ $pklPeriod->batches->first()?->id ?? 'null' }}
                }"
                class="rounded-xl border border-gray-200 bg-white p-6"
            >
                <div>
                    <h3 class="text-base font-semibold text-gray-900">
                        Daftar Batch
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Pilih batch untuk melihat informasi pendaftaran.
                    </p>
                </div>

                @if ($pklPeriod->batches->isEmpty())
                    <div class="mt-6 rounded-lg border border-dashed border-gray-300 p-8 text-center">
                        <p class="font-medium text-gray-800">
                            Belum ada batch PKL
                        </p>

                        <p class="mt-2 text-sm text-gray-500">
                            Buat batch terlebih dahulu melalui halaman Periode PKL.
                        </p>
                    </div>
                @else
                    {{-- Tab batch --}}
                    <div class="mt-5 flex flex-wrap gap-2 border-b border-gray-200 pb-4">
                        @foreach ($pklPeriod->batches as $batch)
                            <button
                                type="button"
                                @click="selectedBatch = {{ $batch->id }}"
                                :class="selectedBatch === {{ $batch->id }}
                                    ? 'bg-gray-900 text-white'
                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="rounded-lg px-4 py-2 text-sm font-medium transition"
                            >
                                {{ $batch->nama_batch }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Konten tiap batch --}}
                    <div class="mt-6 space-y-6">
                        @foreach ($pklPeriod->batches as $batch)
                            <section
                                x-show="selectedBatch === {{ $batch->id }}"
                                x-cloak
                            >
                                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-start">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">
                                            {{ $batch->nama_batch }}
                                        </h4>

                                        <p class="mt-1 text-sm text-gray-500">
                                            Periode pendaftaran
                                        </p>

                                        <p class="mt-1 text-sm text-gray-700">
                                            {{ $batch->tanggal_mulai->format('d M Y') }}
                                            –
                                            {{ $batch->tanggal_selesai->format('d M Y') }}
                                        </p>
                                    </div>

                                    <span class="inline-flex w-fit rounded-full bg-blue-50 px-3 py-1 text-sm text-blue-700">
                                        {{ ucfirst($batch->status) }}
                                    </span>
                                </div>

                                {{-- Ringkasan pendaftar --}}
                                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div class="rounded-lg border border-gray-200 p-4">
                                        <p class="text-sm text-gray-500">
                                            Total pendaftar
                                        </p>
                                        <p class="mt-2 text-2xl font-semibold text-gray-900">
                                            —
                                        </p>
                                    </div>

                                    <div class="rounded-lg border border-gray-200 p-4">
                                        <p class="text-sm text-gray-500">
                                            Menunggu ACC
                                        </p>
                                        <p class="mt-2 text-2xl font-semibold text-gray-900">
                                            —
                                        </p>
                                    </div>

                                    <div class="rounded-lg border border-gray-200 p-4">
                                        <p class="text-sm text-gray-500">
                                            Sudah diputuskan
                                        </p>
                                        <p class="mt-2 text-2xl font-semibold text-gray-900">
                                            —
                                        </p>
                                    </div>
                                </div>

                                {{-- Tabel pendaftar --}}
                                <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
                                    <div class="border-b border-gray-200 px-4 py-4">
                                        <h5 class="font-semibold text-gray-900">
                                            Daftar Mahasiswa
                                        </h5>
                                    </div>

                                    <div class="px-4 py-10 text-center">
                                        <p class="font-medium text-gray-800">
                                            Data pendaftar belum tersedia
                                        </p>

                                        <p class="mx-auto mt-2 max-w-lg text-sm text-gray-500">
                                            Daftar mahasiswa, status pengajuan,
                                            dan tindakan ACC akan ditampilkan
                                            setelah tabel serta proses pendaftaran
                                            mahasiswa diimplementasikan.
                                        </p>
                                    </div>
                                </div>
                            </section>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
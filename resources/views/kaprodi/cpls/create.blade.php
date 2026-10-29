<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah CPL
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Tambahkan Capaian Pembelajaran Lulusan sebagai acuan periode PKL.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="font-semibold text-gray-900">
                        Informasi Capaian Pembelajaran Lulusan
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Lengkapi seluruh informasi berikut.
                    </p>
                </div>

                <form
                    action="{{ route('kaprodi.cpls.store') }}"
                    method="POST"
                    class="space-y-6 p-6"
                >
                    @csrf

                    {{-- Ringkasan kesalahan --}}
                    @if ($errors->any())
                        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                            <p class="mb-2 font-semibold">
                                Data belum dapat disimpan.
                            </p>

                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Kode CPL --}}
                    <div>
                        <label
                            for="kode_cpl"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Kode CPL
                        </label>

                        <input
                            type="text"
                            name="kode_cpl"
                            id="kode_cpl"
                            value="{{ old('kode_cpl') }}"
                            maxlength="20"
                            placeholder="Contoh: CPL-01"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        <p class="mt-1.5 text-xs text-gray-500">
                            Gunakan kode unik untuk setiap CPL, misalnya CPL-01.
                            Kode ini menjadi identitas utama CPL.
                        </p>
                    </div>

                    {{-- Domain kompetensi --}}
                    <div>
                        <label
                            for="domain_kompetensi"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Domain Kompetensi
                        </label>

                        <input
                            type="text"
                            name="domain_kompetensi"
                            id="domain_kompetensi"
                            value="{{ old('domain_kompetensi') }}"
                            maxlength="255"
                            placeholder="Contoh: Pengetahuan"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        <p class="mt-1.5 text-xs text-gray-500">
                            Masukkan domain kompetensi sesuai ketentuan
                            kurikulum program studi.
                        </p>
                    </div>

                    {{-- Deskripsi CPL --}}
                    <div>
                        <label
                            for="deskripsi_cpl"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Deskripsi CPL
                        </label>

                        <textarea
                            name="deskripsi_cpl"
                            id="deskripsi_cpl"
                            rows="5"
                            required
                            placeholder="Tuliskan deskripsi capaian pembelajaran lulusan..."
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('deskripsi_cpl') }}</textarea>

                        <p class="mt-1.5 text-xs text-gray-500">
                            Tuliskan rumusan CPL secara lengkap dan jelas.
                        </p>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">

                        <a
                            href="{{ route('kaprodi.cpls.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Simpan CPL
                        </button>

                    </div>
                </form>
            </div>

            <div class="mt-5 rounded-lg border border-blue-100 bg-blue-50 p-4">
                <p class="text-sm leading-6 text-blue-800">
                    <strong>Catatan:</strong> CPL yang berhasil disimpan
                    akan masuk ke daftar Pengaturan CPL dan dapat dipilih
                    saat Kaprodi membuat periode pendaftaran PKL.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>

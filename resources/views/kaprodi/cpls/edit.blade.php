<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit CPL
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Perbarui data Capaian Pembelajaran Lulusan.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-700">
                        <p class="font-semibold">
                            Terdapat kesalahan pada input:
                        </p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    action="{{ route('kaprodi.cpls.update', $cpl) }}"
                    method="POST"
                >
                    @csrf
                    @method('PUT')

                    {{-- Kode CPL --}}
                    <div class="mb-5">
                        <label for="kode_cpl"
                            class="block text-sm font-medium text-gray-700">
                            Kode CPL
                        </label>

                        <input
                            type="text"
                            id="kode_cpl"
                            name="kode_cpl"
                            value="{{ old('kode_cpl', $cpl->kode_cpl) }}"
                            maxlength="20"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >

                        <p class="mt-1 text-xs text-gray-500">
                            Contoh: CPL-01.
                        </p>
                    </div>

                    {{-- Domain Kompetensi --}}
                    <div class="mb-5">
                        <label for="domain_kompetensi"
                            class="block text-sm font-medium text-gray-700">
                            Domain Kompetensi
                        </label>

                        <input
                            type="text"
                            id="domain_kompetensi"
                            name="domain_kompetensi"
                            value="{{ old('domain_kompetensi', $cpl->domain_kompetensi) }}"
                            maxlength="255"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    {{-- Deskripsi CPL --}}
                    <div class="mb-6">
                        <label for="deskripsi_cpl"
                            class="block text-sm font-medium text-gray-700">
                            Deskripsi CPL
                        </label>

                        <textarea
                            id="deskripsi_cpl"
                            name="deskripsi_cpl"
                            rows="5"
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >{{ old('deskripsi_cpl', $cpl->deskripsi_cpl) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a
                            href="{{ route('kaprodi.cpls.index') }}"
                            class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

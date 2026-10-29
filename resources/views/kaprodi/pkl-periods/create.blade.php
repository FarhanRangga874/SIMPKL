<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Tambah Periode PKL
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Atur semester, tahun akademik, dan CPL untuk periode PKL.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">

                <form
                    action="{{ route('kaprodi.pkl-periods.store') }}"
                    method="POST"
                    class="space-y-8"
                >
                    @csrf

                    {{-- Informasi Periode --}}
                    <section>
                        <div class="mb-5 border-b border-gray-100 pb-3">
                            <h3 class="text-base font-semibold text-gray-900">
                                Informasi Periode
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Nilai awal mengikuti rekomendasi sistem dan dapat disesuaikan.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            {{-- Semester --}}
                            <div>
                                <label
                                    for="semester"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    Semester
                                    <span class="text-red-500">*</span>
                                </label>

                                <select
                                    name="semester"
                                    id="semester"
                                    required
                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option
                                        value="Ganjil"
                                        @selected(old('semester', $defaults['semester']) === 'Ganjil')
                                    >
                                        Ganjil
                                    </option>

                                    <option
                                        value="Genap"
                                        @selected(old('semester', $defaults['semester']) === 'Genap')
                                    >
                                        Genap
                                    </option>
                                </select>

                                @error('semester')
                                    <p class="mt-1.5 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tahun Akademik --}}
                            <div>
                                <label
                                    for="tahun_akademik"
                                    class="mb-1.5 block text-sm font-medium text-gray-700"
                                >
                                    Tahun Akademik
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="tahun_akademik"
                                    id="tahun_akademik"
                                    value="{{ old('tahun_akademik', $defaults['tahun_akademik']) }}"
                                    placeholder="2026/2027"
                                    pattern="[0-9]{4}/[0-9]{4}"
                                    title="Gunakan format YYYY/YYYY, misalnya 2026/2027."
                                    maxlength="9"
                                    required
                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >

                                @error('tahun_akademik')
                                    <p class="mt-1.5 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </section>

                    {{-- CPL --}}
                    <section>
                        <div class="flex flex-col gap-3 border-b border-gray-100 pb-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    Capaian Pembelajaran Lulusan (CPL)
                                    <span class="text-red-500">*</span>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Pilih CPL yang berlaku untuk periode PKL.
                                </p>
                            </div>

                            <div class="sm:shrink-0">
                                <x-modal-cpl />
                            </div>
                        </div>

                        <div
                            class="mt-4"
                            x-data="{
                                cpls: @js($cpls->map(fn ($cpl) => [
                                    'kode_cpl' => (string) $cpl->kode_cpl,
                                    'deskripsi_cpl' => $cpl->deskripsi_cpl,
                                    'domain_kompetensi' => $cpl->domain_kompetensi,
                                ])->values()),

                                selected: @js(array_map('strval', old('cpls', [])))
                            }"
                            @cpl-ditambahkan.window="
                                const kode = String($event.detail.kode_cpl);

                                if (!cpls.some(item => item.kode_cpl === kode)) {
                                    cpls.push({
                                        ...$event.detail,
                                        kode_cpl: kode
                                    });
                                }

                                if (!selected.includes(kode)) {
                                    selected.push(kode);
                                }
                            "
                        >
                            {{-- Kontrol Pilihan --}}
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm text-gray-500">
                                    <span
                                        class="font-medium text-gray-700"
                                        x-text="selected.length"
                                    ></span>
                                    dari
                                    <span x-text="cpls.length"></span>
                                    CPL dipilih
                                </p>

                                <button
                                    type="button"
                                    @click="selected = (cpls.length > 0 && selected.length === cpls.length)
                                        ? []
                                        : cpls.map(cpl => cpl.kode_cpl)"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                    x-text="cpls.length > 0 && selected.length === cpls.length
                                        ? 'Batalkan semua'
                                        : 'Pilih semua'"
                                ></button>
                            </div>

                            {{-- CPL Kosong --}}
                            <div
                                x-cloak
                                x-show="cpls.length === 0"
                                class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-6 text-center"
                            >
                                <p class="text-sm font-medium text-gray-700">
                                    Belum ada data CPL.
                                </p>
                                <p class="mt-1 text-sm text-gray-500">
                                    Tambahkan CPL terlebih dahulu.
                                </p>
                            </div>

                            {{-- Daftar CPL --}}
                            <div class="space-y-3">
                                <template x-for="cpl in cpls" :key="cpl.kode_cpl">
                                    <label
                                        :for="'cpl_' + cpl.kode_cpl"
                                        class="flex cursor-pointer gap-3 rounded-lg border p-4 transition"
                                        :class="selected.includes(cpl.kode_cpl)
                                            ? 'border-indigo-300 bg-indigo-50/40'
                                            : 'border-gray-200 hover:border-gray-300'"
                                    >
                                        <input
                                            type="checkbox"
                                            name="cpls[]"
                                            :id="'cpl_' + cpl.kode_cpl"
                                            :value="cpl.kode_cpl"
                                            x-model="selected"
                                            class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        >

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span
                                                    class="text-sm font-semibold text-gray-900"
                                                    x-text="cpl.kode_cpl"
                                                ></span>

                                                <span
                                                    class="rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-600"
                                                    x-text="cpl.domain_kompetensi"
                                                ></span>
                                            </div>

                                            <p
                                                class="mt-2 text-sm leading-6 text-gray-600"
                                                x-text="cpl.deskripsi_cpl"
                                            ></p>
                                        </div>
                                    </label>
                                </template>
                            </div>

                            {{-- Validasi CPL --}}
                            @error('cpls')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            @if ($errors->has('cpls.*'))
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $errors->first('cpls.*') }}
                                </p>
                            @endif
                        </div>
                    </section>

                    {{-- Status Awal --}}
                    <div class="flex items-center gap-2 rounded-lg bg-gray-50 px-4 py-3">
                        <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                        <p class="text-sm text-gray-600">
                            Status awal:
                            <span class="font-medium text-gray-800">Draft</span>
                        </p>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                        <a
                            href="{{ route('kaprodi.pkl-periods.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Simpan Periode
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

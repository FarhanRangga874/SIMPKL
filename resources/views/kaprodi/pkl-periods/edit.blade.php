
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Edit Periode Pendaftaran PKL
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Perbarui informasi periode dan CPL yang digunakan.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow-sm">

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <p class="mb-2 font-semibold">Periksa kembali data berikut:</p>
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    action="{{ route('kaprodi.pkl-periods.update', $pklPeriod) }}"
                    method="POST"
                    class="space-y-6"
                >
                    @csrf
                    @method('PUT')

                    {{-- Nama periode --}}
                    <div>
                        <label for="nama_periode"
                            class="mb-1 block text-sm font-medium text-gray-700">
                            Nama Periode
                        </label>
                        <input
                            type="text"
                            name="nama_periode"
                            id="nama_periode"
                            value="{{ old('nama_periode', $pklPeriod->nama_periode) }}"
                            placeholder="Contoh: Periode PKL Semester Ganjil 2026/2027"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        @error('nama_periode')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Semester dan tahun akademik --}}
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="semester"
                                class="mb-1 block text-sm font-medium text-gray-700">
                                Semester
                            </label>
                            <select
                                name="semester"
                                id="semester"
                                required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="">Pilih semester</option>
                                <option value="Ganjil"
                                    @selected(old('semester', $pklPeriod->semester) === 'Ganjil')>
                                    Ganjil
                                </option>
                                <option value="Genap"
                                    @selected(old('semester', $pklPeriod->semester) === 'Genap')>
                                    Genap
                                </option>
                            </select>
                            @error('semester')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tahun_akademik"
                                class="mb-1 block text-sm font-medium text-gray-700">
                                Tahun Akademik
                            </label>
                            <input
                                type="text"
                                name="tahun_akademik"
                                id="tahun_akademik"
                                value="{{ old('tahun_akademik', $pklPeriod->tahun_akademik) }}"
                                placeholder="2026/2027"
                                pattern="[0-9]{4}/[0-9]{4}"
                                required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            <p class="mt-1 text-xs text-gray-500">
                                Format: YYYY/YYYY, misalnya 2026/2027.
                            </p>
                            @error('tahun_akademik')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status"
                            class="mb-1 block text-sm font-medium text-gray-700">
                            Status Periode
                        </label>
                        <select
                            name="status"
                            id="status"
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="draft"
                                @selected(old('status', $pklPeriod->status) === 'draft')>
                                Draft
                            </option>
                            <option value="aktif"
                                @selected(old('status', $pklPeriod->status) === 'aktif')>
                                Aktif
                            </option>
                            <option value="selesai"
                                @selected(old('status', $pklPeriod->status) === 'selesai')>
                                Selesai
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- CPL --}}
                    <div class="border-t border-gray-100 pt-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">
                                    Capaian Pembelajaran Lulusan (CPL)
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Pilih minimal satu CPL untuk periode PKL ini.
                                </p>
                            </div>

                            <x-modal-cpl />
                        </div>

                        <div
                            class="mt-4"
                            x-data="{
                                cpls: @js($cpls->map(fn ($cpl) => [
                                    'kode_cpl' => $cpl->kode_cpl,
                                    'deskripsi_cpl' => $cpl->deskripsi_cpl,
                                    'domain_kompetensi' => $cpl->domain_kompetensi,
                                ])->values()),
                                selected: @js(old('cpls', $pklPeriod->cpls->pluck('kode_cpl')->all()))
                            }"
                            @cpl-ditambahkan.window="
                                if (!cpls.some(item => item.kode_cpl === $event.detail.kode_cpl)) {
                                    cpls.push($event.detail);
                                }
                                if (!selected.includes($event.detail.kode_cpl)) {
                                    selected.push($event.detail.kode_cpl);
                                }
                            "
                        >
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-sm text-gray-600">
                                    <span x-text="cpls.length"></span> CPL tersedia
                                </span>

                                <button
                                    type="button"
                                    @click="selected = selected.length === cpls.length ? [] : cpls.map(cpl => cpl.kode_cpl)"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                                    x-text="cpls.length > 0 && selected.length === cpls.length ? 'Batalkan semua pilihan' : 'Pilih semua'"
                                ></button>
                            </div>

                            <div
                                x-show="cpls.length === 0"
                                class="rounded-xl border border-amber-200 bg-amber-50 p-5"
                            >
                                <h4 class="font-semibold text-amber-900">
                                    Belum ada data CPL
                                </h4>
                                <p class="mt-1 text-sm text-amber-800">
                                    Klik tombol Tambah CPL untuk membuat data CPL pertama.
                                </p>
                            </div>

                            <div class="space-y-3">
                                <template x-for="cpl in cpls" :key="cpl.kode_cpl">
                                    <label
                                        :for="'cpl_' + cpl.kode_cpl"
                                        class="flex cursor-pointer gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40"
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
                                                    class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-600"
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

                            @error('cpls')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('cpls.*')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                        <a
                            href="{{ route('kaprodi.pkl-periods.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Batal
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
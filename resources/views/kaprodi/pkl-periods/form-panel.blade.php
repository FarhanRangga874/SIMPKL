<div
    x-cloak
    x-show="panelOpen"
    x-transition.opacity
    @click="closePanel()"
    class="fixed inset-0 z-40 bg-gray-900/40"
    aria-hidden="true"
></div>

{{-- Slide-over panel --}}
<aside
    x-cloak
    x-show="panelOpen"
    x-transition:enter="transform transition ease-in-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    @edit-period.window="openEdit($event.detail.id)"
    class="fixed inset-y-0 right-0 z-50 flex w-full max-w-2xl flex-col bg-white shadow-2xl"
    role="dialog"
    aria-modal="true"
    :aria-label="panelMode === 'create' ? 'Tambah Periode PKL' : 'Edit Periode PKL'"
>
    {{-- Header --}}
    <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5">
        <div>
            <h2 class="text-lg font-semibold text-gray-900"
                x-text="panelMode === 'create' ? 'Tambah Periode PKL' : 'Edit Periode PKL'">
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Atur semester, tahun akademik, dan CPL untuk periode PKL.
            </p>
        </div>

        <button
            type="button"
            @click="closePanel()"
            class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700"
            aria-label="Tutup panel"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    {{-- Form --}}
    <form
        :action="formAction"
        method="POST"
        class="flex min-h-0 flex-1 flex-col"
    >
        @csrf

        <template x-if="panelMode === 'edit'">
            <input type="hidden" name="_method" value="PUT">
        </template>

        {{-- Isi form dapat di-scroll --}}
        <div class="flex-1 space-y-6 overflow-y-auto px-6 py-6">

            {{-- Semester --}}
            <div>
                <label for="panel_semester" class="mb-2 block text-sm font-medium text-gray-700">
                    Semester <span class="text-red-500">*</span>
                </label>

                <select
                    id="panel_semester"
                    name="semester"
                    x-model="form.semester"
                    required
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>

                @error('semester')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tahun Akademik --}}
            <div>
                <label for="panel_tahun_akademik" class="mb-2 block text-sm font-medium text-gray-700">
                    Tahun Akademik <span class="text-red-500">*</span>
                </label>

                <input
                    id="panel_tahun_akademik"
                    type="text"
                    name="tahun_akademik"
                    x-model="form.tahun_akademik"
                    placeholder="Contoh: 2026/2027"
                    pattern="[0-9]{4}/[0-9]{4}"
                    title="Gunakan format YYYY/YYYY, misalnya 2026/2027"
                    required
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >

                <p class="mt-1 text-xs text-gray-500">
                    Tahun akademik direkomendasikan berdasarkan tanggal saat ini dan masih dapat diubah.
                </p>

                @error('tahun_akademik')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- CPL --}}
            <div>
                <div class="mb-3 flex items-center justify-between gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            CPL yang Digunakan <span class="text-red-500">*</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">
                            Pilih CPL yang berlaku untuk periode ini.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="selectAllCpls()"
                        class="shrink-0 text-sm font-medium text-indigo-600 hover:text-indigo-800"
                        x-text="form.cpls.length === cpls.length ? 'Batalkan semua' : 'Pilih semua'"
                    ></button>
                </div>

                <div class="space-y-2">
                    <template x-for="cpl in cpls" :key="cpl.kode_cpl">
                        <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3 transition hover:border-indigo-300 hover:bg-indigo-50/50">
                            <input
                                type="checkbox"
                                name="cpls[]"
                                :value="cpl.kode_cpl"
                                x-model="form.cpls"
                                class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >

                            <span class="min-w-0 flex-1">
                                <span
                                    class="block text-sm font-semibold text-gray-800"
                                    x-text="cpl.kode_cpl"
                                ></span>

                                <span
                                    class="mt-1 block text-sm leading-5 text-gray-600"
                                    x-text="cpl.deskripsi_cpl"
                                ></span>

                                <span
                                    class="mt-1 block text-xs text-indigo-600"
                                    x-text="cpl.domain_kompetensi || 'Domain tidak dicantumkan'"
                                ></span>
                            </span>
                        </label>
                    </template>

                    <div
                        x-show="cpls.length === 0"
                        class="rounded-lg border border-dashed border-gray-300 p-4 text-center text-sm text-gray-500"
                    >
                        Belum ada data CPL yang tersedia.
                    </div>
                </div>

                <p class="mt-2 text-xs text-gray-500">
                    CPL dipilih: <span x-text="form.cpls.length"></span>
                </p>

                @error('cpls')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                @error('cpls.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Pertahankan jika modal tambah CPL sudah digunakan --}}
                <div class="mt-4">
                    <x-modal-cpl />
                </div>
            </div>

            {{-- Informasi status --}}
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="M12 9v3m0 4h.01M10.3 3.9 1.8 18.5A1.5 1.5 0 0 0 3.1 20.8h17.8a1.5 1.5 0 0 0 1.3-2.3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">
                            Status periode dikelola terpisah
                        </p>
                        <p class="mt-1 text-sm leading-5 text-amber-700">
                            Periode baru disimpan sebagai Draft. Untuk mengaktifkan atau mengakhiri periode, gunakan menu aksi pada tabel.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex justify-end gap-3 border-t border-gray-200 bg-white px-6 py-4">
            <button
                type="button"
                @click="closePanel()"
                class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                Batal
            </button>

            <button
                type="submit"
                :disabled="form.cpls.length === 0"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="2" d="m5 12 4 4L19 6" />
                </svg>

                <span x-text="panelMode === 'create' ? 'Simpan Periode' : 'Simpan Perubahan'"></span>
            </button>
        </div>
    </form>
</aside>

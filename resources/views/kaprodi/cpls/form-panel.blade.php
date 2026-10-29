<div
    x-cloak
    x-show="panelOpen"
    x-transition.opacity
    @click="closePanel()"
    class="fixed inset-0 z-40 bg-gray-900/40"
></div>

{{-- Panel slide-over --}}
<aside
    x-cloak
    x-show="panelOpen"
    x-transition:enter="transform transition ease-in-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transform transition ease-in-out duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    @click.stop
    class="fixed inset-y-0 right-0 z-50 flex w-full max-w-2xl flex-col bg-white shadow-2xl"
    role="dialog"
    aria-modal="true"
    aria-labelledby="cpl-panel-title"
>
    {{-- Header --}}
    <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5">
        <div>
            <h2
                id="cpl-panel-title"
                class="text-lg font-semibold text-gray-900"
                x-text="isEdit ? 'Edit CPL' : 'Tambah CPL'"
            ></h2>

            <p
                class="mt-1 text-sm text-gray-500"
                x-text="isEdit
                    ? 'Perbarui informasi Capaian Pembelajaran Lulusan.'
                    : 'Tambahkan Capaian Pembelajaran Lulusan baru.'"
            ></p>
        </div>

        <button
            type="button"
            @click="closePanel()"
            :disabled="saving"
            aria-label="Tutup panel"
            class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 disabled:opacity-50"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
            </svg>
        </button>
    </div>

    {{-- Form --}}
    <form @submit.prevent="submitForm()" class="flex min-h-0 flex-1 flex-col">
        <div class="flex-1 space-y-5 overflow-y-auto px-6 py-6">

            {{-- Pesan error --}}
            <div
                x-cloak
                x-show="message"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                x-text="message"
                role="alert"
            ></div>

            {{-- Kode CPL --}}
            <div>
                <label for="kode_cpl" class="mb-2 block text-sm font-medium text-gray-700">
                    Kode CPL <span class="text-red-500">*</span>
                </label>

                <input
                    id="kode_cpl"
                    type="text"
                    x-model="form.kode_cpl"
                    :disabled="saving"
                    required
                    maxlength="20"
                    placeholder="Contoh: CPL-01"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-100"
                    :class="{ 'border-red-500': errors.kode_cpl }"
                >

                <p
                    x-cloak
                    x-show="errors.kode_cpl"
                    class="mt-1 text-sm text-red-600"
                    x-text="errors.kode_cpl?.[0]"
                ></p>
            </div>

            {{-- Domain kompetensi --}}
            <div>
                <label for="domain_kompetensi" class="mb-2 block text-sm font-medium text-gray-700">
                    Domain Kompetensi <span class="text-red-500">*</span>
                </label>

                <input
                    id="domain_kompetensi"
                    type="text"
                    x-model="form.domain_kompetensi"
                    :disabled="saving"
                    required
                    maxlength="255"
                    placeholder="Masukkan domain kompetensi"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-100"
                    :class="{ 'border-red-500': errors.domain_kompetensi }"
                >

                <p
                    x-cloak
                    x-show="errors.domain_kompetensi"
                    class="mt-1 text-sm text-red-600"
                    x-text="errors.domain_kompetensi?.[0]"
                ></p>
            </div>

            {{-- Deskripsi CPL --}}
            <div>
                <label for="deskripsi_cpl" class="mb-2 block text-sm font-medium text-gray-700">
                    Deskripsi CPL <span class="text-red-500">*</span>
                </label>

                <textarea
                    id="deskripsi_cpl"
                    x-model="form.deskripsi_cpl"
                    :disabled="saving"
                    required
                    rows="5"
                    placeholder="Masukkan deskripsi Capaian Pembelajaran Lulusan"
                    class="w-full resize-y rounded-lg border border-gray-300 px-3 py-2.5 text-sm leading-6 text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:bg-gray-100"
                    :class="{ 'border-red-500': errors.deskripsi_cpl }"
                ></textarea>

                <p
                    x-cloak
                    x-show="errors.deskripsi_cpl"
                    class="mt-1 text-sm text-red-600"
                    x-text="errors.deskripsi_cpl?.[0]"
                ></p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 border-t border-gray-200 bg-white px-6 py-4">
            <button
                type="button"
                @click="closePanel()"
                :disabled="saving"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:opacity-50"
            >
                Batal
            </button>

            <button
                type="submit"
                :disabled="saving"
                class="inline-flex min-w-36 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <svg
                    x-cloak
                    x-show="saving"
                    class="h-4 w-4 animate-spin"
                    fill="none"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>

                <span
                    x-text="saving
                        ? 'Menyimpan...'
                        : (isEdit ? 'Simpan Perubahan' : 'Simpan CPL')"
                ></span>
            </button>
        </div>
    </form>
</aside>

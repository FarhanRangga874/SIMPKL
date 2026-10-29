<div
    x-data="{
        open: false,
        loading: false,
        kode_cpl: '',
        deskripsi_cpl: '',
        domain_kompetensi: '',
        errors: {},
        pesanError: '',

        tutupModal() {
            if (this.loading) return;

            this.open = false;
            this.errors = {};
            this.pesanError = '';
        },

        resetForm() {
            this.kode_cpl = '';
            this.deskripsi_cpl = '';
            this.domain_kompetensi = '';
            this.errors = {};
            this.pesanError = '';
        },

        async simpan() {
            if (this.loading) return;

            this.loading = true;
            this.errors = {};
            this.pesanError = '';

            try {
                const response = await fetch(
                    @js(route('kaprodi.cpls.store')),
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': @js(csrf_token()),
                        },
                        body: JSON.stringify({
                            kode_cpl: this.kode_cpl.trim(),
                            deskripsi_cpl: this.deskripsi_cpl.trim(),
                            domain_kompetensi: this.domain_kompetensi.trim(),
                        }),
                    }
                );

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors || {};
                        this.pesanError = data.message
                            || 'Periksa kembali data CPL yang dimasukkan.';
                    } else {
                        this.pesanError = data.message
                            || 'CPL gagal disimpan. Silakan coba kembali.';
                    }

                    return;
                }

                if (!data.cpl || !data.cpl.kode_cpl) {
                    this.pesanError =
                        'CPL tersimpan, tetapi data CPL tidak ditemukan pada respons server. Periksa respons controller.';
                    console.error('Respons penyimpanan CPL tidak sesuai:', data);
                    return;
                }

                const cplBaru = {
                    kode_cpl: String(data.cpl.kode_cpl),
                    deskripsi_cpl: data.cpl.deskripsi_cpl ?? '',
                    domain_kompetensi: data.cpl.domain_kompetensi ?? '',
                };

                window.dispatchEvent(
                    new CustomEvent('cpl-ditambahkan', {
                        detail: cplBaru,
                    })
                );

                this.resetForm();
                this.open = false;
            } catch (error) {
                console.error('Kesalahan saat menyimpan CPL:', error);

                this.pesanError =
                    'Tidak dapat terhubung ke server. Periksa koneksi Anda, lalu coba kembali.';
            } finally {
                this.loading = false;
            }
        },
    }"
    @buka-modal-cpl.window="
        open = true;
        errors = {};
        pesanError = '';
    "
>
    {{-- Tombol membuka modal --}}
    <button
        type="button"
        @click="open = true; errors = {}; pesanError = ''"
        class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 transition hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
    >
        <span class="text-lg leading-none">+</span>
        Tambah CPL
    </button>

    {{-- Modal --}}
    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-gray-900/50 p-4"
            @keydown.escape.window="tutupModal()"
            style="display: none;"
        >
            <div
                x-show="open"
                x-transition
                @click.outside="tutupModal()"
                @click.stop
                class="my-auto w-full max-w-lg rounded-xl bg-white p-6 shadow-xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="judul-modal-cpl"
            >
                {{-- Header --}}
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h2
                            id="judul-modal-cpl"
                            class="text-lg font-semibold text-gray-900"
                        >
                            Tambah Data CPL
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Masukkan informasi Capaian Pembelajaran Lulusan.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="tutupModal()"
                        :disabled="loading"
                        aria-label="Tutup modal"
                        class="rounded-lg p-1 text-2xl leading-none text-gray-400 transition hover:bg-gray-100 hover:text-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        &times;
                    </button>
                </div>

                {{-- Pesan error umum --}}
                <div
                    x-show="pesanError"
                    x-cloak
                    role="alert"
                    class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                >
                    <p x-text="pesanError"></p>
                </div>

                {{-- Form --}}
                <form @submit.prevent="simpan()" class="space-y-4">
                    {{-- Kode CPL --}}
                    <div>
                        <label
                            for="kode_cpl"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Kode CPL
                        </label>

                        <input
                            id="kode_cpl"
                            type="text"
                            x-model="kode_cpl"
                            required
                            maxlength="20"
                            autocomplete="off"
                            placeholder="Contoh: CPL-01"
                            :disabled="loading"
                            :class="errors.kode_cpl
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                            class="w-full rounded-lg text-sm shadow-sm disabled:bg-gray-100"
                        >

                        <p
                            x-show="errors.kode_cpl"
                            x-text="errors.kode_cpl?.[0]"
                            class="mt-1 text-sm text-red-600"
                        ></p>
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
                            id="deskripsi_cpl"
                            x-model="deskripsi_cpl"
                            required
                            rows="3"
                            :disabled="loading"
                            placeholder="Masukkan deskripsi CPL"
                            :class="errors.deskripsi_cpl
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                            class="w-full rounded-lg text-sm shadow-sm disabled:bg-gray-100"
                        ></textarea>

                        <p
                            x-show="errors.deskripsi_cpl"
                            x-text="errors.deskripsi_cpl?.[0]"
                            class="mt-1 text-sm text-red-600"
                        ></p>
                    </div>

                    {{-- Domain Kompetensi --}}
                    <div>
                        <label
                            for="domain_kompetensi"
                            class="mb-1.5 block text-sm font-medium text-gray-700"
                        >
                            Domain Kompetensi
                        </label>

                        <input
                            id="domain_kompetensi"
                            type="text"
                            x-model="domain_kompetensi"
                            required
                            autocomplete="off"
                            placeholder="Contoh: Pengetahuan"
                            :disabled="loading"
                            :class="errors.domain_kompetensi
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'"
                            class="w-full rounded-lg text-sm shadow-sm disabled:bg-gray-100"
                        >

                        <p
                            x-show="errors.domain_kompetensi"
                            x-text="errors.domain_kompetensi?.[0]"
                            class="mt-1 text-sm text-red-600"
                        ></p>
                    </div>

                    {{-- Tombol aksi --}}
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                        <button
                            type="button"
                            @click="tutupModal()"
                            :disabled="loading"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <svg
                                x-show="loading"
                                class="h-4 w-4 animate-spin"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                ></path>
                            </svg>

                            <span
                                x-text="loading ? 'Menyimpan...' : 'Simpan CPL'"
                            ></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

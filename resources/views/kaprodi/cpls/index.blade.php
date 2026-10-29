<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Pengaturan CPL
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Kelola Capaian Pembelajaran Lulusan untuk mendukung pengelolaan PKL.
            </p>
        </div>
    </x-slot>

    <div
        x-data="cplPanel({
            storeUrl: @js(route('kaprodi.cpls.store')),
            updateUrlTemplate: @js(route('kaprodi.cpls.update', ['cpl' => '__ID__'])),
            csrfToken: @js(csrf_token())
        })"
        @keydown.escape.window="if (panelOpen) closePanel()"
        @buka-panel-cpl.window="openCreate()"
    >
        <div class="py-8">
            <div class="mx-auto max-w-[1600px] space-y-6 px-4 sm:px-6 lg:px-8">

                {{-- Header halaman --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <button
                        type="button"
                        @click="openCreate()"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/>
                        </svg>
                        Tambah CPL
                    </button>
                </div>

                {{-- Kartu daftar CPL --}}
                <div class="overflow-visible rounded-xl border border-gray-200 bg-white shadow-sm">

                    {{-- Header tabel --}}
                    <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                Daftar Capaian Pembelajaran Lulusan
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Informasi kode, deskripsi, dan domain kompetensi setiap CPL.
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-lg bg-indigo-50 px-3 py-2 text-sm font-semibold text-indigo-700">
                                {{ $cpls->total() }} CPL
                            </span>
                        </div>
                    </div>

                    @if ($cpls->isNotEmpty())
                        {{-- Tabel CPL --}}
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="w-16 whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            No.
                                        </th>

                                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Kode CPL
                                        </th>

                                        <th class="min-w-72 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Deskripsi CPL
                                        </th>

                                        <th class="min-w-48 px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Domain Kompetensi
                                        </th>

                                        <th class="w-24 whitespace-nowrap px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($cpls as $cpl)
                                        @php
                                            $sedangDigunakan = $cpl->pklPeriods()->exists();
                                        @endphp

                                        <tr class="transition hover:bg-gray-50/80">
                                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">
                                                {{ $cpls->firstItem() !== null
                                                    ? $cpls->firstItem() + $loop->index
                                                    : $loop->iteration }}
                                            </td>

                                            <td class="whitespace-nowrap px-5 py-4">
                                                <span class="inline-flex rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $cpl->kode_cpl }}
                                                </span>
                                            </td>

                                            <td class="min-w-72 px-5 py-4">
                                                <p class="text-sm leading-6 text-gray-700">
                                                    {{ $cpl->deskripsi_cpl }}
                                                </p>
                                            </td>

                                            <td class="px-5 py-4">
                                                @if ($cpl->domain_kompetensi)
                                                    <span class="inline-flex rounded-md bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                                        {{ $cpl->domain_kompetensi }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-400">
                                                        Belum ditentukan
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Dropdown aksi mengambang --}}
                                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                                <div
                                                    x-data="{
                                                        open: false,
                                                        top: 0,
                                                        left: 0,
                                                        width: 224,

                                                        toggleMenu() {
                                                            if (this.open) {
                                                                this.open = false;
                                                                return;
                                                            }

                                                            this.open = true;

                                                            this.$nextTick(() => {
                                                                const trigger = this.$refs.trigger;
                                                                const menu = this.$refs.menu;

                                                                if (!trigger || !menu) return;

                                                                const rect = trigger.getBoundingClientRect();
                                                                const menuHeight = menu.offsetHeight;
                                                                const menuWidth = menu.offsetWidth;
                                                                const padding = 8;
                                                                const gap = 8;

                                                                const spaceBelow =
                                                                    window.innerHeight - rect.bottom - padding;

                                                                const spaceAbove =
                                                                    rect.top - padding;

                                                                const openUp =
                                                                    spaceBelow < menuHeight + gap &&
                                                                    spaceAbove > spaceBelow;

                                                                this.top = openUp
                                                                    ? rect.top - menuHeight - gap
                                                                    : rect.bottom + gap;

                                                                this.top = Math.max(
                                                                    padding,
                                                                    Math.min(
                                                                        this.top,
                                                                        window.innerHeight - menuHeight - padding
                                                                    )
                                                                );

                                                                this.left = Math.max(
                                                                    padding,
                                                                    Math.min(
                                                                        rect.right - menuWidth,
                                                                        window.innerWidth - menuWidth - padding
                                                                    )
                                                                );
                                                            });
                                                        },

                                                        closeMenu() {
                                                            this.open = false;
                                                        }
                                                    }"
                                                    @keydown.escape.window="closeMenu()"
                                                    @scroll.window="closeMenu()"
                                                    @resize.window="closeMenu()"
                                                    class="inline-flex"
                                                >
                                                    <button
                                                        x-ref="trigger"
                                                        type="button"
                                                        @click="toggleMenu()"
                                                        :aria-expanded="open"
                                                        aria-haspopup="true"
                                                        aria-label="Pilihan tindakan CPL {{ $cpl->kode_cpl }}"
                                                        title="Pilihan tindakan"
                                                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                    >
                                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <circle cx="5" cy="12" r="1.8"/>
                                                            <circle cx="12" cy="12" r="1.8"/>
                                                            <circle cx="19" cy="12" r="1.8"/>
                                                        </svg>
                                                    </button>

                                                    <template x-teleport="body">
                                                        <div
                                                            x-ref="menu"
                                                            x-cloak
                                                            x-show="open"
                                                            :style="`position:fixed;top:${top}px;left:${left}px;width:${width}px;max-width:calc(100vw - 16px);max-height:calc(100vh - 16px);z-index:9999;`"
                                                            @click.outside="closeMenu()"
                                                            @keydown.escape.stop="closeMenu()"
                                                            x-transition:enter="transition ease-out duration-100"
                                                            x-transition:enter-start="opacity-0 scale-95"
                                                            x-transition:enter-end="opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-75"
                                                            x-transition:leave-start="opacity-100 scale-100"
                                                            x-transition:leave-end="opacity-0 scale-95"
                                                            class="overflow-y-auto rounded-xl border border-gray-200 bg-white py-1.5 text-left shadow-xl"
                                                        >
                                                            {{-- Informasi CPL --}}
                                                            <div class="border-b border-gray-100 px-4 py-3">
                                                                <p class="text-xs font-medium uppercase tracking-wider text-gray-400">
                                                                    Informasi CPL
                                                                </p>

                                                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                                                    {{ $cpl->kode_cpl }}
                                                                </p>

                                                                @if ($sedangDigunakan)
                                                                    <span class="mt-2 inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.3 3.9 2.8 17.5A1.7 1.7 0 0 0 4.3 20h15.4a1.7 1.7 0 0 0 1.5-2.5L13.7 3.9a1.9 1.9 0 0 0-3.4 0Z"/>
                                                                        </svg>
                                                                        Sedang digunakan
                                                                    </span>
                                                                @else
                                                                    <span class="mt-2 inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                                                                        Belum digunakan
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            {{-- Edit CPL --}}
                                                            <button
                                                                type="button"
                                                                @click="
                                                                    closeMenu();

                                                                    window.dispatchEvent(
                                                                        new CustomEvent('edit-cpl', {
                                                                            detail: {
                                                                                id: @js($cpl->getKey()),
                                                                                kode_cpl: @js($cpl->kode_cpl),
                                                                                deskripsi_cpl: @js($cpl->deskripsi_cpl),
                                                                                domain_kompetensi: @js($cpl->domain_kompetensi)
                                                                            }
                                                                        })
                                                                    );
                                                                "
                                                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                                                            >
                                                                <svg class="h-4 w-4 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m16.862 4.487 2.651 2.651M7 17l4-1 9.1-9.1a1.875 1.875 0 0 0-2.65-2.65L8.35 13.35 7 17ZM5 21h14"/>
                                                                </svg>
                                                                <span>Edit CPL</span>
                                                            </button>

                                                            <div class="my-1 border-t border-gray-100"></div>

                                                            {{-- Hapus CPL --}}
                                                            @if ($sedangDigunakan)
                                                                <div
                                                                    class="flex cursor-not-allowed items-start gap-3 px-4 py-2.5 text-sm text-gray-400"
                                                                    title="CPL tidak dapat dihapus karena masih digunakan oleh periode PKL."
                                                                >
                                                                    <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M10 11v6m4-6v6M5 7l1 14h12l1-14M9 7V4h6v3"/>
                                                                    </svg>

                                                                    <span>
                                                                        <span class="block font-medium">Hapus CPL</span>
                                                                        <span class="mt-1 block whitespace-normal text-xs leading-5 text-gray-400">
                                                                            Tidak dapat dihapus karena masih digunakan.
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            @else
                                                                <form
                                                                    action="{{ route('kaprodi.cpls.destroy', ['cpl' => $cpl->getRouteKey()]) }}"
                                                                    method="POST"
                                                                    @submit="closeMenu()"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus CPL {{ $cpl->kode_cpl }}? Data yang dihapus tidak dapat dipulihkan.')"
                                                                >
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button
                                                                        type="submit"
                                                                        class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 transition hover:bg-red-50 focus:outline-none focus:bg-red-50"
                                                                    >
                                                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M10 11v6m4-6v6M5 7l1 14h12l1-14M9 7V4h6v3"/>
                                                                        </svg>
                                                                        <span>Hapus CPL</span>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </template>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if ($cpls->hasPages())
                            <div class="flex flex-col gap-3 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-sm text-gray-500">
                                    Menampilkan {{ $cpls->firstItem() ?? 0 }}–{{ $cpls->lastItem() ?? 0 }}
                                    dari {{ $cpls->total() }} CPL
                                </p>

                                {{ $cpls->links() }}
                            </div>
                        @endif
                    @else
                        {{-- Data kosong --}}
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v13H4V6a1 1 0 0 1 1-1Z"/>
                                </svg>
                            </div>

                            <h3 class="mt-4 text-base font-semibold text-gray-900">
                                Belum ada data CPL
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                                Tambahkan Capaian Pembelajaran Lulusan pertama agar dapat dipilih ketika membuat periode PKL.
                            </p>

                            <button
                                type="button"
                                @click="openCreate()"
                                class="mt-5 inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/>
                                </svg>
                                Tambah CPL Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Panel tambah dan edit --}}
        @include('kaprodi.cpls.form-panel')
    </div>

    <script>
        window.cplPanel = function (config) {
            return {
                panelOpen: false,
                saving: false,
                isEdit: false,
                editingId: null,

                message: '',
                successMessage: '',
                errors: {},

                storeUrl: config.storeUrl,
                updateUrlTemplate: config.updateUrlTemplate,
                csrfToken: config.csrfToken,

                form: {
                    kode_cpl: '',
                    deskripsi_cpl: '',
                    domain_kompetensi: '',
                },

                emptyForm() {
                    return {
                        kode_cpl: '',
                        deskripsi_cpl: '',
                        domain_kompetensi: '',
                    };
                },

                openCreate() {
                    if (this.saving) return;

                    this.isEdit = false;
                    this.editingId = null;
                    this.form = this.emptyForm();

                    this.message = '';
                    this.successMessage = '';
                    this.errors = {};

                    this.panelOpen = true;
                },

                openEdit(cpl) {
                    if (this.saving || !cpl || !cpl.id) {
                        console.error('Data CPL tidak valid:', cpl);
                        return;
                    }

                    this.isEdit = true;
                    this.editingId = cpl.id;

                    this.form = {
                        kode_cpl: cpl.kode_cpl ?? '',
                        deskripsi_cpl: cpl.deskripsi_cpl ?? '',
                        domain_kompetensi: cpl.domain_kompetensi ?? '',
                    };

                    this.message = '';
                    this.successMessage = '';
                    this.errors = {};

                    this.panelOpen = true;
                },

                closePanel() {
                    if (this.saving) return;

                    this.panelOpen = false;
                    this.message = '';
                    this.successMessage = '';
                    this.errors = {};
                },

                async submitForm() {
                    if (this.saving) return;

                    this.saving = true;
                    this.message = '';
                    this.successMessage = '';
                    this.errors = {};

                    const editing = this.isEdit;

                    const url = editing
                        ? this.updateUrlTemplate.replace(
                            '__ID__',
                            encodeURIComponent(this.editingId)
                        )
                        : this.storeUrl;

                    try {
                        const response = await fetch(url, {
                            method: editing ? 'PUT' : 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify(this.form),
                        });

                        const data = await response.json().catch(() => ({}));

                        if (response.status === 422) {
                            this.errors = data.errors || {};
                            this.message = data.message ||
                                'Periksa kembali data yang Anda masukkan.';
                            return;
                        }

                        if (response.status === 419) {
                            this.message =
                                'Sesi telah berakhir. Muat ulang halaman lalu coba kembali.';
                            return;
                        }

                        if (!response.ok) {
                            this.message = data.message ||
                                (response.status === 404
                                    ? 'Data CPL tidak ditemukan.'
                                    : 'CPL gagal disimpan. Silakan coba kembali.');
                            return;
                        }

                        this.successMessage = data.message ||
                            (editing
                                ? 'Data CPL berhasil diperbarui.'
                                : 'Data CPL berhasil ditambahkan.');

                        this.panelOpen = false;
                        window.location.reload();
                    } catch (error) {
                        console.error('Gagal menyimpan CPL:', error);
                        this.message =
                            'Terjadi masalah koneksi. Silakan coba kembali.';
                    } finally {
                        this.saving = false;
                    }
                },
            };
        };

        // Menangani event edit dari dropdown yang di-teleport ke body.
        window.addEventListener('edit-cpl', (event) => {
            const root = document.querySelector('[x-data^="cplPanel"]');

            if (!root || !window.Alpine) return;

            const panel = window.Alpine.$data(root);
            panel.openEdit(event.detail);
        });
    </script>
</x-app-layout>

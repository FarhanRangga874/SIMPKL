
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Capaian Pembelajaran Lulusan (CPL)
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Kelola data CPL dan domain kompetensi program studi.
            </p>
        </div>
    </x-slot>

    <div
        x-data="cplPanel({
            storeUrl: @js(route('kaprodi.cpls.store')),
            updateUrlTemplate: @js(route('kaprodi.cpls.update', ['cpl' => '__ID__'])),
            deleteUrlTemplate: @js(route('kaprodi.cpls.destroy', ['cpl' => '__ID__'])),
            csrfToken: @js(csrf_token())
        })"
        @edit-cpl.window="openEdit($event.detail)"
        @keydown.escape.window="if (panelOpen) closePanel()"
    >
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                    {{-- Header --}}
                    <div class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                Daftar CPL
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Informasi kode, deskripsi, dan domain kompetensi CPL.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="openCreate()"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 5v14m-7-7h14"/>
                            </svg>
                            Tambah CPL
                        </button>
                    </div>

                    @if ($cpls->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            No.
                                        </th>
                                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Kode CPL
                                        </th>
                                        <th class="min-w-72 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Deskripsi CPL
                                        </th>
                                        <th class="min-w-48 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Domain Kompetensi
                                        </th>
                                        <th class="whitespace-nowrap px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($cpls as $cpl)
                                        <tr class="align-top transition hover:bg-gray-50">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ $cpls instanceof \Illuminate\Pagination\AbstractPaginator
                                                    ? $cpls->firstItem() + $loop->index
                                                    : $loop->iteration }}
                                            </td>

                                            <td class="whitespace-nowrap px-6 py-4">
                                                <span class="inline-flex rounded-md bg-indigo-50 px-2.5 py-1.5 text-sm font-semibold text-indigo-700">
                                                    {{ $cpl->kode_cpl }}
                                                </span>
                                            </td>

                                            <td class="px-6 py-4 text-sm leading-6 text-gray-700">
                                                {{ $cpl->deskripsi_cpl }}
                                            </td>

                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $cpl->domain_kompetensi ?: 'Belum ditentukan' }}
                                            </td>

                                            {{-- Dropdown aksi --}}
                                            <td class="px-6 py-4 text-right">
                                                <div
                                                    x-data="{
                                                        open: false,
                                                        top: 0,
                                                        left: 0,
                                                        width: 192,
                                                        menuHeight: 100,

                                                        toggleMenu() {
                                                            if (this.open) {
                                                                this.open = false;
                                                                return;
                                                            }

                                                            const rect = this.$refs.trigger.getBoundingClientRect();
                                                            const viewportHeight = window.innerHeight;
                                                            const viewportWidth = window.innerWidth;

                                                            this.width = 192;

                                                            const estimatedHeight = this.menuHeight;
                                                            const spaceBelow = viewportHeight - rect.bottom;
                                                            const spaceAbove = rect.top;

                                                            this.top = (
                                                                spaceBelow >= estimatedHeight + 8 ||
                                                                spaceBelow >= spaceAbove
                                                            )
                                                                ? rect.bottom + 8
                                                                : Math.max(8, rect.top - estimatedHeight - 8);

                                                            this.left = Math.max(
                                                                8,
                                                                Math.min(
                                                                    rect.right - this.width,
                                                                    viewportWidth - this.width - 8
                                                                )
                                                            );

                                                            this.open = true;

                                                            this.$nextTick(() => {
                                                                const menu = this.$refs.menu;

                                                                if (!menu) return;

                                                                const menuRect = menu.getBoundingClientRect();

                                                                if (menuRect.bottom > window.innerHeight - 8) {
                                                                    this.top = Math.max(
                                                                        8,
                                                                        rect.top - menuRect.height - 8
                                                                    );
                                                                }

                                                                if (menuRect.right > window.innerWidth - 8) {
                                                                    this.left = Math.max(
                                                                        8,
                                                                        window.innerWidth - menuRect.width - 8
                                                                    );
                                                                }
                                                            });
                                                        }
                                                    }"
                                                    @keydown.escape.window="open = false"
                                                    @click.outside="open = false"
                                                    @scroll.window="open = false"
                                                    class="inline-block"
                                                >
                                                    <button
                                                        x-ref="trigger"
                                                        type="button"
                                                        @click="toggleMenu()"
                                                        :aria-expanded="open"
                                                        aria-haspopup="true"
                                                        aria-label="Buka menu aksi CPL"
                                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-transparent text-gray-500 transition hover:border-gray-200 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                    >
                                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                            <circle cx="5" cy="12" r="1.8"/>
                                                            <circle cx="12" cy="12" r="1.8"/>
                                                            <circle cx="19" cy="12" r="1.8"/>
                                                        </svg>
                                                    </button>

                                                    <template x-teleport="body">
                                                        <div
                                                            x-cloak
                                                            x-show="open"
                                                            x-ref="menu"
                                                            :style="`position:fixed;top:${top}px;left:${left}px;width:${width}px;z-index:9999;`"
                                                            @click.outside="open = false"
                                                            x-transition:enter="transition ease-out duration-150"
                                                            x-transition:enter-start="opacity-0 scale-95"
                                                            x-transition:enter-end="opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-100"
                                                            x-transition:leave-start="opacity-100 scale-100"
                                                            x-transition:leave-end="opacity-0 scale-95"
                                                            class="origin-top-right overflow-hidden rounded-xl border border-gray-200 bg-white py-1.5 text-left shadow-xl ring-1 ring-black/5"
                                                        >
                                                            {{-- Edit --}}
                                                            <button
                                                                type="button"
                                                                @click="
                                                                    open = false;
                                                                    $dispatch('edit-cpl', {
                                                                        id: @js($cpl->getKey()),
                                                                        kode_cpl: @js($cpl->kode_cpl),
                                                                        deskripsi_cpl: @js($cpl->deskripsi_cpl),
                                                                        domain_kompetensi: @js($cpl->domain_kompetensi)
                                                                    });
                                                                "
                                                                class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-gray-700 transition hover:bg-indigo-50 hover:text-indigo-700 focus:bg-indigo-50 focus:text-indigo-700 focus:outline-none"
                                                            >
                                                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                                                        d="M16.862 4.487l2.651 2.651M5 19l4.5-1 9.6-9.6a1.875 1.875 0 00-2.65-2.65l-9.6 9.6L5 19z"/>
                                                                </svg>
                                                                <span>Edit CPL</span>
                                                            </button>

                                                            {{-- Hapus: tampilan saja, belum terhubung ke backend --}}
                                                            <div class="my-1 border-t border-gray-100"></div>
                                                                <button
                                                                    type="button"
                                                                    @click="
                                                                        open = false;
                                                                        confirmDelete(
                                                                            @js($cpl->getKey()),
                                                                            @js($cpl->kode_cpl)
                                                                        );
                                                                    "
                                                                    :disabled="deleting"
                                                                    class="flex w-full items-center gap-3 px-4 py-2.5
                                                                        text-left text-sm font-medium text-red-600
                                                                        transition hover:bg-red-50
                                                                        focus:bg-red-50 focus:outline-none
                                                                        disabled:cursor-not-allowed disabled:opacity-50"
                                                                >
                                                                    <svg
                                                                        class="h-4 w-4 shrink-0"
                                                                        fill="none"
                                                                        stroke="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                        aria-hidden="true"
                                                                    >
                                                                        <path
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"
                                                                            stroke-width="1.8"
                                                                            d="M6 7h12m-10 0V5h8v2m-7 0v12m6-12v12M5 7l1 14h12l1-14"
                                                                        />
                                                                    </svg>

                                                                    <span x-text="deleting ? 'Menghapus...' : 'Hapus CPL'"></span>
                                                                </button>
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
                        @if ($cpls instanceof \Illuminate\Pagination\AbstractPaginator)
                            <div class="flex flex-col gap-3 border-t border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-sm text-gray-500">
                                    Menampilkan {{ $cpls->firstItem() ?? 0 }}
                                    sampai {{ $cpls->lastItem() ?? 0 }}
                                    dari {{ $cpls->total() }} CPL
                                </p>

                                @if ($cpls->hasPages())
                                    {{ $cpls->links() }}
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7"
                                        d="M12 5v14m-7-7h14"/>
                                </svg>
                            </div>

                            <h3 class="mt-4 text-base font-semibold text-gray-900">
                                Belum ada data CPL
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                                Tambahkan CPL pertama untuk mulai mengelola kode,
                                deskripsi, dan domain kompetensi program studi.
                            </p>

                            <button
                                type="button"
                                @click="openCreate()"
                                class="mt-5 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
                            >
                                Tambah CPL Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Panel form --}}
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
                    domain_kompetensi: ''
                },

                emptyForm() {
                    return {
                        kode_cpl: '',
                        deskripsi_cpl: '',
                        domain_kompetensi: ''
                    };
                },

                openCreate() {
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
                        domain_kompetensi: cpl.domain_kompetensi ?? ''
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
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const result = await response.json();

                        if (!response.ok) {
                            if (response.status === 422 && result.errors) {
                                this.errors = result.errors;
                                this.message = result.message
                                    ?? 'Periksa kembali data yang dimasukkan.';
                            } else {
                                this.message = result.message
                                    ?? 'Terjadi kesalahan saat menyimpan data CPL.';
                            }

                            return;
                        }

                        this.successMessage = result.message
                            ?? 'Data CPL berhasil disimpan.';

                        this.$dispatch(
                            'cpl-ditambahkan',
                            result.cpl ?? this.form
                        );

                        window.location.reload();
                    } catch (error) {
                        console.error('Gagal menyimpan CPL:', error);
                        this.message = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                    } finally {
                        this.saving = false;
                    }
                }
            };
        };
    </script>
</x-app-layout>

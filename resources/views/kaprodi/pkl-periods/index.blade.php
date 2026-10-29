
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Periode Pendaftaran PKL
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Kelola periode akademik, CPL, dan batch pendaftaran PKL.
            </p>
        </div>
    </x-slot>

    {{-- Kontainer utama Alpine --}}
    <div
        x-data="periodPanel({
            createUrl: @js(route('kaprodi.pkl-periods.store')),
            updateUrlTemplate: @js(route('kaprodi.pkl-periods.update', ['pklPeriod' => '__PERIOD_ID__'])),
            defaults: @js($defaults),
            cpls: @js($cpls->map(fn ($cpl) => [
                'kode_cpl' => (string) $cpl->kode_cpl,
                'deskripsi_cpl' => $cpl->deskripsi_cpl,
                'domain_kompetensi' => $cpl->domain_kompetensi,
            ])->values()),
            periods: @js($periods->getCollection()->map(fn ($period) => [
                'id' => $period->getKey(),
                'semester' => $period->semester,
                'tahun_akademik' => $period->tahun_akademik,
                'status' => $period->status,
                'cpls' => $period->cpls->pluck('kode_cpl')
                    ->map(fn ($code) => (string) $code)
                    ->values(),
            ])->values())
        })"
        @keydown.escape.window="if (panelOpen) closePanel()"
        @cpl-ditambahkan.window="
            const data = $event.detail;

            if (!data || !data.kode_cpl) {
                console.error('Data CPL tidak valid:', data);
                return;
            }

            const cplBaru = {
                kode_cpl: String(data.kode_cpl),
                deskripsi_cpl: data.deskripsi_cpl ?? '',
                domain_kompetensi: data.domain_kompetensi ?? ''
            };

            const index = cpls.findIndex(
                item => String(item.kode_cpl) === cplBaru.kode_cpl
            );

            if (index === -1) {
                cpls.push(cplBaru);
            } else {
                cpls.splice(index, 1, cplBaru);
            }
        "
    >
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">


                {{-- Kartu tabel --}}
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                    {{-- Header tabel --}}
                    <div class="flex flex-col gap-4 border-b border-gray-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-900">
                                Daftar Periode PKL
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Informasi periode akademik, CPL, batch, dan status pengelolaan.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="openCreate()"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 5v14m-7-7h14"
                                />
                            </svg>
                            Tambah Periode
                        </button>
                    </div>

                    @if ($periods->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            No.
                                        </th>
                                        <th class="min-w-52 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Periode Akademik
                                        </th>
                                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Semester
                                        </th>
                                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Tahun Akademik
                                        </th>
                                        <th class="min-w-56 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            CPL Digunakan
                                        </th>
                                        <th class="whitespace-nowrap px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Batch
                                        </th>
                                        <th class="whitespace-nowrap px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Status
                                        </th>
                                        <th class="whitespace-nowrap px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($periods as $period)
                                        <tr class="align-top transition hover:bg-gray-50">

                                            {{-- Nomor --}}
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ $periods->firstItem() + $loop->index }}
                                            </td>

                                            {{-- Periode Akademik --}}
                                            <td class="px-6 py-4">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $period->semester }} {{ $period->tahun_akademik }}
                                                </p>
                                                <p class="mt-1 text-xs text-gray-400">
                                                    Dibuat {{ $period->created_at?->format('d M Y') ?? '-' }}
                                                </p>
                                            </td>

                                            {{-- Semester --}}
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                                {{ $period->semester }}
                                            </td>

                                            {{-- Tahun Akademik --}}
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">
                                                {{ $period->tahun_akademik }}
                                            </td>

                                            {{-- CPL --}}
                                            <td class="px-6 py-4">
                                                <details class="group">
                                                    <summary class="flex cursor-pointer list-none items-center gap-2">
                                                        <span class="rounded-md bg-indigo-50 px-2.5 py-1.5 text-sm font-semibold text-indigo-700">
                                                            {{ $period->cpls->count() }} CPL
                                                        </span>

                                                        <span class="text-sm font-medium text-indigo-600 group-open:hidden">
                                                            Lihat
                                                        </span>

                                                        <span class="hidden text-sm font-medium text-indigo-600 group-open:inline">
                                                            Tutup
                                                        </span>

                                                        <svg
                                                            class="h-4 w-4 text-gray-400 transition group-open:rotate-180"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="m19 9-7 7-7-7"
                                                            />
                                                        </svg>
                                                    </summary>

                                                    <div class="mt-3 min-w-64 rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
                                                        @forelse ($period->cpls as $cpl)
                                                            <div class="border-b border-gray-100 py-2 last:border-0">
                                                                <p class="text-sm font-semibold text-gray-800">
                                                                    {{ $cpl->kode_cpl }}
                                                                </p>
                                                                <p class="mt-1 text-sm leading-5 text-gray-600">
                                                                    {{ $cpl->deskripsi_cpl }}
                                                                </p>
                                                                <p class="mt-1 text-xs text-indigo-600">
                                                                    {{ $cpl->domain_kompetensi ?: 'Domain tidak dicantumkan' }}
                                                                </p>
                                                            </div>
                                                        @empty
                                                            <p class="text-sm text-gray-500">
                                                                Belum ada CPL yang ditetapkan untuk periode ini.
                                                            </p>
                                                        @endforelse
                                                    </div>
                                                </details>
                                            </td>

                                            {{-- Batch --}}
                                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                                <span class="inline-flex min-w-8 items-center justify-center rounded-md bg-gray-100 px-2.5 py-1.5 text-sm font-semibold text-gray-700">
                                                    {{ $period->batches->count() }}
                                                </span>
                                            </td>

                                            {{-- Status --}}
                                            <td class="whitespace-nowrap px-6 py-4">
                                                @if ($period->status === 'aktif')
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                        Aktif
                                                    </span>
                                                @elseif ($period->status === 'selesai')
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                        Selesai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                        Draft
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Menu aksi tiga titik --}}
                                            <td class="px-6 py-4 text-right">
                                                <div
                                                    x-data="{
                                                        open: false,
                                                        top: 0,
                                                        left: 0,
                                                        width: 208,
                                                        menuHeight: 260,

                                                        toggleMenu($refs) {
                                                            if (this.open) {
                                                                this.open = false;
                                                                return;
                                                            }

                                                            const rect = $refs.trigger.getBoundingClientRect();
                                                            const vh = window.innerHeight;
                                                            const vw = window.innerWidth;
                                                            const below = vh - rect.bottom;
                                                            const above = rect.top;

                                                            this.top = below >= this.menuHeight || below >= above
                                                                ? rect.bottom + 8
                                                                : Math.max(8, rect.top - this.menuHeight - 8);

                                                            this.left = Math.max(
                                                                8,
                                                                Math.min(rect.right - this.width, vw - this.width - 8)
                                                            );

                                                            this.open = true;

                                                            this.$nextTick(() => {
                                                                const menu = document.getElementById(this.$id('period-menu'));

                                                                if (menu) {
                                                                    const height = menu.getBoundingClientRect().height;

                                                                    if (this.top + height > window.innerHeight - 8) {
                                                                        this.top = Math.max(8, rect.top - height - 8);
                                                                    }
                                                                }
                                                            });
                                                        }
                                                    }"
                                                    x-id="['period-menu']"
                                                    @keydown.escape.window="open = false"
                                                    @click.outside="open = false"
                                                    @scroll.window="open = false"
                                                    class="inline-block"
                                                >
                                                    <button
                                                        x-ref="trigger"
                                                        type="button"
                                                        @click="toggleMenu($refs)"
                                                        :aria-expanded="open"
                                                        aria-label="Buka menu aksi periode"
                                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-transparent text-gray-500 transition hover:border-gray-200 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                    >
                                                        <svg
                                                            class="h-5 w-5"
                                                            fill="currentColor"
                                                            viewBox="0 0 24 24"
                                                            aria-hidden="true"
                                                        >
                                                            <circle cx="5" cy="12" r="1.8" />
                                                            <circle cx="12" cy="12" r="1.8" />
                                                            <circle cx="19" cy="12" r="1.8" />
                                                        </svg>
                                                    </button>

                                                    {{-- Menu dipindahkan ke body agar tidak terpotong tabel --}}
                                                    <template x-teleport="body">
                                                        <div
                                                            x-cloak
                                                            x-show="open"
                                                            :id="$id('period-menu')"
                                                            :style="`position:fixed;top:${top}px;left:${left}px;width:${width}px;z-index:9999;`"
                                                            @click.outside="open = false"
                                                            x-transition:enter="transition ease-out duration-100"
                                                            x-transition:enter-start="opacity-0 scale-95"
                                                            x-transition:enter-end="opacity-100 scale-100"
                                                            x-transition:leave="transition ease-in duration-75"
                                                            x-transition:leave-start="opacity-100 scale-100"
                                                            x-transition:leave-end="opacity-0 scale-95"
                                                            class="max-h-[calc(100vh-16px)] origin-top-right overflow-y-auto rounded-xl border border-gray-200 bg-white py-1.5 text-left shadow-xl ring-1 ring-black/5"
                                                        >
                                                            <div class="border-b border-gray-100 px-4 py-2.5">
                                                                <p class="text-xs font-medium text-gray-400">
                                                                    STATUS PERIODE
                                                                </p>
                                                                <p class="mt-1 text-sm font-semibold text-gray-800">
                                                                    {{ ucfirst($period->status) }}
                                                                </p>
                                                            </div>

                                                            {{-- Aktifkan --}}
                                                            @if ($period->status === 'draft')
                                                                <form
                                                                    action="{{ route('kaprodi.pkl-periods.activate', $period) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan periode ini? Jika periode lain masih aktif, periode tersebut akan otomatis diakhiri dan diubah menjadi Selesai.')"
                                                                >
                                                                    @csrf
                                                                    <button
                                                                        type="submit"
                                                                        class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-gray-700 transition hover:bg-indigo-50 hover:text-indigo-700"
                                                                    >
                                                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12l4 4L19 6" />
                                                                        </svg>
                                                                        Aktifkan Periode
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            {{-- Akhiri --}}
                                                            @if ($period->status === 'aktif')
                                                                <form
                                                                    action="{{ route('kaprodi.pkl-periods.finish', $period) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin mengakhiri periode ini? Status periode akan berubah menjadi Selesai.')"
                                                                >
                                                                    @csrf
                                                                    <button
                                                                        type="submit"
                                                                        class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-amber-700 transition hover:bg-amber-50"
                                                                    >
                                                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l2.5 2.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                        </svg>
                                                                        Akhiri Periode
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            {{-- Edit --}}
                                                            <button
                                                                type="button"
                                                                @click="open = false; $dispatch('edit-period', { id: @js($period->getKey()) })"
                                                                class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-gray-700 transition hover:bg-gray-100"
                                                            >
                                                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m16.862 4.487 2.651 2.651M7 17l4-1 9.1-9.1a1.875 1.875 0 0 0-2.65-2.65L8.35 13.35 7 17ZM5 21h14" />
                                                                </svg>
                                                                Edit Periode
                                                            </button>

                                                            {{-- Hapus --}}
                                                            @if ($period->status !== 'aktif' && $period->batches->isEmpty())
                                                                <div class="my-1 border-t border-gray-100"></div>

                                                                <form
                                                                    action="{{ route('kaprodi.pkl-periods.destroy', $period) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode ini? Data yang dihapus tidak dapat dipulihkan.')"
                                                                >
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button
                                                                        type="submit"
                                                                        class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm text-red-600 transition hover:bg-red-50"
                                                                    >
                                                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M10 11v6m4-6v6M5 7l1 14h12l1-14M9 7V4h6v3" />
                                                                        </svg>
                                                                        Hapus Periode
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
                        <div class="flex flex-col gap-3 border-t border-gray-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-gray-500">
                                Menampilkan {{ $periods->firstItem() ?? 0 }}
                                sampai {{ $periods->lastItem() ?? 0 }}
                                dari {{ $periods->total() }} periode
                            </p>

                            @if ($periods->hasPages())
                                {{ $periods->links() }}
                            @endif
                        </div>
                    @else
                        {{-- Data kosong --}}
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v13H4V6a1 1 0 0 1 1-1Z" />
                                </svg>
                            </div>

                            <h3 class="mt-4 text-base font-semibold text-gray-900">
                                Belum ada periode PKL
                            </h3>

                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                                Tambahkan periode PKL pertama untuk mulai mengatur
                                tahun akademik, CPL, dan batch pendaftaran.
                            </p>

                            <button
                                type="button"
                                @click="openCreate()"
                                class="mt-5 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14" />
                                </svg>
                                Tambah Periode Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Panel form: tetap berada di dalam x-data yang sama --}}
        @include('kaprodi.pkl-periods.form-panel')
    </div>

    {{-- Alpine component --}}
    <script>
        window.periodPanel = function (config) {
            return {
                panelOpen: false,
                panelMode: 'create',

                createUrl: config.createUrl,
                updateUrlTemplate: config.updateUrlTemplate,
                defaults: config.defaults,
                cpls: config.cpls ?? [],
                periods: config.periods ?? [],

                form: {
                    id: null,
                    semester: '',
                    tahun_akademik: '',
                    cpls: [],
                },

                get formAction() {
                    if (this.panelMode === 'edit' && this.form.id !== null) {
                        return this.updateUrlTemplate.replace(
                            '__PERIOD_ID__',
                            encodeURIComponent(this.form.id)
                        );
                    }

                    return this.createUrl;
                },

                openCreate() {
                    this.panelMode = 'create';

                    this.form = {
                        id: null,
                        semester: this.defaults.semester,
                        tahun_akademik: this.defaults.tahun_akademik,
                        cpls: [],
                    };

                    this.panelOpen = true;
                },

                openEdit(id) {
                    const period = this.periods.find(
                        item => String(item.id) === String(id)
                    );

                    if (!period) {
                        console.error('Periode tidak ditemukan:', id);
                        return;
                    }

                    this.panelMode = 'edit';

                    this.form = {
                        id: period.id,
                        semester: period.semester,
                        tahun_akademik: period.tahun_akademik,
                        cpls: (period.cpls ?? []).map(String),
                    };

                    this.panelOpen = true;
                },

                closePanel() {
                    this.panelOpen = false;
                },

                selectAllCpls() {
                    if (this.form.cpls.length === this.cpls.length) {
                        this.form.cpls = [];
                    } else {
                        this.form.cpls = this.cpls.map(
                            cpl => String(cpl.kode_cpl)
                        );
                    }
                },
            };
        };
    </script>
</x-app-layout>

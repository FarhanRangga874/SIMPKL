
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

    <div
        x-data="periodPanel({
            createUrl: @js(route('kaprodi.pkl-periods.store')),
            updateUrlTemplate: @js(route('kaprodi.pkl-periods.update', ['pklPeriod' => '__PERIOD_ID__'])),
            accUrlTemplate: @js(route('kaprodi.pkl-periods.acc', ['pklPeriod' => '__PERIOD_ID__'])),
            batchCreateUrl: @js(route('kaprodi.pkl-batches.store')),
            batchUpdateUrlTemplate: @js(route('kaprodi.pkl-batches.update', ['pklBatch' => '__BATCH_ID__'])),

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
                'created_at' => $period->created_at?->format('d M Y'),

                'cpls' => $period->cpls->map(fn ($cpl) => [
                    'kode_cpl' => (string) $cpl->kode_cpl,
                    'deskripsi_cpl' => $cpl->deskripsi_cpl,
                    'domain_kompetensi' => $cpl->domain_kompetensi,
                ])->values(),

                'batches' => $period->batches->map(fn ($batch) => [
                    'id' => $batch->getKey(),
                    'nama_batch' => $batch->nama_batch,
                    'tanggal_mulai' => $batch->tanggal_mulai?->format('d M Y'),
                    'tanggal_selesai' => $batch->tanggal_selesai?->format('d M Y'),
                    'tanggal_mulai_input' => $batch->tanggal_mulai?->format('Y-m-d'),
                    'tanggal_selesai_input' => $batch->tanggal_selesai?->format('Y-m-d'),
                    'status' => $batch->status,
                ])->values(),
            ])->values()),
        })"
        @keydown.escape.window="
            if (editBatchPanelOpen) closeEditBatch();
            else if (batchPanelOpen) closeBatch();
            else if (panelOpen) closePanel();
        "
        @edit-period.window="openEdit($event.detail.id)"
        @buat-batch.window="openBatch($event.detail.periodId)"
        @edit-batch.window="openEditBatch($event.detail.batchId, $event.detail.periodId)"
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

            if (index === -1) cpls.push(cplBaru);
            else cpls.splice(index, 1, cplBaru);
        "
    >
        <div class="py-8">
            <div class="mx-auto max-w-[1600px] space-y-6 px-4 sm:px-6 lg:px-8">

                {{-- Header halaman --}}
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="mb-2 flex items-center gap-2 text-xs font-medium text-gray-500">
                            <span>Pengelolaan PKL</span>
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
                            </svg>
                            <span class="text-indigo-600">Periode Pendaftaran</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                                Periode Pendaftaran PKL
                            </h1>
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                {{ $periods->total() }} periode
                            </span>
                        </div>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500">
                            Kelola periode akademik, keterkaitan CPL, dan jadwal batch pendaftaran PKL dalam satu halaman.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="openCreate()"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/>
                        </svg>
                        Tambah Periode
                    </button>
                </div>

                {{-- Tabel bertingkat --}}
                <div
                    class="relative rounded-xl border border-gray-200 bg-white shadow-sm"
                    x-data="{
                        expandedPeriods: {},
                        search: '',
                        statusFilter: 'semua',

                        get filteredPeriods() {
                            return this.periods.filter(period => {
                                const nama = `${period.semester} ${period.tahun_akademik}`.toLowerCase();
                                const cocokNama = nama.includes(this.search.toLowerCase().trim());

                                const cocokStatus = this.statusFilter === 'semua'
                                    || period.status === this.statusFilter;

                                return cocokNama && cocokStatus;
                            });
                        },

                        togglePeriod(id) {
                            this.expandedPeriods[id] = !this.expandedPeriods[id];
                        },

                        isExpanded(id) {
                            return Boolean(this.expandedPeriods[id]);
                        },

                        collapseAll() {
                            this.expandedPeriods = {};
                        }
                    }"
                >
                    {{-- Header kartu --}}
                    <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                Daftar Periode PKL
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Pilih periode untuk melihat CPL dan batch pendaftarannya.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="collapseAll()"
                            class="inline-flex items-center justify-center gap-2 self-start rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 transition hover:border-gray-300 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 lg:self-auto"
                            title="Tutup seluruh detail periode"
                            aria-label="Tutup semua periode"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8h14M5 12h14M5 16h14"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 5-4 3 4 3"/>
                            </svg>
                            Tutup Semua
                        </button>
                    </div>

                    {{-- Pencarian dan filter --}}
                    <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50/60 p-4 sm:flex-row">
                        <div class="relative flex-1">
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                                <path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/>
                            </svg>

                            <input
                                type="search"
                                x-model="search"
                                placeholder="Cari semester atau tahun akademik..."
                                aria-label="Cari periode PKL"
                                class="w-full rounded-lg border-gray-200 bg-white py-2.5 pl-9 pr-3 text-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <select
                            x-model="statusFilter"
                            aria-label="Filter status periode"
                            class="rounded-lg border-gray-200 bg-white py-2.5 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500 sm:w-48"
                        >
                            <option value="semua">Semua status</option>
                            <option value="draft">Draft</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>

                    {{-- Tabel periode --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="w-12 px-4 py-3" scope="col"></th>
                                    <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500" scope="col">Periode Akademik</th>
                                    <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500" scope="col">CPL</th>
                                    <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500" scope="col">Jumlah Batch</th>
                                    <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500" scope="col">Dibuat</th>
                                    <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500" scope="col">Status</th>
                                    <th class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500" scope="col">Aksi</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="period in filteredPeriods" :key="period.id">
                                    <tr>
                                        <td colspan="7" class="p-0">
                                            <table class="min-w-full">
                                                <tbody>
                                                    {{-- Baris utama periode --}}
                                                    <tr :class="isExpanded(period.id) ? 'bg-indigo-50/40' : 'hover:bg-gray-50/80'">
                                                        <td class="w-12 px-4 py-4">
                                                            <button
                                                                type="button"
                                                                @click="togglePeriod(period.id)"
                                                                :aria-expanded="isExpanded(period.id)"
                                                                :aria-label="isExpanded(period.id) ? 'Tutup detail periode' : 'Buka detail periode'"
                                                                class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition hover:bg-gray-200 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                            >
                                                                <svg
                                                                    class="h-4 w-4 transition-transform duration-200"
                                                                    :class="isExpanded(period.id) ? 'rotate-90' : ''"
                                                                    fill="none"
                                                                    stroke="currentColor"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6"/>
                                                                </svg>
                                                            </button>
                                                        </td>

                                                        <td class="px-4 py-4">
                                                            <button type="button" @click="togglePeriod(period.id)" class="text-left">
                                                                <p class="text-sm font-semibold text-gray-900 transition hover:text-indigo-700"
                                                                   x-text="`${period.semester} ${period.tahun_akademik}`"></p>
                                                                <p class="mt-1 text-xs text-gray-500">Klik untuk melihat detail</p>
                                                            </button>
                                                        </td>

                                                        <td class="px-4 py-4">
                                                            <span class="inline-flex rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700"
                                                                  x-text="`${period.cpls.length} CPL`"></span>
                                                        </td>

                                                        <td class="px-4 py-4">
                                                            <span class="inline-flex rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700"
                                                                  x-text="`${period.batches.length} batch`"></span>
                                                        </td>

                                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500"
                                                            x-text="period.created_at || '-'"></td>

                                                        <td class="whitespace-nowrap px-4 py-4">
                                                            <span
                                                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                                                :class="{
                                                                    'bg-green-50 text-green-700': period.status === 'aktif',
                                                                    'bg-amber-50 text-amber-700': period.status === 'draft',
                                                                    'bg-gray-100 text-gray-600': period.status === 'selesai'
                                                                }"
                                                                x-text="period.status.charAt(0).toUpperCase() + period.status.slice(1)"
                                                            ></span>
                                                        </td>

                                                        {{-- Aksi periode --}}
                                                        <td class="whitespace-nowrap px-4 py-4 text-right">
                                                            <div class="flex items-center justify-end">
                                                                <div
                                                                    x-data="{
                                                                        actionOpen: false,
                                                                        menuStyle: '',

                                                                        toggleMenu() {
                                                                            if (this.actionOpen) {
                                                                                this.closeMenu();
                                                                                return;
                                                                            }

                                                                            this.actionOpen = true;

                                                                            this.$nextTick(() => {
                                                                                requestAnimationFrame(() => {
                                                                                    const trigger = this.$refs.trigger;
                                                                                    const menu = this.$refs.menu;

                                                                                    if (!trigger || !menu) return;

                                                                                    const rect = trigger.getBoundingClientRect();
                                                                                    const padding = 8;
                                                                                    const gap = 6;
                                                                                    const menuRect = menu.getBoundingClientRect();

                                                                                    const menuHeight = menuRect.height;
                                                                                    const menuWidth = menuRect.width;

                                                                                    const spaceBelow = window.innerHeight - rect.bottom - padding;
                                                                                    const spaceAbove = rect.top - padding;

                                                                                    const openUp =
                                                                                        spaceBelow < menuHeight + gap &&
                                                                                        spaceAbove > spaceBelow;

                                                                                    let top = openUp
                                                                                        ? rect.top - menuHeight - gap
                                                                                        : rect.bottom + gap;

                                                                                    let left = rect.right - menuWidth;

                                                                                    top = Math.max(
                                                                                        padding,
                                                                                        Math.min(top, window.innerHeight - menuHeight - padding)
                                                                                    );

                                                                                    left = Math.max(
                                                                                        padding,
                                                                                        Math.min(left, window.innerWidth - menuWidth - padding)
                                                                                    );

                                                                                    this.menuStyle =
                                                                                        `position:fixed;top:${top}px;left:${left}px;` +
                                                                                        `width:${menuWidth}px;max-height:${window.innerHeight - 16}px;z-index:99999;`;
                                                                                });
                                                                            });
                                                                        },

                                                                        closeMenu() {
                                                                            this.actionOpen = false;
                                                                        }
                                                                    }"
                                                                    @scroll.window="closeMenu()"
                                                                    @resize.window="closeMenu()"
                                                                    @keydown.escape.window="closeMenu()"
                                                                    class="inline-flex"
                                                                >
                                                                    <button
                                                                        x-ref="trigger"
                                                                        type="button"
                                                                        @click="toggleMenu()"
                                                                        :aria-expanded="actionOpen"
                                                                        aria-haspopup="true"
                                                                        aria-label="Pilihan tindakan periode"
                                                                        title="Pilihan tindakan"
                                                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                                                    >
                                                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                            <circle cx="5" cy="12" r="1.8"/>
                                                                            <circle cx="12" cy="12" r="1.8"/>
                                                                            <circle cx="19" cy="12" r="1.8"/>
                                                                        </svg>
                                                                    </button>

                                                                    {{-- Dropdown dipindahkan ke body --}}
                                                                    <template x-teleport="body">
                                                                        <div
                                                                            x-ref="menu"
                                                                            x-cloak
                                                                            x-show="actionOpen"
                                                                            @click.outside="closeMenu()"
                                                                            @keydown.escape.stop="closeMenu()"
                                                                            x-transition:enter="transition ease-out duration-100"
                                                                            x-transition:enter-start="opacity-0 scale-95"
                                                                            x-transition:enter-end="opacity-100 scale-100"
                                                                            x-transition:leave="transition ease-in duration-75"
                                                                            x-transition:leave-start="opacity-100 scale-100"
                                                                            x-transition:leave-end="opacity-0 scale-95"
                                                                            :style="menuStyle"
                                                                            class="w-56 origin-top-right overflow-y-auto rounded-xl border border-gray-200 bg-white py-1.5 text-left shadow-2xl ring-1 ring-black/5"
                                                                        >
                                                                            {{-- Kelola ACC --}}
                                                                            <a
                                                                                :href="accUrlTemplate.replace('__PERIOD_ID__', encodeURIComponent(period.id))"
                                                                                @click="closeMenu()"
                                                                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-indigo-700 transition hover:bg-indigo-50"
                                                                            >
                                                                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m8.5 12 2.2 2.2 4.8-5"/>
                                                                                </svg>
                                                                                <span>Kelola ACC</span>
                                                                            </a>

                                                                            {{-- Edit periode --}}
                                                                            <button
                                                                                type="button"
                                                                                @click="closeMenu(); openEdit(period.id)"
                                                                                class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition hover:bg-gray-50"
                                                                            >
                                                                                <svg class="h-4 w-4 shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m16.5 3.5 4 4M4 20l4.5-1 11-11a2.12 2.12 0 0 0-3-3l-11 11L4 20Z"/>
                                                                                </svg>
                                                                                <span>Edit Periode</span>
                                                                            </button>

                                                                            <div class="my-1.5 border-t border-gray-100"></div>

                                                                            {{-- Aktifkan periode draft --}}
                                                                            <template x-if="period.status === 'draft'">
                                                                                <form
                                                                                    method="POST"
                                                                                    :action="'{{ url('/kaprodi/pkl-periods') }}/' + encodeURIComponent(period.id) + '/activate'"
                                                                                    @submit="closeMenu()"
                                                                                    onsubmit="return confirm('Aktifkan periode ini?')"
                                                                                >
                                                                                    @csrf
                                                                                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-indigo-700 transition hover:bg-indigo-50">
                                                                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m5 12 4 4L19 6"/>
                                                                                        </svg>
                                                                                        <span>Aktifkan Periode</span>
                                                                                    </button>
                                                                                </form>
                                                                            </template>

                                                                            {{-- Akhiri periode aktif --}}
                                                                            <template x-if="period.status === 'aktif'">
                                                                                <form
                                                                                    method="POST"
                                                                                    :action="'{{ url('/kaprodi/pkl-periods') }}/' + encodeURIComponent(period.id) + '/finish'"
                                                                                    @submit="closeMenu()"
                                                                                    onsubmit="return confirm('Akhiri periode ini?')"
                                                                                >
                                                                                    @csrf
                                                                                    <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-amber-700 transition hover:bg-amber-50">
                                                                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 7v5l3 2"/>
                                                                                        </svg>
                                                                                        <span>Akhiri Periode</span>
                                                                                    </button>
                                                                                </form>
                                                                            </template>

                                                                            {{-- Hapus periode jika status bukan aktif --}}
                                                                            <template x-if="period.status !== 'aktif'">
                                                                                <div>
                                                                                    <div class="my-1.5 border-t border-gray-100"></div>
                                                                                    <form
                                                                                        method="POST"
                                                                                        :action="'{{ url('/kaprodi/pkl-periods') }}/' + encodeURIComponent(period.id)"
                                                                                        @submit="closeMenu()"
                                                                                        onsubmit="return confirm('Hapus periode ini beserta data terkait? Tindakan ini tidak dapat dibatalkan.')"
                                                                                    >
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit" class="flex w-full items-center gap-3 px-4 py-2.5 text-sm text-red-600 transition hover:bg-red-50">
                                                                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16m-10 4v6m4-6v6M5 7l1 13h12l1-13M9 7V4h6v3"/>
                                                                                            </svg>
                                                                                            <span>Hapus Periode</span>
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                            </template>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    {{-- Detail periode --}}
                                                    <tr x-cloak x-show="isExpanded(period.id)">
                                                        <td colspan="7" class="border-t border-gray-200 bg-gray-50/70 p-4 sm:p-6">
                                                            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">

                                                                {{-- Panel CPL --}}
                                                                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                                                                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                                                                        <div>
                                                                            <h4 class="text-sm font-semibold text-gray-900">Capaian Pembelajaran Lulusan</h4>
                                                                            <p class="mt-1 text-xs text-gray-500">CPL yang terhubung ke periode ini.</p>
                                                                        </div>
                                                                        <span class="rounded-md bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700" x-text="period.cpls.length"></span>
                                                                    </div>

                                                                    <div class="divide-y divide-gray-100">
                                                                        <template x-for="cpl in period.cpls" :key="cpl.kode_cpl">
                                                                            <div class="p-4">
                                                                                <div class="flex flex-wrap items-start justify-between gap-2">
                                                                                    <p class="text-sm font-semibold text-gray-900" x-text="cpl.kode_cpl"></p>
                                                                                    <span
                                                                                        x-show="cpl.domain_kompetensi"
                                                                                        class="rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-600"
                                                                                        x-text="cpl.domain_kompetensi"
                                                                                    ></span>
                                                                                </div>
                                                                                <p class="mt-2 text-sm leading-6 text-gray-600" x-text="cpl.deskripsi_cpl || 'Deskripsi CPL belum tersedia.'"></p>
                                                                            </div>
                                                                        </template>

                                                                        <div x-show="period.cpls.length === 0" class="px-4 py-8 text-center">
                                                                            <p class="text-sm text-gray-500">Belum ada CPL pada periode ini.</p>
                                                                        </div>
                                                                    </div>
                                                                </section>

                                                                {{-- Panel batch --}}
                                                                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                                                                    <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                                                                        <div>
                                                                            <div class="flex items-center gap-2">
                                                                                <h4 class="text-sm font-semibold text-gray-900">Batch Pendaftaran</h4>
                                                                                <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600" x-text="period.batches.length"></span>
                                                                            </div>
                                                                            <p class="mt-1 text-xs text-gray-500">Jadwal batch pada periode ini.</p>
                                                                        </div>

                                                                        <button
                                                                            type="button"
                                                                            x-show="period.status === 'aktif'"
                                                                            @click="$dispatch('buat-batch', { periodId: period.id })"
                                                                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                                                        >
                                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/>
                                                                            </svg>
                                                                            Buat Batch
                                                                        </button>
                                                                    </div>

                                                                    <div class="overflow-x-auto">
                                                                        <table class="min-w-full divide-y divide-gray-200">
                                                                            <thead class="bg-gray-50">
                                                                                <tr>
                                                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Batch</th>
                                                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                                                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                                                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                                                                </tr>
                                                                            </thead>

                                                                            <tbody class="divide-y divide-gray-100">
                                                                                <template x-for="batch in period.batches" :key="batch.id">
                                                                                    <tr class="transition hover:bg-gray-50">
                                                                                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                                                                            <div class="flex items-center gap-2">
                                                                                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-400"></span>
                                                                                                <span x-text="batch.nama_batch"></span>
                                                                                            </div>
                                                                                        </td>

                                                                                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                                                                            <span x-text="batch.tanggal_mulai || '-'"></span>
                                                                                            <span class="mx-1 text-gray-300">–</span>
                                                                                            <span x-text="batch.tanggal_selesai || '-'"></span>
                                                                                        </td>

                                                                                        <td class="whitespace-nowrap px-4 py-3">
                                                                                            <span
                                                                                                class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold"
                                                                                                :class="{
                                                                                                    'bg-green-50 text-green-700': batch.status === 'dibuka',
                                                                                                    'bg-blue-50 text-blue-700': batch.status === 'terjadwal',
                                                                                                    'bg-gray-100 text-gray-600': batch.status === 'ditutup',
                                                                                                    'bg-amber-50 text-amber-700': batch.status === 'draft'
                                                                                                }"
                                                                                                x-text="batch.status.charAt(0).toUpperCase() + batch.status.slice(1)"
                                                                                            ></span>
                                                                                        </td>

                                                                                        <td class="whitespace-nowrap px-4 py-3 text-right">
                                                                                            <button
                                                                                                type="button"
                                                                                                @click="$dispatch('edit-batch', { batchId: batch.id, periodId: period.id })"
                                                                                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                                                            >
                                                                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m16.5 3.5 4 4M4 20l4.5-1 11-11a2.12 2.12 0 0 0-3-3l-11 11L4 20Z"/>
                                                                                                </svg>
                                                                                                Edit Tanggal
                                                                                            </button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </template>

                                                                                <tr x-show="period.batches.length === 0">
                                                                                    <td colspan="4" class="px-4 py-8 text-center">
                                                                                        <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v13H4V6a1 1 0 0 1 1-1Z"/>
                                                                                            </svg>
                                                                                        </div>
                                                                                        <p class="mt-3 text-sm font-medium text-gray-700">Belum ada batch</p>
                                                                                        <p class="mt-1 text-xs text-gray-500">Buat batch untuk mengatur jadwal pendaftaran.</p>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </section>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Hasil pencarian kosong --}}
                                <tr x-show="filteredPeriods.length === 0">
                                    <td colspan="7" class="px-6 py-14 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle cx="11" cy="11" r="7" stroke-width="1.8"/>
                                                <path stroke-linecap="round" stroke-width="1.8" d="m16 16 4 4"/>
                                            </svg>
                                        </div>
                                        <p class="mt-3 text-sm font-semibold text-gray-900">Periode tidak ditemukan</p>
                                        <p class="mt-1 text-sm text-gray-500">Ubah kata kunci atau filter status.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($periods->hasPages())
                        <div class="flex flex-col gap-3 border-t border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-gray-500">
                                Menampilkan {{ $periods->firstItem() ?? 0 }}–{{ $periods->lastItem() ?? 0 }}
                                dari {{ $periods->total() }} periode
                            </p>
                            {{ $periods->links() }}
                        </div>
                    @endif

                    {{-- Empty state --}}
                    @if ($periods->isEmpty())
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v13H4V6a1 1 0 0 1 1-1Z"/>
                                </svg>
                            </div>
                            <h3 class="mt-4 text-base font-semibold text-gray-900">Belum ada periode PKL</h3>
                            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                                Tambahkan periode pertama untuk mulai mengatur tahun akademik, CPL, dan batch pendaftaran.
                            </p>
                            <button
                                type="button"
                                @click="openCreate()"
                                class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/>
                                </svg>
                                Tambah Periode Pertama
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Buat Batch --}}
        <div
            x-cloak
            x-show="batchPanelOpen"
            x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-gray-900/50 p-4"
            @click.self="closeBatch()"
        >
            <div
                x-show="batchPanelOpen"
                x-transition
                class="my-auto w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="batch-modal-title"
            >
                <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5">
                    <div>
                        <h3 id="batch-modal-title" class="text-lg font-semibold text-gray-900">Buat Batch Pendaftaran</h3>
                        <p class="mt-1 text-sm text-gray-500">Tentukan rentang tanggal pendaftaran untuk batch baru.</p>
                    </div>
                    <button type="button" @click="closeBatch()" class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup modal">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>

                <form :action="batchCreateUrl" method="POST">
                    @csrf
                    <input type="hidden" name="pkl_period_id" :value="batchForm.pkl_period_id">

                    <div class="space-y-5 px-6 py-6">
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Periode Akademik</p>
                            <p class="mt-1 text-sm font-semibold text-indigo-900" x-text="selectedBatchPeriodName"></p>
                        </div>

                        @error('pkl_period_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div>
                            <label for="batch-tanggal-mulai" class="mb-2 block text-sm font-medium text-gray-700">
                                Tanggal Mulai Pendaftaran <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="batch-tanggal-mulai"
                                type="date"
                                name="tanggal_mulai"
                                x-model="batchForm.tanggal_mulai"
                                :max="batchForm.tanggal_selesai || null"
                                required
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('tanggal_mulai')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="batch-tanggal-selesai" class="mb-2 block text-sm font-medium text-gray-700">
                                Tanggal Selesai Pendaftaran <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="batch-tanggal-selesai"
                                type="date"
                                name="tanggal_selesai"
                                x-model="batchForm.tanggal_selesai"
                                :min="batchForm.tanggal_mulai || null"
                                required
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                            @error('tanggal_selesai')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <p class="text-xs leading-5 text-gray-600">
                                Nama batch akan dibuat otomatis oleh sistem. Status awal batch adalah Draft.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeBatch()" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Simpan Batch</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Tanggal Batch --}}
        <div
            x-cloak
            x-show="editBatchPanelOpen"
            x-transition.opacity
            class="fixed inset-0 z-[110] flex items-center justify-center overflow-y-auto bg-gray-900/50 p-4"
            @click.self="closeEditBatch()"
        >
            <div
                x-show="editBatchPanelOpen"
                x-transition
                class="my-auto w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-batch-modal-title"
            >
                <div class="flex items-start justify-between border-b border-gray-200 px-6 py-5">
                    <div>
                        <h3 id="edit-batch-modal-title" class="text-lg font-semibold text-gray-900">Edit Jadwal Batch</h3>
                        <p class="mt-1 text-sm text-gray-500">Ubah rentang tanggal pendaftaran batch.</p>
                    </div>
                    <button type="button" @click="closeEditBatch()" class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700" aria-label="Tutup modal">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>

                <form :action="editBatchForm.action" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5 px-6 py-6">
                        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Informasi Batch</p>
                            <p class="mt-1 text-sm font-semibold text-indigo-900" x-text="editBatchForm.nama_batch"></p>
                            <p class="mt-1 text-xs text-indigo-700" x-text="editBatchForm.period_name"></p>
                        </div>

                        <div>
                            <label for="edit-batch-tanggal-mulai" class="mb-2 block text-sm font-medium text-gray-700">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="edit-batch-tanggal-mulai"
                                type="date"
                                name="tanggal_mulai"
                                x-model="editBatchForm.tanggal_mulai"
                                :max="editBatchForm.tanggal_selesai || null"
                                required
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="edit-batch-tanggal-selesai" class="mb-2 block text-sm font-medium text-gray-700">
                                Tanggal Selesai <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="edit-batch-tanggal-selesai"
                                type="date"
                                name="tanggal_selesai"
                                x-model="editBatchForm.tanggal_selesai"
                                :min="editBatchForm.tanggal_mulai || null"
                                required
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <p class="text-xs leading-5 text-gray-500">
                            Tanggal selesai harus sama dengan atau setelah tanggal mulai.
                        </p>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                        <button type="button" @click="closeEditBatch()" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50">Batal</button>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Panel form periode --}}
        @include('kaprodi.pkl-periods.form-panel')
    </div>

    <script>
        window.periodPanel = function (config) {
            return {
                panelOpen: false,
                panelMode: 'create',

                batchPanelOpen: @js(
                    $errors->has('pkl_period_id')
                    || $errors->has('tanggal_mulai')
                    || $errors->has('tanggal_selesai')
                ),

                editBatchPanelOpen: false,

                createUrl: config.createUrl,
                updateUrlTemplate: config.updateUrlTemplate,
                accUrlTemplate: config.accUrlTemplate,
                batchCreateUrl: config.batchCreateUrl,
                batchUpdateUrlTemplate: config.batchUpdateUrlTemplate,

                defaults: config.defaults,
                cpls: config.cpls ?? [],
                periods: config.periods ?? [],

                batchForm: {
                    pkl_period_id: @js(old('pkl_period_id')),
                    tanggal_mulai: @js(old('tanggal_mulai', '')),
                    tanggal_selesai: @js(old('tanggal_selesai', '')),
                },

                editBatchForm: {
                    id: null,
                    action: '',
                    nama_batch: '',
                    period_name: '',
                    tanggal_mulai: '',
                    tanggal_selesai: '',
                },

                form: {
                    id: null,
                    semester: '',
                    tahun_akademik: '',
                    cpls: [],
                },

                get selectedBatchPeriodName() {
                    const period = this.periods.find(
                        item => String(item.id) === String(this.batchForm.pkl_period_id)
                    );

                    return period
                        ? `${period.semester} ${period.tahun_akademik}`
                        : 'Periode belum dipilih';
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
                        cpls: (period.cpls ?? []).map(cpl =>
                            String(typeof cpl === 'object' ? cpl.kode_cpl : cpl)
                        ),
                    };

                    this.panelOpen = true;
                },

                closePanel() {
                    this.panelOpen = false;
                },

                openBatch(periodId) {
                    const period = this.periods.find(
                        item => String(item.id) === String(periodId)
                    );

                    if (!period) {
                        console.error('Periode tidak ditemukan:', periodId);
                        return;
                    }

                    if (period.status !== 'aktif') {
                        alert('Batch hanya dapat dibuat pada periode yang aktif.');
                        return;
                    }

                    this.batchForm = {
                        pkl_period_id: period.id,
                        tanggal_mulai: '',
                        tanggal_selesai: '',
                    };

                    this.batchPanelOpen = true;
                },

                closeBatch() {
                    this.batchPanelOpen = false;
                },

                openEditBatch(batchId, periodId) {
                    const period = this.periods.find(
                        item => String(item.id) === String(periodId)
                    );

                    const batch = period?.batches.find(
                        item => String(item.id) === String(batchId)
                    );

                    if (!period || !batch) {
                        console.error('Batch atau periode tidak ditemukan.', {
                            batchId,
                            periodId,
                        });
                        return;
                    }

                    this.editBatchForm = {
                        id: batch.id,
                        action: this.batchUpdateUrlTemplate.replace(
                            '__BATCH_ID__',
                            encodeURIComponent(batch.id)
                        ),
                        nama_batch: batch.nama_batch,
                        period_name: `${period.semester} ${period.tahun_akademik}`,
                        tanggal_mulai: batch.tanggal_mulai_input || '',
                        tanggal_selesai: batch.tanggal_selesai_input || '',
                    };

                    this.editBatchPanelOpen = true;
                },

                closeEditBatch() {
                    this.editBatchPanelOpen = false;
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

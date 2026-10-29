<div
    x-data="{
        toasts: [],
        nextId: 1,

        add(message, type = 'success') {
            const id = this.nextId++;

            this.toasts.push({
                id,
                message,
                type,
                leaving: false,
                progress: 100,
                timer: null,
                interval: null
            });

            const toast = this.toasts.find(item => item.id === id);
            const duration = 4000;
            const step = 50;

            toast.interval = setInterval(() => {
                toast.progress = Math.max(
                    0,
                    toast.progress - (step / duration) * 100
                );
            }, step);

            toast.timer = setTimeout(() => {
                this.remove(id);
            }, duration);
        },

        remove(id) {
            const toast = this.toasts.find(item => item.id === id);

            if (!toast || toast.leaving) return;

            toast.leaving = true;

            clearTimeout(toast.timer);
            clearInterval(toast.interval);

            setTimeout(() => {
                this.toasts = this.toasts.filter(item => item.id !== id);
            }, 350);
        },

        clearAll() {
            this.toasts.forEach(toast => {
                clearTimeout(toast.timer);
                clearInterval(toast.interval);
            });

            this.toasts = [];
        }
    }"
    x-on:toast.window="add($event.detail.message, $event.detail.type || 'success')"
    x-on:toast-remove-all.window="clearAll()"
    class="pointer-events-none fixed bottom-5 right-5 z-[9999] flex w-[calc(100%-2.5rem)] max-w-sm flex-col gap-3"
    aria-live="polite"
    aria-atomic="false"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="!toast.leaving"
            x-transition:enter="transition duration-350 ease-out"
            x-transition:enter-start="translate-x-12 opacity-0 scale-95"
            x-transition:enter-end="translate-x-0 opacity-100 scale-100"
            x-transition:leave="transition duration-300 ease-in"
            x-transition:leave-start="translate-x-0 opacity-100 scale-100"
            x-transition:leave-end="translate-x-12 opacity-0 scale-95"
            class="pointer-events-auto relative overflow-hidden rounded-xl border bg-white shadow-xl shadow-gray-900/10"
            :class="{
                'border-emerald-200': toast.type === 'success',
                'border-red-200': toast.type === 'error',
                'border-amber-200': toast.type === 'warning',
                'border-blue-200': toast.type === 'info'
            }"
            :role="toast.type === 'error' ? 'alert' : 'status'"
        >
            <div class="flex items-start gap-3 p-4">

                {{-- Ikon notifikasi --}}
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                    :class="{
                        'bg-emerald-50 text-emerald-600': toast.type === 'success',
                        'bg-red-50 text-red-600': toast.type === 'error',
                        'bg-amber-50 text-amber-600': toast.type === 'warning',
                        'bg-blue-50 text-blue-600': toast.type === 'info'
                    }"
                >
                    {{-- Success --}}
                    <template x-if="toast.type === 'success'">
                        <svg class="h-5 w-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m5 12 4 4L19 6"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </template>

                    {{-- Error --}}
                    <template x-if="toast.type === 'error'">
                        <svg class="h-5 w-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 8v4m0 4h.01"
                                stroke-linecap="round"/>
                        </svg>
                    </template>

                    {{-- Warning --}}
                    <template x-if="toast.type === 'warning'">
                        <svg class="h-5 w-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 9v4m0 4h.01M10.3 3.9 2.1 18a2 2 0 0 0 1.7 2.5h16.4a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z"
                                stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </template>

                    {{-- Info --}}
                    <template x-if="toast.type === 'info'">
                        <svg class="h-5 w-5" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M12 11v5m0-8h.01"
                                stroke-linecap="round"/>
                        </svg>
                    </template>
                </div>

                {{-- Judul dan pesan --}}
                <div class="min-w-0 flex-1 pt-0.5">
                    <p
                        class="text-sm font-semibold"
                        :class="{
                            'text-emerald-800': toast.type === 'success',
                            'text-red-800': toast.type === 'error',
                            'text-amber-800': toast.type === 'warning',
                            'text-blue-800': toast.type === 'info'
                        }"
                        x-text="{
                            success: 'Berhasil',
                            error: 'Terjadi Kesalahan',
                            warning: 'Perhatian',
                            info: 'Informasi'
                        }[toast.type] || 'Notifikasi'"
                    ></p>

                    <p
                        class="mt-1 break-words text-sm leading-relaxed text-gray-600"
                        x-text="toast.message"
                    ></p>
                </div>

                {{-- Tombol tutup --}}
                <button
                    type="button"
                    @click="remove(toast.id)"
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-gray-400 transition duration-200 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300"
                    aria-label="Tutup notifikasi"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m18 6-12 12M6 6l12 12"
                            stroke-linecap="round"/>
                    </svg>
                </button>
            </div>

            {{-- Progress bar waktu --}}
            <div class="h-1 w-full bg-gray-100">
                <div
                    class="h-full transition-[width] duration-100 ease-linear"
                    :class="{
                        'bg-emerald-500': toast.type === 'success',
                        'bg-red-500': toast.type === 'error',
                        'bg-amber-500': toast.type === 'warning',
                        'bg-blue-500': toast.type === 'info'
                    }"
                    :style="`width: ${toast.progress}%`"
                ></div>
            </div>
        </div>
    </template>
</div>

{{-- Notifikasi dari session Laravel --}}
@if (session('success'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message: @json(session('success')),
                    type: 'success'
                }
            }));
        });
    </script>
@endif

@if (session('error'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message: @json(session('error')),
                    type: 'error'
                }
            }));
        });
    </script>
@endif

@if ($errors->any())
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const isDuplicatePeriod =
                @json($errors->has('semester') &&
                    collect($errors->get('semester'))
                        ->contains(fn ($message) =>
                            str_contains(
                                $message,
                                'sudah terdaftar'
                            )
                        ));

            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    message: @json($errors->first()),
                    type: isDuplicatePeriod ? 'warning' : 'error'
                }
            }));
        });
    </script>
@endif

<aside class="col-start-1 row-start-1 flex h-dvh min-h-0 w-64 flex-col border-r border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
    <div>
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-gray-100 dark:border-gray-700">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
            </a>
        </div>

        <!-- Menu Navigasi -->
        <nav class="p-4 space-y-1">
            
        <x-nav-link
            :href="route('dashboard')"
            :active="request()->routeIs('dashboard')"
            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg"
        >
            <!-- Ikon Dashboard -->
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.8"
                stroke="currentColor"
                class="w-5 h-5 shrink-0"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z"
                />
            </svg>

            <span>{{ __('Dashboard') }}</span>
        </x-nav-link>
        </nav>
    </div>
</aside>
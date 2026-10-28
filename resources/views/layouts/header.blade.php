
<header class="relative z-30 flex h-16 w-full shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6 dark:border-gray-700 dark:bg-gray-900">
    {{-- Judul halaman --}}
    <h1 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
        {{ $title ?? 'Dashboard' }}
    </h1>

    {{-- Dropdown Profil --}}
    <div class="relative" x-data="{ open: false }">
        <button
            type="button"
            @click="open = !open"
            @click.outside="open = false"
            :aria-expanded="open"
            aria-label="Menu profil"
            class="flex items-center gap-2 rounded-lg p-2 transition hover:bg-gray-100 dark:hover:bg-gray-800"
        >
            {{-- Foto profil pada header --}}
            @if (Auth::user()->profile_photo)
                <img
                    src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                    alt="Foto profil"
                    class="h-9 w-9 rounded-full border border-gray-200 object-cover"
                />
            @else
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M20 21a8 8 0 0 0-16 0m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                    </svg>
                </div>
            @endif

            {{-- Panah dropdown --}}
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 text-gray-500 transition-transform"
                :class="{ 'rotate-180': open }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2"
                    d="m6 9 6 6 6-6" />
            </svg>
        </button>

        {{-- Konten dropdown --}}
        <div
            x-cloak
            x-show="open"
            x-transition
            class="absolute right-0 mt-2 w-64 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
        >
            {{-- Informasi pengguna --}}
            <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-4 dark:border-gray-700">
                {{-- Foto profil pada dropdown --}}
                @if (Auth::user()->profile_photo)
                    <img
                        src="{{ asset('storage/' . Auth::user()->profile_photo) }}"
                        alt="Foto profil"
                        class="h-11 w-11 shrink-0 rounded-full border border-gray-200 object-cover"
                    />
                @else
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M20 21a8 8 0 0 0-16 0m8-10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" />
                        </svg>
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-gray-800 dark:text-gray-100">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="break-all text-xs text-gray-500 dark:text-gray-400">
                        {{ Auth::user()->email }}
                    </p>
                </div>
            </div>

            <div class="p-2">
                {{-- Edit Profil --}}
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-gray-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M16.862 4.487a2.1 2.1 0 0 1 2.97 2.97L9.75 17.54l-4 1-1-4 10.112-10.053Z" />
                    </svg>
                    Edit Profil
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-red-600 transition hover:bg-red-50 dark:hover:bg-red-900/20">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M18 15l3-3m0 0-3-3m3 3H9" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

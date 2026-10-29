<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIM PKL') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet"
    />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased dark:bg-gray-950">

    {{-- Layout utama --}}
    <div class="grid h-dvh grid-cols-[16rem_minmax(0,1fr)] overflow-hidden">

        {{-- Sidebar berdasarkan role pengguna --}}
        @if (auth()->user()->hasRole('kaprodi'))
            <x-sidebar.kaprodi />
        @elseif (auth()->user()->hasRole('mahasiswa'))
            <x-sidebar.mahasiswa />
        @endif

        {{-- Area utama kanan --}}
        <div class="col-start-2 flex min-h-0 min-w-0 flex-col overflow-hidden">

            {{-- Header atas --}}
            @include('layouts.header', [
                'title' => $title ?? 'Dashboard'
            ])

            {{-- Konten di bawah header --}}
            <main class="min-h-0 flex-1 overflow-y-auto p-6">
                {{ $slot }}
            </main>

        </div>
    </div>

    {{-- Notifikasi global di kanan bawah layar --}}
    <x-toast-notification />

</body>
</html>
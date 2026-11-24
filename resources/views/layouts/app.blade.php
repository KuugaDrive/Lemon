<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Binar Sunnah School') }} - Sistem Informasi Sekolah</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-full text-slate-800" x-data="{ sidebarOpen: false }">
        <div class="min-h-full flex">
            <!-- Mobile Sidebar Backdrop -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false" 
                 class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden" 
                 style="display: none;"></div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                   class="fixed inset-y-0 left-0 z-50 w-154 bg-slate-900 text-slate-200 transition-transform duration-300 ease-in-out flex flex-col shadow-xl lg:static lg:inset-auto lg:translate-x-0">
                
                <!-- Logo & Brand -->
                <div class="h-16 flex items-center justify-between px-5 bg-slate-950/40 border-b border-slate-800">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 font-bold text-white text-base tracking-wide">
                        <img src="{{ asset('storage/logo-binar-693.png') }}" alt="Binar Sunnah School" class="h-9 w-auto object-contain bg-white rounded-md p-1 shadow-sm">
                        <div class="leading-tight">
                            <span class="text-amber-400 font-bold block text-sm">Binar Sunnah</span>
                            <span class="text-xs font-normal text-slate-300">School</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- User Profile Card in Sidebar -->
                <div class="p-4 mx-3 my-3 bg-slate-800/60 rounded-xl border border-slate-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center font-bold text-white shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="overflow-hidden flex-1">
                            <h4 class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</h4>
                            <p class="text-xs text-slate-400 truncate">NIP: {{ Auth::user()->nip }}</p>
                        </div>
                    </div>
                    <div class="mt-2.5 flex flex-wrap gap-1">
                        @foreach(Auth::user()->roles as $role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold {{ $role->name === 'admin' ? 'bg-purple-900/60 text-purple-200 border border-purple-700' : 'bg-emerald-900/60 text-emerald-200 border border-emerald-700' }}">
                                {{ ucfirst($role->name) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-3 py-2 space-y-1 overflow-y-auto">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <!-- Section: Master Data -->
                    <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        Master Data
                    </div>

                    @role('admin')
                    <!-- Guru (Admin only) -->
                    <a href="{{ route('master.guru.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.guru.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Data Guru</span>
                    </a>

                    <!-- Kelas (Admin only) -->
                    <a href="{{ route('master.kelas.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.kelas.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Data Kelas</span>
                    </a>
                    @endrole

                    @hasanyrole('admin|guru')
                    <!-- Siswa (Admin & Guru) -->
                    <a href="{{ route('master.siswa.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.siswa.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Data Santri / Siswa</span>
                    </a>
                    @endhasanyrole

                    @role('admin')
                    <!-- Section: Kumpulan Materi (Admin only) -->
                    <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        Kumpulan Materi
                    </div>

                    <a href="{{ route('katalog.iqro.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('katalog.iqro.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Kumpulan Iqro</span>
                    </a>

                    <a href="{{ route('katalog.surat.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('katalog.surat.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Kumpulan Nama Surat</span>
                    </a>

                    <a href="{{ route('katalog.doa.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('katalog.doa.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span>Kumpulan Doa Harian</span>
                    </a>

                    <a href="{{ route('katalog.hadist.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('katalog.hadist.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Kumpulan Hadits</span>
                    </a>

                    <a href="{{ route('katalog.alfabet.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('katalog.alfabet.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 5v14m0 0l-3-3m3 3l3-3m5-6h4m-2-2v4" />
                        </svg>
                        <span>Huruf Hijaiyah</span>
                    </a>

                    <a href="{{ route('katalog.alfabet-latin.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('katalog.alfabet-latin.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        <span>Kumpulan Alfabet</span>
                    </a>
                    @endrole

                    @hasanyrole('admin|guru')
                    <!-- Section: Presensi -->
                    <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        Presensi Harian
                    </div>

                    <a href="{{ route('presensi.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('presensi.index') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span>Input Presensi</span>
                    </a>

                    <a href="{{ route('presensi.rekap') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('presensi.rekap') || request()->routeIs('presensi.siswa') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Rekap Presensi</span>
                    </a>

                    <!-- Section: Setoran & Hafalan -->
                    <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        Setoran & Hafalan
                    </div>

                    <a href="{{ route('setoran.iqro.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('setoran.iqro.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <span>Setoran Iqro</span>
                    </a>

                    <a href="{{ route('setoran.quran.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('setoran.quran.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        <span>Setoran Nama Surat</span>
                    </a>

                    <a href="{{ route('hafalan.doa.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('hafalan.doa.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span>Setoran Doa Harian</span>
                    </a>

                    <a href="{{ route('hafalan.hadist.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('hafalan.hadist.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Setoran Hadist</span>
                    </a>

                    <a href="{{ route('baca-buku.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('baca-buku.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 5v14m0 0l-3-3m3 3l3-3m5-6h4m-2-2v4" />
                        </svg>
                        <span>Setoran Alfabet</span>
                    </a>
                    @endhasanyrole

                    @can('kelola-spp')
                    <!-- Section: Keuangan SPP (Admin / Bendahara) -->
                    <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        Keuangan SPP
                    </div>

                    <a href="{{ route('spp.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('spp.index') || request()->routeIs('spp.create') || request()->routeIs('spp.edit') || request()->routeIs('spp.riwayat') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Pembayaran SPP</span>
                    </a>

                    <a href="{{ route('spp.tunggakan') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('spp.tunggakan') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Tunggakan SPP</span>
                    </a>

                    <a href="{{ route('spp.massal') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('spp.massal') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Input Massal</span>
                    </a>

                    <a href="{{ route('spp.pengaturan') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('spp.pengaturan') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pengaturan SPP</span>
                    </a>
                    @endcan

                    @hasanyrole('admin|guru')
                    <!-- Section: Laporan Cetak -->
                    <div class="pt-4 pb-1 px-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        Laporan
                    </div>

                    <a href="{{ route('laporan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('laporan.*') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Laporan PDF</span>
                    </a>
                    @endhasanyrole
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-3 border-t border-slate-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 text-sm font-medium text-rose-300 hover:bg-rose-950/40 hover:text-rose-200 rounded-lg transition-colors">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
                <!-- Top Header Bar -->
                <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-30">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        @isset($header)
                            <div>{{ $header }}</div>
                        @else
                            <h1 class="text-lg font-semibold text-slate-800">Binar Sunnah School</h1>
                        @endisset
                    </div>

                    <!-- Right user menu -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('profile.edit') }}" class="text-sm text-slate-600 hover:text-emerald-700 font-medium flex items-center gap-1.5 px-3 py-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Profil</span>
                        </a>
                    </div>
                </header>

                <!-- Main Content Body -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                    <!-- Flash Messages -->
                    <x-flash-messages />

                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

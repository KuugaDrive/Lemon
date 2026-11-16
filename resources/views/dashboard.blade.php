<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Dashboard Utama
        </h2>
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="p-6 text-slate-900">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Assalamu'alaikum, {{ Auth::user()->name }}!</h3>
                        <p class="text-sm text-slate-500 mt-1">Selamat datang di Sistem Informasi PAUD &amp; TPQ Lemon.</p>
                    </div>
                    <div>
                        <span class="text-xs uppercase font-semibold text-slate-500 tracking-wider block sm:text-right">Peran Pengguna</span>
                        <div class="flex sm:justify-end gap-1.5 mt-1">
                            @foreach(Auth::user()->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <h4 class="font-semibold text-slate-800 text-sm mb-3">Informasi Akun Anda</h4>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-slate-500">NIP:</dt>
                                <dd class="font-medium font-mono text-slate-900">{{ Auth::user()->nip }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Nama Lengkap:</dt>
                                <dd class="font-medium text-slate-900">{{ Auth::user()->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Nomor HP:</dt>
                                <dd class="font-medium text-slate-900">{{ Auth::user()->no_hp ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <h4 class="font-semibold text-slate-800 text-sm mb-3">Kelas yang Diampu</h4>
                        @if(Auth::user()->kelas->isEmpty())
                            <p class="text-sm text-slate-500 italic">
                                {{ Auth::user()->hasRole('admin') ? 'Sebagai Administrator, Anda memiliki hak kelola terhadap seluruh kelas.' : 'Belum ada kelas yang ditetapkan.' }}
                            </p>
                        @else
                            <ul class="list-disc list-inside text-sm text-slate-700 space-y-1">
                                @foreach(Auth::user()->kelas as $k)
                                    <li class="font-medium">{{ $k->nama_kelas }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-guest-layout>
    <div x-data="{ submitting: false }" class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 bg-slate-900 relative overflow-hidden">
        <!-- Ambient Mesh Gradient -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl border border-white/20 relative z-10">
            <!-- Brand Header -->
            <div class="text-center mb-6">
                <a href="/" class="inline-flex items-center gap-2">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo" class="h-12 w-auto object-contain">
                </a>
                <h2 class="mt-3 text-xl font-bold text-slate-900">Lupa Kata Sandi?</h2>
                <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                    Masukkan alamat email Anda untuk menerima tautan pemulihan kata sandi.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" @submit="submitting = true" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           placeholder="nama@email.com" 
                           class="block w-full px-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 bg-white border border-slate-300 focus:border-indigo-600 focus:ring-indigo-500/20 rounded-xl transition duration-200 outline-none focus:ring-2">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="space-y-3 pt-2">
                    <button type="submit" 
                            :disabled="submitting" 
                            class="w-full flex items-center justify-center py-3 px-5 rounded-xl font-semibold text-sm text-white bg-indigo-900 hover:bg-indigo-800 transition duration-150 shadow-md">
                        <span x-show="!submitting">Kirim Tautan Pemulihan</span>
                        <span x-show="submitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Mengirim...</span>
                        </span>
                    </button>

                    <a href="{{ route('login') }}" class="block text-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition py-1">
                        &larr; Kembali ke Halaman Masuk
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

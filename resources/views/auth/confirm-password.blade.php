<x-guest-layout>
    <div x-data="{ submitting: false, showPassword: false }" class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 bg-slate-900 relative overflow-hidden">
        <!-- Ambient Mesh Gradient -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl p-6 sm:p-8 shadow-2xl border border-white/20 relative z-10">
            <!-- Brand Header -->
            <div class="text-center mb-6">
                <a href="/" class="inline-flex items-center gap-2">
                    <img src="{{ asset('images/logo-sekolah.png') }}" alt="Logo" class="h-12 w-auto object-contain">
                </a>
                <h2 class="mt-3 text-xl font-bold text-slate-900">Konfirmasi Kata Sandi</h2>
                <p class="mt-1 text-xs text-slate-500 leading-relaxed">
                    Ini adalah area yang dilindungi. Silakan masukkan kembali kata sandi Anda untuk melanjutkan.
                </p>
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" @submit="submitting = true" class="space-y-5">
                @csrf

                <!-- Password Input with Show/Hide -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative rounded-xl shadow-sm">
                        <input id="password" 
                               :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               required 
                               autocomplete="current-password" 
                               placeholder="Masukkan kata sandi" 
                               class="block w-full pl-4 pr-12 py-3 text-sm text-slate-900 placeholder:text-slate-400 bg-white border border-slate-300 focus:border-indigo-600 focus:ring-indigo-500/20 rounded-xl transition duration-200 outline-none focus:ring-2">
                        
                        <button type="button" 
                                @click="showPassword = !showPassword" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                                tabindex="-1">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" 
                            :disabled="submitting" 
                            class="w-full flex items-center justify-center py-3 px-5 rounded-xl font-semibold text-sm text-white bg-indigo-900 hover:bg-indigo-800 transition duration-150 shadow-md">
                        <span x-show="!submitting">Konfirmasi</span>
                        <span x-show="submitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

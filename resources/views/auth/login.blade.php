<x-guest-layout>
    <div x-data="{ 
            submitting: false, 
            showPassword: false 
        }" 
        class="min-h-screen w-full flex flex-col lg:flex-row bg-slate-50 relative overflow-x-hidden selection:bg-indigo-900 selection:text-white">

        <!-- ================= SISI KIRI: FORM LOGIN ================= -->
        <div class="w-full lg:w-1/2 xl:w-5/12 flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-14 bg-white shadow-xl lg:shadow-none z-10">
            
            <!-- Brand / Logo Header -->
            <div class="flex items-center">
                <a href="/" class="flex items-center gap-3.5 group focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg p-1">
                    <img src="{{ asset('storage/logo-binar-693.png') }}" 
                         alt="Logo Binar Sunnah School" 
                         class="h-12 w-auto object-contain transition duration-200 group-hover:opacity-95">
                    <div>
                        <span class="block text-lg font-bold tracking-tight text-slate-900">Binar Sunnah School</span>
                        <span class="block text-xs font-medium text-slate-500">SIAKAD &amp; Administrasi Santri</span>
                    </div>
                </a>
            </div>

            <!-- Login Form Container -->
            <div class="my-auto py-8 sm:py-10 max-w-md w-full mx-auto">
                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                        Masuk ke Sistem
                    </h1>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Silakan masukkan NIP dan kata sandi Anda untuk melanjutkan.
                    </p>
                </div>

                <!-- Session Status Flash Alert -->
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div class="text-xs sm:text-sm">
                            <strong class="font-semibold block mb-1">Gagal Masuk:</strong>
                            <ul class="list-disc pl-4 space-y-0.5 text-xs text-rose-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}" @submit="submitting = true" class="space-y-5">
                    @csrf

                    <!-- Kolom NIP -->
                    <div>
                        <label for="nip" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            NIP (Nomor Induk Pegawai)
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input id="nip" 
                                   type="text" 
                                   name="nip" 
                                   value="{{ old('nip') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="username" 
                                   placeholder="Masukkan NIP Anda" 
                                   class="block w-full pl-11 pr-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 bg-white border {{ $errors->has('nip') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200 bg-rose-50/20' : 'border-slate-300 focus:border-indigo-600 focus:ring-indigo-500/20' }} rounded-xl transition duration-200 outline-none focus:ring-2">
                        </div>
                        @if ($errors->has('nip'))
                            <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $errors->first('nip') }}
                            </p>
                        @endif
                    </div>

                    <!-- Kolom Kata Sandi -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Kata Sandi
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" 
                                   :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="Masukkan kata sandi" 
                                   class="block w-full pl-11 pr-12 py-3 text-sm text-slate-900 placeholder:text-slate-400 bg-white border {{ $errors->has('password') ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-200 bg-rose-50/20' : 'border-slate-300 focus:border-indigo-600 focus:ring-indigo-500/20' }} rounded-xl transition duration-200 outline-none focus:ring-2">
                            
                            <!-- Toggle Show / Hide Password -->
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition duration-150 focus:outline-none"
                                    tabindex="-1"
                                    :title="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @if ($errors->has('password'))
                            <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1 font-medium">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $errors->first('password') }}
                            </p>
                        @endif
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   name="remember" 
                                   class="w-4 h-4 rounded border-slate-300 text-indigo-900 shadow-sm focus:ring-indigo-500/30 focus:ring-offset-0 cursor-pointer">
                            <span class="ml-2 text-sm text-slate-600 font-medium hover:text-slate-800 transition">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk / Submit dengan Loading Spinner -->
                    <div class="pt-3">
                        <button type="submit" 
                                :disabled="submitting" 
                                class="w-full relative flex items-center justify-center py-3.5 px-6 rounded-xl font-semibold text-sm text-white bg-slate-900 hover:bg-slate-800 shadow-md active:scale-[0.99] transition-all duration-200 disabled:opacity-70 disabled:cursor-not-allowed group">
                            
                            <!-- State Normal -->
                            <span x-show="!submitting" class="flex items-center gap-2">
                                <span>Masuk ke Sistem</span>
                                <svg class="w-4 h-4 transition duration-200 transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>

                            <!-- State Loading -->
                            <span x-show="submitting" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memverifikasi Akun...</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer Hak Cipta -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                <p>&copy; {{ date('Y') }} Binar Sunnah School. Hak cipta dilindungi.</p>
                <span>SIAKAD</span>
            </div>
        </div>

        <!-- ================= SISI KANAN: BACKGROUND POLYGON MODERN ================= -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-7/12 relative overflow-hidden bg-slate-900 text-white flex-col justify-center items-center p-12 select-none">
            
            <!-- Modern Low-Poly Geometric Background SVG -->
            <svg class="absolute inset-0 w-full h-full object-cover" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                <!-- Low-Poly Geometric Facets -->
                <polygon points="0,0 240,0 120,180" fill="#0f172a" />
                <polygon points="240,0 520,0 360,150" fill="#1e293b" />
                <polygon points="520,0 800,0 660,190" fill="#1e1b4b" />
                <polygon points="800,0 1120,0 960,160" fill="#172554" />
                <polygon points="1120,0 1440,0 1280,210" fill="#0f172a" />

                <polygon points="0,0 120,180 0,340" fill="#1e293b" />
                <polygon points="120,180 360,150 280,350" fill="#1e1b4b" />
                <polygon points="360,150 660,190 510,380" fill="#1e293b" />
                <polygon points="660,190 960,160 810,360" fill="#1e1b4b" />
                <polygon points="960,160 1280,210 1150,390" fill="#172554" />
                <polygon points="1280,210 1440,0 1440,360" fill="#1e293b" />

                <polygon points="0,340 120,180 280,350" fill="#0f172a" />
                <polygon points="0,340 280,350 140,540" fill="#172554" />
                <polygon points="280,350 510,380 380,560" fill="#1e293b" />
                <polygon points="510,380 810,360 670,570" fill="#1e1b4b" />
                <polygon points="810,360 1150,390 990,560" fill="#1e293b" />
                <polygon points="1150,390 1440,360 1310,550" fill="#0f172a" />
                <polygon points="1440,360 1440,580 1310,550" fill="#1e1b4b" />

                <polygon points="0,340 140,540 0,720" fill="#1e1b4b" />
                <polygon points="140,540 380,560 250,750" fill="#0f172a" />
                <polygon points="380,560 670,570 530,760" fill="#172554" />
                <polygon points="670,570 990,560 840,750" fill="#1e1b4b" />
                <polygon points="990,560 1310,550 1170,760" fill="#1e293b" />
                <polygon points="1310,550 1440,580 1440,770" fill="#0f172a" />

                <polygon points="0,720 140,540 250,750" fill="#1e293b" />
                <polygon points="0,720 250,750 0,900" fill="#0f172a" />
                <polygon points="0,900 250,750 400,900" fill="#172554" />
                <polygon points="250,750 530,760 400,900" fill="#1e1b4b" />
                <polygon points="530,760 670,570 840,750" fill="#1e293b" />
                <polygon points="530,760 840,750 690,900" fill="#0f172a" />
                <polygon points="400,900 530,760 690,900" fill="#1e1b4b" />
                <polygon points="840,750 1170,760 1020,900" fill="#172554" />
                <polygon points="690,900 840,750 1020,900" fill="#1e293b" />
                <polygon points="1170,760 1310,550 1440,770" fill="#1e1b4b" />
                <polygon points="1170,760 1440,770 1320,900" fill="#0f172a" />
                <polygon points="1020,900 1170,760 1320,900" fill="#1e1b4b" />
                <polygon points="1320,900 1440,770 1440,900" fill="#172554" />
            </svg>

            <!-- Subtle dark overlay to keep it calm and normal -->
            <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-[1px]"></div>

            <!-- Normal, clean brand card -->
            <div class="relative z-10 text-center max-w-md p-8 rounded-2xl bg-white/5 backdrop-blur-md border border-white/10 shadow-2xl">
                <img src="{{ asset('storage/logo-binar-693.png') }}" 
                     alt="Logo Binar Sunnah School" 
                     class="h-24 w-auto mx-auto object-contain drop-shadow-md mb-6">
                <h2 class="text-2xl font-bold tracking-tight text-white">
                    Binar Sunnah School
                </h2>
                <p class="mt-2 text-sm text-slate-300">
                    Sistem Informasi Akademik &amp; Administrasi Santri
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>

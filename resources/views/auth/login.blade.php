@extends('layouts.app')

@section('title', 'Login - Koperasi Sejahtera')

@section('body')
<div class="min-h-screen flex bg-slate-50">
    <!-- Kolom Kiri: Lottie Animation (Hidden on Mobile) -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-50 to-primary-100 items-center justify-center relative overflow-hidden">
        <!-- Background Ornaments -->
        <div class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] bg-primary-300/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30vw] h-[30vw] bg-secondary-300/20 rounded-full blur-3xl"></div>
        
        <!-- Lottie Player -->
        <lottie-player 
            src="{{ asset('7c5a8db6-7a92-11ef-9752-3344be889c3f.json') }}"  
            background="transparent"  
            speed="1"  
            style="width: 85%; max-width: 600px; height: auto; position: relative; z-index: 10;"  
            loop 
            autoplay>
        </lottie-player>

        <div class="absolute bottom-10 left-0 right-0 text-center z-10">
            <h2 class="text-2xl font-bold text-primary-800 font-heading">Koperasi Masa Depan</h2>
            <p class="text-primary-600 mt-2 font-medium">Digitalisasi layanan simpan pinjam dan belanja</p>
        </div>
    </div>

    <!-- Kolom Kanan: Login Form -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center py-12 px-6 sm:px-12 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] relative">
        <div class="mx-auto w-full max-w-md page-enter">
            
            <div class="flex justify-center mb-6">
                @if(\App\Models\Setting::get('app_logo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" alt="Logo" class="w-20 h-20 object-cover rounded-2xl shadow-lg shadow-primary-500/30">
                @else
                    <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                        <i class="fas fa-building-columns text-white text-3xl"></i>
                    </div>
                @endif
            </div>
            
            <h2 class="text-center text-3xl font-extrabold text-slate-900 font-heading tracking-tight mb-2">
                {{ \App\Models\Setting::get('app_name', 'Koperasi Sejahtera') }}
            </h2>
            <p class="text-center text-sm text-slate-600 mb-8">
                Silakan login untuk mengakses akun Anda
            </p>

            <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/60 rounded-3xl border border-slate-100 relative z-10" style="animation-delay: 0.1s;">

                @if(session('success'))
                    <div class="mb-5 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 flex gap-3 text-sm">
                        <i class="fas fa-check-circle mt-0.5"></i>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 flex gap-3 text-sm">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <ul class="list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 mb-1">Email Address</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-400"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-11 sm:text-sm border-slate-300 rounded-xl py-3 border bg-slate-50 focus:bg-white transition" 
                                placeholder="anda@koperasi.com" value="{{ old('email') }}">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-slate-700 mb-1">Password</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-11 sm:text-sm border-slate-300 rounded-xl py-3 border bg-slate-50 focus:bg-white transition" 
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center cursor-pointer" onclick="togglePassword()">
                                <i class="fas fa-eye text-slate-400 hover:text-primary-500 transition" id="eyeIcon"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded cursor-pointer">
                            <label for="remember" class="ml-2 block text-sm font-medium text-slate-600 cursor-pointer hover:text-slate-900 transition">
                                Ingat saya
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-bold text-primary-600 hover:text-primary-500 transition">
                                Lupa password?
                            </a>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" id="btnSubmit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all hover:-translate-y-0.5 shadow-lg shadow-primary-500/30">
                            Masuk ke Sistem
                        </button>
                    </div>

                    <div class="text-center pt-2">
                        <p class="text-sm text-slate-500">Belum menjadi anggota? <a href="{{ route('register') }}" class="font-bold text-primary-600 hover:text-primary-700">Daftar sekarang</a></p>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Ajax Login
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#btnSubmit');
        const originalText = btn.html();
        
        btn.html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1000);
                }
            },
            error: function(xhr) {
                btn.html(originalText);
                btn.prop('disabled', false);
                
                // Let global error handler show the toast
            }
        });
    });
</script>
@endpush
@endsection

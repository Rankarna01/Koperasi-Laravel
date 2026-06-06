@extends('layouts.app')

@section('title', 'Login - Koperasi Sejahtera')

@section('body')
<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
    <div class="sm:mx-auto sm:w-full sm:max-w-md page-enter">
        <div class="flex justify-center">
            @if(\App\Models\Setting::get('app_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" alt="Logo" class="w-20 h-20 object-cover rounded-2xl shadow-lg shadow-primary-500/30">
            @else
                <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                    <i class="fas fa-building-columns text-white text-3xl"></i>
                </div>
            @endif
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
            {{ \App\Models\Setting::get('app_name', 'Koperasi Sejahtera') }}
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
            Sistem Informasi Koperasi Simpan Pinjam & Penjualan
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md page-enter" style="animation-delay: 0.1s;">
        <div class="bg-white py-8 px-4 shadow-xl shadow-slate-200/50 sm:rounded-2xl sm:px-10 border border-slate-100">

            @if(session('success'))
                <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 flex gap-3 text-sm">
                    <i class="fas fa-check-circle mt-0.5"></i>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-xl border border-red-200 flex gap-3 text-sm">
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
                    <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-slate-400"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                            class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 border bg-slate-50 transition" 
                            placeholder="anda@koperasi.com" value="{{ old('email') }}">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                            class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-slate-300 rounded-xl py-3 border bg-slate-50 transition" 
                            placeholder="••••••••">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword()">
                            <i class="fas fa-eye text-slate-400 hover:text-slate-600 transition" id="eyeIcon"></i>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-slate-300 rounded cursor-pointer">
                        <label for="remember" class="ml-2 block text-sm text-slate-700 cursor-pointer">
                            Ingat saya
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-primary-600 hover:text-primary-500 transition">
                            Lupa password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" id="btnSubmit" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all hover:-translate-y-0.5 shadow-lg shadow-primary-500/30">
                        Masuk ke Sistem
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-slate-500">Belum menjadi anggota? <a href="{{ route('register') }}" class="font-bold text-primary-600 hover:text-primary-700">Daftar sekarang</a></p>
                </div>
            </form>
            
            <div class="mt-6 text-center text-xs text-slate-400">
                <p>Gunakan kredensial berikut untuk demo:</p>
                <div class="mt-2 grid grid-cols-2 gap-2 text-left">
                    <div class="bg-slate-50 p-2 rounded-lg border border-slate-100">
                        <span class="font-bold text-slate-600">Ketua:</span><br>ketua@koperasi.com<br>pass: password
                    </div>
                    <div class="bg-slate-50 p-2 rounded-lg border border-slate-100">
                        <span class="font-bold text-slate-600">Bendahara:</span><br>bendahara@koperasi.com<br>pass: password
                    </div>
                    <div class="bg-slate-50 p-2 rounded-lg border border-slate-100">
                        <span class="font-bold text-slate-600">Admin:</span><br>admin@koperasi.com<br>pass: password
                    </div>
                    <div class="bg-slate-50 p-2 rounded-lg border border-slate-100">
                        <span class="font-bold text-slate-600">Anggota:</span><br>andi@koperasi.com<br>pass: password
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Koperasi Sejahtera Bersama</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cc7fb',
                            400: '#36abf8',
                            500: '#0c92ec',
                            600: '#0075cb',
                            700: '#015da3',
                            800: '#054e85',
                            900: '#0a416e',
                            950: '#072a4b',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        body {
            background-image: 
                radial-gradient(at 0% 0%, hsla(213,100%,88%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(205,100%,88%,1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(217,100%,88%,1) 0, transparent 50%),
                radial-gradient(at 0% 100%, hsla(210,100%,88%,1) 0, transparent 50%);
            background-color: #f8fafc;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased text-slate-800">
    <div class="w-full max-w-[1000px] flex rounded-[2rem] overflow-hidden shadow-2xl shadow-primary-500/20 glass-effect border border-white/50">
        
        <!-- Left Side: Branding -->
        <div class="hidden lg:flex lg:w-5/12 bg-primary-600 relative overflow-hidden flex-col justify-between p-10 text-white">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-500 to-primary-800"></div>
            <!-- Decorative circles -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/30 shadow-lg mb-8">
                    <i class="fas fa-building-columns text-2xl text-white"></i>
                </div>
                <h1 class="text-4xl font-extrabold font-heading tracking-tight leading-tight mb-4">
                    Koperasi <br>
                    <span class="text-primary-100">Sejahtera</span> Bersama
                </h1>
                <p class="text-primary-100 text-sm leading-relaxed">
                    Bergabunglah bersama kami untuk masa depan finansial yang lebih baik. Nikmati layanan simpan pinjam yang aman, transparan, dan menguntungkan.
                </p>
            </div>

            <div class="relative z-10 mt-12">
                <div class="bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/20">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 rounded-full bg-primary-400 flex items-center justify-center text-white font-bold">
                            SK
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">Sudah Punya Akun?</p>
                            <p class="text-xs text-primary-200">Masuk untuk mengakses layanan.</p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="block w-full py-2.5 bg-white text-primary-700 text-center text-sm font-bold rounded-xl hover:bg-primary-50 transition shadow-lg">
                        Masuk Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-7/12 p-8 sm:p-12">
            <div class="mb-10 text-center lg:text-left">
                <h2 class="text-3xl font-extrabold font-heading text-slate-800 tracking-tight">Daftar Akun Baru</h2>
                <p class="text-slate-500 mt-2 text-sm">Lengkapi data di bawah ini untuk memulai proses pendaftaran keanggotaan koperasi.</p>
            </div>

            <form id="registerForm" action="{{ route('register') }}" method="POST">
                @csrf
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            </div>
                            <input type="text" name="name" required placeholder="Masukkan nama lengkap Anda" 
                                class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                        </div>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                            </div>
                            <input type="email" name="email" required placeholder="nama@email.com" 
                                class="w-full pl-11 pr-4 py-3.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                                </div>
                                <input type="password" name="password" id="password" required placeholder="Minimal 8 karakter" 
                                    class="w-full pl-11 pr-12 py-3.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-primary-500 transition-colors">
                                    <i class="far fa-eye" id="icon-password"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-slate-400 group-focus-within:text-primary-500 transition-colors"></i>
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password" 
                                    class="w-full pl-11 pr-12 py-3.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all shadow-sm">
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-primary-500 transition-colors">
                                    <i class="far fa-eye" id="icon-password_confirmation"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" id="btnSubmit" class="w-full py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 transition-all hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <span>Buat Akun</span>
                        <i class="fas fa-arrow-right text-sm"></i>
                    </button>
                </div>
                
                <!-- Mobile only links -->
                <div class="mt-8 text-center lg:hidden">
                    <p class="text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-700">Masuk di sini</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);
            
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

        $('#registerForm').on('submit', function(e) {
            e.preventDefault();
            
            const btn = $('#btnSubmit');
            const originalContent = btn.html();
            
            btn.html('<i class="fas fa-circle-notch fa-spin"></i> <span>Memproses...</span>');
            btn.prop('disabled', true);
            btn.addClass('opacity-75 cursor-not-allowed');

            // Clear previous errors
            $('.text-red-500.text-xs').remove();
            $('input').removeClass('border-red-500 focus:border-red-500 focus:ring-red-500/20');

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Akun Anda berhasil dibuat. Mengalihkan ke formulir pendaftaran...',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    }
                },
                error: function(xhr) {
                    btn.html(originalContent);
                    btn.prop('disabled', false);
                    btn.removeClass('opacity-75 cursor-not-allowed');

                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        for (const field in errors) {
                            const input = $(`input[name="${field}"]`);
                            input.addClass('border-red-500 focus:border-red-500 focus:ring-red-500/20');
                            input.parent().after(`<p class="text-red-500 text-xs mt-1">${errors[field][0]}</p>`);
                        }
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Terjadi kesalahan sistem. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonColor: '#0c92ec'
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>

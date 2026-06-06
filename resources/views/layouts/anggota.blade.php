<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Koperasi Sejahtera')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f8fafc; }
        /* Hide scrollbar for mobile feel */
        ::-webkit-scrollbar { display: none; }
        * { -ms-overflow-style: none; scrollbar-width: none; }
        
        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            background-color: #f8fafc;
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            max-width: 480px;
            background: white;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            padding-bottom: calc(12px + env(safe-area-inset-bottom));
            z-index: 50;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.02);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 500;
            text-decoration: none;
            gap: 4px;
        }

        .nav-item.active {
            color: #2563eb;
        }

        .nav-item i {
            font-size: 20px;
        }

        /* Top App Bar */
        .app-bar {
            position: sticky;
            top: 0;
            background: rgba(248, 250, 252, 0.9);
            backdrop-filter: blur(8px);
            z-index: 40;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800 antialiased">
    
    <div class="mobile-container pb-24">
        
        <!-- App Bar -->
        <header class="app-bar">
            @hasSection('header-left')
                @yield('header-left')
            @else
                <div class="flex items-center gap-2">
                    @if(\App\Models\Setting::get('app_logo'))
                        <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" alt="Logo" class="w-8 h-8 object-cover rounded-lg">
                    @else
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white text-sm">
                            <i class="fas fa-building-columns"></i>
                        </div>
                    @endif
                    <span class="font-bold text-slate-800 text-lg">{{ \App\Models\Setting::get('app_name', 'KOPKAR') }}</span>
                </div>
            @endif

            @hasSection('header-right')
                @yield('header-right')
            @else
                <div class="flex items-center gap-4">
                    <button class="relative text-slate-400 hover:text-slate-600 transition">
                        <i class="far fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-slate-50"></span>
                    </button>
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=e2e8f0&color=475569' }}" class="w-8 h-8 rounded-full border border-slate-200">
                </div>
            @endif
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-5 pt-2">
            @yield('content')
        </main>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <a href="{{ route('anggota.dashboard') }}" class="nav-item {{ request()->routeIs('anggota.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('anggota.pinjaman.index') }}" class="nav-item {{ request()->routeIs('anggota.pinjaman.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-dollar"></i>
                <span>Pinjaman</span>
            </a>
            <a href="{{ route('anggota.pembayaran.index') }}" class="nav-item {{ request()->routeIs('anggota.pembayaran.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Pembayaran</span>
            </a>
            <a href="{{ route('anggota.profil.index') }}" class="nav-item {{ request()->routeIs('anggota.profil.*') ? 'active' : '' }}">
                <i class="far fa-user"></i>
                <span>Profil</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </nav>
    </div>

    <!-- Global Toast Script -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        function showToast(icon, title) {
            Toast.fire({ icon: icon, title: title });
        }
        
        function showLoading() {
            Swal.fire({
                title: 'Mohon Tunggu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        function hideLoading() {
            Swal.close();
        }
        
        @if(session('success')) showToast('success', '{{ session('success') }}'); @endif
        @if(session('error')) showToast('error', '{{ session('error') }}'); @endif
        @if(session('warning')) showToast('warning', '{{ session('warning') }}'); @endif
        @if(session('info')) showToast('info', '{{ session('info') }}'); @endif
    </script>
    @stack('scripts')
</body>
</html>

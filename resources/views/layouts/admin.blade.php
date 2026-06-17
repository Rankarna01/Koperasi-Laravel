@extends('layouts.app')

@section('body')
<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-slate-200 transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-auto -translate-x-full">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100">
            @if(\App\Models\Setting::get('app_logo'))
                <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" alt="Logo" class="w-10 h-10 object-cover rounded-xl">
            @else
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building-columns text-white text-lg"></i>
                </div>
            @endif
            <div>
                <h1 class="font-poppins font-bold text-primary-600 text-sm leading-tight">{{ \App\Models\Setting::get('app_name', 'KOPKAR') }}</h1>
                <p class="text-[10px] text-slate-400 leading-tight">Sejahtera Bersama</p>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">
            @if(auth()->check())
                @if(auth()->user()->role === 'admin')
                    @include('layouts.sidebar.admin')
                @elseif(auth()->user()->role === 'bendahara')
                    @include('layouts.sidebar.bendahara')
                @elseif(auth()->user()->role === 'ketua')
                    @include('layouts.sidebar.ketua')
                @endif
            @endif
        </nav>

        <!-- User Profile (Bottom) -->
        <div class="border-t border-slate-100 p-4 bg-slate-50 mt-auto">
            <div class="flex items-center gap-3 mb-4">
                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500 capitalize font-medium">{{ auth()->user()->role }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-red-200 text-red-600 rounded-xl hover:bg-red-50 hover:border-red-300 font-bold transition-all text-sm shadow-sm">
                    <i class="fas fa-power-off"></i> Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 px-4 lg:px-8 py-3 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-slate-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Breadcrumb -->
                <nav class="hidden md:flex items-center text-sm text-slate-500">
                    <a href="#" class="hover:text-primary-600">
                        <i class="fas fa-home"></i>
                    </a>
                    @hasSection('breadcrumb')
                        <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
                        @yield('breadcrumb')
                    @endif
                </nav>
            </div>

            <div class="flex items-center gap-3">
                <!-- Search (Desktop) -->
                <div class="hidden md:block relative">
                    <input type="text" placeholder="Cari anggota, transaksi, laporan..." class="w-72 pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-400 transition">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                </div>

                <!-- Notification Bell -->
                <div class="relative">
                    <button onclick="document.getElementById('notifDropdown').classList.toggle('hidden')" class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span id="notifBadge" class="notif-badge absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden z-50">
                        <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b">
                            <h4 class="font-semibold text-sm text-slate-700">Notifikasi</h4>
                            <button onclick="markAllRead()" class="text-xs text-primary-600 hover:underline">Tandai semua dibaca</button>
                        </div>
                        <div id="notifList" class="max-h-80 overflow-y-auto">
                            <div class="p-4 text-center text-sm text-slate-400">Memuat...</div>
                        </div>
                    </div>
                </div>

                <!-- User Avatar -->
                <div class="hidden lg:flex items-center gap-3 pl-3 border-l border-slate-200">
                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="w-9 h-9 rounded-full object-cover">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-slate-400 capitalize">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-background">
            <div class="p-4 lg:p-8 page-enter">
                @yield('content')
            </div>
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Close notification dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const notifDropdown = document.getElementById('notifDropdown');
        if (!e.target.closest('#notifDropdown') && !e.target.closest('[onclick*="notifDropdown"]')) {
            notifDropdown.classList.add('hidden');
        }
    });
</script>
@endsection

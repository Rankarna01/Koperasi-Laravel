<div class="space-y-1">
    <a href="{{ route('ketua.dashboard') }}" class="sidebar-link {{ request()->routeIs('ketua.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-house w-5 text-center {{ request()->routeIs('ketua.dashboard') ? '' : 'text-slate-400' }}"></i>
        <span>Dashboard</span>
    </a>
    
    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Approval (Persetujuan)</p>
    
    <a href="{{ route('ketua.approval-anggota.index') }}" class="sidebar-link {{ request()->routeIs('ketua.approval-anggota.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-user-check w-5 text-center text-slate-400"></i>
        <span>ACC Keanggotaan</span>
    </a>
    <a href="{{ route('ketua.approval-pinjaman.index') }}" class="sidebar-link {{ request()->routeIs('ketua.approval-pinjaman.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-file-signature w-5 text-center text-slate-400"></i>
        <span>ACC Peminjaman</span>
    </a>
    <a href="{{ route('ketua.penarikan.index') }}" class="sidebar-link {{ request()->routeIs('ketua.penarikan.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-money-bill-wave w-5 text-center text-slate-400"></i>
        <span>ACC Penarikan</span>
    </a>

    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Manajemen</p>
    
    <a href="{{ route('ketua.user.index') }}" class="sidebar-link {{ request()->routeIs('ketua.user.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-users-gear w-5 text-center text-slate-400"></i>
        <span>Manajemen User</span>
    </a>

    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Laporan</p>
    
    <a href="{{ route('ketua.laporan.simpan-pinjam') }}" class="sidebar-link {{ request()->routeIs('ketua.laporan.simpan-pinjam') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-book-open w-5 text-center text-slate-400"></i>
        <span>Laporan Simpan Pinjam</span>
    </a>
    <a href="{{ route('ketua.laporan.penjualan') }}" class="sidebar-link {{ request()->routeIs('ketua.laporan.penjualan') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-chart-line w-5 text-center text-slate-400"></i>
        <span>Laporan Penjualan</span>
    </a>
</div>

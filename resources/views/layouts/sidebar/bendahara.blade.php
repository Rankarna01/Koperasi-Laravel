<div class="space-y-1">
    <a href="{{ route('bendahara.dashboard') }}" class="sidebar-link {{ request()->routeIs('bendahara.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-house w-5 text-center {{ request()->routeIs('bendahara.dashboard') ? '' : 'text-slate-400' }}"></i>
        <span>Dashboard</span>
    </a>
    
    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Master Data</p>
    
    <a href="{{ route('bendahara.anggota.index') }}" class="sidebar-link {{ request()->routeIs('bendahara.anggota.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-users-cog w-5 text-center text-slate-400"></i>
        <span>Validasi & Anggota</span>
    </a>
    <a href="{{ route('bendahara.barang.index') }}" class="sidebar-link {{ request()->routeIs('bendahara.barang.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-box w-5 text-center text-slate-400"></i>
        <span>Data Barang</span>
    </a>

    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Simpan Pinjam</p>
    
    <a href="{{ route('bendahara.simpanan.index') }}" class="sidebar-link {{ request()->routeIs('bendahara.simpanan.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-wallet w-5 text-center text-slate-400"></i>
        <span>Simpanan</span>
    </a>
    <a href="{{ route('bendahara.pinjaman.index') }}" class="sidebar-link {{ request()->routeIs('bendahara.pinjaman.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-hand-holding-dollar w-5 text-center text-slate-400"></i>
        <span>Pinjaman</span>
    </a>
    <a href="{{ route('bendahara.angsuran.index') }}" class="sidebar-link {{ request()->routeIs('bendahara.angsuran.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-money-bill-transfer w-5 text-center text-slate-400"></i>
        <span>Angsuran</span>
    </a>
</div>

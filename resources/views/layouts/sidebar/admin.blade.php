<div class="space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-house w-5 text-center {{ request()->routeIs('admin.dashboard') ? '' : 'text-slate-400' }}"></i>
        <span>Dashboard</span>
    </a>
    
    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Sistem</p>
    
    <a href="{{ route('admin.setting.index') }}" class="sidebar-link {{ request()->routeIs('admin.setting.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-cog w-5 text-center text-slate-400"></i>
        <span>Pengaturan Sistem</span>
    </a>

    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Master Data</p>

    <a href="{{ route('admin.barang.index') }}" class="sidebar-link {{ request()->routeIs('admin.barang.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-box text-slate-400 w-5 text-center"></i>
        <span>Data Barang</span>
    </a>
    <a href="{{ route('admin.supplier.index') }}" class="sidebar-link {{ request()->routeIs('admin.supplier.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-truck text-slate-400 w-5 text-center"></i>
        <span>Data Supplier</span>
    </a>

    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Transaksi</p>
    
    <a href="{{ route('admin.penjualan.index') }}" class="sidebar-link {{ request()->routeIs('admin.penjualan.index') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-shopping-cart w-5 text-center text-slate-400"></i>
        <span>Data Penjualan</span>
    </a>
    <a href="{{ route('admin.pembelian.index') }}" class="sidebar-link {{ request()->routeIs('admin.pembelian.index') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-truck-loading w-5 text-center text-slate-400"></i>
        <span>Belanja Barang</span>
    </a>

    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Laporan</p>
    
    <a href="{{ route('admin.shu.index') }}" class="sidebar-link {{ request()->routeIs('admin.shu.index') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-chart-pie w-5 text-center text-slate-400"></i>
        <span>Kalkulasi SHU</span>
    </a>
</div>

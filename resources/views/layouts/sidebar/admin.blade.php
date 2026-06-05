<div class="space-y-1">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-grid-2 w-5 text-center"></i>
        <span>Dashboard</span>
    </a>
    
    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Transaksi</p>
    
    <a href="{{ route('admin.penjualan.kasir') }}" class="sidebar-link {{ request()->routeIs('admin.penjualan.kasir') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-cash-register w-5 text-center text-slate-400"></i>
        <span>Kasir POS</span>
    </a>
    <a href="{{ route('admin.penjualan.index') }}" class="sidebar-link {{ request()->routeIs('admin.penjualan.index') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-shopping-cart w-5 text-center text-slate-400"></i>
        <span>Data Penjualan</span>
    </a>
    <a href="{{ route('admin.pembelian.index') }}" class="sidebar-link {{ request()->routeIs('admin.pembelian.index') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-truck-loading w-5 text-center text-slate-400"></i>
        <span>Pembelian Barang</span>
    </a>

    <p class="px-4 mt-4 mb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">Laporan</p>
    
    <a href="{{ route('admin.shu.index') }}" class="sidebar-link {{ request()->routeIs('admin.shu.index') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-xl font-medium text-sm text-slate-700">
        <i class="fas fa-chart-pie w-5 text-center text-slate-400"></i>
        <span>Kalkulasi SHU</span>
    </a>
</div>

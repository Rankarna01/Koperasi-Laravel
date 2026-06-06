@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <span class="text-primary-600 font-medium">Dashboard</span>
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 font-heading">Dashboard Admin</h2>
    <p class="text-slate-500 text-sm mt-1">Ringkasan aktivitas toko dan operasional</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stat 1 -->
    <div class="stat-card bg-white rounded-2xl p-5 border border-slate-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-xl flex items-center justify-center text-xl mb-4">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <p class="text-sm text-slate-500 font-medium">Total Penjualan</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['total_penjualan'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Stat 2 -->
    <div class="stat-card bg-white rounded-2xl p-5 border border-slate-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-secondary-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-secondary-100 text-secondary-600 rounded-xl flex items-center justify-center text-xl mb-4">
                <i class="fas fa-truck"></i>
            </div>
            <p class="text-sm text-slate-500 font-medium">Total Pembelian</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['total_pembelian'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Stat 3 -->
    <div class="stat-card bg-white rounded-2xl p-5 border border-slate-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-xl mb-4">
                <i class="fas fa-chart-line"></i>
            </div>
            <p class="text-sm text-slate-500 font-medium">Estimasi Laba/SHU</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['estimasi_shu'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Stat 4 -->
    <div class="stat-card bg-white rounded-2xl p-5 border border-slate-100 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center text-xl mb-4">
                <i class="fas fa-box-open"></i>
            </div>
            <p class="text-sm text-slate-500 font-medium">Stok Barang Habis/Menipis</p>
            <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stok['habis'] }} / {{ $stok['menipis'] }}</h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800">Tren Penjualan (6 Bulan)</h3>
            <button class="text-slate-400 hover:text-primary-600"><i class="fas fa-ellipsis-v"></i></button>
        </div>
        <div class="h-72">
            <canvas id="penjualanChart"></canvas>
        </div>
    </div>

    <!-- Top Selling -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
        <h3 class="font-bold text-slate-800 mb-4">Produk Terlaris</h3>
        <div class="space-y-4">
            @forelse($top_selling as $item)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-50 flex items-center justify-center text-primary-600 font-bold border border-slate-100">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $item->nama }}</p>
                        <p class="text-xs text-slate-500">Terjual: {{ $item->total_terjual }} unit</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-primary-600">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-slate-400 text-sm">Belum ada data penjualan.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Penjualan Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800">Penjualan Terbaru</h3>
            <a href="{{ route('admin.penjualan.index') }}" class="text-xs font-medium text-primary-600 hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs text-slate-400 border-b border-slate-100">
                        <th class="pb-2 font-medium">Periode</th>
                        <th class="pb-2 font-medium">Omset</th>
                        <th class="pb-2 font-medium">Admin</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @php
                        $bulanIndo = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                    @endphp
                    @forelse($penjualan_terbaru as $p)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3">
                                <p class="font-semibold text-slate-700">Rekap Bulanan</p>
                                <p class="text-xs text-slate-400">{{ $bulanIndo[$p->bulan] }} {{ $p->tahun }}</p>
                            </td>
                            <td class="py-3 font-semibold text-primary-600">Rp {{ number_format($p->total_omset, 0, ',', '.') }}</td>
                            <td class="py-3 text-slate-600">{{ $p->creator->name ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-slate-400 text-sm">Tidak ada data rekap.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pembelian Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800">Pembelian Barang Terbaru</h3>
            <a href="{{ route('admin.pembelian.index') }}" class="text-xs font-medium text-primary-600 hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs text-slate-400 border-b border-slate-100">
                        <th class="pb-2 font-medium">Tgl / Nota</th>
                        <th class="pb-2 font-medium">Supplier</th>
                        <th class="pb-2 font-medium">Total</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($pembelian_terbaru as $p)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="py-3">
                                <p class="font-semibold text-slate-700">{{ $p->no_nota }}</p>
                                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</p>
                            </td>
                            <td class="py-3 text-slate-600">{{ $p->supplier_nama }}</td>
                            <td class="py-3 font-semibold text-red-600">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-slate-400 text-sm">Tidak ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('penjualanChart').getContext('2d');
    
    // Gradient for line chart
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.5)'); // primary-600
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chart['labels']) !!},
            datasets: [{
                label: 'Penjualan',
                data: {!! json_encode($chart['penjualan']) !!},
                borderColor: '#2563eb',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            if(value >= 1000000) return value / 1000000 + 'M';
                            if(value >= 1000) return value / 1000 + 'k';
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Bendahara Dashboard')

@section('breadcrumb')
    <span class="text-primary-600 font-medium">Dashboard</span>
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 font-heading">Dashboard Bendahara</h2>
    <p class="text-slate-500 text-sm mt-1">Ringkasan keuangan dan simpan pinjam</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card bg-white rounded-2xl p-5 border border-slate-100">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500 font-medium">Total Simpanan</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['total_simpanan'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-slate-100">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500 font-medium">Pinjaman Aktif</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['total_pinjaman_aktif'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-slate-100">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500 font-medium">Angsuran Bulan Ini</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($stats['angsuran_bulan_ini'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-white rounded-2xl p-5 border border-slate-100">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-slate-500 font-medium">Tunggakan (Estimasi)</p>
                <h3 class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($stats['tunggakan'], 0, ',', '.') }}</h3>
            </div>
            <div class="w-10 h-10 bg-red-50 text-red-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Chart -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-800">Tren Simpan Pinjam (6 Bulan)</h3>
        </div>
        <div class="h-72">
            <canvas id="spChart"></canvas>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Pengajuan Pinjaman -->
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">Menunggu Verifikasi</h3>
                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ count($pengajuan_pinjaman) }}</span>
            </div>
            <div class="space-y-3">
                @forelse($pengajuan_pinjaman as $p)
                    <div class="p-3 border border-slate-100 rounded-xl bg-slate-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-sm text-slate-800">{{ $p->anggota->nama_lengkap }}</p>
                                <p class="text-xs text-slate-500">Rp {{ number_format($p->nominal, 0, ',', '.') }} ({{ $p->lama_cicilan }}x)</p>
                            </div>
                            <a href="{{ route('bendahara.pinjaman.index') }}" class="text-xs bg-white border border-slate-200 px-2 py-1 rounded shadow-sm hover:bg-slate-50">Proses</a>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-slate-400 py-2">Tidak ada pengajuan baru.</p>
                @endforelse
            </div>
        </div>

        <!-- Angsuran Jatuh Tempo -->
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm">
            <h3 class="font-bold text-slate-800 mb-4">Jatuh Tempo Mendekat</h3>
            <div class="space-y-3">
                @forelse($angsuran_jatuh_tempo as $a)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium text-sm text-slate-700">{{ $a['anggota'] }}</p>
                            <p class="text-xs text-slate-500">{{ $a['jatuh_tempo'] }} • Rp {{ number_format($a['nominal'], 0, ',', '.') }}</p>
                        </div>
                        @if($a['status'] === 'Telat')
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold">Telat</span>
                        @elseif($a['status'] === 'Segera')
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-[10px] font-bold">H-{{ $a['sisa_hari'] }}</span>
                        @else
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[10px] font-bold">H-{{ $a['sisa_hari'] }}</span>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-sm text-slate-400 py-2">Semua aman.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('spChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chart['labels']) !!},
            datasets: [
                {
                    label: 'Simpanan',
                    data: {!! json_encode($chart['simpanan']) !!},
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                },
                {
                    label: 'Pinjaman Keluar',
                    data: {!! json_encode($chart['pinjaman']) !!},
                    backgroundColor: '#8b5cf6',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            if(value >= 1000000) return value / 1000000 + 'M';
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush

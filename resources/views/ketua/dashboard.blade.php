@extends('layouts.admin')

@section('title', 'Ketua Dashboard')

@section('breadcrumb')
    <span class="text-primary-600 font-medium">Dashboard</span>
@endsection

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-slate-800 font-heading">Dashboard Ketua</h2>
    <p class="text-slate-500 text-sm mt-1">Ringkasan kinerja koperasi dan persetujuan</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/30">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Anggota</p>
                <h3 class="text-3xl font-bold mt-1">{{ $stats['total_anggota'] }}</h3>
                <p class="text-xs text-blue-200 mt-2">
                    <i class="fas fa-arrow-{{ $stats['perubahan']['anggota'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ abs($stats['perubahan']['anggota']) }} dari bulan lalu
                </p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-xl backdrop-blur-sm">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/30">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-emerald-100 text-sm font-medium">Aset Simpanan</p>
                <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($stats['total_simpanan']/1000000, 1, ',', '.') }}M</h3>
                <p class="text-xs text-emerald-200 mt-2">
                    <i class="fas fa-arrow-{{ $stats['perubahan']['simpanan'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ abs($stats['perubahan']['simpanan']) }}% dari bulan lalu
                </p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-xl backdrop-blur-sm">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-5 text-white shadow-lg shadow-purple-500/30">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Pinjaman Beredar</p>
                <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($stats['total_pinjaman_aktif']/1000000, 1, ',', '.') }}M</h3>
                <p class="text-xs text-purple-200 mt-2">
                    <i class="fas fa-arrow-{{ $stats['perubahan']['pinjaman'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ abs($stats['perubahan']['pinjaman']) }}% dari bulan lalu
                </p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-xl backdrop-blur-sm">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>

    <div class="stat-card bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-5 text-white shadow-lg shadow-amber-500/30">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-amber-100 text-sm font-medium">Estimasi SHU Tahun Ini</p>
                <h3 class="text-2xl font-bold mt-1">Rp {{ number_format($stats['estimasi_shu']/1000000, 1, ',', '.') }}M</h3>
                <p class="text-xs text-amber-200 mt-2">Dari Bunga & Penjualan</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-xl backdrop-blur-sm">
                <i class="fas fa-gift"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Approval Pinjaman -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col h-full">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h3 class="font-bold text-slate-800">
                <i class="fas fa-file-signature text-blue-500 mr-2"></i> Menunggu Persetujuan Pinjaman
            </h3>
            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">{{ count($pending_pinjaman) }}</span>
        </div>
        <div class="p-0 overflow-y-auto max-h-80">
            @forelse($pending_pinjaman as $p)
                <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-sm text-slate-800">{{ $p->anggota->nama_lengkap }}</p>
                        <p class="text-xs text-slate-500">Tujuan: {{ $p->tujuan_pinjaman }}</p>
                        <p class="text-sm font-bold text-primary-600 mt-1">Rp {{ number_format($p->nominal, 0, ',', '.') }} <span class="text-xs font-normal text-slate-400">({{ $p->lama_cicilan }} bulan)</span></p>
                    </div>
                    <a href="{{ route('ketua.approval-pinjaman.index') }}" class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="fas fa-check-circle text-4xl text-green-200 mb-3"></i>
                    <p class="text-sm">Semua pinjaman sudah diproses.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Approval Anggota -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm flex flex-col h-full">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
            <h3 class="font-bold text-slate-800">
                <i class="fas fa-user-plus text-emerald-500 mr-2"></i> Persetujuan Anggota Baru
            </h3>
            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs font-bold">{{ count($pending_anggota) }}</span>
        </div>
        <div class="p-0 overflow-y-auto max-h-80">
            @forelse($pending_anggota as $a)
                <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <img src="{{ $a->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($a->nama_lengkap) }}" class="w-10 h-10 rounded-full border border-slate-200">
                        <div>
                            <p class="font-semibold text-sm text-slate-800">{{ $a->nama_lengkap }}</p>
                            <p class="text-xs text-slate-500">Pekerjaan: {{ $a->pekerjaan }}</p>
                        </div>
                    </div>
                    <a href="{{ route('ketua.approval-anggota.index') }}" class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="fas fa-check-circle text-4xl text-green-200 mb-3"></i>
                    <p class="text-sm">Semua anggota baru sudah diproses.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

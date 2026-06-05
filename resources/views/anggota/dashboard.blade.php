@extends('layouts.anggota')

@section('title', 'Dashboard Anggota')

@section('content')
<div class="mb-5">
    <h1 class="text-xl font-bold text-slate-800">Halo, {{ explode(' ', $user->name)[0] }} 👋</h1>
    <p class="text-slate-500 text-xs mt-1">Selamat datang kembali di Koperasi Sejahtera Bersama</p>
</div>

<!-- Blue Card Simpanan -->
<div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl p-5 text-white shadow-lg shadow-primary-500/30 mb-6 relative overflow-hidden">
    <!-- Dekorasi Lingkaran -->
    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-bl-full -mr-8 -mt-8"></div>
    <div class="absolute bottom-0 left-0 w-24 h-24 bg-black/5 rounded-tr-full -ml-8 -mb-8"></div>
    
    <div class="relative z-10">
        <div class="flex justify-between items-center mb-1">
            <p class="text-primary-100 text-sm font-medium">Total Simpanan</p>
            <button class="text-primary-100 hover:text-white transition"><i class="far fa-eye"></i></button>
        </div>
        <h2 class="text-3xl font-bold mb-4 tracking-tight">Rp {{ number_format($total_simpanan, 0, ',', '.') }}</h2>
        
        <div class="flex justify-between items-center">
            <a href="{{ route('anggota.simpanan.index') }}" class="text-xs font-medium text-white/90 hover:text-white flex items-center gap-1">
                Rincian simpanan <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
            <i class="fas fa-wallet text-white/30 text-4xl absolute right-2 bottom-0"></i>
        </div>
    </div>
</div>

<!-- Grid Cards Sisa Pinjaman & Angsuran -->
<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
        <p class="text-xs text-slate-500 font-medium mb-1">Sisa Pinjaman</p>
        <h3 class="font-bold text-slate-800 text-lg mb-2">Rp {{ number_format($sisa_pinjaman, 0, ',', '.') }}</h3>
        <a href="{{ route('anggota.pinjaman.index') }}" class="text-[10px] font-bold text-primary-600">Lihat Detail ></a>
        <i class="fas fa-hand-holding-dollar absolute right-3 top-3 text-slate-100 text-3xl"></i>
    </div>
    
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm relative overflow-hidden">
        <p class="text-xs text-slate-500 font-medium mb-1">Angsuran Bulan Ini</p>
        <h3 class="font-bold text-slate-800 text-lg mb-1">Rp {{ number_format($angsuran_bulan_ini, 0, ',', '.') }}</h3>
        <p class="text-[9px] text-red-500 font-medium">Jatuh tempo: 25 {{ date('M Y') }}</p>
        <i class="far fa-calendar-alt absolute right-3 top-3 text-slate-100 text-3xl"></i>
    </div>
</div>

<!-- Menu Cepat -->
<div class="mb-6">
    <h3 class="font-bold text-slate-800 text-sm mb-3">Menu Cepat</h3>
    <div class="grid grid-cols-4 gap-3">
        <!-- Ajukan Pinjaman -->
        <a href="{{ route('anggota.pinjaman.index') }}" class="flex flex-col items-center gap-2">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-blue-100">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
            <span class="text-[10px] font-medium text-slate-600 text-center leading-tight">Ajukan<br>Pinjaman</span>
        </a>
        
        <!-- Bayar Angsuran -->
        <a href="{{ route('anggota.pembayaran.index') }}" class="flex flex-col items-center gap-2">
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-green-100">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <span class="text-[10px] font-medium text-slate-600 text-center leading-tight">Bayar<br>Angsuran</span>
        </a>
        
        <!-- Simpanan -->
        <a href="{{ route('anggota.simpanan.index') }}" class="flex flex-col items-center gap-2">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-purple-100">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <span class="text-[10px] font-medium text-slate-600 text-center leading-tight">Data<br>Simpanan</span>
        </a>
        
        <!-- Riwayat -->
        <a href="{{ route('anggota.pembayaran.index') }}" class="flex flex-col items-center gap-2">
            <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-xl shadow-sm border border-orange-100">
                <i class="fas fa-history"></i>
            </div>
            <span class="text-[10px] font-medium text-slate-600 text-center leading-tight">Riwayat<br>Transaksi</span>
        </a>
    </div>
</div>

<!-- Status Pengajuan Terbaru -->
<div class="mb-6">
    <div class="flex justify-between items-end mb-3">
        <h3 class="font-bold text-slate-800 text-sm">Status Pengajuan Terbaru</h3>
        <a href="{{ route('anggota.pinjaman.index') }}" class="text-[10px] font-bold text-primary-600">Lihat Detail ></a>
    </div>
    
    @if($pengajuan_terbaru)
        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex gap-4 items-center relative overflow-hidden">
            <div class="absolute right-0 bottom-0 opacity-10">
                <i class="fas fa-clipboard-check text-6xl -mr-4 -mb-2"></i>
            </div>
            
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-slate-50 border border-slate-100 z-10">
                @if($pengajuan_terbaru->status === 'disetujui' || $pengajuan_terbaru->status === 'lunas')
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                @elseif($pengajuan_terbaru->status === 'ditolak')
                    <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                @else
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                @endif
            </div>
            <div class="flex-1 z-10">
                @php
                    $statusText = 'Menunggu';
                    $statusColor = 'text-yellow-600';
                    if($pengajuan_terbaru->status === 'disetujui' || $pengajuan_terbaru->status === 'lunas') { $statusText = 'Disetujui'; $statusColor = 'text-green-600'; }
                    if($pengajuan_terbaru->status === 'ditolak') { $statusText = 'Ditolak'; $statusColor = 'text-red-600'; }
                @endphp
                <p class="text-[10px] font-bold {{ $statusColor }} uppercase tracking-wider mb-0.5">{{ $statusText }}</p>
                <h4 class="font-bold text-slate-800 text-sm">Pinjaman {{ $pengajuan_terbaru->tujuan_pinjaman }}</h4>
                <p class="text-xs text-slate-500 mt-1">Rp {{ number_format($pengajuan_terbaru->nominal, 0, ',', '.') }} • {{ $pengajuan_terbaru->lama_cicilan }} Bulan</p>
            </div>
        </div>
    @else
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-center">
            <p class="text-xs text-slate-400">Belum ada pengajuan pinjaman.</p>
        </div>
    @endif
</div>

<!-- Pengumuman -->
<div class="mb-2">
    <div class="flex justify-between items-end mb-3">
        <h3 class="font-bold text-slate-800 text-sm">Pengumuman</h3>
        <a href="#" class="text-[10px] font-bold text-primary-600">Lihat Semua</a>
    </div>
    
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
        <h4 class="font-bold text-slate-800 text-sm mb-1">Rapat Anggota Tahunan (RAT)</h4>
        <p class="text-xs text-slate-500 mb-2 leading-relaxed">Rapat anggota tahunan akan dilaksanakan pada tanggal 20 Mei 2024 di Aula Koperasi. Kehadiran sangat diharapkan.</p>
        <p class="text-[10px] text-slate-400 font-medium">2 hari yang lalu</p>
    </div>
</div>
@endsection

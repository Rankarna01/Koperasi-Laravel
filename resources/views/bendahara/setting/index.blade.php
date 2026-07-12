@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('breadcrumb')
    <a href="{{ route('bendahara.dashboard') }}" class="hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-slate-700">Pengaturan</span>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Pengaturan Sistem</h1>
    <p class="text-sm text-slate-500 mt-1">Kelola pengaturan umum koperasi</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Informasi Koperasi</h3>
        </div>
        <form action="{{ route('bendahara.setting.update') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Nama Koperasi</label>
                <input type="text" name="app_name" value="{{ \App\Models\Setting::get('app_name', 'KOPKAR') }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20" required>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Alamat</label>
                <textarea name="company_address" rows="2" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20">{{ \App\Models\Setting::get('company_address', '') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Logo Koperasi</label>
                @if(\App\Models\Setting::get('app_logo'))
                    <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" class="w-16 h-16 rounded-xl mb-2">
                @endif
                <input type="file" name="app_logo" accept="image/*" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm">
            </div>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-xl transition text-sm">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Pengaturan Keuangan</h3>
        </div>
        <form action="{{ route('bendahara.setting.update') }}" method="POST" class="p-4 space-y-4">
            @csrf
            <input type="hidden" name="app_name" value="{{ \App\Models\Setting::get('app_name', 'KOPKAR') }}">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Minimal Saldo Simpanan Pokok (Rp)</label>
                <input type="number" name="minimal_saldo_pokok" value="{{ \App\Models\Setting::get('minimal_saldo_pokok', 500000) }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20" min="0" required>
                <p class="text-[10px] text-slate-400 mt-1">Batas minimal saldo saat anggota melakukan penarikan</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Iuran Wajib Bulanan (Rp)</label>
                <input type="number" name="iuran_wajib_bulanan" value="{{ \App\Models\Setting::get('iuran_wajib_bulanan', 50000) }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20" min="0" required>
                <p class="text-[10px] text-slate-400 mt-1">Nominal iuran wajib yang dibebankan setiap bulan</p>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Bunga Simpanan (% per tahun)</label>
                <input type="number" name="bunga_simpanan_persen" value="{{ \App\Models\Setting::get('bunga_simpanan_persen', 0.2) }}" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-500/20" min="0" max="100" step="0.01" required>
                <p class="text-[10px] text-slate-400 mt-1">Persentase bunga simpanan per tahun (masuk ke SHU)</p>
            </div>
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-6 rounded-xl transition text-sm">
                <i class="fas fa-save mr-1"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection

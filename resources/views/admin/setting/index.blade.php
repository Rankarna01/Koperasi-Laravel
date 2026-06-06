@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Pengaturan</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Pengaturan Sistem</h2>
        <p class="text-slate-500 text-sm mt-1">Konfigurasi nama, logo, dan alamat Koperasi.</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden max-w-3xl">
    <div class="p-6">
        <form action="{{ route('admin.setting.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                <!-- App Name -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Nama Koperasi <span class="text-red-500">*</span></label>
                    <p class="text-[11px] text-slate-500 mb-2">Nama ini akan ditampilkan pada sistem dan aplikasi.</p>
                    <input type="text" name="app_name" value="{{ \App\Models\Setting::get('app_name', 'Koperasi Sejahtera') }}" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                </div>

                <!-- App Logo -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Logo Koperasi</label>
                    <p class="text-[11px] text-slate-500 mb-3">Format JPG, PNG maksimal 2MB. Kosongkan jika tidak ingin mengubah.</p>
                    
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if(\App\Models\Setting::get('app_logo'))
                                <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-building-columns text-3xl text-slate-300"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="app_logo" accept="image/jpeg,image/png" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition cursor-pointer bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea name="company_address" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">{{ \App\Models\Setting::get('company_address') }}</textarea>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

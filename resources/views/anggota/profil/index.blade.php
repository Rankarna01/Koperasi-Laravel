@extends('layouts.anggota')

@section('title', 'Profil Saya')

@section('header-left')
    <a href="{{ route('anggota.dashboard') }}" class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-chevron-left text-lg"></i>
    </a>
    <span class="ml-4 font-bold text-slate-800 text-lg">Profil Saya</span>
@endsection

@section('content')

<div class="flex flex-col items-center mt-6 mb-8">
    <div class="relative mb-4">
        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=120&background=e2e8f0&color=475569' }}" class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
        <button class="absolute bottom-0 right-0 w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center border-2 border-white shadow-md">
            <i class="fas fa-camera text-xs"></i>
        </button>
    </div>
    <h2 class="font-bold text-slate-800 text-xl">{{ auth()->user()->name }}</h2>
    <p class="text-sm text-primary-600 font-medium">No. Anggota: {{ auth()->user()->anggota->no_anggota ?? '-' }}</p>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
    <div class="p-4 border-b border-slate-50 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-500">
            <i class="fas fa-phone-alt text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">No. Telepon</p>
            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->anggota->no_telepon ?? '-' }}</p>
        </div>
    </div>
    <div class="p-4 border-b border-slate-50 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-500">
            <i class="fas fa-envelope text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</p>
            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->email }}</p>
        </div>
    </div>
    <div class="p-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-500">
            <i class="fas fa-map-marker-alt text-sm"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat</p>
            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->anggota->alamat ?? '-' }}</p>
        </div>
    </div>
</div>

<button class="w-full bg-primary-600 text-white rounded-xl py-3.5 font-bold hover:bg-primary-700 transition shadow-lg shadow-primary-500/30 mb-6">
    Ubah Profil
</button>

<div class="border-t border-slate-200 pt-6">
    <button onclick="confirmLogout()" class="w-full flex items-center justify-center gap-2 bg-red-50 text-red-600 rounded-xl py-3.5 font-bold hover:bg-red-100 transition border border-red-100">
        <i class="fas fa-sign-out-alt"></i> Keluar (Logout)
    </button>
</div>

@endsection

@push('scripts')
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Keluar dari akun?',
            text: "Anda harus login kembali untuk mengakses aplikasi.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
@endpush

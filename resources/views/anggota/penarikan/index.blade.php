@extends('layouts.anggota')

@section('title', 'Penarikan Dana')

@section('header-left')
    <a href="{{ route('anggota.dashboard') }}" class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-chevron-left text-lg"></i>
    </a>
    <span class="ml-4 font-bold text-slate-800 text-lg">Penarikan Dana</span>
@endsection

@section('content')

@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
        <div class="flex items-center gap-2 mb-2">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <p class="text-sm font-bold text-red-700">Terjadi kesalahan</p>
        </div>
        <ul class="list-disc list-inside text-xs text-red-600 space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>
            <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
        </div>
    </div>
@endif

<!-- Status Penarikan Aktif -->
@php
    $penarikanAktif = \App\Models\PenarikanDana::where('anggota_id', auth()->user()->anggota->id)
        ->whereIn('status', ['menunggu_bendahara', 'disetujui_ketua', 'diproses'])
        ->latest()
        ->first();
@endphp

@if($penarikanAktif)
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-clock text-blue-500 text-sm"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800">Penarikan Sedang Diproses</p>
            <p class="text-[10px] text-slate-400">{{ $penarikanAktif->no_penarikan }}</p>
        </div>
    </div>

    <!-- Timeline -->
    <div class="relative pl-6">
        <!-- Garis -->
        <div class="absolute left-[9px] top-2 bottom-2 w-0.5 bg-slate-200"></div>

        <!-- Step 1: Diajukan -->
        <div class="relative flex items-start mb-4">
            <div class="absolute -left-6 w-[18px] h-[18px] rounded-full flex items-center justify-center
                @if(in_array($penarikanAktif->status, ['menunggu_bendahara', 'disetujui_ketua', 'diproses', 'selesai'])) bg-green-500 @else bg-slate-300 @endif">
                <i class="fas fa-check text-white text-[8px]"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-800">Diajukan</p>
                <p class="text-[10px] text-slate-400">{{ $penarikanAktif->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <!-- Step 2: Verifikasi Bendahara -->
        <div class="relative flex items-start mb-4">
            <div class="absolute -left-6 w-[18px] h-[18px] rounded-full flex items-center justify-center
                @if(in_array($penarikanAktif->status, ['disetujui_ketua', 'diproses', 'selesai'])) bg-green-500
                @elseif($penarikanAktif->status === 'menunggu_bendahara') bg-yellow-400 animate-pulse
                @else bg-slate-300 @endif">
                @if(in_array($penarikanAktif->status, ['disetujui_ketua', 'diproses', 'selesai']))
                    <i class="fas fa-check text-white text-[8px]"></i>
                @else
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                @endif
            </div>
            <div>
                <p class="text-xs font-bold {{ $penarikanAktif->status === 'menunggu_bendahara' ? 'text-yellow-600' : 'text-slate-800' }}">
                    Verifikasi Bendahara
                    @if($penarikanAktif->status === 'menunggu_bendahara')
                        <span class="text-[9px] bg-yellow-100 text-yellow-600 px-1.5 py-0.5 rounded-full ml-1">Proses</span>
                    @endif
                </p>
                @if($penarikanAktif->verified_at)
                    <p class="text-[10px] text-slate-400">{{ $penarikanAktif->verified_at->format('d M Y, H:i') }}</p>
                @endif
            </div>
        </div>

        <!-- Step 3: ACC Ketua -->
        <div class="relative flex items-start mb-4">
            <div class="absolute -left-6 w-[18px] h-[18px] rounded-full flex items-center justify-center
                @if(in_array($penarikanAktif->status, ['diproses', 'selesai'])) bg-green-500
                @elseif($penarikanAktif->status === 'disetujui_ketua') bg-yellow-400 animate-pulse
                @else bg-slate-300 @endif">
                @if(in_array($penarikanAktif->status, ['diproses', 'selesai']))
                    <i class="fas fa-check text-white text-[8px]"></i>
                @else
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                @endif
            </div>
            <div>
                <p class="text-xs font-bold {{ $penarikanAktif->status === 'disetujui_ketua' ? 'text-yellow-600' : 'text-slate-800' }}">
                    ACC Ketua
                    @if($penarikanAktif->status === 'disetujui_ketua')
                        <span class="text-[9px] bg-yellow-100 text-yellow-600 px-1.5 py-0.5 rounded-full ml-1">Proses</span>
                    @endif
                </p>
                @if($penarikanAktif->approved_at)
                    <p class="text-[10px] text-slate-400">{{ $penarikanAktif->approved_at->format('d M Y, H:i') }}</p>
                @endif
            </div>
        </div>

        <!-- Step 4: Proses Transfer -->
        <div class="relative flex items-start">
            <div class="absolute -left-6 w-[18px] h-[18px] rounded-full flex items-center justify-center
                @if($penarikanAktif->status === 'selesai') bg-green-500
                @elseif($penarikanAktif->status === 'diproses') bg-yellow-400 animate-pulse
                @else bg-slate-300 @endif">
                @if($penarikanAktif->status === 'selesai')
                    <i class="fas fa-check text-white text-[8px]"></i>
                @else
                    <div class="w-2 h-2 bg-white rounded-full"></div>
                @endif
            </div>
            <div>
                <p class="text-xs font-bold {{ $penarikanAktif->status === 'diproses' ? 'text-yellow-600' : 'text-slate-800' }}">
                    {{ $penarikanAktif->metode_pembayaran === 'transfer' ? 'Proses Transfer' : 'Penyerahan Cash' }}
                    @if($penarikanAktif->status === 'diproses')
                        <span class="text-[9px] bg-yellow-100 text-yellow-600 px-1.5 py-0.5 rounded-full ml-1">Proses</span>
                    @endif
                </p>
                @if($penarikanAktif->tanggal_proses)
                    <p class="text-[10px] text-slate-400">{{ $penarikanAktif->tanggal_proses->format('d M Y') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center">
        <p class="text-xs text-slate-500">Nominal</p>
        <p class="text-sm font-bold text-slate-800">Rp {{ number_format($penarikanAktif->nominal, 0, ',', '.') }}</p>
    </div>
</div>
@endif

<!-- Saldo Card -->
<div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/30 mb-6 relative overflow-hidden">
    <div class="relative z-10">
        <p class="text-blue-100 text-sm font-medium mb-1">Saldo Simpanan</p>
        <h2 class="text-3xl font-bold tracking-tight">Rp {{ number_format($saldoSimpanan, 0, ',', '.') }}</h2>
        <p class="text-blue-200 text-xs mt-1">Minimal saldo pokok: Rp {{ number_format($minimalPokok, 0, ',', '.') }}</p>
    </div>
    <i class="fas fa-money-bill-wave text-white/10 text-6xl absolute right-0 bottom-0 -mb-4 -mr-2"></i>
</div>

<!-- Form Penarikan -->
<form action="{{ route('anggota.penarikan.store') }}" method="POST" id="formPenarikan">
    @csrf
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
        <h3 class="font-bold text-slate-800 text-sm mb-4">Form Pengajuan Penarikan</h3>

        <div class="mb-4">
            <label class="block text-xs font-medium text-slate-600 mb-1">Nominal Penarikan</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                <input type="number" name="nominal" id="nominal" min="10000"
                    class="w-full pl-12 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400 @error('nominal') border-red-300 bg-red-50 @enderror"
                    placeholder="Masukkan nominal" value="{{ old('nominal') }}">
            </div>
            @error('nominal') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
            <p class="text-[10px] text-slate-400 mt-1">Sisa saldo: <span id="sisaSaldo" class="font-medium text-slate-600">Rp {{ number_format($saldoSimpanan, 0, ',', '.') }}</span></p>
        </div>

        <div class="mb-4">
            <label class="block text-xs font-medium text-slate-600 mb-1">Metode Pembayaran</label>
            <div class="flex gap-3">
                <label class="flex-1">
                    <input type="radio" name="metode_pembayaran" value="cash" class="peer hidden" {{ old('metode_pembayaran', 'cash') == 'cash' ? 'checked' : '' }}>
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                        <i class="fas fa-money-bill text-lg text-slate-400 peer-checked:text-blue-500"></i>
                        <p class="text-xs font-medium text-slate-600 mt-1">Cash</p>
                    </div>
                </label>
                <label class="flex-1">
                    <input type="radio" name="metode_pembayaran" value="transfer" class="peer hidden" {{ old('metode_pembayaran') == 'transfer' ? 'checked' : '' }}>
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 transition">
                        <i class="fas fa-university text-lg text-slate-400 peer-checked:text-blue-500"></i>
                        <p class="text-xs font-medium text-slate-600 mt-1">Transfer</p>
                    </div>
                </label>
            </div>
            @error('metode_pembayaran') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
        </div>

        <div id="rekeningFields" class="{{ old('metode_pembayaran') != 'transfer' ? 'hidden' : '' }}">
            <label class="block text-xs font-medium text-slate-600 mb-2">Data Rekening Bank</label>
            <div class="mb-2">
                <input type="text" name="rekening_bank[nama_bank]" placeholder="Nama Bank (contoh: BCA, Mandiri)"
                    value="{{ old('rekening_bank.nama_bank') }}"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('rekening_bank.nama_bank') border-red-300 bg-red-50 @enderror">
                @error('rekening_bank.nama_bank') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
            </div>
            <div class="mb-2">
                <input type="text" name="rekening_bank[no_rekening]" placeholder="Nomor Rekening"
                    value="{{ old('rekening_bank.no_rekening') }}"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('rekening_bank.no_rekening') border-red-300 bg-red-50 @enderror">
                @error('rekening_bank.no_rekening') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
            </div>
            <div>
                <input type="text" name="rekening_bank[nama_rekening]" placeholder="Nama Pemilik Rekening"
                    value="{{ old('rekening_bank.nama_rekening') }}"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 @error('rekening_bank.nama_rekening') border-red-300 bg-red-50 @enderror">
                @error('rekening_bank.nama_rekening') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-4 mb-4">
            <label class="block text-xs font-medium text-slate-600 mb-1">Keterangan (Opsional)</label>
            <textarea name="keterangan" rows="2" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" placeholder="Alasan penarikan...">{{ old('keterangan') }}</textarea>
        </div>
    </div>

    <div class="mb-6" style="padding-bottom: 80px;">
        <button type="submit" id="btnSubmit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl transition text-sm shadow-lg shadow-blue-500/30 relative z-10">
            <span id="btnText"><i class="fas fa-paper-plane mr-1"></i> Ajukan Penarikan</span>
            <span id="btnLoading" class="hidden"><i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...</span>
        </button>
    </div>
</form>

<!-- Riwayat -->
@if($riwayat->isNotEmpty())
<div class="mb-6">
    <h3 class="font-bold text-slate-800 text-sm mb-3">Riwayat Penarikan</h3>
    <div class="space-y-3">
        @foreach($riwayat as $item)
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $item->no_penarikan }}</p>
                        <p class="text-[10px] text-slate-400">{{ $item->created_at->format('d M Y') }} • {{ ucfirst($item->metode_pembayaran) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-sm text-red-500">-Rp {{ number_format($item->nominal, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] badge bg-{{ $item->badge_status }}">{{ $item->label_status }}</span>
                    @if($item->status === 'selesai' && $item->metode_pembayaran === 'transfer' && $item->bukti_transfer)
                        <span class="text-[10px] text-green-600 flex items-center gap-1"><i class="fas fa-check-circle"></i> Transfer Berhasil</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    var saldo = {{ $saldoSimpanan }};
    var minimalPokok = {{ $minimalPokok }};

    $('input[name="metode_pembayaran"]').change(function() {
        if ($(this).val() === 'transfer') {
            $('#rekeningFields').removeClass('hidden');
        } else {
            $('#rekeningFields').addClass('hidden');
        }
    });

    $('#nominal').on('input', function() {
        var nominal = parseFloat($(this).val()) || 0;
        var sisa = saldo - nominal;
        $('#sisaSaldo').text('Rp ' + sisa.toLocaleString('id-ID'));
        if (sisa < 0) {
            $('#sisaSaldo').removeClass('text-slate-600').addClass('text-red-500 font-bold');
        } else if (sisa < minimalPokok) {
            $('#sisaSaldo').removeClass('text-red-500 font-bold').addClass('text-yellow-500 font-bold');
        } else {
            $('#sisaSaldo').removeClass('text-red-500 font-bold text-yellow-500').addClass('text-slate-600');
        }
    });

    $('#formPenarikan').on('submit', function(e) {
        var nominal = parseFloat($('#nominal').val()) || 0;
        var metode = $('input[name="metode_pembayaran"]:checked').val();

        if (nominal <= 0) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Nominal kosong', text: 'Masukkan nominal penarikan terlebih dahulu.', confirmButtonColor: '#2563eb' });
            return false;
        }

        if (nominal < 10000) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Nominal terlalu kecil', text: 'Minimal penarikan adalah Rp 10.000.', confirmButtonColor: '#2563eb' });
            return false;
        }

        var sisa = saldo - nominal;
        if (sisa < 0) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'Saldo tidak mencukupi', text: 'Saldo simpanan Anda tidak cukup untuk penarikan ini.', confirmButtonColor: '#2563eb' });
            return false;
        }

        if (sisa < minimalPokok) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'Saldo minimal terlampaui', text: 'Penarikan ini akan membuat saldo Anda kurang dari Rp ' + minimalPokok.toLocaleString('id-ID') + ' (minimal simpanan pokok).', confirmButtonColor: '#2563eb' });
            return false;
        }

        if (metode === 'transfer') {
            var namaBank = $('input[name="rekening_bank[nama_bank]"]').val().trim();
            var noRekening = $('input[name="rekening_bank[no_rekening]"]').val().trim();
            var namaRekening = $('input[name="rekening_bank[nama_rekening]"]').val().trim();

            if (!namaBank || !noRekening || !namaRekening) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Data rekening belum lengkap', text: 'Lengkapi nama bank, nomor rekening, dan nama pemilik rekening.', confirmButtonColor: '#2563eb' });
                return false;
            }
        }

        $('#btnText').addClass('hidden');
        $('#btnLoading').removeClass('hidden');
        $('#btnSubmit').prop('disabled', true).removeClass('bg-blue-600 hover:bg-blue-700').addClass('bg-blue-400 cursor-not-allowed');
    });
});
</script>
@endpush

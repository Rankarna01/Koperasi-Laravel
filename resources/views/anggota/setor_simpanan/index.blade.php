@extends('layouts.anggota')

@section('title', 'Setor Simpanan')

@section('header-left')
    <a href="{{ route('anggota.dashboard') }}" class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-chevron-left text-lg"></i>
    </a>
    <span class="ml-4 font-bold text-slate-800 text-lg">Setor Simpanan</span>
@endsection

@section('content')

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-6">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>
            <p class="text-sm font-medium text-green-700">{{ session('success') }}</p>
        </div>
    </div>
@endif

<!-- Status Setor Aktif -->
@php
    $setorAktif = \App\Models\SetorSimpanan::where('anggota_id', auth()->user()->anggota->id)
        ->where('status', 'menunggu_bendahara')
        ->latest()
        ->first();
@endphp

@if($setorAktif)
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
            <i class="fas fa-clock text-yellow-500 text-sm"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-slate-800">Setor Sedang Diproses</p>
            <p class="text-[10px] text-slate-400">{{ $setorAktif->no_setor }}</p>
        </div>
    </div>

    <!-- Timeline -->
    <div class="relative pl-6">
        <div class="absolute left-[9px] top-2 bottom-2 w-0.5 bg-slate-200"></div>

        <!-- Step 1: Diajukan -->
        <div class="relative flex items-start mb-4">
            <div class="absolute -left-6 w-[18px] h-[18px] rounded-full flex items-center justify-center bg-green-500">
                <i class="fas fa-check text-white text-[8px]"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-800">Diajukan</p>
                <p class="text-[10px] text-slate-400">{{ $setorAktif->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <!-- Step 2: Verifikasi Bendahara -->
        <div class="relative flex items-start">
            <div class="absolute -left-6 w-[18px] h-[18px] rounded-full flex items-center justify-center bg-yellow-400 animate-pulse">
                <div class="w-2 h-2 bg-white rounded-full"></div>
            </div>
            <div>
                <p class="text-xs font-bold text-yellow-600">
                    Verifikasi Bendahara
                    <span class="text-[9px] bg-yellow-100 text-yellow-600 px-1.5 py-0.5 rounded-full ml-1">Proses</span>
                </p>
            </div>
        </div>
    </div>

    <div class="mt-4 pt-3 border-t border-slate-100 flex justify-between items-center">
        <div>
            <p class="text-[10px] text-slate-400">{{ $setorAktif->label_jenis }} • {{ ucfirst($setorAktif->metode_pembayaran) }}</p>
        </div>
        <p class="text-sm font-bold text-green-600">+Rp {{ number_format($setorAktif->nominal, 0, ',', '.') }}</p>
    </div>
</div>
@endif

<!-- Saldo Card -->
<div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-5 text-white shadow-lg shadow-green-500/30 mb-6 relative overflow-hidden">
    <div class="relative z-10">
        <p class="text-green-100 text-sm font-medium mb-1">Saldo Simpanan</p>
        <h2 class="text-3xl font-bold tracking-tight">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h2>
    </div>
    <i class="fas fa-piggy-bank text-white/10 text-6xl absolute right-0 bottom-0 -mb-4 -mr-2"></i>
</div>

<!-- Form Setor Simpanan -->
<form action="{{ route('anggota.setor_simpanan.store') }}" method="POST" enctype="multipart/form-data" id="formSetor">
    @csrf
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 mb-6">
        <h3 class="font-bold text-slate-800 text-sm mb-4">Form Setor Simpanan</h3>

        <!-- Jenis Simpanan -->
        <div class="mb-4">
            <label class="block text-xs font-medium text-slate-600 mb-2">Jenis Simpanan</label>
            <div class="grid grid-cols-2 gap-2">
                <label>
                    <input type="radio" name="jenis_simpanan" value="wajib" class="peer hidden" checked>
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                        <i class="fas fa-clipboard-check text-lg text-slate-400 peer-checked:text-green-500"></i>
                        <p class="text-[10px] font-medium text-slate-600 mt-1">Wajib</p>
                    </div>
                </label>
                <label>
                    <input type="radio" name="jenis_simpanan" value="sukarela" class="peer hidden">
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                        <i class="fas fa-hand-holding-heart text-lg text-slate-400 peer-checked:text-green-500"></i>
                        <p class="text-[10px] font-medium text-slate-600 mt-1">Sukarela</p>
                    </div>
                </label>
                <label>
                    <input type="radio" name="jenis_simpanan" value="pokok" class="peer hidden">
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                        <i class="fas fa-coins text-lg text-slate-400 peer-checked:text-green-500"></i>
                        <p class="text-[10px] font-medium text-slate-600 mt-1">Pokok</p>
                    </div>
                </label>
                <label>
                    <input type="radio" name="jenis_simpanan" value="deposito" class="peer hidden">
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                        <i class="fas fa-piggy-bank text-lg text-slate-400 peer-checked:text-green-500"></i>
                        <p class="text-[10px] font-medium text-slate-600 mt-1">Deposito</p>
                    </div>
                </label>
            </div>
            @error('jenis_simpanan') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
        </div>

        <!-- Nominal -->
        <div class="mb-4">
            <label class="block text-xs font-medium text-slate-600 mb-1">Nominal Setor</label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                <input type="number" name="nominal" id="nominal" min="10000"
                    class="w-full pl-12 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 @error('nominal') border-red-300 bg-red-50 @enderror"
                    placeholder="Masukkan nominal" value="{{ old('nominal') }}">
            </div>
            @error('nominal') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
            <p class="text-[10px] text-slate-400 mt-1">Minimal setor: Rp 10.000</p>
        </div>

        <!-- Metode Pembayaran -->
        <div class="mb-4">
            <label class="block text-xs font-medium text-slate-600 mb-2">Metode Pembayaran</label>
            <div class="flex gap-3">
                <label class="flex-1">
                    <input type="radio" name="metode_pembayaran" value="transfer" class="peer hidden" {{ old('metode_pembayaran') == 'transfer' ? 'checked' : '' }}>
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                        <i class="fas fa-university text-lg text-slate-400 peer-checked:text-green-500"></i>
                        <p class="text-xs font-medium text-slate-600 mt-1">Transfer</p>
                    </div>
                </label>
                <label class="flex-1">
                    <input type="radio" name="metode_pembayaran" value="cash" class="peer hidden" {{ old('metode_pembayaran', 'cash') == 'cash' ? 'checked' : '' }}>
                    <div class="p-3 border-2 border-slate-200 rounded-xl text-center cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 transition">
                        <i class="fas fa-money-bill text-lg text-slate-400 peer-checked:text-green-500"></i>
                        <p class="text-xs font-medium text-slate-600 mt-1">Cash</p>
                    </div>
                </label>
            </div>
            @error('metode_pembayaran') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
        </div>

        <!-- Bukti Transfer (only for transfer) -->
        <div id="buktiFields" class="{{ old('metode_pembayaran') != 'transfer' ? 'hidden' : '' }}">
            <div class="mb-4">
                <label class="block text-xs font-medium text-slate-600 mb-1">Bukti Transfer</label>
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center hover:border-green-300 transition">
                    <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/*" class="hidden" @error('bukti_transfer') border-red-300 @enderror>
                    <label for="bukti_transfer" class="cursor-pointer">
                        <div id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt text-2xl text-slate-300 mb-2"></i>
                            <p class="text-xs text-slate-500">Klik untuk upload bukti transfer</p>
                            <p class="text-[10px] text-slate-400 mt-1">JPG, PNG (maks. 2MB)</p>
                        </div>
                        <div id="uploadPreview" class="hidden">
                            <img id="previewImg" class="max-h-32 mx-auto rounded-lg mb-2">
                            <p id="previewName" class="text-xs text-green-600 font-medium"></p>
                        </div>
                    </label>
                </div>
                @error('bukti_transfer') <p class="text-red-500 text-xs mt-1 flex items-center gap-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Keterangan -->
        <div class="mb-2">
            <label class="block text-xs font-medium text-slate-600 mb-1">Keterangan (Opsional)</label>
            <textarea name="keterangan" rows="2" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20" placeholder="Catatan untuk setor simpanan...">{{ old('keterangan') }}</textarea>
        </div>
    </div>

    <div class="mb-6" style="padding-bottom: 80px;">
        <button type="submit" id="btnSubmit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-2xl transition text-sm shadow-lg shadow-green-500/30 relative z-10">
            <span id="btnText"><i class="fas fa-paper-plane mr-1"></i> Ajukan Setor Simpanan</span>
            <span id="btnLoading" class="hidden"><i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...</span>
        </button>
    </div>
</form>

<!-- Riwayat Setor Simpanan -->
<div class="mb-6">
    <h3 class="font-bold text-slate-800 text-sm mb-3">Riwayat Setor Simpanan</h3>
    <div id="riwayatList" class="space-y-3">
        <div class="text-center py-6 text-xs text-slate-400">Memuat data...</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // Toggle bukti transfer
    $('input[name="metode_pembayaran"]').change(function() {
        if ($(this).val() === 'transfer') {
            $('#buktiFields').removeClass('hidden');
        } else {
            $('#buktiFields').addClass('hidden');
        }
    });

    // Preview bukti transfer
    $('#bukti_transfer').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#previewName').text(file.name);
                $('#uploadPlaceholder').addClass('hidden');
                $('#uploadPreview').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Load riwayat
    loadRiwayat();

    // Form submit via AJAX
    $('#formSetor').on('submit', function(e) {
        e.preventDefault();

        var nominal = parseFloat($('#nominal').val()) || 0;
        var metode = $('input[name="metode_pembayaran"]:checked').val();

        if (nominal <= 0) {
            Swal.fire({ icon: 'warning', title: 'Nominal kosong', text: 'Masukkan nominal setor terlebih dahulu.', confirmButtonColor: '#16a34a' });
            return false;
        }

        if (nominal < 10000) {
            Swal.fire({ icon: 'warning', title: 'Nominal terlalu kecil', text: 'Minimal setor adalah Rp 10.000.', confirmButtonColor: '#16a34a' });
            return false;
        }

        if (metode === 'transfer') {
            var fileInput = document.getElementById('bukti_transfer');
            if (!fileInput.files.length) {
                Swal.fire({ icon: 'warning', title: 'Bukti transfer belum diupload', text: 'Upload bukti transfer terlebih dahulu.', confirmButtonColor: '#16a34a' });
                return false;
            }
        }

        var formData = new FormData(this);

        $('#btnText').addClass('hidden');
        $('#btnLoading').removeClass('hidden');
        $('#btnSubmit').prop('disabled', true).removeClass('bg-green-600 hover:bg-green-700').addClass('bg-green-400 cursor-not-allowed');

        $.ajax({
            url: '{{ route("anggota.setor_simpanan.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        confirmButtonColor: '#16a34a',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                }
            },
            error: function(xhr) {
                $('#btnText').removeClass('hidden');
                $('#btnLoading').addClass('hidden');
                $('#btnSubmit').prop('disabled', false).removeClass('bg-green-400 cursor-not-allowed').addClass('bg-green-600 hover:bg-green-700');

                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var msg = Object.values(errors).flat().join('\n');
                    Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: msg, confirmButtonColor: '#16a34a' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan. Silakan coba lagi.', confirmButtonColor: '#16a34a' });
                }
            }
        });
    });

    function loadRiwayat() {
        $.ajax({
            url: '{{ route("anggota.setor_simpanan.data") }}',
            type: 'GET',
            success: function(res) {
                var html = '';
                if (res.data && res.data.length > 0) {
                    res.data.forEach(function(item) {
                        html += `
                            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="text-sm font-medium text-slate-800">${item.no_setor}</p>
                                        <p class="text-[10px] text-slate-400">${item.created_at} • ${item.jenis_simpanan}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-sm text-green-500">+Rp ${item.nominal}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold ${item.status === 'selesai' ? 'bg-green-100 text-green-700' : item.status === 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'}">${item.label_status}</span>
                                    <span class="text-[10px] text-slate-400 capitalize">${item.metode_pembayaran}</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="text-center py-6 text-xs text-slate-400">Belum ada riwayat setor simpanan.</div>';
                }
                $('#riwayatList').html(html);
            },
            error: function() {
                $('#riwayatList').html('<div class="text-center py-6 text-xs text-red-400">Gagal memuat data.</div>');
            }
        });
    }
});
</script>
@endpush

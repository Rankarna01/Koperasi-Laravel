@extends('layouts.anggota')

@section('title', 'Pinjaman Saya')

@section('header-left')
    <a href="{{ route('anggota.dashboard') }}" class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-chevron-left text-lg"></i>
    </a>
    <span class="ml-4 font-bold text-slate-800 text-lg">Ajukan Pinjaman</span>
@endsection

@section('header-right')
    <button class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-file-download text-lg"></i>
    </button>
@endsection

@section('content')

<!-- Progress indicator -->
<div class="flex justify-between items-center mb-8 px-4 relative">
    <div class="absolute top-3 left-8 right-8 h-0.5 bg-slate-200 -z-10"></div>
    <div class="absolute top-3 left-8 w-1/3 h-0.5 bg-primary-600 -z-10"></div>
    
    <div class="flex flex-col items-center gap-1">
        <div class="w-6 h-6 rounded-full bg-primary-600 text-white text-[10px] flex items-center justify-center font-bold">1</div>
        <span class="text-[9px] font-bold text-primary-600">Data Diri</span>
    </div>
    <div class="flex flex-col items-center gap-1">
        <div class="w-6 h-6 rounded-full bg-primary-600 text-white text-[10px] flex items-center justify-center font-bold">2</div>
        <span class="text-[9px] font-bold text-primary-600">Detail</span>
    </div>
    <div class="flex flex-col items-center gap-1">
        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-400 text-[10px] flex items-center justify-center font-bold">3</div>
        <span class="text-[9px] font-medium text-slate-400">Dokumen</span>
    </div>
    <div class="flex flex-col items-center gap-1">
        <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-400 text-[10px] flex items-center justify-center font-bold">4</div>
        <span class="text-[9px] font-medium text-slate-400">Konfirmasi</span>
    </div>
</div>

<h3 class="font-bold text-slate-800 text-base mb-4">Detail Pinjaman</h3>

<form id="formPinjaman" action="{{ route('anggota.pinjaman.store') }}" method="POST">
    @csrf
    
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Jenis Pinjaman</label>
            <div class="relative">
                <select class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm appearance-none font-medium text-slate-700">
                    <option>Pinjaman Modal Usaha</option>
                    <option>Pinjaman Pendidikan</option>
                    <option>Pinjaman Darurat</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Jumlah Pinjaman</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-800 font-bold">Rp</span>
                <input type="text" id="inputNominal" onkeyup="formatCurrency(this)" required placeholder="15.000.000" class="w-full pl-10 pr-4 py-3.5 bg-white border border-slate-200 rounded-xl text-base font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                <input type="hidden" name="nominal" id="rawNominal">
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Tenor Pinjaman</label>
            <div class="relative">
                <select name="lama_cicilan" id="inputTenor" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm appearance-none font-medium text-slate-700">
                    <option value="6">6 Bulan</option>
                    <option value="12" selected>12 Bulan</option>
                    <option value="18">18 Bulan</option>
                    <option value="24">24 Bulan</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Tujuan Pinjaman</label>
            <textarea name="tujuan_pinjaman" required rows="3" placeholder="Modal usaha toko kelontong..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm font-medium text-slate-700"></textarea>
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Metode Pembayaran (Pencairan)</label>
            <div class="relative">
                <select name="metode_pembayaran" id="metodePembayaran" required onchange="toggleTransferFields()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm appearance-none font-medium text-slate-700">
                    <option value="cash">Tunai / Cash</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
            </div>
        </div>

        <div id="transferFields" class="hidden space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Nama Bank / E-Wallet</label>
                <input type="text" name="nama_bank" id="namaBank" placeholder="Contoh: BCA / BRI / OVO" class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Nomor Rekening / No. HP</label>
                <input type="number" name="nomor_rekening" id="nomorRekening" placeholder="Masukkan nomor rekening valid" class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
            </div>
        </div>
    </div>

    <div class="mt-8 mb-6">
        <button type="submit" id="btnProses" class="w-full bg-primary-600 text-white rounded-xl py-3.5 font-bold hover:bg-primary-700 transition shadow-lg shadow-primary-500/30">
            Lanjutkan
        </button>
    </div>
</form>

<div class="mt-8 pt-6 border-t border-slate-100">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-slate-800 text-sm">Riwayat Pinjaman</h3>
    </div>
    
    <div class="space-y-3">
        @forelse(\App\Models\Peminjaman::where('anggota_id', auth()->user()->anggota->id)->latest()->get() as $p)
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-bold text-slate-800 text-sm">{{ $p->tujuan_pinjaman }}</p>
                        <p class="text-[10px] text-slate-500">{{ $p->tanggal_pengajuan->format('d M Y') }}</p>
                    </div>
                    @if($p->status === 'disetujui' || $p->status === 'lunas')
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-[10px] font-bold uppercase">Disetujui</span>
                    @elseif($p->status === 'ditolak')
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase">Ditolak</span>
                    @else
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-[10px] font-bold uppercase">Proses</span>
                    @endif
                </div>
                <div class="flex justify-between items-end">
                    <p class="font-bold text-primary-600">Rp {{ number_format($p->nominal, 0, ',', '.') }}</p>
                    <p class="text-xs font-medium text-slate-600">{{ $p->lama_cicilan }} Bln</p>
                </div>
            </div>
        @empty
            <p class="text-center text-xs text-slate-400 py-4">Belum ada riwayat pinjaman.</p>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script>
    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        if(value !== '') {
            $('#rawNominal').val(value);
            input.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            $('#rawNominal').val(0);
        }
    }

    function toggleTransferFields() {
        const metode = document.getElementById('metodePembayaran').value;
        const transferFields = document.getElementById('transferFields');
        const namaBank = document.getElementById('namaBank');
        const nomorRekening = document.getElementById('nomorRekening');
        
        if(metode === 'transfer') {
            transferFields.classList.remove('hidden');
            namaBank.required = true;
            nomorRekening.required = true;
        } else {
            transferFields.classList.add('hidden');
            namaBank.required = false;
            nomorRekening.required = false;
            namaBank.value = '';
            nomorRekening.value = '';
        }
    }

    $('#formPinjaman').on('submit', function(e) {
        e.preventDefault();
        
        const nominal = parseInt($('#rawNominal').val()) || 0;
        if(nominal < 1000000) {
            showToast('error', 'Minimal pinjaman Rp 1.000.000');
            return;
        }

        const btn = $('#btnProses');
        const originalText = btn.html();
        btn.html('<i class="fas fa-circle-notch fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    $('#formPinjaman')[0].reset();
                    showToast('success', res.message);
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
</script>
@endpush

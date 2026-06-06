@extends('layouts.anggota')

@section('title', 'Pembayaran Angsuran')

@section('header-left')
    <a href="{{ route('anggota.dashboard') }}" class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-chevron-left text-lg"></i>
    </a>
    <span class="ml-4 font-bold text-slate-800 text-lg">Pembayaran</span>
@endsection

@section('content')

<div class="mb-6 mt-2">
    <div class="bg-gradient-to-br from-primary-600 to-primary-800 p-6 rounded-2xl text-white shadow-lg shadow-primary-500/30 relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-sm font-medium text-primary-100 mb-1">Bayar Angsuran</h3>
            <p class="text-xs text-primary-100 mb-4 max-w-[85%] leading-relaxed">Pilih pinjaman aktif Anda dan bayar angsuran bulan ini dengan praktis secara online.</p>
            <button onclick="openModal()" class="bg-white text-primary-600 px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 transition flex items-center gap-2">
                <i class="fas fa-wallet"></i> Bayar Sekarang
            </button>
        </div>
        <i class="fas fa-hand-holding-dollar absolute -bottom-4 -right-2 text-8xl text-white opacity-10"></i>
    </div>
</div>

<div class="mt-8 pt-2">
    <div class="flex justify-between items-center mb-4 px-1">
        <h3 class="font-bold text-slate-800 text-sm">Riwayat Pembayaran</h3>
    </div>
    
    <div class="space-y-3">
        @php
            $history = \App\Models\Angsuran::whereHas('peminjaman', function($q) {
                $q->where('anggota_id', auth()->user()->anggota->id);
            })->latest('tanggal_bayar')->latest('id')->get();
        @endphp

        @forelse($history as $a)
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm relative overflow-hidden">
                <!-- Status bar left -->
                <div class="absolute left-0 top-0 bottom-0 w-1 {{ $a->status === 'berhasil' ? 'bg-emerald-500' : ($a->status === 'pending' ? 'bg-amber-500' : 'bg-red-500') }}"></div>
                
                <div class="pl-2">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-bold text-slate-800 text-xs mb-0.5">Angsuran Ke-{{ $a->angsuran_ke }}</p>
                            <p class="text-[10px] text-slate-500 font-mono">{{ $a->peminjaman->no_pinjaman }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1.5">
                            @if($a->status === 'berhasil')
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[9px] font-bold uppercase tracking-wider border border-emerald-200">Berhasil</span>
                            @elseif($a->status === 'pending')
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-bold uppercase tracking-wider border border-amber-200">Menunggu</span>
                            @else
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded text-[9px] font-bold uppercase tracking-wider border border-red-200">Gagal</span>
                            @endif
                            <p class="text-[9px] text-slate-400"><i class="far fa-calendar-alt mr-1"></i>{{ $a->tanggal_bayar->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-end mt-1">
                        <div>
                            <p class="text-[9px] text-slate-400 mb-0.5 uppercase tracking-wider">Nominal Dibayar</p>
                            <p class="font-extrabold text-primary-600 text-sm">Rp {{ number_format($a->nominal, 0, ',', '.') }}</p>
                        </div>
                        
                        @if($a->status === 'pending' && $a->snap_token)
                            <button onclick="lanjutkanBayar('{{ $a->snap_token }}')" class="px-3 py-1.5 bg-primary-600 text-white rounded-lg text-[10px] font-bold shadow-md shadow-primary-500/30 hover:bg-primary-700 transition flex items-center gap-1.5">
                                <i class="fas fa-credit-card"></i> Bayar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-dashed border-slate-300 p-8 text-center">
                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-receipt text-slate-400 text-lg"></i>
                </div>
                <p class="text-sm font-bold text-slate-600 mb-1">Belum Ada Riwayat</p>
                <p class="text-[11px] text-slate-400">Anda belum melakukan pembayaran angsuran apapun.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="modal-backdrop hidden flex items-end sm:items-center justify-center p-0 sm:p-4 z-50">
    <div class="modal-content bg-white w-full max-w-md sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Bayar Angsuran</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="mainForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pinjaman Aktif <span class="text-red-500">*</span></label>
                        <select name="peminjaman_id" id="peminjaman_id" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition text-sm">
                            <option value="">-- Pilih Pinjaman --</option>
                            @foreach($pinjamanList as $p)
                                <option value="{{ $p->id }}" data-angsuran="{{ $p->angsuran_per_bulan }}">
                                    Pinjaman Rp {{ number_format($p->nominal, 0, ',', '.') }} - Angsuran Rp {{ number_format($p->angsuran_per_bulan, 0, ',', '.') }} (Sisa: {{ $p->lama_cicilan - $p->jumlah_angsuran_dibayar }}x)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none text-sm cursor-not-allowed text-slate-500" readonly>
                        <input type="hidden" name="metode_pembayaran" value="midtrans">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">Rp</span>
                            <input type="number" id="nominal" name="nominal" readonly required class="w-full bg-slate-100 border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:outline-none text-sm font-bold text-primary-700 cursor-not-allowed">
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1" id="info_nominal">*Pilih pinjaman untuk memuat nominal tagihan.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Pembayaran bulan Mei..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                    </div>
                </div>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition text-sm">Batal</button>
            <button onclick="saveData()" id="btnSubmit" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition text-sm flex items-center gap-2">
                Bayar Sekarang
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    $(document).ready(function() {
        // Auto fill nominal when pinjaman is selected
        $('#peminjaman_id').change(function() {
            const val = $(this).find(':selected').data('angsuran');
            if(val) {
                $('#nominal').val(val);
                $('#info_nominal').text('Sesuai tagihan bulan ini.');
            } else {
                $('#nominal').val('');
                $('#info_nominal').text('*Pilih pinjaman untuk memuat nominal tagihan.');
            }
        });
    });

    function openModal() {
        $('#mainForm')[0].reset();
        $('#nominal').val('');
        $('#formModal').removeClass('hidden');
    }

    function closeModal() { $('#formModal').addClass('hidden'); }

    function lanjutkanBayar(snapToken) {
        snap.pay(snapToken, {
            onSuccess: function(result){
                showToast('success', 'Pembayaran berhasil dikonfirmasi!');
                setTimeout(() => location.reload(), 1500);
            },
            onPending: function(result){
                showToast('info', 'Menunggu pembayaran diselesaikan.');
                setTimeout(() => location.reload(), 1500);
            },
            onError: function(result){
                showToast('error', 'Pembayaran gagal.');
                setTimeout(() => location.reload(), 1500);
            },
            onClose: function(){
                showToast('warning', 'Anda menutup popup sebelum pembayaran selesai.');
                setTimeout(() => location.reload(), 1500);
            }
        });
    }

    function saveData() {
        if(!$('#mainForm')[0].checkValidity()) {
            $('#mainForm')[0].reportValidity();
            return;
        }

        const btn = $('#btnSubmit');
        const ori = btn.html();

        Swal.fire({
            title: 'Lanjut Pembayaran Online?',
            text: 'Anda akan diarahkan ke halaman pembayaran aman Midtrans.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Ya, Lanjutkan'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('anggota.pembayaran.store') }}",
                    method: 'POST',
                    data: $('#mainForm').serialize() + '&_token={{ csrf_token() }}',
                    success: function(res) {
                        btn.html(ori).prop('disabled', false);
                        if(res.success) {
                            closeModal();
                            if(res.is_midtrans) {
                                lanjutkanBayar(res.snap_token);
                            } else {
                                showToast('success', res.message);
                                setTimeout(() => location.reload(), 1500);
                            }
                        }
                    },
                    error: function(err) {
                        btn.html(ori).prop('disabled', false);
                        let msg = 'Terjadi kesalahan.';
                        if(err.responseJSON && err.responseJSON.message) msg = err.responseJSON.message;
                        showToast('error', msg);
                    }
                });
            }
        });
    }
</script>
@endpush

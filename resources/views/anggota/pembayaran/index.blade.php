@extends('layouts.anggota')

@section('title', 'Pembayaran Angsuran')

@section('header-left')
    <a href="{{ route('anggota.dashboard') }}" class="text-slate-800 hover:text-slate-600">
        <i class="fas fa-chevron-left text-lg"></i>
    </a>
    <span class="ml-4 font-bold text-slate-800 text-lg">Pembayaran Angsuran</span>
@endsection

@section('content')

<div class="mb-6">
    <div class="bg-primary-600 p-6 rounded-2xl text-white shadow-lg shadow-primary-500/30 relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-sm font-medium text-primary-100 mb-1">Bayar Angsuran Bulanan</h3>
            <p class="text-xs text-primary-100 mb-4 max-w-[80%]">Laporkan pembayaran cicilan pinjaman aktif Anda di sini agar tercatat otomatis oleh Bendahara.</p>
            <button onclick="openModal()" class="bg-white text-primary-600 px-5 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Buat Laporan Bayar
            </button>
        </div>
        <i class="fas fa-hand-holding-dollar absolute -bottom-4 -right-2 text-8xl text-white opacity-10"></i>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5">
        <h3 class="font-bold text-slate-800 text-sm mb-4">Riwayat Angsuran Anda</h3>
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-[10px] text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">No Pinjaman</th>
                    <th class="px-4 py-3 font-medium text-center">Angsuran Ke</th>
                    <th class="px-4 py-3 font-medium text-right">Nominal</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-center">Status</th>
                </tr>
            </thead>
        </table>
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
            <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl mb-4 flex gap-3">
                <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                <p class="text-[11px] text-blue-800 leading-relaxed">
                    Silakan transfer ke <strong>BCA 123456789 (KSP Sejahtera)</strong> lalu catatkan pembayarannya di bawah ini.
                </p>
            </div>

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

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Bayar <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode <span class="text-red-500">*</span></label>
                            <select name="metode_pembayaran" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="tunai">Tunai ke Kasir</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" id="nominal" name="nominal" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none text-sm font-bold text-primary-700">
                        <p class="text-[10px] text-slate-500 mt-1" id="info_nominal">*Nominal otomatis sesuai angsuran.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Pembayaran dari BCA an Budi..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                    </div>
                </div>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition text-sm">Batal</button>
            <button onclick="saveData()" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition text-sm">Laporkan Bayar</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let table;
    $(document).ready(function() {
        table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('anggota.pembayaran.data') }}",
            columns: [
                {data: 'tanggal_bayar', name: 'tanggal_bayar', className: 'text-xs'},
                {
                    data: 'no_referensi', name: 'peminjaman.no_pinjaman',
                    render: function(data, type, row) {
                        return `<div class="py-1">
                            <p class="font-bold text-slate-800 text-xs">${row.peminjaman ? row.peminjaman.no_pinjaman : '-'}</p>
                            <p class="text-[9px] font-mono text-slate-500">Ref: ${data}</p>
                        </div>`;
                    }
                },
                {data: 'angsuran_ke', name: 'angsuran_ke', className: 'text-center font-bold text-xs'},
                {data: 'nominal', name: 'nominal', className: 'font-bold text-primary-600 text-right text-xs'},
                {data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            order: [[0, 'desc']],
            dom: '<"flex justify-between items-center mb-4"f>rt<"flex justify-between items-center mt-4"p>',
        });

        // Auto fill nominal when pinjaman is selected
        $('#peminjaman_id').change(function() {
            const val = $(this).find(':selected').data('angsuran');
            if(val) {
                $('#nominal').val(val);
                $('#info_nominal').text('Sesuai tagihan bulan ini.');
            } else {
                $('#nominal').val('');
            }
        });
    });

    function openModal() {
        $('#mainForm')[0].reset();
        $('#nominal').val('');
        $('#formModal').removeClass('hidden');
    }

    function closeModal() { $('#formModal').addClass('hidden'); }

    function saveData() {
        if(!$('#mainForm')[0].checkValidity()) {
            $('#mainForm')[0].reportValidity();
            return;
        }

        const btnLabel = 'Konfirmasi Bayar';
        Swal.fire({
            title: 'Lapor Pembayaran?',
            text: 'Pastikan Anda sudah mentransfer sesuai nominal yang diinput.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Ya, Laporkan'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: "{{ route('anggota.pembayaran.store') }}",
                    method: 'POST',
                    data: $('#mainForm').serialize() + '&_token={{ csrf_token() }}',
                    success: function(res) {
                        hideLoading();
                        if(res.success) {
                            closeModal();
                            showToast('success', res.message);
                            table.ajax.reload();
                        }
                    }
                });
            }
        });
    }
</script>
@endpush

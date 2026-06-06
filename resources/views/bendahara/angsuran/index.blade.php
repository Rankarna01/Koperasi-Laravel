@extends('layouts.admin')

@section('title', 'Data Angsuran')

@section('breadcrumb')
    <a href="{{ route('bendahara.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Data Angsuran</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Data Angsuran Pinjaman</h2>
        <p class="text-slate-500 text-sm mt-1">Pencatatan pembayaran cicilan pinjaman anggota koperasi.</p>
    </div>
    
    <div class="flex gap-2">
        <button onclick="openModal()" class="bg-primary-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-500/30 hover:bg-primary-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Terima Angsuran Tunai
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center">
        <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
            <i class="fas fa-file-invoice-dollar text-primary-500"></i> Daftar Pembayaran
        </h3>
        <span class="text-xs text-slate-400">Terakhir dimuat: <span id="lastRefresh"></span></span>
    </div>
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider">
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-l-xl border-b-2 border-slate-200">Tanggal</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">No Referensi</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Anggota</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Nominal</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-center">Status</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-r-xl border-b-2 border-slate-200 text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-primary-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-hand-holding-dollar text-primary-500"></i> Terima Pembayaran Tunai
            </h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="bg-amber-50 border border-amber-200 p-3 rounded-xl mb-4 flex gap-3">
                <i class="fas fa-info-circle text-amber-500 mt-0.5"></i>
                <p class="text-[11px] text-amber-800 leading-relaxed">
                    Form ini hanya ditujukan untuk penerimaan angsuran secara <strong>Tunai</strong> / Cash dari anggota secara langsung.
                </p>
            </div>

            <form id="mainForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Pinjaman Aktif <span class="text-red-500">*</span></label>
                        <select id="peminjaman_id" name="peminjaman_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition text-sm">
                            <option value="">Cari Anggota / No Pinjaman...</option>
                            @foreach($pinjamanList as $p)
                                <option value="{{ $p->id }}" data-angsuran="{{ $p->angsuran_per_bulan }}" data-ke="{{ $p->jumlah_angsuran_dibayar + 1 }}">
                                    {{ $p->no_pinjaman }} - {{ $p->anggota->nama_lengkap }} (Sisa: {{ $p->lama_cicilan - $p->jumlah_angsuran_dibayar }}x)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Bayar <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Angsuran Ke-</label>
                            <input type="text" id="info_ke" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-600 outline-none" value="-">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Tunai (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">Rp</span>
                            <input type="number" id="nominal" name="nominal" required class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition text-sm font-bold text-primary-700">
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1" id="info_nominal">*Nominal otomatis sesuai tagihan.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 outline-none transition text-sm">
                    </div>
                </div>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition">Batal</button>
            <button onclick="saveData()" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition">Terima Dana</button>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-blue-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-receipt text-blue-500"></i> Detail Angsuran
            </h3>
            <button onclick="closeDetail()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="text-center mb-6">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status Pembayaran</p>
                <div id="d_status" class="mb-3"></div>
                <p class="text-3xl font-extrabold text-blue-600" id="d_nominal">Rp 0</p>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-sm font-medium text-slate-500">No Referensi</span>
                    <span class="text-sm font-bold text-slate-800 font-mono" id="d_ref"></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-sm font-medium text-slate-500">Nama Anggota</span>
                    <span class="text-sm font-bold text-slate-800" id="d_anggota"></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-sm font-medium text-slate-500">Pembayaran Ke</span>
                    <span class="text-sm font-bold text-slate-800" id="d_ke"></span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-sm font-medium text-slate-500">Metode</span>
                    <span class="text-sm font-bold text-slate-800 capitalize" id="d_metode"></span>
                </div>
                <div class="flex justify-between pb-1">
                    <span class="text-sm font-medium text-slate-500">Tanggal</span>
                    <span class="text-sm font-bold text-slate-800" id="d_tgl"></span>
                </div>
            </div>
        </div>
        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end">
            <button onclick="closeDetail()" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    #dataTable tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.15s ease;
    }
    #dataTable tbody tr:hover {
        background-color: #f8fafc !important;
    }
    #dataTable tbody td {
        padding: 14px 16px !important;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        color: #475569 !important;
        font-size: 0.8rem !important;
        font-weight: 500 !important;
        margin: 0 2px !important;
        transition: all 0.15s ease !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #1e293b !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        border-color: #2563eb !important;
        color: white !important;
        font-weight: 700 !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem !important;
        background: #f8fafc !important;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.4rem 2.5rem 0.4rem 0.75rem !important;
        font-size: 0.85rem !important;
        background-color: #f8fafc !important;
    }
</style>
@endpush

@push('scripts')
<script>
    let table;
    $(document).ready(function() {
        $('#lastRefresh').text(new Date().toLocaleTimeString('id-ID'));

        table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('bendahara.angsuran.data') }}",
            columns: [
                {
                    data: 'tanggal_bayar', name: 'tanggal_bayar',
                    render: function(data, type, row) {
                        return `<div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-slate-100 to-slate-50 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200">
                                <i class="far fa-calendar-alt text-slate-400 text-xs"></i>
                            </div>
                            <span class="font-semibold text-slate-700 text-sm">${data}</span>
                        </div>`;
                    }
                },
                {
                    data: 'no_referensi', name: 'no_referensi',
                    render: function(data, type, row) {
                        return `<span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">${data}</span>`;
                    }
                },
                {
                    data: 'anggota_nama', name: 'peminjaman.anggota.nama_lengkap',
                    render: function(data, type, row) {
                        const initial = data ? data.charAt(0).toUpperCase() : '?';
                        return `<div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary-100 to-primary-50 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-600 text-xs font-bold">${initial}</span>
                            </div>
                            <div>
                                <span class="font-semibold text-slate-800 text-sm">${data}</span>
                                <p class="text-[10px] font-bold text-slate-400 mt-0.5">Angsuran Ke-${row.angsuran_ke}</p>
                            </div>
                        </div>`;
                    }
                },
                {data: 'nominal', name: 'nominal', className: 'font-bold text-primary-600 text-right tabular-nums'},
                {data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { search: "Cari:", lengthMenu: "Tampilkan _MENU_ data", info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data", infoEmpty: "Menampilkan 0 sampai 0 dari 0 data", infoFiltered: "(disaring dari _MAX_ data)", zeroRecords: "Tidak ada data", paginate: { next: "›", previous: "‹" } },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-5 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-5 gap-4"ip>',
            drawCallback: function() {
                $('#lastRefresh').text(new Date().toLocaleTimeString('id-ID'));
            },
            order: [[0, 'desc']]
        });

        $('#peminjaman_id').change(function() {
            const selected = $(this).find(':selected');
            const angsuran = selected.data('angsuran');
            const ke = selected.data('ke');
            
            if(angsuran) {
                $('#nominal').val(angsuran);
                $('#info_ke').val('Angsuran ke-' + ke);
            } else {
                $('#nominal').val('');
                $('#info_ke').val('-');
            }
        });

        $('#dataTable').on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            showLoading();
            $.get(`/bendahara/angsuran/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    const data = res.data;
                    $('#d_nominal').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.nominal));
                    $('#d_ref').text(data.no_referensi);
                    $('#d_anggota').text(data.peminjaman.anggota.nama_lengkap);
                    $('#d_ke').text('Angsuran ke-' + data.angsuran_ke);
                    $('#d_metode').text(data.metode_pembayaran === 'midtrans' ? 'Midtrans (Online)' : data.metode_pembayaran);
                    $('#d_tgl').text(new Date(data.tanggal_bayar).toLocaleDateString('id-ID'));
                    
                    if(data.status === 'berhasil') {
                        $('#d_status').html('<span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Berhasil Selesai</span>');
                    } else if(data.status === 'pending') {
                        $('#d_status').html('<span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Menunggu (Midtrans)</span>');
                    } else {
                        $('#d_status').html('<span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Gagal</span>');
                    }

                    $('#detailModal').removeClass('hidden');
                }
            });
        });

        $('#dataTable').on('click', '.btn-print', function() {
            // Kita belum mengubah fungsi print, misal panggil url /print
            const id = $(this).data('id');
            // Jika ada route print, window.open(`/bendahara/angsuran/${id}/print`, '_blank');
        });
    });

    function openModal() {
        $('#mainForm')[0].reset();
        $('#info_ke').val('-');
        $('#formModal').removeClass('hidden');
    }

    function closeModal() { $('#formModal').addClass('hidden'); }
    function closeDetail() { $('#detailModal').addClass('hidden'); }

    function saveData() {
        if(!$('#mainForm')[0].checkValidity()) {
            $('#mainForm')[0].reportValidity();
            return;
        }

        Swal.fire({
            title: 'Terima Angsuran?',
            text: 'Pastikan Anda telah menerima uang tunai sesuai nominal yang tercantum.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Ya, Terima'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: "{{ route('bendahara.angsuran.store') }}",
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

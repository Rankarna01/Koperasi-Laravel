@extends('layouts.admin')

@section('title', 'Data Simpanan')

@section('breadcrumb')
    <a href="{{ route('bendahara.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Data Simpanan</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Data Simpanan Anggota</h2>
        <p class="text-slate-500 text-sm mt-1">Pencatatan setoran simpanan wajib, pokok, sukarela, dan deposito anggota.</p>
    </div>
    
    <div class="flex gap-2">
        <button onclick="openModal()" class="bg-primary-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-500/20 hover:bg-primary-700 hover:shadow-primary-500/30 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Setor Simpanan
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-4 flex flex-col md:flex-row gap-3">
    <div class="flex-1">
        <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Jenis Simpanan</label>
        <select id="filterJenis" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
            <option value="">Semua Jenis</option>
            <option value="pokok">Simpanan Pokok</option>
            <option value="wajib">Simpanan Wajib</option>
            <option value="sukarela">Simpanan Sukarela</option>
            <option value="deposito">Deposito</option>
        </select>
    </div>
    <div class="flex-1">
        <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Anggota</label>
        <select id="filterAnggota" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
            <option value="">Semua Anggota</option>
            @foreach($anggotaList as $a)
                <option value="{{ $a->id }}">{{ $a->nama_lengkap }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center">
        <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
            <i class="fas fa-wallet text-primary-500"></i> Riwayat Simpanan
        </h3>
        <span class="text-xs text-slate-400">Terakhir dimuat: <span id="lastRefresh"></span></span>
    </div>
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider">
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-l-xl border-b-2 border-slate-200">Transaksi</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Anggota</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Jenis Simpanan</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Nominal</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-r-xl border-b-2 border-slate-200 text-center" style="width:80px">Aksi</th>
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
                <i class="fas fa-piggy-bank text-primary-500"></i> Input Simpanan Baru
            </h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="mainForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Anggota Penyetor</label>
                        <select name="anggota_id" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                            <option value="">Pilih Anggota...</option>
                            @foreach($anggotaList as $a)
                                <option value="{{ $a->id }}">{{ $a->no_anggota }} - {{ $a->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Simpanan</label>
                        <select name="jenis" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                            <option value="wajib">Simpanan Wajib</option>
                            <option value="pokok">Simpanan Pokok</option>
                            <option value="sukarela">Simpanan Sukarela</option>
                            <option value="deposito">Deposito</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nominal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">Rp</span>
                            <input type="number" name="nominal" required class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>
                </div>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition">Batal</button>
            <button onclick="saveData()" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan
            </button>
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
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem !important;
        background: #f8fafc !important;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        background: white !important;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.4rem 2.5rem 0.4rem 0.75rem !important;
        font-size: 0.85rem !important;
        background-color: #f8fafc !important;
    }
    .dataTables_wrapper .dataTables_info {
        font-size: 0.8rem !important;
        color: #64748b !important;
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
            ajax: {
                url: "{{ route('bendahara.simpanan.data') }}",
                data: function (d) {
                    d.jenis = $('#filterJenis').val();
                    d.anggota_id = $('#filterAnggota').val();
                }
            },
            columns: [
                {
                    data: 'no_transaksi', name: 'no_transaksi',
                    render: function(data, type, row) {
                        return `<div>
                            <p class="font-mono text-xs font-bold text-slate-700">${row.no_transaksi}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5"><i class="far fa-calendar text-[9px] mr-1"></i>${row.tanggal}</p>
                        </div>`;
                    }
                },
                {
                    data: 'anggota.nama_lengkap', name: 'anggota.nama_lengkap',
                    render: function(data, type, row) {
                        const initial = data ? data.charAt(0).toUpperCase() : '?';
                        return `<div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary-100 to-primary-50 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-600 text-xs font-bold">${initial}</span>
                            </div>
                            <span class="font-semibold text-slate-800 text-sm">${data}</span>
                        </div>`;
                    }
                },
                {data: 'jenis_label', name: 'jenis_label', orderable: false, searchable: false},
                {data: 'nominal', name: 'nominal', className: 'text-right font-bold text-primary-600 tabular-nums'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { search: "Cari:", lengthMenu: "Tampilkan _MENU_ data", info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data", infoEmpty: "Menampilkan 0 sampai 0 dari 0 data", infoFiltered: "(disaring dari _MAX_ data)", zeroRecords: "Tidak ada data", paginate: { next: "›", previous: "‹" } },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-5 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-5 gap-4"ip>',
            drawCallback: function() {
                $('#lastRefresh').text(new Date().toLocaleTimeString('id-ID'));
            },
            order: [[0, 'desc']]
        });

        // Filter change handlers
        $('#filterJenis, #filterAnggota').change(function() {
            table.draw();
        });

        // Print kwitansi
        $('#dataTable').on('click', '.btn-print', function() {
            const id = $(this).data('id');
            window.open(`/bendahara/simpanan/${id}/print`, '_blank');
        });
    });

    function openModal() {
        $('#mainForm')[0].reset();
        $('#formModal').removeClass('hidden');
    }

    function closeModal() { $('#formModal').addClass('hidden'); }

    function saveData() {
        if(!$('#mainForm')[0].checkValidity()) {
            $('#mainForm')[0].reportValidity();
            return;
        }

        showLoading();
        $.ajax({
            url: "{{ route('bendahara.simpanan.store') }}",
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
</script>
@endpush

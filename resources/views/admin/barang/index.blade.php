@extends('layouts.admin')

@section('title', 'Data Barang')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Data Barang</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Data Barang Toko</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola master data barang, harga, dan kontrol stok minimarket.</p>
    </div>
    
    <div class="flex gap-2">
        <button onclick="openModal('add')" class="bg-primary-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-primary-500/20 hover:bg-primary-700 hover:shadow-primary-500/30 transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Barang
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center">
        <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
            <i class="fas fa-boxes-stacked text-primary-500"></i> Inventaris Barang
        </h3>
        <span class="text-xs text-slate-400">Terakhir dimuat: <span id="lastRefresh"></span></span>
    </div>
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider">
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-l-xl border-b-2 border-slate-200">Produk</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Kategori</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Harga Beli</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Harga Jual</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-center">Stok</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-r-xl border-b-2 border-slate-200 text-center" style="width:100px">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-primary-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2" id="modalTitle">
                <i class="fas fa-box-open text-primary-500"></i> Tambah Barang Baru
            </h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto custom-scrollbar">
            <form id="mainForm">
                <input type="hidden" id="id" name="id">
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Barang</label>
                        <input type="text" id="nama" name="nama" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                        <select id="kategori_id" name="kategori_id" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                            <option value="">Pilih...</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
                        <input type="text" id="satuan" name="satuan" placeholder="Pcs, Kg, Dus" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Harga Beli</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">Rp</span>
                            <input type="number" id="harga_beli" name="harga_beli" required class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Harga Jual</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">Rp</span>
                            <input type="number" id="harga_jual" name="harga_jual" required class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                        </div>
                    </div>

                    <div id="wrap_stok">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Stok Awal</label>
                        <input type="number" id="stok" name="stok" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Stok Minimal</label>
                        <input type="number" id="stok_minimal" name="stok_minimal" value="5" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
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

<!-- Modal Tambah Stok -->
<div id="stockModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-blue-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-cubes text-blue-500"></i> Tambah Stok
            </h3>
            <button onclick="closeStockModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="p-6">
            <form id="stockForm">
                <input type="hidden" id="s_id" name="s_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Masuk (+)</label>
                    <input type="number" id="jumlah" name="jumlah" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan (Opsional)</label>
                    <input type="text" id="keterangan" name="keterangan" placeholder="Faktur Supplier #..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                </div>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeStockModal()" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition">Batal</button>
            <button onclick="saveStock()" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-md shadow-blue-500/30 transition flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> Tambah
            </button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Premium DataTables Overrides */
    #dataTable tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.15s ease;
    }
    #dataTable tbody tr:hover {
        background-color: #f8fafc !important;
        transform: scale(1.001);
    }
    #dataTable tbody td {
        padding: 14px 16px !important;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    
    /* Pagination styling */
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
    
    /* Search & Length */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem !important;
        transition: all 0.2s ease !important;
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
        // Set last refresh timestamp
        $('#lastRefresh').text(new Date().toLocaleTimeString('id-ID'));

        table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.barang.data') }}",
            columns: [
                {
                    data: 'nama', name: 'nama', 
                    render: function(data, type, row) {
                        return `<div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-primary-100 to-primary-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-box text-primary-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800 text-sm leading-tight">${row.nama}</p>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5">${row.kode_barang}</p>
                            </div>
                        </div>`;
                    }
                },
                {
                    data: 'kategori.nama', name: 'kategori.nama',
                    render: function(data) {
                        return `<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium">${data}</span>`;
                    }
                },
                {data: 'harga_beli', name: 'harga_beli', className: 'text-right font-medium text-slate-600 tabular-nums'},
                {data: 'harga_jual', name: 'harga_jual', className: 'text-right font-semibold text-primary-600 tabular-nums'},
                {data: 'stok_status', name: 'stok_status', orderable: false, searchable: false, className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { search: "Cari:", lengthMenu: "Tampilkan _MENU_ data", info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data", infoEmpty: "Menampilkan 0 sampai 0 dari 0 data", infoFiltered: "(disaring dari _MAX_ data)", zeroRecords: "Tidak ada data", paginate: { next: "›", previous: "‹" } },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-5 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-5 gap-4"ip>',
            drawCallback: function() {
                $('#lastRefresh').text(new Date().toLocaleTimeString('id-ID'));
            },
            order: [[0, 'asc']]
        });

        $('#dataTable').on('click', '.btn-edit', function() {
            const row = table.row($(this).parents('tr')).data();
            $('#modalTitle').html('<i class="fas fa-pen-to-square text-amber-500"></i> Edit Barang');
            $('#id').val(row.id);
            $('#nama').val(row.nama);
            $('#kategori_id').val(row.kategori_id);
            $('#satuan').val(row.satuan);
            $('#harga_beli').val(row.harga_beli);
            $('#harga_jual').val(row.harga_jual);
            $('#stok_minimal').val(row.stok_minimal);
            
            $('#wrap_stok').hide();
            $('#stok').prop('required', false);
            
            $('#formModal').removeClass('hidden');
        });

        $('#dataTable').on('click', '.btn-stock', function() {
            const row = table.row($(this).parents('tr')).data();
            $('#s_id').val(row.id);
            $('#jumlah').val('');
            $('#keterangan').val('');
            $('#stockModal').removeClass('hidden');
        });
    });

    function openModal() {
        $('#mainForm')[0].reset();
        $('#id').val('');
        $('#modalTitle').html('<i class="fas fa-box-open text-primary-500"></i> Tambah Barang Baru');
        $('#wrap_stok').show();
        $('#stok').prop('required', true);
        $('#formModal').removeClass('hidden');
    }

    function closeModal() { $('#formModal').addClass('hidden'); }
    function closeStockModal() { $('#stockModal').addClass('hidden'); }

    function saveData() {
        if(!$('#mainForm')[0].checkValidity()) {
            $('#mainForm')[0].reportValidity();
            return;
        }

        const id = $('#id').val();
        const url = id ? `/bendahara/barang/${id}` : `{{ route('admin.barang.store') }}`;
        let data = $('#mainForm').serialize();
        if(id) data += '&_method=PUT';

        showLoading();
        $.ajax({
            url: url,
            method: 'POST',
            data: data + '&_token={{ csrf_token() }}',
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

    function saveStock() {
        if(!$('#stockForm')[0].checkValidity()) {
            $('#stockForm')[0].reportValidity();
            return;
        }

        const id = $('#s_id').val();
        showLoading();
        $.ajax({
            url: `/bendahara/barang/${id}/add-stock`,
            method: 'POST',
            data: $('#stockForm').serialize() + '&_token={{ csrf_token() }}',
            success: function(res) {
                hideLoading();
                if(res.success) {
                    closeStockModal();
                    showToast('success', res.message);
                    table.ajax.reload();
                }
            }
        });
    }
</script>
@endpush

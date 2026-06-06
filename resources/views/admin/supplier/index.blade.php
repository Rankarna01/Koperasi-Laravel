@extends('layouts.admin')

@section('title', 'Data Supplier')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Data Supplier</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Master Data Supplier</h2>
        <p class="text-slate-500 text-sm mt-1">Kelola data pemasok barang untuk keperluan restock.</p>
    </div>
    
    <div class="flex gap-2">
        <button onclick="openModal('add')" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-primary-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Supplier
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center">
        <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
            <i class="fas fa-truck text-primary-500"></i> Daftar Supplier
        </h3>
    </div>
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider">
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-l-xl border-b-2 border-slate-200">Nama Supplier</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">No. Telepon</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Email</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Alamat</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-r-xl border-b-2 border-slate-200 text-center" style="width:100px">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg" id="modalTitle">Tambah Supplier</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="mainForm">
                <input type="hidden" id="data_id" name="id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Supplier <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" id="nama" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Telepon</label>
                            <input type="text" name="no_telepon" id="no_telepon" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" id="email" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm"></textarea>
                    </div>
                </div>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition text-sm">Batal</button>
            <button onclick="saveData()" id="btnSubmit" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition text-sm flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan
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
    let saveMode = 'add';

    $(document).ready(function() {
        table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.supplier.data') }}",
            columns: [
                {data: 'nama', name: 'nama', className: 'font-bold text-slate-800'},
                {data: 'no_telepon', name: 'no_telepon'},
                {data: 'email', name: 'email'},
                {data: 'alamat', name: 'alamat'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { search: "Cari:", lengthMenu: "_MENU_", info: "_START_ - _END_ dari _TOTAL_", infoEmpty: "0 data", zeroRecords: "Tidak ada data" },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4 gap-4"ip>',
            order: [[0, 'asc']]
        });

        $('#dataTable').on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            saveMode = 'edit';
            showLoading();
            $.get(`/admin/supplier/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    $('#modalTitle').text('Edit Supplier');
                    $('#data_id').val(res.data.id);
                    $('#nama').val(res.data.nama);
                    $('#no_telepon').val(res.data.no_telepon);
                    $('#email').val(res.data.email);
                    $('#alamat').val(res.data.alamat);
                    $('#formModal').removeClass('hidden');
                }
            });
        });

        $('#dataTable').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            confirmAction('Hapus Supplier?', 'Data supplier akan dihapus permanen. Tidak bisa dihapus jika memiliki riwayat transaksi.', function() {
                $.ajax({
                    url: `/admin/supplier/${id}`,
                    method: 'DELETE',
                    success: function(res) {
                        if(res.success) {
                            showToast('success', res.message);
                            table.ajax.reload();
                        }
                    }
                });
            });
        });
    });

    function openModal() {
        saveMode = 'add';
        $('#modalTitle').text('Tambah Supplier');
        $('#mainForm')[0].reset();
        $('#data_id').val('');
        $('#formModal').removeClass('hidden');
    }

    function closeModal() { $('#formModal').addClass('hidden'); }

    function saveData() {
        if(!$('#mainForm')[0].checkValidity()) {
            $('#mainForm')[0].reportValidity();
            return;
        }

        const btn = $('#btnSubmit');
        const ori = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

        const id = $('#data_id').val();
        const url = saveMode === 'add' ? "{{ route('admin.supplier.store') }}" : `/admin/supplier/${id}`;
        let data = $('#mainForm').serialize();
        if(saveMode === 'edit') data += '&_method=PUT';

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(res) {
                btn.html(ori).prop('disabled', false);
                if(res.success) {
                    closeModal();
                    showToast('success', res.message);
                    table.ajax.reload();
                }
            },
            error: function(err) {
                btn.html(ori).prop('disabled', false);
            }
        });
    }
</script>
@endpush

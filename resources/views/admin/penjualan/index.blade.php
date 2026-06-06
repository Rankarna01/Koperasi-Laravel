@extends('layouts.admin')

@section('title', 'Rekap Penjualan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Rekap Penjualan</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Rekap Penjualan Bulanan</h2>
        <p class="text-slate-500 text-sm mt-1">Pencatatan omset dan laba penjualan koperasi per bulan.</p>
    </div>
    
    <div class="flex gap-2">
        <button onclick="openModal('add')" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-primary-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Rekap
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">Periode</th>
                    <th class="px-4 py-3 font-medium text-right">Total Omset</th>
                    <th class="px-4 py-3 font-medium text-right">Total Laba Bersih</th>
                    <th class="px-4 py-3 font-medium">Keterangan</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg" id="modalTitle">Tambah Rekap</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="mainForm">
                <input type="hidden" id="data_id" name="id">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Bulan <span class="text-red-500">*</span></label>
                            <select name="bulan" id="bulan" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                                @foreach($bulanIndo as $k => $v)
                                    <option value="{{ $k }}" {{ date('n') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                            <input type="number" name="tahun" id="tahun" value="{{ date('Y') }}" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Omset (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="total_omset" id="total_omset" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm font-bold text-primary-700">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Total Laba Bersih (Rp) <span class="text-red-500">*</span></label>
                        <p class="text-[10px] text-slate-500 mb-2">Nilai ini yang akan digunakan untuk perhitungan SHU nantinya.</p>
                        <input type="number" name="total_laba" id="total_laba" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm font-bold text-green-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Catatan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm"></textarea>
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

@push('scripts')
<script>
    let table;
    let saveMode = 'add';

    $(document).ready(function() {
        table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.penjualan.data') }}",
            columns: [
                {data: 'periode', name: 'bulan', className: 'font-bold text-slate-800'},
                {data: 'total_omset', name: 'total_omset', className: 'text-right font-medium text-slate-600'},
                {data: 'total_laba', name: 'total_laba', className: 'text-right font-bold text-green-600'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { search: "Cari:", lengthMenu: "_MENU_", info: "_START_ - _END_ dari _TOTAL_", infoEmpty: "0 data", zeroRecords: "Tidak ada data" },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4 gap-4"ip>',
            order: [[1, 'desc'], [0, 'desc']]
        });

        $('#dataTable').on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            saveMode = 'edit';
            showLoading();
            $.get(`/admin/penjualan/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    $('#modalTitle').text('Edit Rekap Penjualan');
                    $('#data_id').val(res.data.id);
                    $('#bulan').val(res.data.bulan);
                    $('#tahun').val(res.data.tahun);
                    $('#total_omset').val(res.data.total_omset);
                    $('#total_laba').val(res.data.total_laba);
                    $('#keterangan').val(res.data.keterangan);
                    $('#formModal').removeClass('hidden');
                }
            });
        });

        $('#dataTable').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            confirmAction('Hapus Data?', 'Data rekap penjualan ini akan dihapus permanen.', function() {
                $.ajax({
                    url: `/admin/penjualan/${id}`,
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
        $('#modalTitle').text('Tambah Rekap Penjualan');
        $('#mainForm')[0].reset();
        $('#data_id').val('');
        $('#tahun').val(new Date().getFullYear());
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
        const url = saveMode === 'add' ? "{{ route('admin.penjualan.store') }}" : `/admin/penjualan/${id}`;
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

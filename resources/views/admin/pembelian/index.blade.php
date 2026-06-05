@extends('layouts.admin')

@section('title', 'Data Pembelian')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Data Pembelian</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Pembelian Barang / Restock</h2>
        <p class="text-slate-500 text-sm mt-1">Riwayat pembelian barang (restock) ke Supplier.</p>
    </div>
    
    <div class="flex gap-2">
        <button onclick="openModal()" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-primary-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Transaksi Pembelian Baru
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">No. Nota</th>
                    <th class="px-4 py-3 font-medium">Supplier</th>
                    <th class="px-4 py-3 font-medium text-right">Total Transaksi</th>
                    <th class="px-4 py-3 font-medium text-center">Status</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Buat Pembelian Baru</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto custom-scrollbar">
            <form id="mainForm">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Supplier</label>
                        <select name="supplier_id" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                            <option value="">Pilih Supplier...</option>
                            @foreach($supplierList as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>
                </div>

                <div class="mb-2 flex justify-between items-end">
                    <label class="block text-sm font-bold text-slate-700">Daftar Barang</label>
                    <button type="button" onclick="addItem()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-1 px-3 rounded-lg transition"><i class="fas fa-plus"></i> Tambah Item</button>
                </div>
                
                <table class="w-full text-sm mb-4">
                    <thead class="bg-slate-50 text-slate-500 text-xs">
                        <tr>
                            <th class="py-2 px-2 text-left">Barang</th>
                            <th class="py-2 px-2 text-right w-32">Harga Beli</th>
                            <th class="py-2 px-2 text-center w-24">Jumlah</th>
                            <th class="py-2 px-2 text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsContainer">
                        <!-- Items injected here -->
                    </tbody>
                </table>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition">Batal</button>
            <button onclick="saveData()" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition">Proses Pembelian</button>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <!-- Similar to penjualan detail, simplified for brevity -->
    <div class="modal-content bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Detail Pembelian <span id="d_nota" class="text-primary-600"></span></h3>
            <button onclick="closeDetail()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                <div><p class="text-slate-500">Supplier:</p><p class="font-bold" id="d_supplier"></p></div>
                <div><p class="text-slate-500">Tanggal:</p><p class="font-bold" id="d_tanggal"></p></div>
            </div>
            <table class="w-full text-left text-sm mb-4">
                <thead class="bg-slate-50 border-y border-slate-100">
                    <tr><th class="py-2 px-3">Item</th><th class="py-2 px-3 text-right">Harga</th><th class="py-2 px-3 text-center">Qty</th><th class="py-2 px-3 text-right">Subtotal</th></tr>
                </thead>
                <tbody id="d_items"></tbody>
                <tfoot class="border-t border-slate-200 bg-slate-50">
                    <tr><th colspan="3" class="py-3 px-3 text-right">Total Transaksi</th><th class="py-3 px-3 text-right font-bold text-primary-600 text-lg" id="d_total"></th></tr>
                </tfoot>
            </table>
        </div>
        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeDetail()" class="px-6 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 font-bold transition">Tutup</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let table;
    let itemIndex = 0;
    
    $(document).ready(function() {
        table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.pembelian.data') }}",
            columns: [
                {data: 'tanggal', name: 'tanggal'},
                {data: 'no_nota', name: 'no_nota', className: 'font-mono font-bold'},
                {data: 'supplier.nama', name: 'supplier.nama'},
                {data: 'total', name: 'total', className: 'text-right font-bold text-primary-600'},
                {data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            order: [[0, 'desc']]
        });

        $('#dataTable').on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            showLoading();
            $.get(`/admin/pembelian/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    const data = res.data;
                    $('#d_nota').text(data.no_nota);
                    $('#d_supplier').text(data.supplier.nama);
                    $('#d_tanggal').text(new Date(data.tanggal).toLocaleString('id-ID'));
                    $('#d_total').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.total));
                    
                    let rows = '';
                    data.detail.forEach(function(item) {
                        rows += `<tr class="border-b border-slate-50">
                            <td class="py-2 px-3">${item.barang.nama}</td>
                            <td class="py-2 px-3 text-right">Rp ${new Intl.NumberFormat('id-ID').format(item.harga_beli)}</td>
                            <td class="py-2 px-3 text-center">${item.jumlah}</td>
                            <td class="py-2 px-3 text-right font-medium">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</td>
                        </tr>`;
                    });
                    $('#d_items').html(rows);
                    
                    $('#detailModal').removeClass('hidden');
                }
            });
        });
    });

    function openModal() {
        $('#mainForm')[0].reset();
        $('#itemsContainer').empty();
        addItem();
        $('#formModal').removeClass('hidden');
    }

    function closeModal() { $('#formModal').addClass('hidden'); }
    function closeDetail() { $('#detailModal').addClass('hidden'); }

    function addItem() {
        const tr = `<tr class="border-b border-slate-100" id="tr_${itemIndex}">
            <td class="py-2 pr-2">
                <select name="items[${itemIndex}][barang_id]" required class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    <option value="">Pilih...</option>
                    @foreach($barangList as $b)
                        <option value="{{ $b->id }}">{{ $b->nama }}</option>
                    @endforeach
                </select>
            </td>
            <td class="py-2 pr-2">
                <input type="number" name="items[${itemIndex}][harga_beli]" required class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm text-right">
            </td>
            <td class="py-2 pr-2">
                <input type="number" name="items[${itemIndex}][jumlah]" required min="1" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-primary-500 outline-none text-sm text-center">
            </td>
            <td class="py-2 text-center">
                <button type="button" onclick="$('#tr_${itemIndex}').remove()" class="text-red-500 hover:text-red-700 p-1"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`;
        $('#itemsContainer').append(tr);
        itemIndex++;
    }

    function saveData() {
        if(!$('#mainForm')[0].checkValidity()) {
            $('#mainForm')[0].reportValidity();
            return;
        }

        showLoading();
        $.ajax({
            url: "{{ route('admin.pembelian.store') }}",
            method: 'POST',
            data: $('#mainForm').serialize() + '&_token={{ csrf_token() }}',
            success: function(res) {
                hideLoading();
                if(res.success) {
                    closeModal();
                    showToast('success', res.message);
                    table.ajax.reload();
                }
            },
            error: function() {
                hideLoading();
            }
        });
    }
</script>
@endpush

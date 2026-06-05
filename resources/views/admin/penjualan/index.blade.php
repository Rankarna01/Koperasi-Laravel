@extends('layouts.admin')

@section('title', 'Data Penjualan')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Data Penjualan</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Data Penjualan POS</h2>
        <p class="text-slate-500 text-sm mt-1">Riwayat transaksi penjualan toko / minimarket.</p>
    </div>
    
    <div class="flex gap-2">
        <a href="{{ route('admin.penjualan.kasir') }}" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-primary-700 transition flex items-center gap-2">
            <i class="fas fa-cash-register"></i> Buka Kasir
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">No. Nota</th>
                    <th class="px-4 py-3 font-medium">Pembeli</th>
                    <th class="px-4 py-3 font-medium">Metode</th>
                    <th class="px-4 py-3 font-medium text-right">Total Transaksi</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Detail Transaksi <span id="d_nota" class="text-primary-600"></span></h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto custom-scrollbar">
            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                <div>
                    <p class="text-slate-500 font-medium">Tanggal:</p>
                    <p class="font-bold text-slate-800" id="d_tanggal"></p>
                </div>
                <div>
                    <p class="text-slate-500 font-medium">Kasir:</p>
                    <p class="font-bold text-slate-800" id="d_kasir"></p>
                </div>
                <div>
                    <p class="text-slate-500 font-medium">Pembeli:</p>
                    <p class="font-bold text-slate-800" id="d_pembeli"></p>
                </div>
                <div>
                    <p class="text-slate-500 font-medium">Metode Pembayaran:</p>
                    <p class="font-bold text-slate-800 uppercase" id="d_metode"></p>
                </div>
            </div>

            <table class="w-full text-left text-sm mb-4">
                <thead class="bg-slate-50 border-y border-slate-100">
                    <tr>
                        <th class="py-2 px-3">Item</th>
                        <th class="py-2 px-3 text-right">Harga</th>
                        <th class="py-2 px-3 text-center">Qty</th>
                        <th class="py-2 px-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="d_items">
                    <!-- Items injected via JS -->
                </tbody>
                <tfoot class="border-t border-slate-200 bg-slate-50">
                    <tr>
                        <th colspan="3" class="py-3 px-3 text-right">Total Transaksi</th>
                        <th class="py-3 px-3 text-right font-bold text-primary-600 text-lg" id="d_total"></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="py-2 px-3 text-right text-slate-500">Tunai / Bayar</th>
                        <th class="py-2 px-3 text-right font-medium" id="d_bayar"></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="py-2 px-3 text-right text-slate-500">Kembalian</th>
                        <th class="py-2 px-3 text-right font-medium text-orange-600" id="d_kembalian"></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button class="px-6 py-2 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 font-bold transition flex items-center gap-2">
                <i class="fas fa-print"></i> Cetak Struk
            </button>
            <button onclick="closeModal()" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition">Tutup</button>
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
            ajax: "{{ route('admin.penjualan.data') }}",
            columns: [
                {data: 'tanggal', name: 'tanggal'},
                {data: 'no_nota', name: 'no_nota', className: 'font-mono font-bold'},
                {
                    data: 'anggota', 
                    render: function(data) {
                        return data ? data.nama_lengkap : '<span class="italic text-slate-500">Umum / Non-Anggota</span>';
                    }
                },
                {data: 'metode_pembayaran', name: 'metode_pembayaran', className: 'uppercase'},
                {data: 'total', name: 'total', className: 'text-right font-bold text-primary-600'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            order: [[0, 'desc']]
        });

        $('#dataTable').on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            showLoading();
            $.get(`/admin/penjualan/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    const data = res.data;
                    $('#d_nota').text(data.no_nota);
                    $('#d_tanggal').text(new Date(data.tanggal).toLocaleString('id-ID'));
                    $('#d_kasir').text(data.creator.name);
                    $('#d_pembeli').html(data.anggota ? data.anggota.nama_lengkap : '<span class="italic text-slate-500">Umum</span>');
                    $('#d_metode').text(data.metode_pembayaran);
                    
                    $('#d_total').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.total));
                    $('#d_bayar').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.bayar));
                    $('#d_kembalian').text('Rp ' + new Intl.NumberFormat('id-ID').format(data.kembalian));
                    
                    let rows = '';
                    data.detail.forEach(function(item) {
                        rows += `<tr class="border-b border-slate-50">
                            <td class="py-2 px-3">${item.barang.nama}</td>
                            <td class="py-2 px-3 text-right">Rp ${new Intl.NumberFormat('id-ID').format(item.harga_jual)}</td>
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

    function closeModal() { $('#detailModal').addClass('hidden'); }
</script>
@endpush

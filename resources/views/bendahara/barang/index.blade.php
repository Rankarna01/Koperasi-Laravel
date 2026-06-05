@extends('layouts.admin')

@section('title', 'Data Barang')

@section('breadcrumb')
    <a href="{{ route('bendahara.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
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
        <button onclick="openModal('add')" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-primary-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Barang
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">Kode/Nama</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium">Harga Beli</th>
                    <th class="px-4 py-3 font-medium">Harga Jual</th>
                    <th class="px-4 py-3 font-medium">Stok</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="formModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg" id="modalTitle">Tambah Barang Baru</h3>
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
                        <input type="text" id="nama" name="nama" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                        <select id="kategori_id" name="kategori_id" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                            <option value="">Pilih...</option>
                            @foreach($kategoriList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
                        <input type="text" id="satuan" name="satuan" placeholder="Pcs, Kg, Dus" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Harga Beli</label>
                        <input type="number" id="harga_beli" name="harga_beli" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Harga Jual</label>
                        <input type="number" id="harga_jual" name="harga_jual" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div id="wrap_stok">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Stok Awal</label>
                        <input type="number" id="stok" name="stok" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Stok Minimal</label>
                        <input type="number" id="stok_minimal" name="stok_minimal" value="5" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>
                </div>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition">Batal</button>
            <button onclick="saveData()" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition">Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Tambah Stok -->
<div id="stockModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Tambah Stok</h3>
            <button onclick="closeStockModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="p-6">
            <form id="stockForm">
                <input type="hidden" id="s_id" name="s_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Masuk (+)</label>
                    <input type="number" id="jumlah" name="jumlah" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan (Opsional)</label>
                    <input type="text" id="keterangan" name="keterangan" placeholder="Faktur Supplier #..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                </div>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="closeStockModal()" class="px-6 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition">Batal</button>
            <button onclick="saveStock()" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-md shadow-blue-500/30 transition">Tambah</button>
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
            ajax: "{{ route('bendahara.barang.data') }}",
            columns: [
                {
                    data: 'nama', name: 'nama', 
                    render: function(data, type, row) {
                        return `<div>
                            <p class="font-bold text-slate-800 text-sm">${row.nama}</p>
                            <p class="text-[10px] text-slate-500 font-mono">${row.kode_barang}</p>
                        </div>`;
                    }
                },
                {data: 'kategori.nama', name: 'kategori.nama'},
                {data: 'harga_beli', name: 'harga_beli'},
                {data: 'harga_jual', name: 'harga_jual'},
                {data: 'stok_status', name: 'stok_status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
        });

        $('#dataTable').on('click', '.btn-edit', function() {
            const row = table.row($(this).parents('tr')).data();
            $('#modalTitle').text('Edit Barang');
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
        $('#modalTitle').text('Tambah Barang Baru');
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
        const url = id ? `/bendahara/barang/${id}` : `{{ route('bendahara.barang.store') }}`;
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

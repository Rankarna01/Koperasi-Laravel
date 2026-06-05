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
        <button onclick="openModal()" class="bg-primary-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-primary-700 transition flex items-center gap-2">
            <i class="fas fa-plus"></i> Setor Simpanan
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">No Transaksi</th>
                    <th class="px-4 py-3 font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">Anggota</th>
                    <th class="px-4 py-3 font-medium">Jenis Simpanan</th>
                    <th class="px-4 py-3 font-medium">Nominal</th>
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
            <h3 class="font-bold text-slate-800 text-lg">Input Simpanan Baru</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <form id="mainForm">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Anggota Penyetor</label>
                        <select name="anggota_id" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                            <option value="">Pilih Anggota...</option>
                            @foreach($anggotaList as $a)
                                <option value="{{ $a->id }}">{{ $a->no_anggota }} - {{ $a->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Simpanan</label>
                        <select name="jenis" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                            <option value="wajib">Simpanan Wajib</option>
                            <option value="pokok">Simpanan Pokok</option>
                            <option value="sukarela">Simpanan Sukarela</option>
                            <option value="deposito">Deposito</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" name="nominal" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm">
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

@endsection

@push('scripts')
<script>
    let table;
    $(document).ready(function() {
        table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('bendahara.simpanan.data') }}",
            columns: [
                {data: 'no_transaksi', name: 'no_transaksi', className: 'font-mono text-sm'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'anggota.nama_lengkap', name: 'anggota.nama_lengkap'},
                {data: 'jenis_label', name: 'jenis_label', orderable: false, searchable: false},
                {data: 'nominal', name: 'nominal', className: 'font-bold text-primary-600 text-right'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            order: [[1, 'desc']]
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

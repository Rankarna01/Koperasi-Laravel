@extends('layouts.admin')

@section('title', 'Laporan Simpan Pinjam')

@section('breadcrumb')
    <a href="{{ route('ketua.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Laporan Simpan Pinjam</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Laporan Simpan Pinjam</h2>
        <p class="text-slate-500 text-sm mt-1">Laporan rekapitulasi mutasi simpanan dan pinjaman anggota Koperasi.</p>
</div>
    <div class="flex gap-2">
        <a href="{{ route('ketua.laporan.simpan-pinjam.export', 'excel') }}" class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-green-700 transition flex items-center gap-2">
            <i class="fas fa-file-excel"></i> Excel
        </a>
        <a href="{{ route('ketua.laporan.simpan-pinjam.export', 'pdf') }}" class="bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-red-700 transition flex items-center gap-2">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center">
        <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
            <i class="fas fa-book text-primary-500"></i> Rekapitulasi Data
        </h3>
    </div>
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider">
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-l-xl border-b-2 border-slate-200">No. Anggota</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Nama Anggota</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Total Simpanan</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Total Pinjaman</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-r-xl border-b-2 border-slate-200 text-right">Sisa Pinjaman</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Premium DataTables Overrides */
    #dataTable tbody tr { border-bottom: 1px solid #f1f5f9; transition: all 0.15s ease; }
    #dataTable tbody tr:hover { background-color: #f8fafc !important; transform: scale(1.001); }
    #dataTable tbody td { padding: 14px 16px !important; vertical-align: middle; font-size: 0.875rem; }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px !important; border-radius: 8px !important; border: 1px solid #e2e8f0 !important;
        background: white !important; color: #475569 !important; font-size: 0.8rem !important;
        font-weight: 500 !important; margin: 0 2px !important; transition: all 0.15s ease !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f1f5f9 !important; border-color: #cbd5e1 !important; color: #1e293b !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #2563eb !important; border-color: #2563eb !important; color: white !important; font-weight: 700 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity: 0.4 !important; cursor: not-allowed !important; }
    
    .dataTables_wrapper .dataTables_filter input { border: 1px solid #e2e8f0 !important; border-radius: 0.75rem !important; padding: 0.5rem 1rem !important; font-size: 0.85rem !important; background: #f8fafc !important; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important; background: white !important; }
    .dataTables_wrapper .dataTables_length select { border: 1px solid #e2e8f0 !important; border-radius: 0.75rem !important; padding: 0.4rem 2.5rem 0.4rem 0.75rem !important; font-size: 0.85rem !important; background-color: #f8fafc !important; }
    .dataTables_wrapper .dataTables_info { font-size: 0.8rem !important; color: #64748b !important; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ketua.laporan.simpan-pinjam.data') }}",
            columns: [
                {data: 'no_anggota', name: 'no_anggota', className: 'font-mono'},
                {data: 'nama', name: 'nama_lengkap', className: 'font-bold text-slate-800'},
                {data: 'total_simpanan', name: 'total_simpanan', className: 'text-right font-semibold text-primary-600', searchable: false},
                {data: 'total_pinjaman', name: 'total_pinjaman', className: 'text-right font-semibold text-red-600', searchable: false},
                {data: 'sisa_pinjaman', name: 'sisa_pinjaman', className: 'text-right font-bold text-slate-600', searchable: false},
            ],
            language: { search: "Cari Anggota:", lengthMenu: "_MENU_", info: "_START_ - _END_ dari _TOTAL_", infoEmpty: "0 data", zeroRecords: "Tidak ada data", paginate: { next: "Selanjutnya", previous: "Sebelumnya" } },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4 gap-4"ip>',
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('flex items-center gap-1');
            }
        });
    });
</script>
@endpush

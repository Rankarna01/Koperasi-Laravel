@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('breadcrumb')
    <a href="{{ route('ketua.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Laporan Penjualan</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Laporan Rekap Penjualan</h2>
        <p class="text-slate-500 text-sm mt-1">Laporan rekapitulasi omset dan laba penjualan bulanan Koperasi.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('ketua.laporan.penjualan.export', 'excel') }}" class="bg-green-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-green-700 transition flex items-center gap-2">
            <i class="fas fa-file-excel"></i> Excel
        </a>
        <a href="{{ route('ketua.laporan.penjualan.export', 'pdf') }}" class="bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-red-700 transition flex items-center gap-2">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
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
                    <th class="px-4 py-3 font-medium">Catatan</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium">Admin Input</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ketua.laporan.penjualan.data') }}",
            columns: [
                {data: 'periode', name: 'periode', className: 'font-bold text-slate-800', searchable: false},
                {data: 'total_omset', name: 'total_omset', className: 'text-right font-semibold text-primary-600'},
                {data: 'total_laba', name: 'total_laba', className: 'text-right font-bold text-green-600'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'admin', name: 'admin', orderable: false, searchable: false},
            ],
            language: { search: "Cari:", lengthMenu: "_MENU_", info: "_START_ - _END_ dari _TOTAL_", infoEmpty: "0 data", zeroRecords: "Tidak ada data" },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4 gap-4"ip>',
            order: [] // Use default from controller
        });
    });
</script>
@endpush

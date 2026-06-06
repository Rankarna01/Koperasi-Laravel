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
    <div class="p-5">
        <table id="dataTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">No. Anggota</th>
                    <th class="px-4 py-3 font-medium">Nama Anggota</th>
                    <th class="px-4 py-3 font-medium text-right">Total Simpanan</th>
                    <th class="px-4 py-3 font-medium text-right">Total Pinjaman</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-right">Sisa Pinjaman</th>
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
            ajax: "{{ route('ketua.laporan.simpan-pinjam.data') }}",
            columns: [
                {data: 'no_anggota', name: 'no_anggota', className: 'font-mono'},
                {data: 'nama', name: 'nama_lengkap', className: 'font-bold text-slate-800'},
                {data: 'total_simpanan', name: 'total_simpanan', className: 'text-right font-semibold text-primary-600', searchable: false},
                {data: 'total_pinjaman', name: 'total_pinjaman', className: 'text-right font-semibold text-red-600', searchable: false},
                {data: 'sisa_pinjaman', name: 'sisa_pinjaman', className: 'text-right font-bold text-slate-600', searchable: false},
            ],
            language: { search: "Cari Anggota:", lengthMenu: "_MENU_", info: "_START_ - _END_ dari _TOTAL_", infoEmpty: "0 data", zeroRecords: "Tidak ada data" },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4 gap-4"ip>',
        });
    });
</script>
@endpush

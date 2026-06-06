@extends('layouts.admin')

@section('title', 'Verifikasi Pinjaman')

@section('breadcrumb')
    <a href="{{ route('bendahara.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Pinjaman</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Verifikasi Pinjaman</h2>
        <p class="text-slate-500 text-sm mt-1">Evaluasi kelayakan pengajuan pinjaman anggota.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-4 flex flex-col md:flex-row gap-3">
    <div class="w-full md:w-1/3">
        <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Pengajuan</label>
        <select id="filterStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
            <option value="">Semua Status</option>
            <option value="menunggu_bendahara" selected>Perlu Verifikasi Saya</option>
            <option value="menunggu_ketua">Menunggu ACC Ketua</option>
            <option value="disetujui">Aktif Berjalan</option>
            <option value="lunas">Lunas</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center">
        <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
            <i class="fas fa-hand-holding-dollar text-primary-500"></i> Daftar Pengajuan
        </h3>
        <span class="text-xs text-slate-400">Terakhir dimuat: <span id="lastRefresh"></span></span>
    </div>
    <div class="p-5">
        <table id="pinjamanTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider">
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-l-xl border-b-2 border-slate-200">No. Pinjaman / Tgl</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Anggota</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Nominal Pengajuan</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Angsuran / Bln</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-center">Status</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-r-xl border-b-2 border-slate-200 text-center" style="width:100px">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Verifikasi Pinjaman -->
<div id="verifyModal" class="modal-backdrop hidden flex items-center justify-center p-4 z-50">
    <div class="modal-content bg-white w-full max-w-3xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-primary-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-search-dollar text-primary-500"></i> Evaluasi Pengajuan Pinjaman
            </h3>
            <button onclick="closeVerifyModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1 bg-slate-50/50 custom-scrollbar">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Data Pengajuan -->
                <div>
                    <h4 class="font-bold text-slate-800 text-sm mb-3 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-primary-500"></i> Detail Pengajuan
                    </h4>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm mb-4">
                        <div class="p-5 border-b border-slate-100 bg-gradient-to-br from-primary-50 to-white text-center">
                            <p class="text-[11px] text-primary-600 font-bold uppercase tracking-wider mb-1">Nominal Diajukan</p>
                            <p class="text-3xl font-extrabold text-primary-700 tracking-tight" id="v_nominal">Rp 0</p>
                        </div>
                        <div class="p-5 grid grid-cols-2 gap-y-5 gap-x-4">
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tenor</p>
                                <p class="font-bold text-slate-800 text-lg"><span id="v_tenor">0</span> <span class="text-sm font-medium text-slate-500">Bulan</span></p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Bunga</p>
                                <p class="font-bold text-slate-800 text-lg"><span id="v_bunga">0</span>% <span class="text-sm font-medium text-slate-500">/ Bln</span></p>
                            </div>
                            <div class="col-span-2 p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                <p class="text-sm font-bold text-slate-600">Angsuran Per Bulan</p>
                                <p class="font-bold text-slate-800 text-xl text-right" id="v_angsuran">Rp 0</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Tujuan Pinjaman</p>
                                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 text-sm text-slate-700 leading-relaxed italic" id="v_tujuan">
                                    -
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evaluasi Risiko -->
                <div class="flex flex-col h-full">
                    <h4 class="font-bold text-slate-800 text-sm mb-3 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-shield text-emerald-500"></i> Kelayakan Anggota
                    </h4>
                    
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-4">
                        <div class="flex items-center gap-4 mb-5">
                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-emerald-50 text-emerald-600 rounded-full flex items-center justify-center font-bold text-xl border border-emerald-200 shadow-sm" id="v_inisial"></div>
                            <div>
                                <p class="font-bold text-slate-800 text-lg" id="v_nama"></p>
                                <p class="text-xs font-mono font-medium text-slate-500 mt-0.5" id="v_noanggota"></p>
                            </div>
                        </div>
                        
                        <div class="space-y-4 pt-4 border-t border-slate-100">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-slate-500 flex items-center gap-2"><i class="fas fa-briefcase w-4 text-slate-400"></i> Pekerjaan</span>
                                <span class="text-sm font-bold text-slate-800" id="v_pekerjaan">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-slate-500 flex items-center gap-2"><i class="fas fa-check-circle w-4 text-slate-400"></i> Status Keanggotaan</span>
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200">Anggota Aktif</span>
                            </div>
                        </div>
                    </div>

                    <form id="formVerify" class="flex-1 flex flex-col">
                        <input type="hidden" id="v_id">
                        <div class="flex-1 bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Catatan Evaluasi Bendahara <span class="text-red-500">*</span></label>
                            <p class="text-xs text-slate-500 mb-3 leading-relaxed">Berikan catatan atau opini kelayakan finansial anggota ini untuk menjadi pertimbangan Ketua saat memberikan persetujuan akhir.</p>
                            <textarea id="v_catatan" class="w-full flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm resize-none min-h-[120px]" placeholder="Contoh: Anggota ini memiliki riwayat simpanan yang lancar, gaji memadai untuk nominal cicilan ini. Direkomendasikan untuk disetujui..."></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-5 border-t border-slate-100 bg-white flex justify-end gap-3" id="actionButtons">
            <button onclick="prosesVerifikasi('reject')" class="px-6 py-2.5 bg-white border border-red-200 text-red-600 rounded-xl hover:bg-red-50 hover:border-red-300 font-bold transition flex items-center gap-2">
                <i class="fas fa-times-circle"></i> Tolak
            </button>
            <button onclick="prosesVerifikasi('approve')" class="px-6 py-2.5 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-lg shadow-primary-500/30 transition flex items-center gap-2">
                <i class="fas fa-paper-plane"></i> Teruskan ke Ketua
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #pinjamanTable tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.15s ease;
    }
    #pinjamanTable tbody tr:hover {
        background-color: #f8fafc !important;
    }
    #pinjamanTable tbody td {
        padding: 14px 16px !important;
        vertical-align: middle;
        font-size: 0.875rem;
    }
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
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem !important;
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
        $('#lastRefresh').text(new Date().toLocaleTimeString('id-ID'));

        table = $('#pinjamanTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('bendahara.pinjaman.data') }}",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {
                    data: 'no_pinjaman', name: 'no_pinjaman', 
                    render: function(data, type, row) {
                        return `<div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-gradient-to-br from-slate-100 to-slate-50 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200">
                                <i class="fas fa-file-invoice-dollar text-slate-400 text-xs"></i>
                            </div>
                            <div>
                                <p class="font-mono text-xs font-bold text-slate-700">${row.no_pinjaman}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5"><i class="far fa-calendar text-[9px] mr-1"></i>${new Date(row.tanggal_pengajuan).toLocaleDateString('id-ID')}</p>
                            </div>
                        </div>`;
                    }
                },
                {
                    data: 'anggota.nama_lengkap', name: 'anggota.nama_lengkap', 
                    render: function(data, type, row) {
                        const initial = data ? data.charAt(0).toUpperCase() : '?';
                        return `<div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary-100 to-primary-50 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-600 text-xs font-bold">${initial}</span>
                            </div>
                            <span class="font-semibold text-slate-800 text-sm">${data}</span>
                        </div>`;
                    }
                },
                {data: 'nominal', name: 'nominal', className: 'text-right font-bold text-primary-600 tabular-nums'},
                {data: 'angsuran_per_bulan', name: 'angsuran_per_bulan', className: 'text-right font-semibold text-slate-600 tabular-nums'},
                {data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { search: "Cari:", lengthMenu: "Tampilkan _MENU_ data", info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data", infoEmpty: "Menampilkan 0 sampai 0 dari 0 data", infoFiltered: "(disaring dari _MAX_ data)", zeroRecords: "Tidak ada data", paginate: { next: "›", previous: "‹" } },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-5 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-5 gap-4"ip>',
            drawCallback: function() {
                $('#lastRefresh').text(new Date().toLocaleTimeString('id-ID'));
            },
            order: [[0, 'desc']]
        });

        $('#filterStatus').change(function(){
            table.draw();
        });

        $('#pinjamanTable').on('click', '.btn-verify, .btn-detail', function() {
            const id = $(this).data('id');
            const isVerify = $(this).hasClass('btn-verify');
            
            showLoading();
            $.get(`/bendahara/pinjaman/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    const p = res.data;
                    $('#v_id').val(p.id);
                    $('#v_nominal').text('Rp ' + new Intl.NumberFormat('id-ID').format(p.nominal));
                    $('#v_tenor').text(p.lama_cicilan);
                    $('#v_bunga').text(p.bunga_persen);
                    $('#v_angsuran').text('Rp ' + new Intl.NumberFormat('id-ID').format(p.angsuran_per_bulan));
                    $('#v_tujuan').text(p.tujuan_pinjaman || '-');
                    
                    $('#v_nama').text(p.anggota.nama_lengkap);
                    $('#v_inisial').text(p.anggota.nama_lengkap.charAt(0).toUpperCase());
                    $('#v_noanggota').text('No. ' + p.anggota.no_anggota);
                    $('#v_pekerjaan').text(p.anggota.pekerjaan || '-');
                    
                    if(isVerify) {
                        $('#v_catatan').val('').prop('readonly', false);
                        $('#actionButtons').removeClass('hidden');
                    } else {
                        $('#v_catatan').val(p.catatan_bendahara || 'Tidak ada catatan.').prop('readonly', true);
                        $('#actionButtons').addClass('hidden');
                    }
                    
                    $('#verifyModal').removeClass('hidden');
                }
            });
        });
    });

    function closeVerifyModal() {
        $('#verifyModal').addClass('hidden');
    }

    function prosesVerifikasi(action) {
        const id = $('#v_id').val();
        const catatan = $('#v_catatan').val();
        
        if(action === 'reject' && catatan.trim() === '') {
            showToast('warning', 'Wajib mengisi alasan penolakan pada kolom catatan.');
            $('#v_catatan').focus();
            return;
        }

        const btnLabel = action === 'approve' ? 'Teruskan' : 'Tolak';
        
        Swal.fire({
            title: btnLabel + ' Pinjaman?',
            text: action === 'approve' ? 'Apakah Anda yakin ingin merekomendasikan pinjaman ini ke Ketua?' : 'Apakah Anda yakin ingin menolak pinjaman ini?',
            icon: action === 'approve' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'approve' ? '#2563eb' : '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, ' + btnLabel,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: `/bendahara/pinjaman/${id}/verify`,
                    method: 'POST',
                    data: { action: action, catatan: catatan, _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        hideLoading();
                        if(res.success) {
                            closeVerifyModal();
                            showToast('success', res.message);
                            table.ajax.reload();
                        }
                    }
                });
            }
        });
    }
</script>
@endpush

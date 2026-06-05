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
        <p class="text-slate-500 text-sm mt-1">Evaluasi kelayakan pengajuan pinjaman anggota</p>
    </div>
    
    <div class="flex gap-2">
        <select id="filterStatus" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 shadow-sm">
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
    <div class="p-5">
        <table id="pinjamanTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">No. Pinjaman / Tgl</th>
                    <th class="px-4 py-3 font-medium">Anggota</th>
                    <th class="px-4 py-3 font-medium text-right">Nominal Pengajuan</th>
                    <th class="px-4 py-3 font-medium text-right">Angsuran / Bln</th>
                    <th class="px-4 py-3 font-medium text-center">Status</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Verifikasi Pinjaman -->
<div id="verifyModal" class="modal-backdrop hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg"><i class="fas fa-search-dollar text-primary-500 mr-2"></i> Evaluasi Pengajuan Pinjaman</h3>
            <button onclick="closeVerifyModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1 bg-slate-50/30">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Pengajuan -->
                <div>
                    <h4 class="font-bold text-slate-800 text-sm mb-3 uppercase tracking-wider">Detail Pengajuan</h4>
                    
                    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden mb-4">
                        <div class="p-4 border-b border-slate-100 bg-primary-50">
                            <p class="text-xs text-primary-600 font-medium">Nominal Diajukan</p>
                            <p class="text-2xl font-bold text-primary-700" id="v_nominal">Rp 0</p>
                        </div>
                        <div class="p-4 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-500">Tenor</p>
                                <p class="font-bold text-slate-800"><span id="v_tenor">0</span> Bulan</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Bunga</p>
                                <p class="font-bold text-slate-800"><span id="v_bunga">0</span>% / Bln</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-slate-500">Angsuran Per Bulan</p>
                                <p class="font-bold text-slate-800 text-lg" id="v_angsuran">Rp 0</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-slate-500">Tujuan Pinjaman</p>
                                <p class="font-medium text-slate-700 text-sm mt-1 p-2 bg-slate-50 rounded-lg border border-slate-100" id="v_tujuan">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evaluasi Risiko -->
                <div>
                    <h4 class="font-bold text-slate-800 text-sm mb-3 uppercase tracking-wider">Kelayakan Anggota</h4>
                    
                    <div class="bg-white p-4 rounded-xl border border-slate-200 mb-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center font-bold" id="v_inisial"></div>
                            <div>
                                <p class="font-bold text-slate-800" id="v_nama"></p>
                                <p class="text-xs text-slate-500" id="v_noanggota"></p>
                            </div>
                        </div>
                        
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Pekerjaan</span>
                                <span class="text-sm font-semibold text-slate-800" id="v_pekerjaan">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Status</span>
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-bold rounded-full">Anggota Aktif</span>
                            </div>
                        </div>
                    </div>

                    <form id="formVerify">
                        <input type="hidden" id="v_id">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Evaluasi / Opini Bendahara</label>
                        <textarea id="v_catatan" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm" rows="3" placeholder="Berikan catatan pertimbangan untuk Ketua (misal: Anggota rajin menabung, riwayat cicilan sebelumnya lancar, dsb)."></textarea>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="prosesVerifikasi('reject')" id="btnReject" class="px-6 py-2 bg-white border border-red-200 text-red-600 rounded-xl hover:bg-red-50 font-bold transition">
                Tolak
            </button>
            <button onclick="prosesVerifikasi('approve')" id="btnApprove" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition flex items-center gap-2">
                Teruskan ke Ketua <i class="fas fa-paper-plane ml-1"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let table;
    $(document).ready(function() {
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
                        return `<div class="py-2"><p class="font-mono text-xs font-bold text-slate-700">${row.no_pinjaman}</p>
                                <p class="text-[10px] text-slate-500">${new Date(row.tanggal_pengajuan).toLocaleDateString('id-ID')}</p></div>`;
                    }
                },
                {data: 'anggota.nama_lengkap', name: 'anggota.nama_lengkap', className: 'font-bold text-slate-800 text-sm'},
                {data: 'nominal', name: 'nominal', className: 'text-right font-semibold text-primary-600'},
                {data: 'angsuran_per_bulan', name: 'angsuran_per_bulan', className: 'text-right font-medium text-slate-700'},
                {data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('flex items-center gap-1 mt-4');
            }
        });

        $('#filterStatus').change(function(){
            table.draw();
        });

        $('#pinjamanTable').on('click', '.btn-verify', function() {
            const id = $(this).data('id');
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
                    $('#v_tujuan').text(p.tujuan_pinjaman);
                    
                    $('#v_nama').text(p.anggota.nama_lengkap);
                    $('#v_inisial').text(p.anggota.nama_lengkap.charAt(0).toUpperCase());
                    $('#v_noanggota').text('No. ' + p.anggota.no_anggota);
                    $('#v_pekerjaan').text(p.anggota.pekerjaan);
                    
                    $('#v_catatan').val('');
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
            icon: action === 'approve' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'approve' ? '#2563eb' : '#ef4444',
            confirmButtonText: 'Ya, ' + btnLabel
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: `/bendahara/pinjaman/${id}/verify`,
                    method: 'POST',
                    data: { action: action, catatan: catatan },
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

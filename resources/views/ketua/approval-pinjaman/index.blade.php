@extends('layouts.admin')

@section('title', 'Persetujuan Pinjaman')

@section('breadcrumb')
    <a href="{{ route('ketua.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Persetujuan Pinjaman</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Persetujuan Pinjaman</h2>
        <p class="text-slate-500 text-sm mt-1">Daftar pengajuan pinjaman yang telah direkomendasikan Bendahara.</p>
    </div>
    
    <div class="flex gap-2">
        <select id="filterStatus" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 shadow-sm">
            <option value="menunggu_ketua" selected>Menunggu Persetujuan</option>
            <option value="disetujui">Telah Disetujui (Aktif)</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5">
        <table id="pinjamanTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">Anggota / No. Pinjaman</th>
                    <th class="px-4 py-3 font-medium text-right">Nominal Diajukan</th>
                    <th class="px-4 py-3 font-medium">Catatan Bendahara</th>
                    <th class="px-4 py-3 font-medium text-center">Status</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Approval -->
<div id="approvalModal" class="modal-backdrop hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Persetujuan Final Pencairan Pinjaman</h3>
            <button onclick="closeApprovalModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="flex items-center gap-4 border-b border-slate-100 pb-4 mb-4">
                <div class="w-12 h-12 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-xl font-bold" id="m_inisial"></div>
                <div>
                    <h4 class="font-bold text-slate-800" id="m_nama">Nama</h4>
                    <p class="text-xs text-slate-500" id="m_no_anggota">No: -</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-primary-50 p-4 rounded-xl border border-primary-100">
                    <p class="text-xs font-bold text-primary-600 mb-1">Nominal Pinjaman</p>
                    <p class="text-xl font-bold text-primary-700" id="m_nominal">Rp 0</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <p class="text-xs text-slate-500 mb-1">Tenor & Bunga</p>
                    <p class="font-bold text-slate-800"><span id="m_tenor">0</span> Bln (<span id="m_bunga">0</span>%)</p>
                </div>
                <div class="col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <p class="text-xs text-slate-500 mb-1">Tujuan Pinjaman</p>
                    <p class="text-sm font-semibold text-slate-800" id="m_tujuan">-</p>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-4">
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Rekomendasi Bendahara:</p>
                <p class="text-sm text-amber-900 italic" id="m_catatan_bendahara">"-"</p>
            </div>

            <form id="formApproval">
                <input type="hidden" id="m_id">
                <label class="block text-sm font-medium text-slate-700 mb-1">Keputusan & Catatan Ketua</label>
                <textarea id="m_catatan" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm" rows="2" placeholder="Catatan untuk diarsipkan..."></textarea>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="prosesApproval('reject')" id="btnReject" class="px-6 py-2 bg-white border border-red-200 text-red-600 rounded-xl hover:bg-red-50 font-bold transition">
                Tolak
            </button>
            <button onclick="prosesApproval('approve')" id="btnApprove" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition flex items-center gap-2">
                <i class="fas fa-check"></i> ACC & Cairkan
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
                url: "{{ route('ketua.approval-pinjaman.data') }}",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {
                    data: 'no_pinjaman', name: 'no_pinjaman', 
                    render: function(data, type, row) {
                        return `<div>
                            <p class="font-bold text-slate-800 text-sm">${row.anggota.nama_lengkap}</p>
                            <p class="text-[10px] text-slate-500 font-mono">${row.no_pinjaman}</p>
                        </div>`;
                    }
                },
                {
                    data: 'nominal', 
                    className: 'text-right font-bold text-primary-600',
                },
                {
                    data: 'catatan_bendahara', 
                    render: function(data) {
                        return data ? `<span class="text-xs italic text-slate-600">"${data}"</span>` : '-';
                    }
                },
                {data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4 gap-4"ip>',
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('flex items-center gap-1');
            }
        });

        $('#filterStatus').change(function(){
            table.draw();
        });

        $('#pinjamanTable').on('click', '.btn-approve', function() {
            const id = $(this).data('id');
            showLoading();
            
            // Use new ketua endpoint
            $.get(`/ketua/approval-pinjaman/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    const p = res.data;
                    $('#m_id').val(p.id);
                    $('#m_nama').text(p.anggota.nama_lengkap);
                    $('#m_inisial').text(p.anggota.nama_lengkap.charAt(0).toUpperCase());
                    $('#m_no_anggota').text('No: ' + p.anggota.no_anggota);
                    
                    $('#m_nominal').text('Rp ' + new Intl.NumberFormat('id-ID').format(p.nominal));
                    $('#m_tenor').text(p.lama_cicilan);
                    $('#m_bunga').text(p.bunga_persen);
                    $('#m_tujuan').text(p.tujuan_pinjaman);
                    
                    $('#m_catatan_bendahara').text(p.catatan_bendahara || 'Tidak ada catatan dari Bendahara.');
                    $('#m_catatan').val('');
                    
                    $('#approvalModal').removeClass('hidden');
                }
            });
        });
    });

    function closeApprovalModal() {
        $('#approvalModal').addClass('hidden');
    }

    function prosesApproval(action) {
        const id = $('#m_id').val();
        const catatan = $('#m_catatan').val();
        
        if(action === 'reject' && catatan.trim() === '') {
            showToast('warning', 'Wajib mengisi alasan penolakan.');
            $('#m_catatan').focus();
            return;
        }

        const url = action === 'approve' 
            ? `/ketua/approval-pinjaman/${id}/approve`
            : `/ketua/approval-pinjaman/${id}/reject`;
            
        const btnLabel = action === 'approve' ? 'ACC Pinjaman' : 'Tolak';
        
        Swal.fire({
            title: btnLabel + '?',
            icon: action === 'approve' ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'approve' ? '#2563eb' : '#ef4444',
            confirmButtonText: 'Ya, ' + btnLabel
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', catatan_ketua: catatan },
                    success: function(res) {
                        hideLoading();
                        if(res.success) {
                            closeApprovalModal();
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

@extends('layouts.admin')

@section('title', 'Persetujuan Anggota Baru')

@section('breadcrumb')
    <a href="{{ route('ketua.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Persetujuan Anggota</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Persetujuan Anggota Baru</h2>
        <p class="text-slate-500 text-sm mt-1">Daftar pendaftaran anggota yang telah diverifikasi Bendahara dan menunggu pengesahan Anda.</p>
    </div>
    
    <div class="flex gap-2">
        <select id="filterStatus" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 shadow-sm">
            <option value="menunggu_ketua" selected>Menunggu Persetujuan</option>
            <option value="aktif">Telah Disetujui (Aktif)</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5">
        <table id="anggotaTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">Calon Anggota</th>
                    <th class="px-4 py-3 font-medium">Pekerjaan</th>
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
    <div class="modal-content bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Persetujuan Final</h3>
            <button onclick="closeApprovalModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-3" id="m_inisial"></div>
                <h4 class="font-bold text-slate-800 text-lg" id="m_nama">Nama</h4>
                <p class="text-sm text-slate-500" id="m_nik">NIK: -</p>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-4">
                <p class="text-xs font-bold text-amber-700 uppercase tracking-wider mb-1">Catatan Verifikasi Bendahara:</p>
                <p class="text-sm text-amber-900 italic" id="m_catatan_bendahara">"-"</p>
            </div>

            <form id="formApproval">
                <input type="hidden" id="m_id">
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea id="m_catatan" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm" rows="2" placeholder="Catatan persetujuan atau alasan penolakan..."></textarea>
            </form>
        </div>

        <div class="p-5 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button onclick="prosesApproval('reject')" id="btnReject" class="px-6 py-2 bg-white border border-red-200 text-red-600 rounded-xl hover:bg-red-50 font-bold transition">
                Tolak
            </button>
            <button onclick="prosesApproval('approve')" id="btnApprove" class="px-6 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-bold shadow-md shadow-primary-500/30 transition flex items-center gap-2">
                <i class="fas fa-check"></i> Setujui Menjadi Anggota
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let table;
    $(document).ready(function() {
        table = $('#anggotaTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('ketua.approval-anggota.data') }}",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {
                    data: 'nama_lengkap', name: 'nama_lengkap', 
                    render: function(data, type, row) {
                        return `<div>
                            <p class="font-bold text-slate-800 text-sm">${row.nama_lengkap}</p>
                            <p class="text-[10px] text-slate-500">NIK: ${row.nik}</p>
                        </div>`;
                    }
                },
                {data: 'pekerjaan', name: 'pekerjaan', className: 'text-sm text-slate-700'},
                {
                    data: 'catatan_verifikasi', 
                    render: function(data) {
                        return data ? `<span class="text-xs italic text-slate-600">"${data}"</span>` : '-';
                    }
                },
                {data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            language: { search: "Cari:", lengthMenu: "Tampilkan _MENU_ data", info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data", infoEmpty: "Menampilkan 0 sampai 0 dari 0 data", infoFiltered: "(disaring dari _MAX_ data)", zeroRecords: "Tidak ada data", paginate: { next: "Selanjutnya", previous: "Sebelumnya" } },
            dom: '<"flex flex-col md:flex-row justify-between items-center mb-4 gap-4"lf>rt<"flex flex-col md:flex-row justify-between items-center mt-4 gap-4"ip>',
            drawCallback: function() {
                $('.dataTables_paginate > .pagination').addClass('flex items-center gap-1');
            },
            order: [[0, 'desc']]
        });

        $('#filterStatus').change(function(){
            table.draw();
        });

        $('#anggotaTable').on('click', '.btn-approve', function() {
            const id = $(this).data('id');
            showLoading();
            
            // Use new ketua endpoint
            $.get(`/ketua/approval-anggota/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    const a = res.data;
                    $('#m_id').val(a.id);
                    $('#m_nama').text(a.nama_lengkap);
                    $('#m_inisial').text(a.nama_lengkap.charAt(0).toUpperCase());
                    $('#m_nik').text('NIK: ' + a.nik);
                    $('#m_catatan_bendahara').text(a.catatan_bendahara || 'Tidak ada catatan dari Bendahara.');
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
            ? `/ketua/approval-anggota/${id}/approve`
            : `/ketua/approval-anggota/${id}/reject`;
            
        const btnLabel = action === 'approve' ? 'Setujui' : 'Tolak';
        
        Swal.fire({
            title: btnLabel + ' Anggota?',
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

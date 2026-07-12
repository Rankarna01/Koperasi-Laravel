@extends('layouts.admin')

@section('title', 'Penarikan Dana')

@section('breadcrumb')
    <a href="{{ route('bendahara.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Penarikan Dana</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Penarikan Dana</h2>
        <p class="text-slate-500 text-sm mt-1">Validasi dan proses penarikan dana anggota.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-4 flex flex-col md:flex-row gap-3">
    <div class="w-full md:w-1/3">
        <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Status Penarikan</label>
        <select id="filterStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
            <option value="">Semua Status</option>
            <option value="menunggu_bendahara">Perlu Verifikasi Saya</option>
            <option value="disetujui_ketua">Menunggu ACC Ketua</option>
            <option value="diproses">Diproses</option>
            <option value="selesai">Selesai</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white flex justify-between items-center">
        <h3 class="font-bold text-slate-700 text-sm flex items-center gap-2">
            <i class="fas fa-money-bill-wave text-primary-500"></i> Daftar Penarikan
        </h3>
        <span class="text-xs text-slate-400">Terakhir dimuat: <span id="lastRefresh"></span></span>
    </div>
    <div class="p-5">
        <table id="tablePenarikan" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider">
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-l-xl border-b-2 border-slate-200">No. Penarikan / Tgl</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Anggota</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-right">Nominal</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200">Metode</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 border-b-2 border-slate-200 text-center">Status</th>
                    <th class="px-4 py-3.5 font-semibold bg-slate-50 rounded-r-xl border-b-2 border-slate-200 text-center" style="min-width:260px">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Detail Penarikan -->
<div id="detailModal" class="modal-backdrop hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-blue-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-money-bill-wave text-blue-500"></i> Detail Penarikan
            </h3>
            <button onclick="closeModal('detailModal')" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="detailContent" class="p-6 overflow-y-auto bg-slate-50/50"></div>
    </div>
</div>

<!-- Modal Verifikasi -->
<div id="verifyModal" class="modal-backdrop hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-green-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-check-circle text-green-500"></i> Verifikasi Penarikan
            </h3>
            <button onclick="closeModal('verifyModal')" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="verifyForm" class="p-6 bg-slate-50/50">
            <input type="hidden" id="verify_id">
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-2">Catatan Verifikasi</label>
                <p class="text-xs text-slate-500 mb-3 leading-relaxed">Berikan catatan untuk pertimbangan Ketua saat memberikan persetujuan.</p>
                <textarea id="verify_catatan" rows="3" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm resize-none" placeholder="Contoh: Anggota ini memiliki riwayat simpanan yang lancar..."></textarea>
            </div>
        </form>
        <div class="p-5 border-t border-slate-100 bg-white flex justify-end gap-3">
            <button onclick="closeModal('verifyModal')" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition text-sm">Batal</button>
            <button onclick="submitVerify()" class="px-5 py-2.5 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold shadow-lg shadow-green-500/30 transition text-sm flex items-center gap-2">
                <i class="fas fa-check"></i> Verifikasi & Teruskan
            </button>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div id="rejectModal" class="modal-backdrop hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-red-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-times-circle text-red-500"></i> Tolak Penarikan
            </h3>
            <button onclick="closeModal('rejectModal')" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="rejectForm" class="p-6 bg-slate-50/50">
            <input type="hidden" id="reject_id">
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                <textarea id="reject_catatan" rows="4" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition text-sm resize-none" placeholder="Tuliskan alasan penolakan..." required></textarea>
            </div>
        </form>
        <div class="p-5 border-t border-slate-100 bg-white flex justify-end gap-3">
            <button onclick="closeModal('rejectModal')" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition text-sm">Batal</button>
            <button onclick="submitReject()" class="px-5 py-2.5 bg-red-600 text-white rounded-xl hover:bg-red-700 font-bold shadow-lg shadow-red-500/30 transition text-sm flex items-center gap-2">
                <i class="fas fa-times"></i> Tolak Penarikan
            </button>
        </div>
    </div>
</div>

<!-- Modal Proses Transfer -->
<div id="processModal" class="modal-backdrop hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-gradient-to-r from-blue-50 to-white">
            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-money-bill-transfer text-blue-500"></i> Proses Transfer
            </h3>
            <button onclick="closeModal('processModal')" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="processForm" class="p-6 bg-slate-50/50" enctype="multipart/form-data">
            <input type="hidden" id="process_id">
            <div id="rekeningInfo" class="mb-4 p-4 bg-white rounded-xl border border-slate-200 text-sm space-y-1"></div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-2">Bukti Transfer <span class="text-red-500">*</span></label>
                <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/*" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            </div>
        </form>
        <div class="p-5 border-t border-slate-100 bg-white flex justify-end gap-3">
            <button onclick="closeModal('processModal')" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 font-bold transition text-sm">Batal</button>
            <button onclick="submitProcess()" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold shadow-lg shadow-blue-500/30 transition text-sm flex items-center gap-2">
                <i class="fas fa-money-bill-transfer"></i> Proses Transfer
            </button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    #tablePenarikan tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.15s ease;
    }
    #tablePenarikan tbody tr:hover {
        background-color: #f8fafc !important;
    }
    #tablePenarikan tbody td {
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

    table = $('#tablePenarikan').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("bendahara.penarikan.data") }}',
            data: function(d) {
                d.status = $('#filterStatus').val();
            }
        },
        columns: [
            {
                data: 'no_penarikan', name: 'no_penarikan',
                render: function(data, type, row) {
                    return `<div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-gradient-to-br from-slate-100 to-slate-50 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-200">
                            <i class="fas fa-money-bill-wave text-slate-400 text-xs"></i>
                        </div>
                        <div>
                            <p class="font-mono text-xs font-bold text-slate-700">${row.no_penarikan}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5"><i class="far fa-calendar text-[9px] mr-1"></i>${new Date(row.created_at).toLocaleDateString('id-ID')}</p>
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
            {
                data: 'metode_pembayaran', name: 'metode_pembayaran',
                render: function(data) {
                    const icon = data === 'transfer' ? 'fa-university' : 'fa-money-bill';
                    return `<span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-medium rounded-lg"><i class="fas ${icon} text-[10px]"></i> ${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                }
            },
            {data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center'},
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
});

function showDetail(id) {
    $.get('{{ route("bendahara.penarikan.data") }}', function(data) {
        const item = data.data.find(d => d.id === id);
        if (item) {
            let html = `
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 p-4 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-blue-100 text-center">
                        <p class="text-[11px] text-blue-600 font-bold uppercase tracking-wider mb-1">Nominal Penarikan</p>
                        <p class="text-2xl font-extrabold text-blue-700 tracking-tight">${item.nominal}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">No. Penarikan</p>
                        <p class="font-mono text-sm font-bold text-slate-800">${item.no_penarikan}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Tanggal</p>
                        <p class="text-sm font-medium text-slate-800">${new Date(item.created_at).toLocaleDateString('id-ID')}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Anggota</p>
                        <p class="text-sm font-bold text-slate-800">${item.anggota.nama_lengkap}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Metode</p>
                        <p class="text-sm font-medium text-slate-800 capitalize">${item.metode_pembayaran}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Status</p>
                        ${item.status}
                    </div>
                </div>
                ${item.rekening_bank ? `
                <div class="mt-4 p-4 bg-white rounded-xl border border-slate-200">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Data Rekening Bank</p>
                    <div class="space-y-2">
                        <div class="flex justify-between"><span class="text-xs text-slate-500">Bank</span><span class="text-sm font-medium">${item.rekening_bank.nama_bank || '-'}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-slate-500">No. Rekening</span><span class="text-sm font-medium">${item.rekening_bank.no_rekening || '-'}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-slate-500">Nama Pemilik</span><span class="text-sm font-medium">${item.rekening_bank.nama_rekening || '-'}</span></div>
                    </div>
                </div>` : ''}
                ${item.catatan_bendahara ? `<div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100"><p class="text-xs font-bold text-slate-500 mb-1">Catatan Bendahara</p><p class="text-sm text-slate-700">${item.catatan_bendahara}</p></div>` : ''}
                ${item.catatan_ketua ? `<div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100"><p class="text-xs font-bold text-slate-500 mb-1">Catatan Ketua</p><p class="text-sm text-slate-700">${item.catatan_ketua}</p></div>` : ''}
            `;
            $('#detailContent').html(html);
            document.getElementById('detailModal').classList.remove('hidden');
        }
    });
}

function showVerifyModal(id) {
    document.getElementById('verify_id').value = id;
    document.getElementById('verify_catatan').value = '';
    document.getElementById('verifyModal').classList.remove('hidden');
}

function showRejectModal(id) {
    document.getElementById('reject_id').value = id;
    document.getElementById('reject_catatan').value = '';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function showProcessModal(id) {
    document.getElementById('process_id').value = id;
    $.get('{{ route("bendahara.penarikan.data") }}', function(data) {
        const item = data.data.find(d => d.id === id);
        if (item && item.rekening_bank) {
            $('#rekeningInfo').html(`
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Data Rekening Tujuan</p>
                <div class="flex justify-between"><span class="text-xs text-slate-500">Bank</span><span class="text-sm font-medium">${item.rekening_bank.nama_bank}</span></div>
                <div class="flex justify-between"><span class="text-xs text-slate-500">No. Rekening</span><span class="text-sm font-medium">${item.rekening_bank.no_rekening}</span></div>
                <div class="flex justify-between"><span class="text-xs text-slate-500">Nama</span><span class="text-sm font-medium">${item.rekening_bank.nama_rekening}</span></div>
            `);
        }
    });
    document.getElementById('processModal').classList.remove('hidden');
}

function showProcessCashModal(id) {
    Swal.fire({
        title: 'Proses Penarikan Cash?',
        text: 'Konfirmasi penarikan cash telah diserahkan ke anggota.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Proses',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            $.ajax({
                url: '/bendahara/penarikan/' + id + '/process-cash',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    hideLoading();
                    if (res.success) {
                        showToast('success', res.message);
                        table.ajax.reload();
                    }
                }
            });
        }
    });
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function submitVerify() {
    const id = document.getElementById('verify_id').value;
    showLoading();
    $.ajax({
        url: '/bendahara/penarikan/' + id + '/verify',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            catatan_bendahara: document.getElementById('verify_catatan').value,
        },
        success: function(res) {
            hideLoading();
            if (res.success) {
                closeModal('verifyModal');
                showToast('success', res.message);
                table.ajax.reload();
            }
        }
    });
}

function submitReject() {
    const id = document.getElementById('reject_id').value;
    const catatan = document.getElementById('reject_catatan').value;
    if (catatan.trim() === '') {
        showToast('warning', 'Wajib mengisi alasan penolakan.');
        document.getElementById('reject_catatan').focus();
        return;
    }
    showLoading();
    $.ajax({
        url: '/bendahara/penarikan/' + id + '/reject',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            catatan_bendahara: catatan,
        },
        success: function(res) {
            hideLoading();
            if (res.success) {
                closeModal('rejectModal');
                showToast('success', res.message);
                table.ajax.reload();
            }
        }
    });
}

function submitProcess() {
    const id = document.getElementById('process_id').value;
    const fileInput = document.getElementById('bukti_transfer');
    if (!fileInput.files.length) {
        showToast('warning', 'Pilih bukti transfer terlebih dahulu.');
        return;
    }
    showLoading();
    const formData = new FormData(document.getElementById('processForm'));
    formData.append('_token', '{{ csrf_token() }}');
    $.ajax({
        url: '/bendahara/penarikan/' + id + '/process-transfer',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            hideLoading();
            if (res.success) {
                closeModal('processModal');
                showToast('success', res.message);
                table.ajax.reload();
            }
        }
    });
}
</script>
@endpush

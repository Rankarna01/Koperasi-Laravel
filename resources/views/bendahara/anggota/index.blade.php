@extends('layouts.admin')

@section('title', 'Data Anggota')

@section('breadcrumb')
    <a href="{{ route('bendahara.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">Data Anggota</span>
@endsection

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Manajemen Anggota</h2>
        <p class="text-slate-500 text-sm mt-1">Verifikasi anggota baru dan pantau daftar keanggotaan</p>
    </div>
    
    <div class="flex gap-2">
        <select id="filterStatus" class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 shadow-sm">
            <option value="menunggu_bendahara">Perlu Verifikasi Saya</option>
            <option value="menunggu_ketua">Menunggu ACC Ketua</option>
            <option value="aktif">Aktif</option>
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
        <h3 class="font-bold text-slate-800"><i class="fas fa-users text-primary-500 mr-2"></i> Daftar Anggota</h3>
    </div>
    <div class="p-5">
        <table id="anggotaTable" class="w-full text-left border-collapse" style="width:100%">
            <thead>
                <tr class="text-xs text-slate-500 uppercase tracking-wider bg-slate-50">
                    <th class="px-4 py-3 rounded-l-lg font-medium">No. Anggota</th>
                    <th class="px-4 py-3 font-medium">Nama Lengkap</th>
                    <th class="px-4 py-3 font-medium">No. Telepon</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 rounded-r-lg font-medium text-center">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal Verifikasi -->
<div id="verifyModal" class="modal-backdrop hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Verifikasi Pendaftaran</h3>
            <button onclick="closeVerifyModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1 bg-slate-50/30">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-2xl font-bold" id="v_inisial">
                    A
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-lg" id="v_nama">Nama Anggota</h4>
                    <p class="text-sm text-slate-500" id="v_nik">NIK: -</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-3 rounded-xl border border-slate-200">
                        <p class="text-xs text-slate-500 mb-1">Tempat, Tgl Lahir</p>
                        <p class="text-sm font-semibold text-slate-800" id="v_ttl">-</p>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-slate-200">
                        <p class="text-xs text-slate-500 mb-1">Pekerjaan</p>
                        <p class="text-sm font-semibold text-slate-800" id="v_pekerjaan">-</p>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-xl border border-slate-200">
                    <p class="text-xs text-slate-500 mb-1">Alamat Lengkap</p>
                    <p class="text-sm font-semibold text-slate-800" id="v_alamat">-</p>
                </div>

                <div class="border-t border-slate-200 pt-4 mt-2">
                    <h5 class="font-bold text-slate-800 text-sm mb-3">Pengecekan Pembayaran Awal</h5>
                    
                    <div class="flex items-center gap-3 bg-green-50 text-green-700 p-3 rounded-xl border border-green-200 mb-4">
                        <i class="fas fa-check-circle text-xl"></i>
                        <div>
                            <p class="text-sm font-bold">Total Diterima: Rp 600.000</p>
                            <p class="text-xs">Simpanan Pokok + Wajib Bulan Pertama</p>
                        </div>
                    </div>

                    <div id="v_bukti_container" class="hidden mb-4">
                        <p class="text-xs font-bold text-slate-500 mb-2">Bukti Pembayaran (Transfer BCA)</p>
                        <a id="v_bukti_link" href="#" target="_blank" class="block w-full border border-slate-200 rounded-xl overflow-hidden hover:opacity-90 transition">
                            <img id="v_bukti_img" src="" alt="Bukti Pembayaran" class="w-full h-auto object-contain max-h-48 bg-slate-100">
                        </a>
                        <p class="text-[10px] text-slate-400 mt-1 italic">*Klik gambar untuk melihat ukuran penuh</p>
                    </div>

                    <form id="formVerify" onsubmit="event.preventDefault(); submitVerification();">
                        <input type="hidden" id="v_id">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Verifikasi (Opsional)</label>
                        <textarea id="v_catatan" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition text-sm" rows="2" placeholder="Cth: Dokumen KTP valid dan uang pendaftaran sudah masuk ke rekening Kas Koperasi."></textarea>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-5 border-t border-slate-100 bg-white flex justify-end gap-3">
            <button onclick="closeVerifyModal()" class="px-4 py-2 border border-slate-300 rounded-xl text-slate-600 hover:bg-slate-50 font-medium transition">Batal</button>
            <button onclick="submitVerification()" id="btnProses" class="px-6 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold shadow-md shadow-green-500/30 transition flex items-center gap-2">
                <i class="fas fa-check-double"></i> Verifikasi Valid
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#anggotaTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('bendahara.anggota.data') }}",
                data: function (d) {
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {data: 'no_anggota', name: 'no_anggota', className: 'font-mono text-xs font-semibold text-slate-600 py-4'},
                {data: 'nama_lengkap', name: 'nama_lengkap', className: 'font-bold text-slate-800 text-sm py-4'},
                {data: 'no_telepon', name: 'no_telepon', className: 'text-sm py-4'},
                {data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'py-4'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center py-4'},
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

        // Delegate verifikasi
        $('#anggotaTable').on('click', '.btn-verify', function() {
            const id = $(this).data('id');
            showLoading();
            
            $.get(`/bendahara/anggota/${id}`, function(res) {
                hideLoading();
                if(res.success) {
                    const a = res.data;
                    $('#v_id').val(a.id);
                    $('#v_nama').text(a.nama_lengkap);
                    $('#v_inisial').text(a.nama_lengkap.charAt(0).toUpperCase());
                    $('#v_nik').text('NIK: ' + a.nik);
                    $('#v_ttl').text(a.tempat_lahir + ', ' + new Date(a.tanggal_lahir).toLocaleDateString('id-ID'));
                    $('#v_pekerjaan').text(a.pekerjaan);
                    $('#v_alamat').text(a.alamat);
                    $('#v_catatan').val('');
                    
                    if (a.bukti_pembayaran) {
                        const url = '/storage/' + a.bukti_pembayaran;
                        $('#v_bukti_img').attr('src', url);
                        $('#v_bukti_link').attr('href', url);
                        $('#v_bukti_container').removeClass('hidden');
                    } else {
                        $('#v_bukti_container').addClass('hidden');
                    }
                    
                    $('#verifyModal').removeClass('hidden');
                }
            });
        });

        // Delegate detail (View Only)
        $('#anggotaTable').on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Sedang Memuat',
                html: '<i class="fas fa-spinner fa-spin text-2xl text-primary-500"></i>',
                showConfirmButton: false,
                didOpen: () => {
                    $.get(`/bendahara/anggota/${id}`, function(res) {
                        const a = res.data;
                        let buktiHtml = '<p class="text-slate-400 italic text-xs">Belum ada bukti pembayaran</p>';
                        if (a.bukti_pembayaran) {
                            const buktiUrl = '/storage/' + a.bukti_pembayaran;
                            buktiHtml = `<a href="${buktiUrl}" target="_blank" class="block border border-slate-200 rounded-xl overflow-hidden hover:opacity-90 transition"><img src="${buktiUrl}" alt="Bukti Pembayaran" class="w-full h-auto object-contain max-h-48 bg-slate-100"></a><p class="text-[10px] text-slate-400 mt-1 italic">*Klik gambar untuk melihat ukuran penuh</p>`;
                        }
                        Swal.fire({
                            title: 'Profil Anggota',
                            html: `
                                <div class="text-left text-sm space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-slate-50 p-3 rounded-xl"><p class="text-xs text-slate-500 mb-1">Nama Lengkap</p><p class="font-semibold text-slate-800">${a.nama_lengkap}</p></div>
                                        <div class="bg-slate-50 p-3 rounded-xl"><p class="text-xs text-slate-500 mb-1">NIK</p><p class="font-semibold text-slate-800 font-mono text-xs">${a.nik}</p></div>
                                        <div class="bg-slate-50 p-3 rounded-xl"><p class="text-xs text-slate-500 mb-1">Pekerjaan</p><p class="font-semibold text-slate-800">${a.pekerjaan || '-'}</p></div>
                                        <div class="bg-slate-50 p-3 rounded-xl"><p class="text-xs text-slate-500 mb-1">No. Telepon</p><p class="font-semibold text-slate-800">${a.no_telepon || '-'}</p></div>
                                    </div>
                                    <div class="bg-slate-50 p-3 rounded-xl"><p class="text-xs text-slate-500 mb-1">Alamat</p><p class="font-semibold text-slate-800">${a.alamat || '-'}</p></div>
                                    <div class="border-t border-slate-200 pt-3">
                                        <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Bukti Pembayaran Pendaftaran</p>
                                        ${buktiHtml}
                                    </div>
                                </div>
                            `,
                            width: 520,
                            confirmButtonColor: '#2563eb'
                        });
                    });
                }
            });
        });
    });

    function closeVerifyModal() {
        $('#verifyModal').addClass('hidden');
    }

    function submitVerification() {
        const id = $('#v_id').val();
        const catatan = $('#v_catatan').val();
        
        const btn = $('#btnProses');
        const originalText = btn.html();
        btn.html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...').prop('disabled', true);

        $.ajax({
            url: `/bendahara/anggota/${id}/verify`,
            method: 'POST',
            data: { catatan: catatan },
            success: function(res) {
                if(res.success) {
                    closeVerifyModal();
                    showToast('success', res.message);
                    $('#anggotaTable').DataTable().ajax.reload();
                }
            },
            error: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    }
</script>
@endpush

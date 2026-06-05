@extends('layouts.admin')

@section('title', 'Kalkulasi SHU (Sisa Hasil Usaha)')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-slate-500 hover:text-primary-600">Dashboard</a>
    <i class="fas fa-chevron-right mx-2 text-xs text-slate-300"></i>
    <span class="text-primary-600 font-medium">SHU</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 font-heading">Sisa Hasil Usaha (SHU)</h2>
        <p class="text-slate-500 text-sm mt-1">Kalkulasi dan distribusi SHU Anggota secara otomatis berdasarkan Jasa Modal & Usaha</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Form Kalkulasi -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">
            <i class="fas fa-calculator text-primary-500 mr-2"></i> Kalkulasi SHU Baru
        </h3>
        
        <form id="formShu" action="{{ route('admin.shu.calculate') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Pembukuan</label>
                <select name="tahun" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                    @for($i = date('Y'); $i >= 2023; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <p class="text-xs text-slate-400 mt-2">Sistem akan otomatis menghitung total pendapatan (bunga + laba toko) selama tahun yang dipilih, lalu membaginya sesuai porsi AD/ART koperasi.</p>
            </div>

            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl border border-blue-100 mb-6 flex gap-3">
                <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                <div class="text-xs space-y-1">
                    <p class="font-bold">Pembagian Persentase Baku:</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        <li>Dana Cadangan: 20%</li>
                        <li>Dana Anggota: 50%</li>
                        <li>Dana Pengurus: 10%</li>
                        <li>Dana Pendidikan: 10%</li>
                        <li>Dana Sosial: 10%</li>
                    </ul>
                </div>
            </div>

            <button type="submit" id="btnHitung" class="w-full bg-primary-600 text-white font-bold py-3 rounded-xl hover:bg-primary-700 transition shadow-lg shadow-primary-500/30 flex justify-center items-center gap-2">
                <i class="fas fa-cogs"></i> Hitung SHU Sekarang
            </button>
        </form>
    </div>

    <!-- Riwayat SHU -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-bold text-slate-800"><i class="fas fa-history text-primary-500 mr-2"></i> Riwayat & Draft SHU</h3>
        </div>
        
        <div class="flex-1 overflow-y-auto p-0">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 sticky top-0">
                    <tr class="text-xs text-slate-500 uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-3 font-medium">Tahun</th>
                        <th class="px-6 py-3 font-medium">SHU Bersih</th>
                        <th class="px-6 py-3 font-medium">Dana Anggota</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($periodeList as $p)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $p->tahun }}</td>
                            <td class="px-6 py-4 text-primary-600 font-semibold">Rp {{ number_format($p->shu_bersih, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 font-semibold">Rp {{ number_format($p->shu_bersih * 0.5, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($p->status === 'draft')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full">DRAFT</span>
                                @else
                                    <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">FINAL</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="lihatDetail({{ $p->id }})" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition font-medium text-xs">Lihat Rincian</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada kalkulasi SHU.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail SHU -->
<div id="detailModal" class="modal-backdrop hidden flex items-center justify-center p-4">
    <div class="modal-content bg-white w-full max-w-5xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 text-lg">Rincian Pembagian SHU Tahun <span id="m_tahun"></span></h3>
            <div class="flex items-center gap-3">
                <span id="m_status" class="text-xs font-bold px-3 py-1 rounded-full"></span>
                <button onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-200 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1 bg-slate-50/30">
            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs text-slate-500 font-medium">Total Pendapatan</p>
                    <p class="text-lg font-bold text-slate-800" id="m_pendapatan"></p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-xs text-slate-500 font-medium">Biaya Operasional (20%)</p>
                    <p class="text-lg font-bold text-red-600" id="m_biaya"></p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-primary-200 shadow-sm bg-primary-50">
                    <p class="text-xs text-primary-700 font-medium">SHU Bersih</p>
                    <p class="text-xl font-bold text-primary-700" id="m_bersih"></p>
                </div>
                <div class="bg-white p-4 rounded-xl border border-green-200 shadow-sm bg-green-50">
                    <p class="text-xs text-green-700 font-medium">Total Dana Anggota (50%)</p>
                    <p class="text-xl font-bold text-green-700" id="m_anggota"></p>
                </div>
            </div>

            <h4 class="font-bold text-slate-800 mb-3 text-sm uppercase tracking-wider">Distribusi Proporsional per Anggota</h4>
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs text-slate-500 border-b border-slate-200">
                            <th class="px-4 py-3 font-medium">No. Anggota</th>
                            <th class="px-4 py-3 font-medium">Nama Anggota</th>
                            <th class="px-4 py-3 font-medium text-right text-blue-600">Kontribusi Simpanan</th>
                            <th class="px-4 py-3 font-medium text-right text-purple-600">Bunga Pinjaman</th>
                            <th class="px-4 py-3 font-medium text-right text-orange-600">Belanja Toko</th>
                            <th class="px-4 py-3 font-medium text-right text-green-600 font-bold">Total SHU Diterima</th>
                        </tr>
                    </thead>
                    <tbody id="m_table_body">
                        <!-- Data injected by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="p-5 border-t border-slate-100 bg-white flex justify-end gap-3" id="m_action_buttons">
            <!-- Buttons injected by JS -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#formShu').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#btnHitung');
        const originalText = btn.html();
        
        btn.html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Mengkalkulasi (Membutuhkan Waktu)...').prop('disabled', true);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Kalkulasi Selesai!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Lihat Hasil',
                        confirmButtonColor: '#2563eb'
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    let currentShuId = null;

    function formatRp(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    function lihatDetail(id) {
        currentShuId = id;
        showLoading();
        
        $.get(`/admin/shu/${id}`, function(res) {
            hideLoading();
            if(res.success) {
                const data = res.data;
                
                $('#m_tahun').text(data.tahun);
                
                if(data.status === 'draft') {
                    $('#m_status').text('DRAFT').attr('class', 'text-[10px] font-bold px-3 py-1 rounded-full bg-yellow-100 text-yellow-800');
                    $('#m_action_buttons').html(`
                        <button onclick="closeDetailModal()" class="px-4 py-2 border border-slate-300 rounded-xl text-slate-600 hover:bg-slate-50 font-medium transition">Tutup</button>
                        <button onclick="finalizeShu()" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 font-bold shadow-md shadow-green-500/30 transition flex items-center gap-2">
                            <i class="fas fa-check-double"></i> Finalisasi & Posting
                        </button>
                    `);
                } else {
                    $('#m_status').text('FINAL').attr('class', 'text-[10px] font-bold px-3 py-1 rounded-full bg-green-100 text-green-800');
                    $('#m_action_buttons').html(`
                        <button class="px-4 py-2 border border-slate-300 rounded-xl text-slate-600 hover:bg-slate-50 font-medium transition flex items-center gap-2"><i class="fas fa-print"></i> Cetak Laporan</button>
                        <button onclick="closeDetailModal()" class="px-4 py-2 bg-slate-800 text-white rounded-xl hover:bg-slate-900 font-medium transition">Tutup</button>
                    `);
                }

                $('#m_pendapatan').text(formatRp(data.total_pendapatan));
                $('#m_biaya').text(formatRp(data.total_biaya));
                $('#m_bersih').text(formatRp(data.shu_bersih));
                $('#m_anggota').text(formatRp(data.shu_bersih * (data.dana_anggota_persen/100)));

                let trs = '';
                data.shu_anggota.forEach(item => {
                    trs += `
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="px-4 py-3 font-mono text-xs">${item.anggota.no_anggota}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800">${item.anggota.nama_lengkap}</td>
                            <td class="px-4 py-3 text-right text-blue-600">${formatRp(item.kontribusi_simpanan)}</td>
                            <td class="px-4 py-3 text-right text-purple-600">${formatRp(item.kontribusi_pinjaman)}</td>
                            <td class="px-4 py-3 text-right text-orange-600">${formatRp(item.kontribusi_penjualan)}</td>
                            <td class="px-4 py-3 text-right font-bold text-green-600 bg-green-50/30">${formatRp(item.total_shu)}</td>
                        </tr>
                    `;
                });
                $('#m_table_body').html(trs);

                $('#detailModal').removeClass('hidden');
            }
        });
    }

    function closeDetailModal() {
        $('#detailModal').addClass('hidden');
    }

    function finalizeShu() {
        confirmAction('Finalisasi SHU?', 'SHU yang sudah difinalisasi tidak dapat diubah atau dikalkulasi ulang. Data akan diposting sebagai laporan final.', function() {
            showLoading();
            $.post(`/admin/shu/${currentShuId}/finalize`, function(res) {
                hideLoading();
                if(res.success) {
                    showToast('success', res.message);
                    setTimeout(() => location.reload(), 1500);
                }
            });
        });
    }
</script>
@endpush

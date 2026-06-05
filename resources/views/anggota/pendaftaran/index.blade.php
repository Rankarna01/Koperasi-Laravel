@extends('layouts.app')

@section('title', 'Pendaftaran Anggota')

@section('body')
<div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
    <div class="max-w-3xl mx-auto page-enter">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30 mx-auto mb-4">
                <i class="fas fa-building-columns text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Pendaftaran <span class="text-gradient">Anggota</span>
            </h2>
            <p class="mt-2 text-slate-600">Lengkapi data diri Anda untuk bergabung dengan Koperasi Sejahtera Bersama.</p>
        </div>

        <div class="bg-white shadow-xl shadow-slate-200/50 rounded-2xl overflow-hidden border border-slate-100">
            <!-- Progress Bar -->
            <div class="bg-slate-50 border-b border-slate-100 px-8 py-4 flex justify-between items-center relative">
                <div class="absolute top-1/2 left-8 right-8 h-0.5 bg-slate-200 -z-0 -translate-y-1/2"></div>
                
                <div class="relative z-10 flex flex-col items-center step-indicator active">
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm border-4 border-white shadow-sm">1</div>
                    <span class="text-[10px] font-bold text-primary-600 mt-1 uppercase tracking-wider">Data Diri</span>
                </div>
                <div class="relative z-10 flex flex-col items-center step-indicator">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm border-4 border-white shadow-sm" id="step2-icon">2</div>
                    <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-wider" id="step2-text">Pekerjaan</span>
                </div>
                <div class="relative z-10 flex flex-col items-center step-indicator">
                    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-sm border-4 border-white shadow-sm" id="step3-icon">3</div>
                    <span class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-wider" id="step3-text">Simpanan Awal</span>
                </div>
            </div>

            <form id="formPendaftaran" action="{{ route('anggota.pendaftaran.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                
                <!-- Step 1: Data Diri -->
                <div class="step-content active" id="step1">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-2">Informasi Pribadi</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">NIK (KTP) <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" required minlength="16" maxlength="16" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition" placeholder="16 Digit NIK">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" required value="{{ $user->name }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition" placeholder="Sesuai KTP">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
                            <input type="text" name="tempat_lahir" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition" placeholder="Kota Kelahiran">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_lahir" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <div class="flex gap-4">
                                <label class="flex items-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition w-full">
                                    <input type="radio" name="jenis_kelamin" value="L" required class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                    <span class="ml-3 text-sm text-slate-700">Laki-laki</span>
                                </label>
                                <label class="flex items-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition w-full">
                                    <input type="radio" name="jenis_kelamin" value="P" required class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-slate-300">
                                    <span class="ml-3 text-sm text-slate-700">Perempuan</span>
                                </label>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="alamat" required rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition" placeholder="Alamat sesuai KTP"></textarea>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end">
                        <button type="button" onclick="nextStep(2)" class="bg-primary-600 text-white px-6 py-2.5 rounded-xl font-semibold shadow-md shadow-primary-500/30 hover:bg-primary-700 hover:-translate-y-0.5 transition-all">Selanjutnya <i class="fas fa-arrow-right ml-2"></i></button>
                    </div>
                </div>

                <!-- Step 2: Pekerjaan -->
                <div class="step-content hidden" id="step2">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-2">Pekerjaan & Kontak</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Pekerjaan <span class="text-red-500">*</span></label>
                            <input type="text" name="pekerjaan" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition" placeholder="Contoh: Karyawan Swasta">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telepon" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition" placeholder="08123456789">
                        </div>
                        <div class="md:col-span-2">
                            <div class="bg-blue-50 text-blue-800 p-4 rounded-xl border border-blue-100 flex items-start gap-3">
                                <i class="fas fa-info-circle mt-0.5 text-blue-500"></i>
                                <p class="text-sm">Pastikan nomor telepon/WhatsApp Anda aktif agar kami dapat mengirimkan informasi penting terkait keanggotaan koperasi.</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="prevStep(1)" class="bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl font-semibold hover:bg-slate-300 transition">Kembai</button>
                        <button type="button" onclick="nextStep(3)" class="bg-primary-600 text-white px-6 py-2.5 rounded-xl font-semibold shadow-md shadow-primary-500/30 hover:bg-primary-700 hover:-translate-y-0.5 transition-all">Selanjutnya <i class="fas fa-arrow-right ml-2"></i></button>
                    </div>
                </div>

                <!-- Step 3: Simpanan -->
                <div class="step-content hidden" id="step3">
                    <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-2">Persetujuan Simpanan Awal</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="p-4 border border-slate-200 rounded-xl flex justify-between items-center bg-slate-50">
                            <div>
                                <h4 class="font-bold text-slate-800">Simpanan Pokok</h4>
                                <p class="text-xs text-slate-500">Dibayarkan sekali saat pendaftaran.</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-lg text-primary-600">Rp 500.000</span>
                                <input type="hidden" name="simpanan_pokok" value="500000">
                            </div>
                        </div>

                        <div class="p-4 border border-slate-200 rounded-xl flex justify-between items-center bg-slate-50">
                            <div>
                                <h4 class="font-bold text-slate-800">Simpanan Wajib (Bulan Pertama)</h4>
                                <p class="text-xs text-slate-500">Akan ditagih rutin setiap bulan.</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-lg text-primary-600">Rp 100.000</span>
                                <input type="hidden" name="simpanan_wajib" value="100000">
                            </div>
                        </div>

                        <div class="p-4 border border-primary-200 rounded-xl flex justify-between items-center bg-primary-50">
                            <div>
                                <h4 class="font-bold text-primary-800">Total Pembayaran Awal</h4>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-2xl text-primary-700">Rp 600.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 p-5 rounded-xl mb-6">
                        <div class="flex items-center gap-3 mb-3">
                            <i class="fas fa-building-columns text-blue-600 text-xl"></i>
                            <h4 class="font-bold text-blue-900 text-sm">Instruksi Pembayaran</h4>
                        </div>
                        <p class="text-sm text-blue-800 mb-2">Silakan transfer total pembayaran awal sebesar <strong>Rp 600.000</strong> ke rekening berikut:</p>
                        <div class="bg-white p-4 rounded-lg border border-blue-100 mb-2">
                            <p class="text-xs text-slate-500 mb-1">Bank BCA</p>
                            <p class="text-lg font-bold text-slate-800 font-mono tracking-wider">123-456-7890</p>
                            <p class="text-xs font-bold text-slate-600 mt-1">A.n. KSP Sejahtera Bersama</p>
                        </div>
                        <p class="text-xs text-blue-700 italic">*Setelah mentransfer dan mengajukan pendaftaran, Bendahara akan memverifikasi pendaftaran Anda.</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Upload Bukti Pembayaran <span class="text-red-500">*</span></label>
                        <input type="file" name="bukti_pembayaran" required accept="image/*" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG. Maksimal 2MB.</p>
                    </div>

                    <div class="mb-8">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" required class="mt-1 w-5 h-5 text-primary-600 focus:ring-primary-500 border-slate-300 rounded">
                            <span class="text-sm text-slate-600">Saya menyatakan bahwa data yang saya isi adalah benar dan saya bersedia mematuhi Anggaran Dasar & Anggaran Rumah Tangga (AD/ART) Koperasi Sejahtera Bersama serta membayar simpanan awal yang telah ditentukan.</span>
                        </label>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="prevStep(2)" class="bg-slate-200 text-slate-700 px-6 py-2.5 rounded-xl font-semibold hover:bg-slate-300 transition">Kembali</button>
                        <button type="submit" id="btnSubmit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-semibold shadow-md shadow-green-500/30 hover:bg-green-700 hover:-translate-y-0.5 transition-all"><i class="fas fa-check-circle mr-2"></i> Ajukan Pendaftaran</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-slate-500 hover:text-slate-700 transition">
                    Batal dan Keluar
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function nextStep(step) {
        // Basic validation for current step
        const currentStep = step - 1;
        const inputs = document.getElementById('step' + currentStep).querySelectorAll('input[required], textarea[required]');
        
        let isValid = true;
        inputs.forEach(input => {
            if (!input.value) {
                isValid = false;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            showToast('warning', 'Harap isi semua field yang wajib.');
            return;
        }

        // Hide all
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        
        // Show target
        document.getElementById('step' + step).classList.remove('hidden');
        document.getElementById('step' + step).classList.add('page-enter');
        
        // Update indicator
        const icon = document.getElementById('step' + step + '-icon');
        icon.classList.remove('bg-slate-200', 'text-slate-500');
        icon.classList.add('bg-primary-600', 'text-white');
        
        const text = document.getElementById('step' + step + '-text');
        text.classList.remove('text-slate-400');
        text.classList.add('text-primary-600');
    }

    function prevStep(step) {
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('step' + step).classList.remove('hidden');
        document.getElementById('step' + step).classList.add('page-enter');
    }

    // Handle Submission
    $('#formPendaftaran').on('submit', function(e) {
        e.preventDefault();
        
        const btn = $('#btnSubmit');
        const originalText = btn.html();
        
        btn.html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...');
        btn.prop('disabled', true);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonColor: '#2563eb',
                        confirmButtonText: 'Ke Dashboard'
                    }).then(() => {
                        window.location.href = "{{ route('anggota.dashboard') }}";
                    });
                }
            },
            error: function(xhr) {
                btn.html(originalText);
                btn.prop('disabled', false);
            }
        });
    });
</script>
@endpush
@endsection

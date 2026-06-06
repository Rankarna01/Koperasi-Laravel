<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Ketua;
use App\Http\Controllers\Bendahara;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Anggota;

/*
|--------------------------------------------------------------------------
| Web Routes - Koperasi Sejahtera Bersama
|--------------------------------------------------------------------------
*/

// Webhook
Route::post('/webhook/midtrans', [\App\Http\Controllers\Webhook\MidtransController::class, 'handling']);

// ===========================
// AUTH ROUTES
// ===========================
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ===========================
// NOTIFICATION ROUTES (shared)
// ===========================
Route::middleware('auth')->group(function () {
    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications()->latest()->take(20)->get();
        return response()->json($notifications);
    })->name('notifications.index');

    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications()->update(['is_read' => true, 'read_at' => now()]);
        return response()->json(['success' => true]);
    })->name('notifications.read-all');

    Route::post('/notifications/{notification}/read', function (\App\Models\Notification $notification) {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.read');
});

// ===========================
// KETUA ROUTES
// ===========================
Route::middleware(['auth', 'role:ketua'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/dashboard', [Ketua\DashboardController::class, 'index'])->name('dashboard');

    // Approval Anggota
    Route::get('/approval-anggota', [Ketua\ApprovalAnggotaController::class, 'index'])->name('approval-anggota.index');
    Route::get('/approval-anggota/data', [Ketua\ApprovalAnggotaController::class, 'data'])->name('approval-anggota.data');
    Route::get('/approval-anggota/{anggota}', [Ketua\ApprovalAnggotaController::class, 'show'])->name('approval-anggota.show');
    Route::post('/approval-anggota/{anggota}/approve', [Ketua\ApprovalAnggotaController::class, 'approve'])->name('approval-anggota.approve');
    Route::post('/approval-anggota/{anggota}/reject', [Ketua\ApprovalAnggotaController::class, 'reject'])->name('approval-anggota.reject');

    // Approval Pinjaman
    Route::get('/approval-pinjaman', [Ketua\ApprovalPinjamanController::class, 'index'])->name('approval-pinjaman.index');
    Route::get('/approval-pinjaman/data', [Ketua\ApprovalPinjamanController::class, 'data'])->name('approval-pinjaman.data');
    Route::get('/approval-pinjaman/{peminjaman}', [Ketua\ApprovalPinjamanController::class, 'show'])->name('approval-pinjaman.show');
    Route::post('/approval-pinjaman/{peminjaman}/approve', [Ketua\ApprovalPinjamanController::class, 'approve'])->name('approval-pinjaman.approve');
    Route::post('/approval-pinjaman/{peminjaman}/reject', [Ketua\ApprovalPinjamanController::class, 'reject'])->name('approval-pinjaman.reject');

    // Laporan
    Route::get('/laporan/simpan-pinjam', [Ketua\LaporanController::class, 'simpanPinjam'])->name('laporan.simpan-pinjam');
    Route::get('/laporan/simpan-pinjam/data', [Ketua\LaporanController::class, 'simpanPinjamData'])->name('laporan.simpan-pinjam.data');
    Route::get('/laporan/simpan-pinjam/export/{type}', [Ketua\LaporanController::class, 'exportSimpanPinjam'])->name('laporan.simpan-pinjam.export');
    
    Route::get('/laporan/penjualan', [Ketua\LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    Route::get('/laporan/penjualan/data', [Ketua\LaporanController::class, 'penjualanData'])->name('laporan.penjualan.data');
    Route::get('/laporan/penjualan/export/{type}', [Ketua\LaporanController::class, 'exportPenjualan'])->name('laporan.penjualan.export');
});

// ===========================
// BENDAHARA ROUTES
// ===========================
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [Bendahara\DashboardController::class, 'index'])->name('dashboard');

    // Data Anggota
    Route::get('/anggota', [Bendahara\AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('/anggota/data', [Bendahara\AnggotaController::class, 'data'])->name('anggota.data');
    Route::get('/anggota/{anggota}', [Bendahara\AnggotaController::class, 'show'])->name('anggota.show');
    Route::post('/anggota/{anggota}/verify', [Bendahara\AnggotaController::class, 'verify'])->name('anggota.verify');

    // Simpanan
    Route::get('/simpanan', [Bendahara\SimpananController::class, 'index'])->name('simpanan.index');
    Route::get('/simpanan/data', [Bendahara\SimpananController::class, 'data'])->name('simpanan.data');
    Route::post('/simpanan', [Bendahara\SimpananController::class, 'store'])->name('simpanan.store');
    Route::get('/simpanan/{simpanan}/print', [Bendahara\SimpananController::class, 'print'])->name('simpanan.print');

    // Pinjaman
    Route::get('/pinjaman', [Bendahara\PeminjamanController::class, 'index'])->name('pinjaman.index');
    Route::get('/pinjaman/data', [Bendahara\PeminjamanController::class, 'data'])->name('pinjaman.data');
    Route::get('/pinjaman/{peminjaman}', [Bendahara\PeminjamanController::class, 'show'])->name('pinjaman.show');
    Route::post('/pinjaman/{peminjaman}/verify', [Bendahara\PeminjamanController::class, 'verify'])->name('pinjaman.verify');

    // Angsuran
    Route::get('/angsuran', [Bendahara\AngsuranController::class, 'index'])->name('angsuran.index');
    Route::get('/angsuran/data', [Bendahara\AngsuranController::class, 'data'])->name('angsuran.data');
    Route::post('/angsuran', [Bendahara\AngsuranController::class, 'store'])->name('angsuran.store');
    Route::get('/angsuran/{angsuran}', [Bendahara\AngsuranController::class, 'show'])->name('angsuran.show');

    // Barang
    Route::get('/barang', [Bendahara\BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/data', [Bendahara\BarangController::class, 'data'])->name('barang.data');
    Route::post('/barang', [Bendahara\BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{barang}', [Bendahara\BarangController::class, 'update'])->name('barang.update');
    Route::post('/barang/{barang}/add-stock', [Bendahara\BarangController::class, 'addStock'])->name('barang.add-stock');
});

// ===========================
// ADMIN ROUTES
// ===========================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Rekap Penjualan Bulanan
    Route::get('/penjualan', [Admin\PenjualanController::class, 'index'])->name('penjualan.index');
    Route::get('/penjualan/data', [Admin\PenjualanController::class, 'data'])->name('penjualan.data');
    Route::post('/penjualan', [Admin\PenjualanController::class, 'store'])->name('penjualan.store');
    Route::get('/penjualan/{penjualan}', [Admin\PenjualanController::class, 'show'])->name('penjualan.show');
    Route::put('/penjualan/{penjualan}', [Admin\PenjualanController::class, 'update'])->name('penjualan.update');
    Route::delete('/penjualan/{penjualan}', [Admin\PenjualanController::class, 'destroy'])->name('penjualan.destroy');

    // Data Barang (Sama dengan bendahara)
    Route::get('/barang', [Admin\BarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/data', [Admin\BarangController::class, 'data'])->name('barang.data');
    Route::post('/barang', [Admin\BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{barang}', [Admin\BarangController::class, 'show'])->name('barang.show');
    Route::put('/barang/{barang}', [Admin\BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang}', [Admin\BarangController::class, 'destroy'])->name('barang.destroy');

    // Data Supplier
    Route::get('/supplier', [Admin\SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/supplier/data', [Admin\SupplierController::class, 'data'])->name('supplier.data');
    Route::post('/supplier', [Admin\SupplierController::class, 'store'])->name('supplier.store');
    Route::get('/supplier/{supplier}', [Admin\SupplierController::class, 'show'])->name('supplier.show');
    Route::put('/supplier/{supplier}', [Admin\SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('/supplier/{supplier}', [Admin\SupplierController::class, 'destroy'])->name('supplier.destroy');

    // Pengaturan Sistem
    Route::get('/setting', [Admin\SettingController::class, 'index'])->name('setting.index');
    Route::post('/setting', [Admin\SettingController::class, 'update'])->name('setting.update');

    // Pembelian
    Route::get('/pembelian', [Admin\PembelianBarangController::class, 'index'])->name('pembelian.index');
    Route::get('/pembelian/data', [Admin\PembelianBarangController::class, 'data'])->name('pembelian.data');
    Route::post('/pembelian', [Admin\PembelianBarangController::class, 'store'])->name('pembelian.store');
    Route::get('/pembelian/{pembelian}', [Admin\PembelianBarangController::class, 'show'])->name('pembelian.show');

    // SHU
    Route::get('/shu', [Admin\SHUController::class, 'index'])->name('shu.index');
    Route::post('/shu/calculate', [Admin\SHUController::class, 'calculate'])->name('shu.calculate');
    Route::get('/shu/{shu_periode}', [Admin\SHUController::class, 'show'])->name('shu.show');
    Route::post('/shu/{shu_periode}/finalize', [Admin\SHUController::class, 'finalize'])->name('shu.finalize');
});

// ===========================
// ANGGOTA ROUTES
// ===========================
Route::middleware(['auth', 'role:anggota'])->prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/dashboard', [Anggota\DashboardController::class, 'index'])->name('dashboard');

    // Pendaftaran
    Route::get('/pendaftaran', [Anggota\PendaftaranController::class, 'index'])->name('pendaftaran');
    Route::post('/pendaftaran', [Anggota\PendaftaranController::class, 'store'])->name('pendaftaran.store');

    // Simpanan
    Route::get('/simpanan', [Anggota\SimpananController::class, 'index'])->name('simpanan.index');
    Route::get('/simpanan/data', [Anggota\SimpananController::class, 'data'])->name('simpanan.data');
    Route::get('/simpanan/summary', [Anggota\SimpananController::class, 'summary'])->name('simpanan.summary');

    // Pinjaman
    Route::get('/pinjaman', [Anggota\PeminjamanController::class, 'index'])->name('pinjaman.index');
    Route::get('/pinjaman/data', [Anggota\PeminjamanController::class, 'data'])->name('pinjaman.data');
    Route::post('/pinjaman', [Anggota\PeminjamanController::class, 'store'])->name('pinjaman.store');

    // Pembayaran
    Route::get('/pembayaran', [Anggota\PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/data', [Anggota\PembayaranController::class, 'data'])->name('pembayaran.data');
    Route::post('/pembayaran', [Anggota\PembayaranController::class, 'store'])->name('pembayaran.store');

    // Profil
    Route::view('/profil', 'anggota.profil.index')->name('profil.index');
});

<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PendaftaranController extends Controller
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $anggota = $user->anggota;
        return view('anggota.pendaftaran.index', compact('anggota', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16|unique:anggota,nik',
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:20',
            'pekerjaan' => 'required|string|max:255',
            'simpanan_pokok' => 'required|numeric|min:500000',
            'simpanan_wajib' => 'required|numeric|min:100000',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nik.size' => 'Maaf, NIK KTP Anda harus berjumlah tepat 16 digit/karakter.',
            'bukti_pembayaran.required' => 'Anda wajib melampirkan foto bukti pembayaran deposit.',
            'bukti_pembayaran.image' => 'Bukti pembayaran harus berupa gambar (JPG, PNG).',
            'bukti_pembayaran.max' => 'Ukuran gambar bukti pembayaran maksimal 2MB.',
        ]);

        $user = auth()->user();

        // Handle file upload
        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        }

        // Create anggota profile
        $anggota = Anggota::create([
            'user_id' => $user->id,
            'no_anggota' => Anggota::generateNoAnggota(),
            'nik' => $request->nik,
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
            'pekerjaan' => $request->pekerjaan,
            'bukti_pembayaran' => $buktiPath,
            'status' => 'menunggu_bendahara',
        ]);

        // Record simpanan pokok
        Simpanan::create([
            'anggota_id' => $anggota->id,
            'no_transaksi' => Simpanan::generateNoTransaksi(),
            'jenis' => 'pokok',
            'nominal' => $request->simpanan_pokok,
            'tanggal' => now(),
            'keterangan' => 'Simpanan pokok saat pendaftaran',
        ]);

        // Record simpanan wajib awal
        Simpanan::create([
            'anggota_id' => $anggota->id,
            'no_transaksi' => Simpanan::generateNoTransaksi(),
            'jenis' => 'wajib',
            'nominal' => $request->simpanan_wajib,
            'tanggal' => now(),
            'keterangan' => 'Simpanan wajib awal',
        ]);

        // Notify bendahara
        $this->notificationService->notifyRole(
            'bendahara',
            'Pendaftaran Anggota Baru',
            $request->nama_lengkap . ' telah mendaftar dan menunggu verifikasi.',
            'info',
            route('bendahara.anggota.index')
        );

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil! Silakan tunggu verifikasi dari Bendahara.',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('bendahara.setting.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:100',
            'company_address' => 'nullable|string',
            'app_logo' => 'nullable|image|max:2048',
            'minimal_saldo_pokok' => 'nullable|numeric|min:0',
            'iuran_wajib_bulanan' => 'nullable|numeric|min:0',
            'bunga_simpanan_persen' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($request->has('app_name')) Setting::updateOrCreate(['key' => 'app_name'], ['value' => $request->app_name]);
        if ($request->has('company_address')) Setting::updateOrCreate(['key' => 'company_address'], ['value' => $request->company_address]);
        if ($request->has('minimal_saldo_pokok')) Setting::updateOrCreate(['key' => 'minimal_saldo_pokok'], ['value' => $request->minimal_saldo_pokok]);
        if ($request->has('iuran_wajib_bulanan')) Setting::updateOrCreate(['key' => 'iuran_wajib_bulanan'], ['value' => $request->iuran_wajib_bulanan]);
        if ($request->has('bunga_simpanan_persen')) Setting::updateOrCreate(['key' => 'bunga_simpanan_persen'], ['value' => $request->bunga_simpanan_persen]);

        if ($request->hasFile('app_logo')) {
            $path = $request->file('app_logo')->store('settings', 'public');

            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            Setting::updateOrCreate(['key' => 'app_logo'], ['value' => $path]);
        }

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}

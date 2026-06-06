<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.setting.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:100',
            'company_address' => 'nullable|string',
            'app_logo' => 'nullable|image|max:2048',
        ]);

        Setting::updateOrCreate(['key' => 'app_name'], ['value' => $request->app_name]);
        Setting::updateOrCreate(['key' => 'company_address'], ['value' => $request->company_address]);

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

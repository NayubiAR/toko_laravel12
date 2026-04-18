<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('id')->get()->groupBy('group');

        return view('settings.general', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings'   => 'required|array',
            'settings.*' => 'nullable|string|max:1000',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
                Cache::forget("setting.{$key}");
            }
        }

        activity('settings')
            ->causedBy(Auth::user())
            ->withProperties(['keys' => array_keys($validated['settings'])])
            ->log('Pengaturan toko diperbarui');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
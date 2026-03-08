<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }

    public function geo()
    {
        return view('admin.settings.geo');
    }

    public function api()
    {
        return view('admin.settings.api');
    }

    public function maintenance()
    {
        return view('admin.tools.maintenance');
    }

    public function cache()
    {
        return view('admin.tools.cache');
    }

    public function logs()
    {
        return view('admin.tools.logs');
    }
}

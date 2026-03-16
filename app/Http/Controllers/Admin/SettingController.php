<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Paramètres mis à jour.');
    }

    public function geo()
    {
        return view('admin.settings.geo');
    }

    public function api()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.api', compact('settings'));
    }

    public function maintenance()
    {
        $isDown = file_exists(storage_path('framework/down'));
        return view('admin.tools.maintenance', compact('isDown'));
    }

    public function toggleMaintenance(Request $request)
    {
        try {
            if (file_exists(storage_path('framework/down'))) {
                \Illuminate\Support\Facades\Artisan::call('up');
                $status = false;
            } else {
                \Illuminate\Support\Facades\Artisan::call('down');
                $status = true;
            }
            return response()->json(['success' => true, 'isDown' => $status]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function cache()
    {
        return view('admin.tools.cache');
    }

    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');
        try {
            switch ($type) {
                case 'config': \Illuminate\Support\Facades\Artisan::call('config:clear'); break;
                case 'route': \Illuminate\Support\Facades\Artisan::call('route:clear'); break;
                case 'view': \Illuminate\Support\Facades\Artisan::call('view:clear'); break;
                default:
                    \Illuminate\Support\Facades\Artisan::call('cache:clear');
                    \Illuminate\Support\Facades\Artisan::call('config:clear');
                    \Illuminate\Support\Facades\Artisan::call('view:clear');
            }
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function logs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = '';
        if (file_exists($logPath)) {
            $logs = shell_exec('tail -n 100 ' . escapeshellarg($logPath));
        }
        return view('admin.tools.logs', compact('logs'));
    }

    public function clearLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }
        return response()->json(['success' => true]);
    }
}

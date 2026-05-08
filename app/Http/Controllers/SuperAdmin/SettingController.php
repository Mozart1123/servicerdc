<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\AuditLogger;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use AuditLogger;

    /**
     * Display the system settings page.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        // Define default settings if they don't exist
        $defaults = [
            'app_name' => config('app.name', 'ServiceRDC'),
            'support_email' => 'support@servicerdc.com',
            'support_phone' => '+243 000 000 000',
            'maintenance_mode' => '0',
            'allow_registration' => '1',
            'default_currency' => 'USD',
            'footer_text' => '© ' . date('Y') . ' ServiceRDC. All rights reserved.',
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($settings[$key])) {
                $settings[$key] = $value;
            }
        }

        return view('super-admin.settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');
        $oldSettings = Setting::all()->pluck('value', 'key')->toArray();

        foreach ($data as $key => $value) {
            // Special handling for checkboxes/toggles if they are missing when unchecked
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '0']
            );
        }

        // Handle specific toggles that might be missing from the request if unchecked
        $toggles = ['maintenance_mode', 'allow_registration'];
        foreach ($toggles as $toggle) {
            if (!$request->has($toggle)) {
                Setting::updateOrCreate(['key' => $toggle], ['value' => '0']);
            }
        }

        $newSettings = Setting::all()->pluck('value', 'key')->toArray();

        $this->auditLog('updated', 'Updated System Settings', null, [
            'old' => $oldSettings,
            'new' => $newSettings
        ], 'warning');

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }
}

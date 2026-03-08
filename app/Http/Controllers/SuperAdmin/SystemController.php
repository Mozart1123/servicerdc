<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SystemController extends Controller
{
    public function reports(): View
    {
        return view('super-admin.reports.index');
    }

    public function settings(): View
    {
        return view('super-admin.settings.index');
    }

    public function system(): View
    {
        return view('super-admin.system.index');
    }

    public function logs(): View
    {
        return view('super-admin.logs');
    }

    public function services(): View
    {
        return view('super-admin.services');
    }

    public function jobs(): View
    {
        return view('super-admin.jobs.index');
    }
}

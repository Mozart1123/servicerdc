<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function tickets()
    {
        return view('admin.support.tickets');
    }

    public function docs()
    {
        return view('admin.support.docs');
    }

    public function suggestions()
    {
        return view('admin.support.suggestions');
    }
}

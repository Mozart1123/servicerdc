<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function transactions()
    {
        return view('admin.finances.transactions');
    }

    public function commissions()
    {
        return view('admin.finances.commissions');
    }

    public function invoicing()
    {
        return view('admin.finances.invoicing');
    }
}

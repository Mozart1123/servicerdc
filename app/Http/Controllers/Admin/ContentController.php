<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function news()
    {
        return view('admin.content.news');
    }

    public function newsletter()
    {
        return view('admin.content.newsletter');
    }

    public function push()
    {
        return view('admin.content.push');
    }
}

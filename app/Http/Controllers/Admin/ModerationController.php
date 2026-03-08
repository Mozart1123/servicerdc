<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    /**
     * Display a listing of reviews to moderate.
     */
    public function reviews()
    {
        return view('admin.moderation.reviews');
    }

    /**
     * Display services to moderate (if different from index).
     */
    public function services()
    {
        // This could be a filtered view of services pending validation
        return redirect()->route('admin.services.index');
    }
}

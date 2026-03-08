<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('generator')->latest()->paginate(20);
        return view('admin.reports.index', compact('reports'));
    }

    public function generate(Request $request)
    {
        // Mock generation logic
        // In reality, this might dispatch a job
        $type = $request->input('type', 'daily');

        Report::create([
            'type' => $type,
            'file_path' => '/reports/dummy_report.pdf',
            'generated_by_user_id' => Auth::id() ?? 1, // Fallback for dev if needed
            'status' => 'completed',
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Report generated successfully.');
    }

    public function download(Report $report)
    {
        // Check if file exists and download
        // return Storage::download($report->file_path);
        return back()->with('info', 'Download feature mocked. Path: ' . $report->file_path);
    }

    public function analytics()
    {
        return view('admin.reports.analytics');
    }

    public function financial()
    {
        return view('admin.reports.financial');
    }

    public function export()
    {
        return view('admin.reports.export');
    }
}

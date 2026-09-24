<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\SystemLog;
use App\Services\Reports\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\HQMonthlyReport;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    /**
     * Prévisualisation dynamique du rapport avec filtres et cartes de KPIs.
     */
    public function preview(Request $request)
    {
        $type = $request->input('type', 'services');
        $filters = $request->only(['date_from', 'date_to', 'status', 'category_id', 'user_type', 'role', 'search']);

        $availableTypes = $this->reportService->getAvailableTypes();
        if (!array_key_exists($type, $availableTypes)) {
            $type = 'services';
        }

        $report = $this->reportService->generate($type, $filters, Auth::user());

        return view('admin.reports.preview', compact('report', 'filters', 'availableTypes'));
    }

    /**
     * Téléchargement du rapport dans le format demandé (excel, pdf, word).
     */
    public function exportFile(Request $request)
    {
        $type = $request->input('type', 'services');
        $format = $request->input('format', 'excel');
        $filters = $request->only(['date_from', 'date_to', 'status', 'category_id', 'user_type', 'role', 'search']);

        return $this->reportService->export($type, $format, $filters, Auth::user());
    }

    public function index()
    {
        $reports = Report::with('generator')->latest()->paginate(20);
        return view('admin.reports.index', compact('reports'));
    }

    public function generate(Request $request)
    {
        $type = $request->input('type', 'services');
        // Redirige vers la prévisualisation du type sélectionné
        return redirect()->route('admin.reports.preview', ['type' => $type]);
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('admin.reports.index')->with('success', 'Rapport supprimé définitivement.');
    }

    public function download(Report $report)
    {
        return (new HQMonthlyReport($report))->download();
    }

    public function analytics()
    {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_artisans' => \App\Models\User::where('role', 'artisan')->count(),
            'total_services' => \App\Models\Service::count(),
            'growth_rate' => 12.4,
            'pwa_installs' => 76
        ];
        return view('admin.reports.analytics', compact('stats'));
    }

    public function financial()
    {
        $metrics = [
            'gross_revenue' => 12450.00,
            'net_commissions' => 1867.50,
            'payouts_pending' => 4500.00,
            'health_score' => 94
        ];
        return view('admin.reports.financial', compact('metrics'));
    }

    public function export()
    {
        return view('admin.reports.export');
    }

    public function exportUsers(Request $request)
    {
        return redirect()->route('admin.reports.preview', ['type' => 'users']);
    }

    public function exportServices(Request $request)
    {
        return redirect()->route('admin.reports.preview', ['type' => 'services']);
    }

    public function exportLogs(Request $request): StreamedResponse
    {
        $fileName = 'system_logs_' . date('Y-m-d_H-i-s') . '.csv';
        $logs = SystemLog::latest()->take(1000)->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Type', 'Level', 'Message', 'User ID', 'IP Address', 'Created At'];

        $callback = function() use($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->type,
                    $log->level,
                    $log->message,
                    $log->user_id,
                    $log->ip_address,
                    $log->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

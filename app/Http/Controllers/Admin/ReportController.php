<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemLog;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HQMonthlyReport;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('generator')->latest()->paginate(20);
        return view('admin.reports.index', compact('reports'));
    }

    public function generate(Request $request)
    {
        $type = $request->input('type', 'daily');
        $period = $request->input('period', 'day'); // day, week, month

        // In a real app, we would query counts/sums based on the period
        // For HQ Demo, we create the record which will link to a dynamic stream
        Report::create([
            'type' => strtoupper($type) . ' (' . strtoupper($period) . ')',
            'file_path' => '/reports/' . $type . '_' . now()->format('YmdHis') . '.csv',
            'generated_by_user_id' => Auth::id() ?? 1,
            'status' => 'completed',
        ]);

        return redirect()->route('admin.reports.index')->with('success', 'Rapport ' . $type . ' généré avec succès.');
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
            'growth_rate' => 12.4, // Mock calculated rate
            'pwa_installs' => 76
        ];
        return view('admin.reports.analytics', compact('stats'));
    }

    public function financial()
    {
        // Mock financial health data - in real app would query payments table
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

    public function exportUsers(): StreamedResponse
    {
        $fileName = 'users_export_' . date('Y-m-d') . '.csv';
        $users = \App\Models\User::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Name', 'Email', 'Role', 'Status', 'Created At'];

        $callback = function() use($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($users as $user) {
                fputcsv($file, [$user->id, $user->name, $user->email, $user->role, $user->status, $user->created_at]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportServices(): StreamedResponse
    {
        $fileName = 'services_export_' . date('Y-m-d') . '.csv';
        $services = \App\Models\Service::all();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Title', 'Price', 'Provider', 'Status', 'Created At'];

        $callback = function() use($services, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($services as $service) {
                fputcsv($file, [$service->id, $service->title, $service->price, $service->user->name ?? 'N/A', $service->status, $service->created_at]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

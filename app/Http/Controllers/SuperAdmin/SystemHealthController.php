<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Service;
use App\Models\Organization;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SystemHealthController extends Controller
{
    /**
     * Display the system health dashboard.
     */
    public function index(): View
    {
        $health = [
            'server' => $this->getServerMetrics(),
            'php' => $this->getPhpMetrics(),
            'database' => $this->getDatabaseMetrics(),
            'app' => $this->getAppMetrics(),
            'storage' => $this->getStorageMetrics(),
        ];

        return view('super-admin.system-health.index', compact('health'));
    }

    /**
     * Server-level metrics.
     */
    private function getServerMetrics(): array
    {
        $uptime = 'N/A';
        $loadAvg = [0, 0, 0];

        if (PHP_OS_FAMILY === 'Linux') {
            try {
                $raw = (float) trim(file_get_contents('/proc/uptime'));
                $days = floor($raw / 86400);
                $hours = floor(($raw % 86400) / 3600);
                $mins = floor(($raw % 3600) / 60);
                $uptime = "{$days}d {$hours}h {$mins}m";
            } catch (\Exception $e) {
                $uptime = 'N/A';
            }

            if (function_exists('sys_getloadavg')) {
                $loadAvg = sys_getloadavg();
            }
        }

        return [
            'hostname' => gethostname() ?: 'Unknown',
            'os' => PHP_OS,
            'uptime' => $uptime,
            'load_avg' => array_map(fn($v) => round($v, 2), $loadAvg),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI',
        ];
    }

    /**
     * PHP runtime metrics.
     */
    private function getPhpMetrics(): array
    {
        return [
            'version' => PHP_VERSION,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution' => ini_get('max_execution_time'),
            'upload_max' => ini_get('upload_max_filesize'),
            'post_max' => ini_get('post_max_size'),
            'extensions' => get_loaded_extensions(),
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status() !== false,
        ];
    }

    /**
     * Database connection and size metrics.
     */
    private function getDatabaseMetrics(): array
    {
        $dbSize = 0;
        $tableCount = 0;
        $connectionStatus = 'Unknown';

        try {
            DB::connection()->getPdo();
            $connectionStatus = 'Connected';

            $dbName = config('database.connections.' . config('database.default') . '.database');

            // Get database size (MySQL)
            $result = DB::select("SELECT 
                SUM(data_length + index_length) as size 
                FROM information_schema.TABLES 
                WHERE table_schema = ?", [$dbName]);

            $dbSize = $result[0]->size ?? 0;

            // Get table count
            $tables = DB::select("SELECT COUNT(*) as cnt FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            $tableCount = $tables[0]->cnt ?? 0;

        } catch (\Exception $e) {
            $connectionStatus = 'Error: ' . $e->getMessage();
        }

        return [
            'driver' => config('database.default'),
            'status' => $connectionStatus,
            'name' => config('database.connections.' . config('database.default') . '.database'),
            'size' => $dbSize,
            'tables' => $tableCount,
        ];
    }

    /**
     * Application-level metrics.
     */
    private function getAppMetrics(): array
    {
        return [
            'name' => config('app.name'),
            'env' => config('app.env'),
            'debug' => config('app.debug'),
            'url' => config('app.url'),
            'laravel' => app()->version(),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'total_users' => User::count(),
            'total_services' => Service::count(),
            'total_orgs' => Organization::count(),
            'recent_logs' => ActivityLog::where('created_at', '>=', now()->subHours(24))->count(),
        ];
    }

    /**
     * Disk storage metrics.
     */
    private function getStorageMetrics(): array
    {
        $storagePath = storage_path();
        $totalDisk = disk_total_space('/');
        $freeDisk = disk_free_space('/');
        $usedDisk = $totalDisk - $freeDisk;

        // Get storage directory size
        $storageSize = 0;
        try {
            $output = shell_exec("du -sb {$storagePath} 2>/dev/null");
            if ($output) {
                $storageSize = (int) explode("\t", trim($output))[0];
            }
        } catch (\Exception $e) {
            $storageSize = 0;
        }

        return [
            'disk_total' => $totalDisk,
            'disk_free' => $freeDisk,
            'disk_used' => $usedDisk,
            'disk_percent' => round(($usedDisk / $totalDisk) * 100, 1),
            'storage_size' => $storageSize,
        ];
    }
}

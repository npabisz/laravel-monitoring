<?php

namespace Npabisz\LaravelMetrics\Commands;

use Illuminate\Console\Command;
use Npabisz\LaravelMetrics\Services\MonitoringService;

class MonitoringCleanCommand extends Command
{
    protected $signature = 'monitoring:clean';
    protected $description = 'Clean up old monitoring data based on retention settings';

    public function handle(MonitoringService $service): int
    {
        $deleted = $service->cleanup();

        $this->info(sprintf(
            'Cleaned up: %d metrics, %d slow logs',
            $deleted['metrics'],
            $deleted['slow_logs']
        ));

        return self::SUCCESS;
    }
}

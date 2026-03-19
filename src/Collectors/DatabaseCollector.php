<?php

namespace Npabisz\LaravelMonitoring\Collectors;

use Npabisz\LaravelMonitoring\Services\MonitoringService;

class DatabaseCollector implements CollectorInterface
{
    public function collect(): array
    {
        $service = app(MonitoringService::class);
        $data = $service->flushRedisCounters('db');
        $durations = MonitoringService::decodeDurations($data, 'durations');

        return [
            'db_queries_total'  => (int) ($data['queries_total'] ?? 0),
            'db_slow_queries'   => (int) ($data['slow_queries'] ?? 0),
            'db_avg_query_ms'   => MonitoringService::avgDuration($durations),
            'db_max_query_ms'   => MonitoringService::maxDuration($durations),
        ];
    }
}

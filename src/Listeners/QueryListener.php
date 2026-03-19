<?php

namespace Npabisz\LaravelMonitoring\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Npabisz\LaravelMonitoring\Services\MonitoringService;

class QueryListener
{
    protected MonitoringService $monitoring;

    public function __construct(MonitoringService $monitoring)
    {
        $this->monitoring = $monitoring;
    }

    public function handle(QueryExecuted $event): void
    {
        // Ignore queries to the monitoring tables themselves
        if (str_contains($event->sql, 'monitoring_')) {
            return;
        }

        // Track aggregate metrics
        $this->monitoring->increment('db', 'queries_total');
        $this->monitoring->pushToList('db', 'durations', round($event->time, 2));

        // Log slow queries to database
        $threshold = config('monitoring.slow_query_threshold', 100);

        if ($event->time > $threshold) {
            $this->monitoring->increment('db', 'slow_queries');

            $this->monitoring->logSlowQuery(
                $event->sql,
                $event->bindings,
                $event->time,
                $event->connectionName
            );
        }
    }
}

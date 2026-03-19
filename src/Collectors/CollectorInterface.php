<?php

namespace Npabisz\LaravelMonitoring\Collectors;

interface CollectorInterface
{
    /**
     * Return an array of metric key => value pairs.
     *
     * Keys should match column names in monitoring_metrics table,
     * or be placed under the 'custom' key for JSON storage.
     *
     * @return array<string, mixed>
     */
    public function collect(): array;
}

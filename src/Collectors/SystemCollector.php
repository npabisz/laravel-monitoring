<?php

namespace Npabisz\LaravelMonitoring\Collectors;

class SystemCollector implements CollectorInterface
{
    public function collect(): array
    {
        $load = null;

        if (function_exists('sys_getloadavg')) {
            $avg = sys_getloadavg();
            $load = $avg[0] ?? null;
        }

        return [
            'cpu_load_1m'      => $load !== null ? round($load, 2) : null,
            'memory_usage_mb'  => round(memory_get_usage(true) / 1048576, 2),
            'disk_free_gb'     => round(disk_free_space(base_path()) / 1073741824, 2),
        ];
    }
}

<?php

namespace Npabisz\LaravelMetrics\Collectors;

class HorizonCollector implements CollectorInterface
{
    public function collect(): array
    {
        $custom = [];

        if (class_exists(\Laravel\Horizon\Contracts\MasterSupervisorRepository::class)) {
            try {
                $masters = app(\Laravel\Horizon\Contracts\MasterSupervisorRepository::class)->all();
                $status = !empty($masters) ? 'running' : 'inactive';

                $custom['horizon_status'] = $status;
            } catch (\Throwable $e) {
                $custom['horizon_status'] = 'unknown';
            }
        }

        return [
            'custom' => $custom,
        ];
    }
}

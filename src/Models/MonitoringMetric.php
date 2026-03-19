<?php

namespace Npabisz\LaravelMonitoring\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoringMetric extends Model
{
    public $timestamps = false;

    protected $table = 'monitoring_metrics';

    protected $guarded = ['id'];

    protected $casts = [
        'recorded_at' => 'datetime',
        'custom' => 'array',
    ];

    public function getConnectionName()
    {
        return config('monitoring.connection') ?? parent::getConnectionName();
    }
}

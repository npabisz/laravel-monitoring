<?php

use Illuminate\Support\Facades\Route;
use Npabisz\LaravelMetrics\Http\Controllers\MonitoringController;
use Npabisz\LaravelMetrics\Http\Middleware\AuthorizeBearerToken;

Route::middleware(AuthorizeBearerToken::class)->group(function () {
    Route::get('summary', [MonitoringController::class, 'summary']);
    Route::get('metrics', [MonitoringController::class, 'metrics']);
    Route::get('slow-logs', [MonitoringController::class, 'slowLogs']);
});

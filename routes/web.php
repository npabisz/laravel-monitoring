<?php

use Illuminate\Support\Facades\Route;
use Npabisz\LaravelMonitoring\Http\Controllers\DashboardController;
use Npabisz\LaravelMonitoring\Http\Middleware\AuthorizeMonitoring;

Route::middleware(AuthorizeMonitoring::class)->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('api/data', [DashboardController::class, 'apiData']);
    Route::get('api/slow-logs', [DashboardController::class, 'apiSlowLogs']);
});

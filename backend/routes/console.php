<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

\Illuminate\Support\Facades\Schedule::call(function () {
    \App\Modules\Auth\Models\RefreshToken::where('expires_at', '<', now())
        ->orWhereNotNull('revoked_at')
        ->where('updated_at', '<', now()->subDays(7))
        ->delete();
})->daily();

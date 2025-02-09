<?php

use App\Http\Controllers\Agent\PromptController;
use App\Http\Middleware\CheckAgentPasswordProtected;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;

Route::middleware([
    EncryptCookies::class,
    StartSession::class,
    CheckAgentPasswordProtected::class,
])->group(function () {
    Route::post('agent/{agent}/prompt', [PromptController::class, 'prompt'])
        ->name('agent.prompt.prompt');
});

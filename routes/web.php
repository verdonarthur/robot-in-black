<?php

use App\Http\Controllers\Agent\PromptController;
use App\Http\Middleware\CheckAgentPasswordProtected;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return view('welcome');
});

Route::get('agent/{agent}/auth', [PromptController::class, 'auth'])->name('agent.auth');
Route::post('agent/{agent}/auth', [PromptController::class, 'authCheck'])->name('agent.auth-check');

Route::middleware([CheckAgentPasswordProtected::class])->group(function () {
    Route::get('agent/{agent}/prompt', [PromptController::class, 'index'])
        ->name('agent.prompt');
});

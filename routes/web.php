<?php

use App\Http\Controllers\Agent\PromptController;
use App\Http\Middleware\CheckAgentPasswordProtected;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return view('welcome');
});

Route::middleware([CheckAgentPasswordProtected::class])->group(function () {
    Route::get('agent/{agent}/prompt', [PromptController::class, 'index'])->name('agent.prompt.index');
});

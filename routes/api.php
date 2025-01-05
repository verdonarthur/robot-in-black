<?php

use App\Http\Controllers\Agent\PromptController;
use Illuminate\Support\Facades\Route;

Route::post('agent/{id}/prompt', [PromptController::class, 'prompt'])
    ->name('agent.prompt.prompt');

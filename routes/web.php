<?php

use App\Http\Controllers\Agent\PromptController;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return view('welcome');
});

Route::get('agent/{agent}/prompt', [PromptController::class, 'index'])->name('agent.prompt.index');

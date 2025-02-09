<?php

namespace App\Http\Middleware;

use App\Enums\ChatOptions;
use App\Models\Agent;
use Closure;
use Illuminate\Http\Request;

class CheckAgentPasswordProtected
{
    public const AUTH_SESSION_KEY = 'agent-authenticated';

    public function handle(Request $request, Closure $next)
    {
        /**
         * @type ?Agent $agent
         */
        $agent = $request->route('agent');

        if (! $agent) {
            return $request->isJson() ? response(status: 401) : redirect(status: 401)->route('welcome');
        }

        if ($agent->getOption(ChatOptions::PASSWORD_HASH) === null) {
            return $next($request);
        }

        $sessionAgentAuthKey = self::AUTH_SESSION_KEY . '-' . $agent->name;
        if (! session()?->has($sessionAgentAuthKey)) {
            return $request->isJson() ? response(status: 401) : redirect(status: 401)->route('agent.auth', ['agent' => $agent]);
        }

        return $next($request);
    }
}

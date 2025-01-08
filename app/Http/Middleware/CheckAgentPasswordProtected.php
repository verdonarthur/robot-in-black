<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAgentPasswordProtected
{
    public function handle(Request $request, Closure $next): Response
    {
        $agent = $request->route('agent');
        $agentPassword = $agent->chatOptions['password'];

        if ($agent && $agentPassword) {
            $expectedPassword = $agentPassword;

            $providedPassword = $request->getPassword();

            if (!$providedPassword || $providedPassword !== $expectedPassword) {
                return response('Unauthorized', 401, [
                    'WWW-Authenticate' => 'Basic realm="Chat Access"'
                ]);
            }
        }

        return $next($request);
    }
}

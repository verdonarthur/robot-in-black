<?php

namespace App\Http\Controllers\Agent;

use App\Enums\ChatOptions;
use App\Exceptions\PromptException;
use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckAgentPasswordProtected;
use App\Models\Agent;
use App\Models\DocumentEmbedding;
use App\Services\RetrievalAugmentedGenerationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JsonException;

class PromptController extends Controller
{
    public function index(Agent $agent): View|Factory|Application
    {
        return view('agent.prompt.index', ['agent' => $agent]);
    }

    public function auth(Agent $agent): View|Factory|Application
    {
        return view('agent.prompt.auth', ['agent' => $agent]);
    }

    public function authCheck(Agent $agent, Request $request): RedirectResponse
    {
        $password = $request->get('password');
        $hashPasswordOption = $agent->getOption(ChatOptions::PASSWORD_HASH);

        if (! Hash::check($password, $hashPasswordOption)) {
            return back()->withErrors(['password' => 'Invalid password']);
        }

        $sessionAgentAuthKey = CheckAgentPasswordProtected::AUTH_SESSION_KEY . '-' . $agent->name;
        session([$sessionAgentAuthKey => true]);

        return redirect()->route('agent.prompt', ['agent' => $agent]);
    }

    /**
     * @throws PromptException
     * @throws JsonException
     */
    public function prompt(
        Agent $agent,
        RetrievalAugmentedGenerationService $rag,
        Request $request,
    ): array
    {
        $search = $request->get('search');

        if (! $search || ! $agent?->prompt) {
            return [
                'answer' => '',
            ];
        }

        $context = DocumentEmbedding::orderedByContentDistance($search, $agent)
            ->map(function (DocumentEmbedding $documentEmbedding) {
                return $documentEmbedding->content;
            })
            ->implode("\n");

        return [
            'answer' => Str::markdown(
                $rag->generateAnswer($search, $agent, $context),
                [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ],
            ),
        ];
    }
}

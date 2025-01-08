<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Document;
use App\Providers\AI\GeminiProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JsonException;

class PromptController extends Controller
{
    public function index(Agent $agent): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application
    {
        return view('agent.prompt.index', ['agent' => $agent]);
    }

    /**
     * @throws JsonException
     */
    public function prompt(GeminiProvider $gemini, string $id, Request $request): array
    {
        $search = $request->get('search');
        $agent = Agent::query()->findOrFail($id);

        $context = Document::orderedByContentDistance($search)
            ->take(2)
            ->map(function ($document) use ($agent) {
                return $document->content;
            })
            ->implode("\n")
        ;

        $prompt = Str::of($agent->prompt)
            ->replace('%REQUEST%', $search)
            ->replace('%CONTEXT%', $context);

        $promptResult = $gemini->prompt($prompt);

        return [
            'answer' => Str::markdown($promptResult),
        ];
    }
}

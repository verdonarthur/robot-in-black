<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Document;
use App\Services\RetrievalAugmentedGenerationService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JsonException;

class PromptController extends Controller
{
    public function index(Agent $agent): View|Factory|Application
    {
        return view('agent.prompt.index', ['agent' => $agent]);
    }

    /**
     * @throws JsonException
     */
    public function prompt(RetrievalAugmentedGenerationService $rag, string $id, Request $request): array
    {
        $search = $request->get('search');
        $agent = Agent::query()->findOrFail($id);

        if (! $search || ! $agent || ! $agent?->prompt) {
            return [
                'answer' => '',
            ];
        }

        $context = Document::orderedByContentDistance($search, $agent)
            ->map(function ($document) {
                return $document->content;
            })
            ->implode("\n");

        return [
            'answer' => Str::markdown(
                $rag->generateAnswer($search, $agent->prompt, $context),
                [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ],
            ),
        ];
    }
}

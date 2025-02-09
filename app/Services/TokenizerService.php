<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TokenizerService
{
    public const NBR_OF_CHAR_OPENAI_TOKEN = 4;

    public function tokenize(string $input): Collection
    {
        return Str::of($input)->explode(' ')->map(fn(string $token) => Str::of($token)->split(self::NBR_OF_CHAR_OPENAI_TOKEN))->flatten();
    }
}

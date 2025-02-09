<?php

return [
    'embedding_service' => env('AI_EMBEDDING_SERVICE', 'gemini'),

    'gemini' => [
        'key' => env('GEMINI_KEY', ''),
    ],

    'groq' => [
        'key' => env('GROQ_KEY', ''),
    ],

    'infomaniak' => [
        'key' => env('INFOMANIAK_KEY', ''),
        'product_id' => env('INFOMANIAK_PRODUCT_ID', ''),
    ],
];

<?php

namespace App\Enums;

enum ChatOptions: string
{
    case PASSWORD_HASH = 'passwordHash';
    case SEARCH_PLACEHOLDER = 'searchPlaceholder';
    case SEARCH_SUBTITLE = 'searchSubtitle';
    case PROMPT_MODEL = 'promptModel';
}

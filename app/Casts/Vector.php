<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JsonException;

class Vector implements CastsAttributes
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return $value ? base64_encode($value) : null;
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        if(is_array($value)){
            $value = json_encode($value, JSON_THROW_ON_ERROR);
        }

        return DB::raw('VEC_FromText('. json_encode($value) .')');
    }
}

<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use App\Enums\UserRole;

class RoleCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return UserRole::from($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value instanceof UserRole ? $value->value : $value;
    }
}

<?php

namespace App\Models\Concerns;

use Ulid\Ulid;

trait HasUlid
{
    public static function bootHasUlid()
    {
        // when creating models, we will generate a new ULID before saving
        static::creating(function ($model) {
            if (! isset($model->id)) {
                $model->id = (string) Ulid::generate(true);
            }
        });
    }

    public function initializeHasUlid()
    {
        // initialize for this trait runs for every new instance, here
        // we can change some default parameters for this model, specifically
        // we can turn off incrementing and tell Eloquent the PK is a string
        $this->incrementing = false;

        $this->keyType = 'string';
    }
}

<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CustomSoftDeletes
{
    /**
     * Boot the CustomSoftDeletes trait for the model.
     */
    protected static function bootCustomSoftDeletes()
    {

        static::addGlobalScope('exclude_trashed', function (Builder $builder) {
            $builder->whereNull($builder->getModel()->getTable() . '.deleted_at');
        });
    }

    public static function withTrashed()
    {
        return static::withoutGlobalScope('exclude_trashed');
    }
}
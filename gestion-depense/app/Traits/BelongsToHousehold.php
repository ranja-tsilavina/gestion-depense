<?php

namespace App\Traits;

use App\Models\Household;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToHousehold
{
    protected static function bootBelongsToHousehold()
    {
        static::addGlobalScope('household', function (Builder $builder) {
            if (auth()->check()) {
                // Ensure the user is in a household
                $householdId = session('active_household_id');
                if ($householdId) {
                    $builder->where($builder->getQuery()->from . '.household_id', $householdId);
                }
            }
        });

        static::creating(function ($model) {
            if (auth()->check() && !$model->household_id) {
                $model->household_id = session('active_household_id');
            }
        });
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }
}

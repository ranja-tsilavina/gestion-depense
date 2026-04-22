<?php

namespace App\Traits;

use App\Models\Household;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

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
            if (auth()->check()) {
                if (!$model->household_id) {
                    $model->household_id = session('active_household_id');
                }
                // Check if the model has a created_by attribute
                if (Schema::hasColumn($model->getTable(), 'created_by') && !$model->created_by) {
                    $model->created_by = auth()->id();
                }
            }
        });
    }

    public function household()
    {
        return $this->belongsTo(Household::class);
    }
}

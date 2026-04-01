<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\BelongsToHousehold;

class Category extends Model
{
    use HasFactory, BelongsToHousehold;

    protected $fillable = ['name', 'household_id'];

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}

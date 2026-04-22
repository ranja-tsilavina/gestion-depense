<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToHousehold;

class Budget extends Model
{
    use HasFactory, BelongsToHousehold;

    protected $fillable = [
        'user_id',
        'household_id',
        'category_id',
        'amount',
        'month'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

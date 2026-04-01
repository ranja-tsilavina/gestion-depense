<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToHousehold;

class Revenue extends Model
{
    use HasFactory, BelongsToHousehold;

    protected $fillable = [
        'user_id',
        'household_id',
        'account_id',
        'source',
        'amount',
        'description',
        'revenue_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

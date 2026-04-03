<?php

namespace App\Models;

use App\Traits\BelongsToHousehold;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use BelongsToHousehold;

    protected $fillable = [
        'household_id',
        'user_id',
        'from_account_id',
        'to_account_id',
        'amount',
        'transfer_date',
        'description'
    ];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

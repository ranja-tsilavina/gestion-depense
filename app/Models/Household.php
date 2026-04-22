<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    protected $fillable = ['name', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}

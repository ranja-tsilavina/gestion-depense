<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Category;
use App\Models\User;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'description',
        'expense_date'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoryBudget()
    {
        return $this->belongsTo(Budget::class, 'category_id', 'category_id')
                    ->whereMonth('month', date('m'))
                    ->whereYear('month', date('Y'))
                    ->where('user_id', $this->user_id);
    }
}

<?php

namespace App\Models\Expenses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ExpenseCategory extends Model
{
    use HasUuids;

    protected $table = 'expense_categories';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'status',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_category_id');
    }
}

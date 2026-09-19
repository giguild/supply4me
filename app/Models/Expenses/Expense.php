<?php

namespace App\Models\Expenses;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Expense extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'expenses';

    protected $fillable = [
        'company_id',
        'expense_category_id',
        'amount',
        'description',
        'expense_date',
        'payment_method',
        'reference',
        'vendor',
        'receipt_path',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

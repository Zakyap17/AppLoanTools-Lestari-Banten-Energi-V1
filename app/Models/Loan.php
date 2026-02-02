<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'loan_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            try {
                \Illuminate\Support\Facades\Cache::forget('topbar_notifications');
            } catch (\Exception $e) {
                // Silently fail if cache is not reachable, do not block the transaction
            }
        });

        static::deleted(function ($model) {
            try {
                \Illuminate\Support\Facades\Cache::forget('topbar_notifications');
            } catch (\Exception $e) {
                // Silently fail
            }
        });
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function borrower()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function keeper()
    {
        return $this->belongsTo(Employee::class, 'keeper_id');
    }

    public function item()
    {
        return $this->belongsTo(ToolItem::class, 'tool_item_id');
    }
}

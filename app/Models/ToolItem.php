<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToolItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tool_id',
        'serial_number',
        'status', // available, in_use, damage, lost, calibration
        'condition',
        'purchase_date',
        'created_by'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function activeDamageReport()
    {
        return $this->hasOne(DamageLost::class)->where('status', 'open')->where('type', 'damage')->latest();
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'tool_item_id');
    }
}

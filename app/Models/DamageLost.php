<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamageLost extends Model
{
    protected $table = 'damage_losts';
    protected $guarded = ['id'];

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function item()
    {
        return $this->belongsTo(ToolItem::class, 'tool_item_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolsMatSubGroup extends Model
{
    protected $table = 'tools_mat_sub_groups';

    protected $fillable = [
        'sub_code',
        'sub_description',
        'mat_group_code',
    ];

    public function matGroup()
    {
        return $this->belongsTo(
            ToolsMatGroup::class,
            'mat_group_code',
            'code'
        );
    }
}

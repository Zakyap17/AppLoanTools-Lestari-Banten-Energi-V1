<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolsMatGroup extends Model
{
    protected $table = 'tools_mat_groups';

    protected $fillable = [
        'code',
        'description',
    ];

    public function subGroups()
    {
        return $this->hasMany(
            ToolsMatSubGroup::class,
            'mat_group_code',
            'code'
        );
    }
}

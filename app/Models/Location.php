<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';
    protected $guarded = ['id'];

    public function racks()
    {
        return $this->hasMany(Rack::class);
    }
}

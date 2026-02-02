<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutationLog extends Model
{
    protected $guarded = ['id'];

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function fromRack()
    {
        return $this->belongsTo(Rack::class, 'from_rack_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function toRack()
    {
        return $this->belongsTo(Rack::class, 'to_rack_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

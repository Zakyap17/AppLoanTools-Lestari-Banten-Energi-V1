<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $table = 'tools';
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saved(function ($model) {
            try {
                \Illuminate\Support\Facades\Cache::forget('topbar_notifications');
            } catch (\Exception $e) {
                // Silently fail
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

    public function type()
    {
        return $this->belongsTo(ToolType::class, 'type_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function items()
    {
        return $this->hasMany(ToolItem::class);
    }

    // Helper to get available count dynamically
    public function getAvailableQuantityAttribute()
    {
        return $this->items()->where('status', 'available')->count();
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }

    public function subGroup()
    {
        return $this->belongsTo(ToolsMatSubGroup::class, 'sub_group_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}

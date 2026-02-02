<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalibrationIssuing extends Model
{
    protected $table = 'calibration_issuings';
    protected $guarded = ['id'];

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function vendor()
    {
        return $this->belongsTo(VendorCalibration::class, 'vendor_calibration_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asset extends Model
{
    use HasFactory;
    protected $fillable = [
        'Device_Name',
        'Device_Type_Id',
        'Specification',
        'Description',
    ];

    public function deviceType()
    {
        return $this->belongsTo(device_type::class, 'Device_Type_Id');
    }
}

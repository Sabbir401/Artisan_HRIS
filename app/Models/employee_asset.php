<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employee_asset extends Model
{
    use HasFactory;
    protected $fillable = [
        'EID',
        'Device_Id',
        'Serial_Number',
        'Quantity',
        'Date',
    ];

    public function employee()
    {
        return $this->belongsTo(employee::class, 'EID');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'EID',
        'Leave_Type_Id',
        'From_Date',
        'To_Date',
        'Purpose',
        'Status',
        'Attachment_Url',
        'created_by',
        'updated_by',
        'Notification',
    ];

    public function employee()
    {
        return $this->belongsTo(employee::class, 'EID');
    }
    public function leave_type()
    {
        return $this->belongsTo(leave_type::class, 'Leave_Type_Id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequestNote extends Model
{
    use SoftDeletes;

    protected $table = "service_request_note";
    
    protected $fillable = [
        'serviceRequestId',
        'serviceRequestNumber',
        'serviceRequestNote',
        'serviceRequestProvider'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $table = "service_request";

    protected $fillable = [
        'serviceRequestNumber',
        'serviceName',
        'serviceDescription',
        'serviceDepartment',
        'serviceDepartmentType',
        'serviceRecipient',
        'servicePriority',
        'serviceProvider',
        'serviceRequestName',
        'serviceFileUpload',
        'serviceStatus',
        'serviceDateTime',
        'serviceFlag'
    ];
}

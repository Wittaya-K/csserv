<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceStatus extends Model
{
    use SoftDeletes;

    protected $table = "service_status";

    protected $fillable = [
        'serviceStatusName',
        'serviceStatus',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

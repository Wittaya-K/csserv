<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceAssign extends Model
{
    use SoftDeletes;

    protected $table = "service_assign";

    protected $fillable = [
        'serviceName',
        'serviceProvider',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

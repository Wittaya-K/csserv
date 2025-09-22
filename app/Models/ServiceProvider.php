<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use SoftDeletes;

    protected $table = "service_provider";

    protected $fillable = [
        'reqid',
        'serviceProvider',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priority extends Model
{
    use SoftDeletes;

    protected $table = "priority";

    protected $fillable = [
        'priorityName',
        'priorityStatus',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

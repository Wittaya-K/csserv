<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageLink extends Model
{
    use SoftDeletes;

    protected $table = "manage_link";

    protected $fillable = [
        'id',
        'department_name',
        'link_name',
        'link',
        'file_name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

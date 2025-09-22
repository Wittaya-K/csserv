<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequestMessage extends Model
{
    use SoftDeletes;

    protected $table = "request_message";

    protected $fillable = [
        'reqid',
        'requestMessageServiceName',
        'requestMessageSender',
        'requestMessage',
        'requestMessageDateTime'
    ];
}

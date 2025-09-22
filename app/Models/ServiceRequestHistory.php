<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequestHistory extends Model
{
    use SoftDeletes;

    protected $table = "service_request_history";

    protected $fillable = [
        'reqid',
        'serviceRequestHistoryNumber',
        'serviceHistoryName',
        'serviceHistoryDescription',
        'serviceHistoryDepartment',
        'serviceHistoryDepartmentType',
        'serviceHistoryRecipient',
        'serviceHistoryPriority',
        'serviceHistoryProvider',
        'serviceHistoryFileUpload',
        'serviceHistoryStatus',
        'serviceHistoryDateTime'
    ];
}

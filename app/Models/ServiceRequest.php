<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $table = "service_request";

    // LAYER 4 — Mass Assignment Protection:
    // ลบ created_at, updated_at, deleted_at ออกจาก fillable
    // เพราะ Laravel จัดการให้อัตโนมัติ และอนุญาตให้ user กำหนดค่าเองได้อันตราย
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

    // LAYER 4 — Output filter:
    // field ที่ไม่ควรส่งออก JSON response โดยตรง
    // (ในกรณีนี้ยังไม่มี sensitive field แต่เพิ่ม deleted_at เพื่อความปลอดภัย)
    protected $hidden = [
        'deleted_at',
    ];
}

@extends('layouts.admin')
@section('content')
<style>
    .rating .fa-star {
        font-size: 18px;
        color: #ccc;
        cursor: pointer;
    }

    .rating .fa-star.checked {
        color: #ffc107;
    }

</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                {{-- <h3>ขอใช้บริการ</h3> --}}
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="">แดชบอร์ด</a></li>
                    <li class="breadcrumb-item active">ขอใช้บริการ</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div style="margin-bottom: 10px;" class="row">
    <div class="col-lg-12">
        <a class="btn btn-info" href="{{ route('admin.service_requests.index') }}"><i class="fad fa-arrow-square-left"></i> ย้อนกลับ</a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-secondary">
        <div class="row">
            <div class="col-lg-1 col-12">
                <i class="fad fa-stream"></i> เพิ่มคำขอ
            </div>
            <div class="col-lg-4 col-12">
                <div class="rating" data-selected="0">
                    <i class="fas fa-star" data-value="1"></i> ติดดาว
                    <input type="hidden" name="rating" id="ratingValue" value="0">
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" class="needs-validation" id="schedule_form" action="{{ route('admin.service_requests.save') }}" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="id" id="id" />
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">หมายเลขคำขอ</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <input type="text" name="serviceRequestNumber" id="serviceRequestNumber" class="form-control" value="{{ $requestId }}" readonly required placeholder="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">รายละเอียด</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <textarea type="textarea" name="serviceDescription" id="serviceDescription" class="form-control" required placeholder="ระบุรายละเอียดที่ต้องการ"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">ประเภทหน่วยงาน</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <select name="departmentType" id="departmentType" class="form-control select2" required>
                                        <option value="">เลือก</option>
                                        <option value="ภายในคณะวิทยาศาสตร์">ภายในคณะวิทยาศาสตร์</option>
                                        <option value="ภายนอกคณะวิทยาศาสตร์">ภายนอกคณะวิทยาศาสตร์</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">หน่วยงาน</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <select name="serviceDepartment" id="serviceDepartment" class="form-control select2" onchange="selectdepartments(this.value, this)" required>
                                        <option value="">เลือก</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="departmentEtc" hidden>
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">ชื่อหน่วยงาน</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <input type="text" name="departmentName" id="departmentName" class="form-control form-control is-warning" required placeholder="" />
                                    <input type="hidden" name="departmentId" id="departmentId" class="form-control" required placeholder="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">ประเภทหน่วยงาน</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <select name="departmentType" id="departmentType" class="form-control form-control is-warning" required>
                                        <option value="">เลือก</option>
                                        <option value="ภายในคณะวิทยาศาสตร์">ภายในคณะวิทยาศาสตร์</option>
                                        <option value="ภายนอกคณะวิทยาศาสตร์">ภายนอกคณะวิทยาศาสตร์</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">ผู้ขอใช้บริการ</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <input type="text" name="txtServiceRecipient" id="txtServiceRecipient" value="{{ Auth()->user()->name }}" class="form-control" required placeholder="" readonly />
                                    <input type="hidden" name="serviceRecipient" id="serviceRecipient" value="{{ Auth()->user()->username }}" class="form-control" required placeholder="" />
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">ระบุผู้ขอใช้บริการ</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <input type="text" class="form-control" name="request_name" id="request_name" value="">
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">วันที่ขอใช้บริการ</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <input type="text" class="form-control" name="serviceDueDate" id="serviceDueDate" value="{{ now()->format('Y-m-d H:i') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">เลือกผู้ให้บริการ</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <select name="serviceProvider" id="serviceProvider" class="form-control select2" required>
                                        <option value="">เลือก</option>
                                        @foreach ($staffUsers as $staffUser)
                                        <option value="{{ $staffUser->username }}">{{ $staffUser->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-12">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">งานบริการ</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                    </div>
                                    <select name="serviceName" id="serviceName" class="form-control select2" required>
                                        <option value="">เลือก</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-12">
                            <div class="btcd-f-input">
                                <label class="col-sm-12 control-label">ไฟล์</label>
                                <input type="file" name="serviceFileUpload" id="serviceFileUpload">
                            </div>
                        </div>
                    </div>
                </div>
            </div> {{-- end row --}}

            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-success" id="saveBtn" value="create"><i class="fad fa-save"></i>
                    บันทึก
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script type="text/javascript" src="{{ asset('js/sweetalert2@11.js') }}"></script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {
        $("#serviceName").select2({
            // width: '85%'
        });
        $("#serviceDepartment").select2({
            // width: '85%'
        });
        $("#servicePriority").select2({
            // width: '85%'
        });
        $("#serviceProvider").select2({
            // width: '85%'
        });
        $("#serviceStatus").select2({
            // width: '85%'
        });

        $(".rating .fa-star").on("click", function() {
            var index = $(this).data("value");
            var container = $(this).closest(".rating");
            var current = container.data("selected");
            var ratingValue = container.data("selected");
            if (index === current) {
                // ถ้าคลิกซ้ำ = ยกเลิกดาว
                container.data("selected", 0);
                container.find(".fa-star").removeClass("checked");
            } else {
                // ให้คะแนน
                container.data("selected", index);
                container.find(".fa-star").each(function() {
                    var starIndex = $(this).data("value");
                    $(this).toggleClass("checked", starIndex <= index);
                });
            }
            // $("#ratingValue").val(container.data("selected"));
            console.log("คะแนนที่เลือก:", container.data("selected"));
        });
    });

    var tbl_schedule;
    show_schedule();

    function show_schedule() {
        if (tbl_schedule) {
            tbl_schedule.destroy();
        }
        tbl_samples = $('#tbl_schedule').DataTable({
            destroy: true
            , pageLength: 10
            , responsive: true
            , ajax: "{{ route('admin.service_requests.list') }}"
            , deferRender: true
            , columns: [{
                    className: ''
                    , data: 'id'
                    , title: '<i class="fad fa-list-ol"></i>'
                    , orderable: false
                    , render: function(data, type, row, meta) {
                        // Return the content with a data-id attribute
                        return '<span data-id="' + row.id + '">' + data + '</span>';
                    }
                }
                , {
                    className: ''
                    , data: 'serviceDateTime'
                    , title: '<i class="fad fa-calendar-alt"></i> วันที่/เวลา'
                    , orderable: false
                , }
                , {
                    data: 'serviceName'
                    , orderable: false
                    , title: '<i class="fad fa-hands-heart"></i> งานบริการ'
                    , render: function(data, type, row, meta) {
                        @foreach($service_assigns as $service_assign)
                        if (row.serviceName == '{{ $service_assign->id }}') {
                            return '{{ $service_assign->serviceName }}';
                        }
                        @endforeach
                    }
                }
                , {
                    className: ''
                    , data: 'serviceDescription'
                    , title: '<i class="fad fa-poll-people"></i> รายละเอียด'
                    , orderable: false
                , }
                , {
                    data: 'serviceRecipient'
                    , orderable: false
                    , title: '<i class="fad fa-users"></i> ผู้ขอใช้บริการ'
                    , render: function(data, type, row, meta) {
                        @foreach($users as $user)
                        if (row.serviceRecipient == '{{ $user->username }}') {
                            return '{{ $user->name }}';
                        }
                        @endforeach
                    }
                }
                , {
                    data: 'servicePriority'
                    , orderable: false
                    , title: '<i class="fad fa-layer-group"></i> ชั้นความเร็ว'
                    , render: function(data, type, row, meta) {

                        var priorityStatus = '';

                        @foreach($prioritys as $priority)
                        if (row.servicePriority == 'Normal') {
                            priorityStatus =
                                '<div class="bg-success color-palette"><span>&nbsp;</span></div>';
                        } else if (row.servicePriority == 'Urgent') {
                            priorityStatus =
                                '<div class="bg-warning color-palette"><span>&nbsp;</span></div>';
                        } else if (row.servicePriority == 'VeryUrgent') {
                            priorityStatus =
                                '<div class="bg-orange color-palette"><span>&nbsp;</span></div>';
                        } else if (row.servicePriority == 'MostUrgent') {
                            priorityStatus =
                                '<div class="bg-danger color-palette"><span>&nbsp;</span></div>';
                        }
                        @endforeach

                        return priorityStatus;
                    }
                }
                , {
                    className: 'width-option-1 text-center'
                    , width: '6%'
                    , data: 'id'
                    , orderable: false
                    , title: ''
                    , render: function(data, type, row, meta) {
                        newdata = '';
                        @if(Auth::user() -> roles -> contains('title', 'Staff') == true || Auth::user() -> roles -> contains('title', 'Admin') == true)
                        newdata +=
                            '<button class="btn btn-sm btn-warning btn-sm font-base mt-1" data-toggle="tooltip" data-placement="right" title="แก้ไข"  onclick="edit_schedule(' +
                            row.id +
                            ')" type="button"><i class="fa fa-edit"></i></button> ';
                        @endif
                        return newdata;
                    }
                }
            ]
        });
    }

    $("#schedule_form").on('submit', function(e) {
        e.preventDefault();
        let url = $(this).attr('action');
        let formData = new FormData(this); // use 'this' directly to refer to the HTML form element
        if ($('#serviceRequestNumber').val() == '') {
            Swal.fire({
                title: "แจ้งเตือน!"
                , text: "กรุณาระบุชื่องานบริการ!"
                , icon: "warning"
            });
        } else if ($('#serviceDescription').val() == '') {
            Swal.fire({
                title: "แจ้งเตือน!"
                , text: "กรุณาระบุประเภทหน่วยงาน!"
                , icon: "warning"
            });
        } else if ($('#departmentType').val() == '') {
            Swal.fire({
                title: "แจ้งเตือน!"
                , text: "กรุณาระบุประเภทหน่วยงาน!"
                , icon: "warning"
            });
        } else if ($('#serviceDepartment').val() == '') {
            Swal.fire({
                title: "แจ้งเตือน!"
                , text: "กรุณาระบุหน่วยงาน!"
                , icon: "warning"
            });
        } else if ($('#serviceDepartment').val() == '') {
            Swal.fire({
                title: "แจ้งเตือน!"
                , text: "กรุณาระบุหน่วยงานผู้ขอใช้บริการ!"
                , icon: "warning"
            });
        } else if ($('#txtServiceRecipient').val() == '') {
            Swal.fire({
                title: "แจ้งเตือน!"
                , text: "กรุณาระบุผู้ขอใช้บริการ!"
                , icon: "warning"
            });
        } else if ($('#serviceDueDate').val() == '') {
            Swal.fire({
                title: "แจ้งเตือน!"
                , text: "กรุณาระบุวันที่ต้องการใช้บริการ!"
                , icon: "warning"
            });
        // } else if (!($('#serviceProvider').length)) {
        // const selected = $('#serviceProvider').val();
        } else if ($('#serviceProvider').val() == '') {
            if (!selected || selected.length === 0) {
                Swal.fire({
                    title: "แจ้งเตือน!"
                    , text: "กรุณาระบุผู้ให้บริการ!"
                    , icon: "warning"
                });
            }
        } else if ($('#serviceName').val() == '') {
            Swal.fire({
                title: "แจ้งเตือน!"
                , text: "กรุณาระบุงานบริการ!"
                , icon: "warning"
            });
        }

        $.ajax({
            type: "POST"
            , url: url
            , data: formData
            , dataType: 'json'
            , processData: false, // important for file uploads
            contentType: false, // important for file uploads
            beforeSend: function() {
                $('#schedule_form_btn').prop('disabled', true);
            }
            , success: function(response) {
                if (response.status) {
                    Swal.fire({
                        title: "Success"
                        , text: response.message
                        , icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.history.back();
                        }
                    });
                } else {
                    console.log(response);
                }
                // validation('schedule_form', response.error);
                $('#schedule_form_btn').prop('disabled', false);
            }
            , error: function(error) {
                $('#schedule_form_btn').prop('disabled', false);
                console.log(error);
            }
        });
    });

    $("#updateBtn").on('click', function(e) {
        e.preventDefault();

        var serviceRequestId = $('#id').val();
        var serviceRequestNumber = $('#serviceRequestNumber').val();
        var serviceRequestNote = $('#serviceRequestNote').val();

        $.ajax({
            type: "POST"
            , url: "{{ route('admin.service_requests.updateServiceRequestNote') }}"
            , data: {
                serviceRequestId: serviceRequestId
                , serviceRequestNumber: serviceRequestNumber
                , serviceRequestNote: serviceRequestNote
            , }
            , dataType: 'json'
            , beforeSend: function() {}
            , success: function(response) {
                if (response.status == true) {
                    Swal.fire("Success", response.message, "success");
                    $('#modal_schedule_form').modal('hide');
                    // console.log(response);
                } else {
                    // console.log(response);
                }
            }
            , error: function(error) {
                console.log(error);
            }
        });
    });

    function add_schedule() {
        $("#id").val('');
        $('#description').val('');
        $('#file').val('');
        $('#department').val('');
        $('#service').val('');
        $('#schedule_from').val('');
        $('#schedule_to').val('');
        $('#saveBtn').removeAttr('hidden');
        $('#divServiceNote').attr('hidden', true);
        $('#divServiceUpdateBtn').attr('hidden', true);
        $("#modal_schedule_form").modal('show');
    }


    function edit_schedule(id) {
        $.ajax({
            type: "GET"
            , url: "{{ route('admin.service_requests.find') }}/" + id
            , data: {}
            , dataType: 'json'
            , beforeSend: function() {}
            , success: function(response) {
                // console.log(response);
                if (response.status == true) {
                    $('#id').val(response.data.id);
                    $('#serviceRequestNumber').val(response.data.serviceRequestNumber);
                    $('#serviceName').val(response.data.serviceName).select2({
                        width: '90%'
                    });
                    $('#serviceDescription').val(response.data.serviceDescription);
                    $('#serviceDepartment').val(response.data.serviceDepartment).select2({
                        width: '90%'
                    });
                    $('#serviceRecipient').val(response.data.serviceRecipient);
                    $('#servicePriority').val(response.data.servicePriority).select2({
                        width: '90%'
                    });
                    $('#divServiceNote').attr('hidden', true);
                    $('#divServiceUpdateBtn').attr('hidden', true);
                    $('#modal_schedule_form').modal('show');
                } else {
                    console.log(response);
                }
            }
            , error: function(error) {
                console.log(error);
            }
        });
    }

    function update_schedule(id) {
        $.ajax({
            type: "GET"
            , url: "{{ route('admin.service_requests.find') }}/" + id
            , data: {}
            , dataType: 'json'
            , beforeSend: function() {}
            , success: function(response) {
                // console.log(response);
                if (response.status == true) {
                    $('#id').val(response.data.id);
                    $('#serviceRequestNumber').val(response.data.serviceRequestNumber);
                    $('#serviceName').val(response.data.serviceName).select2({
                        width: '90%'
                    });
                    $('#serviceDescription').val(response.data.serviceDescription);
                    $('#serviceDepartment').val(response.data.serviceDepartment).select2({
                        width: '90%'
                    });
                    $('#serviceRecipient').val(response.data.serviceRecipient);
                    $('#servicePriority').val(response.data.servicePriority).select2({
                        width: '90%'
                    });
                    $('#serviceDescription').attr('readonly', true);
                    $('#serviceName').attr('disabled', true);
                    $('#servicePriority').attr('disabled', true);
                    $('#serviceDepartment').attr('disabled', true);
                    $('#txtServiceRecipient').attr('readonly', true);
                    $('#serviceFileUpload').attr('disabled', true);
                    $('#saveBtn').attr('hidden', true);
                    $('#divServiceNote').removeAttr('hidden');
                    $('#divServiceUpdateBtn').removeAttr('hidden');
                    $('#modal_schedule_form').modal('show');
                } else {
                    console.log(response);
                }
            }
            , error: function(error) {
                console.log(error);
            }
        });
    }


    function selectdepartments() {
        let serviceDepartment = $('#serviceDepartment').val();

        if (serviceDepartment == 'etc') {
            $('#departmentEtc').removeAttr('hidden');

        } else {
            $('#departmentEtc').attr('hidden', true);
        }
    }


    function delete_schedule(id) {
        Swal.fire({
            title: "แน่ใจหรือไม่?"
            , text: "ต้องการลบข้อมูลใช่หรือไม่?"
            , icon: "warning", // Use 'icon' instead of 'type'
            showCancelButton: true
            , confirmButtonColor: "#DD6B55"
            , confirmButtonText: "ใช่"
            , cancelButtonText: "ไม่"
        , }).then((result) => {
            if (result.isConfirmed) { // Check if the user clicked 'Yes'
                $.ajax({
                    type: "POST"
                    , url: "{{ route('admin.service_requests.delete') }}/" + id
                    , data: {}
                    , dataType: 'json'
                    , success: function(response) {
                        if (response.status == true) {
                            show_schedule();
                            Swal.fire("Success", response.message, "success");
                        } else {
                            console.log(response);
                        }
                    }
                    , error: function(error) {
                        console.log(error);
                    }
                });
            }
        });
    }

    function show_timeline_form(id) {
        $.ajax({
            type: "GET"
            , url: "{{ route('admin.service_lists.getTimeLine') }}/" + id
            , data: {}
            , dataType: 'json'
            , beforeSend: function() {}
            , success: function(response) {
                if (response.status == true) {
                    let serviceRequestNote = response.data;
                    let timelineHtml = '';
                    serviceRequestNote.forEach((value) => {
                        const formatDate = new Date(value.created_at);
                        timelineHtml += `
                                    <div class="time-label">
                                        <span class="bg-gray">${formatDate.toLocaleDateString()}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-sticky-note bg-success"></i>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> ${formatDate.toLocaleTimeString()}</span>
                                            <h3 class="timeline-header"><a href="#">${value.name || ''}</a> ${value.serviceName || ''}</h3>
                                            <div class="timeline-body">
                                                ${value.serviceRequestNote || ''}
                                            </div>
                                        </div>
                                    </div>
                                `;
                    });
                    $('#timelineContainer').html(timelineHtml);
                } else {
                    $('#timelineContainer').html('<div>ไม่พบข้อมูล Timeline</div>');
                }
            }
            , error: function(error) {
                $('#timelineContainer').html('<div>เกิดข้อผิดพลาดในการโหลด Timeline</div>');
                console.log(error);
            }
        });
        $('#modal_timeline_form').modal('show');
    }

    function service_status_change(serviceStatus, element) {
        // Find the nearest `span` element with `data-id` relative to `element`
        var dataId = $(element).closest('tr').find('span').data('id');
        $.ajax({
            type: "POST"
            , url: "{{ route('admin.service_requests.service_status_change') }}"
            , data: {
                serviceStatus: serviceStatus
                , dataId: dataId
            }
            , dataType: 'json'
            , success: function(response) {
                if (response.status == true) {
                    show_schedule();
                    Swal.fire("Success", response.message, "success");
                } else {
                    console.log(response);
                }
            }
            , error: function(error) {
                console.log(error);
            }
        });
    }

    $('#departmentType').change(function() {
        var departmentType = $(this).val();

        $.ajax({
            type: "GET",
            url: "{{ route('admin.service_requests.departmentType') }}",
            data: {
                departmentType: departmentType,
            },
            dataType: 'json',
            beforeSend: function() {
                $('#serviceDepartment').html('<option value="">-- Loading... --</option>');
            },
            success: function(response) {
                $('#serviceDepartment').empty();
                $('#serviceDepartment').append('<option value=""> เลือก </option>');

                if (response.status == true) {
                    $.each(response.data, function(index, item) {
                        $('#serviceDepartment').append(
                            $('<option>', {
                                value: item.id,
                                text: item.departmentName
                            })
                        );
                    });
                    $('#serviceDepartment').append('<option value="etc">อื่นๆ</option>');
                } else {
                    $('#serviceDepartment').append('<option value="">-- ไม่พบข้อมูล --</option>');
                }
            },
            error: function(error) {
                console.log(error);
                $('#serviceDepartment').html('<option value="">-- เกิดข้อผิดพลาด --</option>');
            }
        });
    });

    $('#serviceProvider').change(function() {
        var serviceProvider = $(this).val();

        $.ajax({
            type: "GET",
            url: "{{ route('admin.service_requests.servicename') }}",
            data: {
                serviceProvider: serviceProvider,
            },
            dataType: 'json',
            beforeSend: function() {
                $('#serviceName').html('<option value="">-- Loading... --</option>');
            },
            success: function(response) {
                $('#serviceName').empty();
                $('#serviceName').append('<option value=""> เลือก </option>');

                if (response.status == true) {
                    $.each(response.data, function(index, item) {
                        $('#serviceName').append(
                            $('<option>', {
                                value: item.id,
                                text: item.serviceName
                            })
                        );
                    });
                } else {
                    $('#serviceName').append('<option value="">-- ไม่พบข้อมูล --</option>');
                }
            },
            error: function(error) {
                console.log(error);
                $('#serviceName').html('<option value="">-- เกิดข้อผิดพลาด --</option>');
            }
        });
    });
    

</script>
@endsection

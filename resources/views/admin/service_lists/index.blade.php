@extends('layouts.admin')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    {{-- <h3>ขอใช้บริการ</h3> --}}
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="">แดชบอร์ด</a></li>
                        <li class="breadcrumb-item active">รายการคำขอ</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" onclick="add_schedule();"><i class="fad fa-folder-plus"></i> เพิ่มคำขอ</a>
        </div>
    </div> --}}

    <div class="card">
        <div class="card-header bg-secondary">
            <i class="fad fa-stream"></i> รายการ
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tbl_schedule" style="width: 100%;">
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modal_schedule_form" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h4 class="modal-title" id="modelHeading">รายละเอียด</h4> --}}
                    <h6 class="modal-title" id="modelHeading"><i class="fad fa-file-alt"></i> ข้อมูลคำขอใช้บริการ</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fad fa-window-close"
                                style="--fa-primary-color: #bd0000; --fa-secondary-color: #bd0000;"></i></span>
                    </button>
                </div>
                <form method="POST" class="needs-validation" id="schedule_form" action="{{ route('admin.service_lists.save') }}"
                    enctype="multipart/form-data" novalidate>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header">
                                &nbsp;
                            </div>

                            <div class="card-body">
                                <input type="hidden" name="id" id="id" />
                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">หมายเลขคำขอ</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <input type="text" name="serviceRequestNumber" id="serviceRequestNumber" class="form-control" value="{{ $requestId }}" readonly required placeholder="" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">รายละเอียด</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <textarea type="textarea" name="serviceDescription" id="serviceDescription" class="form-control" required placeholder="" readonly></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">งานบริการ</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <select name="serviceName" id="serviceName" class="form-control select2" readonly disabled>
                                                    <option value="">เลือก</option>
                                                    @foreach ($service_assigns as $service_assign)
                                                        <option value="{{ $service_assign->id }}">
                                                            {{ $service_assign->serviceName }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">หน่วยงาน</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <select name="serviceDepartment" id="serviceDepartment" class="form-control select2" disabled required>
                                                    <option value="">เลือก</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}">
                                                            {{ $department->departmentName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">ชั้นความเร็ว</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <select name="servicePriority" id="servicePriority" class="form-control select2" disabled required>
                                                    <option value="">เลือก</option>
                                                    @foreach ($prioritys as $priority)
                                                        <option value="{{ $priority->priorityStatus }}">
                                                            {{ $priority->priorityName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">ผู้ขอใช้บริการ</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <input type="text" value="{{ Auth()->user()->name }}" class="form-control" required placeholder="" disabled />
                                                <input type="hidden" name="serviceRecipient" id="serviceRecipient" value="{{ Auth()->user()->username }}" class="form-control" required placeholder=""/>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">ไฟล์</label>
                                            <a id="download" href="#"></a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success" id="saveBtn" value="create"><i
                                    class="fad fa-save"></i> บันทึก</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Modal Form -->
    <div class="modal fade" id="modal_timeline_form" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h4 class="modal-title" id="modelHeading">รายละเอียด</h4> --}}
                    {{-- <h4 class="modal-title" id="modelHeading"><i class="fad fa-file-alt"></i></h4> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fad fa-window-close"
                                style="--fa-primary-color: #bd0000; --fa-secondary-color: #bd0000;"></i></span>
                    </button>
                </div>
                <form method="POST" class="needs-validation" id="schedule_form" action=""
                    enctype="multipart/form-data" novalidate>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-header bg-light">
                                <i class="fad fa-history"></i> ติดตามการดำเนินการ
                            </div>

                            <div class="card-body">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="timeline" id="timelineContainer">
                                                <!-- Timeline items will be injected here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() { 
            $("#serviceName").select2({ width: '90%' });
            $("#serviceDepartment").select2({ width: '90%' });
            $("#servicePriority").select2({ width: '90%' });
        });
        const baseDownloadUrl = "{{ route('admin.service_requests.download') }}";

        var tbl_schedule;
        show_schedule();

        function show_schedule() {
            if (tbl_schedule) {
                tbl_schedule.destroy();
            }
            tbl_samples = $('#tbl_schedule').DataTable({
                destroy: true,
                pageLength: 10,
                responsive: true,
                ajax: "{{ route('admin.service_lists.list') }}",
                deferRender: true,
                columns: [{
                        className: '',
                        data: 'id',
                        title: '<i class="fad fa-list-ol"></i>',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            // Return the content with a data-id attribute
                            return '<span data-id="' + row.id + '">' + data + '</span>';
                        }
                    },
                    {
                        data: 'serviceName',
                        orderable: false,
                        title: '<i class="fad fa-hands-heart"></i> งานบริการ',
                        render: function(data, type, row, meta) {
                            @foreach ($service_assigns as $service_assign)
                            if(row.serviceName == '{{ $service_assign->id }}') {
                                return '{{ $service_assign->serviceName }}';
                            }
                            @endforeach
                        }
                    },
                    {
                        data: 'serviceDepartment',
                        orderable: false,
                        title: '<i class="fad fa-share-alt"></i> หน่วยงาน',
                        render: function(data, type, row, meta) {
                            @foreach ($departments as $department)
                            if(row.serviceDepartment == '{{ $department->id }}') {
                                return '{{ $department->departmentName }}';
                            }
                            @endforeach
                        }
                    },
                    {
                        className: '',
                        data: 'serviceDepartmentType',
                        title: '<i class="fad fa-poll-people"></i> ประเภทหน่วยงาน',
                        orderable: false,
                    },
                    {
                        className: '',
                        data: 'serviceDateTime',
                        title: '<i class="fad fa-calendar-alt"></i> วันที่/เวลา',
                        orderable: false,
                    },
                    {
                        data: 'serviceRecipient',
                        orderable: false,
                        title: '<i class="fad fa-users"></i> ผู้ขอใช้บริการ',
                        render: function(data, type, row, meta) {
                            @foreach ($users as $user)
                            if(row.serviceRecipient == '{{ $user->username }}') {
                                return '{{ $user->name }}';
                            }
                            @endforeach
                        }
                    },
                    {
                        data: 'servicePriority',
                        orderable: false,
                        title: '<i class="fad fa-layer-group"></i> ชั้นความเร็ว',
                        render: function(data, type, row, meta) {

                            var priorityStatus = '';

                            @foreach ($prioritys as $priority)
                            if (row.servicePriority == 'Normal') {
                                priorityStatus = '<div class="bg-success color-palette"><span>&nbsp;</span></div>';
                            } else if (row.servicePriority == 'Urgent') {
                                priorityStatus = '<div class="bg-warning color-palette"><span>&nbsp;</span></div>';
                            } else if (row.servicePriority == 'VeryUrgent') {
                                priorityStatus = '<div class="bg-orange color-palette"><span>&nbsp;</span></div>';
                            } else if (row.servicePriority == 'MostUrgent') {
                                priorityStatus = '<div class="bg-danger color-palette"><span>&nbsp;</span></div>';
                            }
                            @endforeach

                            return priorityStatus;
                        }
                    },
                    {
                        className: 'width-option-1 text-center',
                        width: '12%',
                        "data": 'id',
                        "orderable": false,
                        "title": '<i class="fad fa-ballot-check"></i> สถานะ',
                        "render": function(data, type, row, meta) {

                            // Initialize selected variable for each status
                            var select = '<select name="serviceStatus" class="form-control" onchange="service_status_change(this.value, this)" disabled>';
                                select += '<option value="">เลือก</option>';
                                @foreach ($servicesStatus as $serviceStatus)
                                    var selected = (row.serviceStatus == '{{ $serviceStatus->serviceStatus }}') ? 'selected' : '';
                                    select += '<option value="{{ $serviceStatus->serviceStatus }}" ' + selected + '><i class="fad fa-alarm-clock"></i>{{ $serviceStatus->serviceStatusName }}</option>';
                                @endforeach
                                select += '</select>';
                            return select;
                        }
                    },
                    {
                        className: 'width-option-1 text-center',
                        width: '6%',
                        "data": 'id',
                        "orderable": false,
                        "title": '',
                        "render": function(data, type, row, meta) {
                            newdata = '';
                            newdata +=
                                '<button class="btn btn-sm btn-success btn-sm font-base mt-1"  onclick="edit_schedule(' +
                                row.id + ')" type="button"><i class="fad fa-eye"></i></button> ';
                            newdata +=
                                '<button class="btn btn-sm btn-info btn-sm font-base mt-1"  onclick="show_timeline_form(' +
                                row.id + ')" type="button"><i class="fad fa-stream"></i></button> ';
                            // newdata +=
                            //     ' <button class="btn btn-xs btn-danger btn-sm font-base mt-1" onclick="delete_schedule(' +
                            //     row.id + ');" type="button"><i class="fa fa-trash"></i></button>';

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
            if ($('#title').val() == '') {
                Swal.fire({
                    title: "แจ้งเตือน!",
                    text: "กรุณาระบุเรื่องที่ต้องการใช้บริการ!",
                    icon: "warning"
                });
            } else if ($('#description').val() == '') {
                Swal.fire({
                    title: "แจ้งเตือน!",
                    text: "กรุณาระบุรายละเอียดงานที่ต้องการใช้บริการ!",
                    icon: "warning"
                });
            } else if ($('#department').val() == '') {
                Swal.fire({
                    title: "แจ้งเตือน!",
                    text: "กรุณาเลือกหน่วยงานที่เกี่ยวข้อง!",
                    icon: "warning"
                });
            } else if ($('#service').val() == '') {
                Swal.fire({
                    title: "แจ้งเตือน!",
                    text: "กรุณาเลือกงานบริการที่ร้องขอ!",
                    icon: "warning"
                });
            } else if ($('#schedule_from').val() == '') {
                Swal.fire({
                    title: "แจ้งเตือน!",
                    text: "กรุณาระบุรายวันที่เริ่ม!",
                    icon: "warning"
                });
            } else if ($('#schedule_to').val() == '') {
                Swal.fire({
                    title: "แจ้งเตือน!",
                    text: "กรุณาระบุถึงวันที่!",
                    icon: "warning"
                });
            }

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                dataType: 'json',
                processData: false, // important for file uploads
                contentType: false, // important for file uploads
                beforeSend: function() {
                    $('#schedule_form_btn').prop('disabled', true);
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire("Success", response.message, "success");
                        show_schedule();
                        $('#modal_schedule_form').modal('hide');
                    } else {
                        console.log(response);
                    }
                    // validation('schedule_form', response.error);
                    $('#schedule_form_btn').prop('disabled', false);
                },
                error: function(error) {
                    $('#schedule_form_btn').prop('disabled', false);
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
            $("#modal_schedule_form").modal('show');
        }


        function edit_schedule(id) {
            $.ajax({
                type: "GET",
                url: "{{ route('admin.service_lists.find') }}/" + id,
                data: {},
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    // console.log(response);
                    if (response.status == true) {
                        let fileName = response.data.serviceFileUpload;
                        let fileLinksHtml = '';
                        fileLinksHtml += `<a href="${baseDownloadUrl}/${fileName.trim()}" target="_blank">${fileName.trim()}</a>`;

                        if(fileName == '') {
                            $('#download').attr('hidden', true);
                        } else {
                            $('#download').removeAttr('hidden');
                        }

                        $('#id').val(response.data.id);
                        $('#serviceRequestNumber').val(response.data.serviceRequestNumber);
                        $('#serviceName').val(response.data.serviceName).select2({ width: '90%' });
                        $('#serviceDescription').val(response.data.serviceDescription);
                        $('#serviceDepartment').val(response.data.serviceDepartment).select2({ width: '90%' });
                        $('#serviceRecipient').val(response.data.serviceRecipient);
                        $('#servicePriority').val(response.data.servicePriority).select2({ width: '90%' });
                        $('#download').html(fileLinksHtml);
                        $('#modal_schedule_form').modal('show');
                    } else {
                        console.log(response);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function show_timeline_form(id) {
            $.ajax({
                type: "GET",
                url: "{{ route('admin.service_lists.getTimeLine') }}/" + id,
                data: {},
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
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
                },
                error: function(error) {
                    $('#timelineContainer').html('<div>เกิดข้อผิดพลาดในการโหลด Timeline</div>');
                    console.log(error);
                }
            });
            $('#modal_timeline_form').modal('show');
        }

        function delete_schedule(id) {
            Swal.fire({
                title: "แน่ใจหรือไม่?",
                text: "ต้องการลบข้อมูลใช่หรือไม่?",
                icon: "warning", // Use 'icon' instead of 'type'
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "ใช่",
                cancelButtonText: "ไม่",
            }).then((result) => {
                if (result.isConfirmed) { // Check if the user clicked 'Yes'
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.service_lists.delete') }}/" + id,
                        data: {},
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == true) {
                                show_schedule();
                                Swal.fire("Success", response.message, "success");
                            } else {
                                console.log(response);
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
            });
        }

        function job_status_schedule(job_status, element) {
            // Find the nearest `span` element with `data-id` relative to `element`
            var dataId = $(element).closest('tr').find('span').data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('admin.service_lists.job_status') }}",
                data: {
                    job_status: job_status,
                    dataId: dataId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        show_schedule();
                        Swal.fire("Success", response.message, "success");
                    } else {
                        console.log(response);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    </script>
@endsection

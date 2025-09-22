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
                        <li class="breadcrumb-item active">ขอใช้บริการ</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            {{-- <a class="btn btn-success" onclick="add_schedule();"><i class="fad fa-folder-plus"></i> เพิ่มคำขอ</a> --}}
            <a class="btn btn-success" href="{{ route('admin.service_requests.create') }}"><i class="fad fa-folder-plus"></i> เพิ่มคำขอ</a>
        </div>
    </div>

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


        var tbl_schedule;
        var columnVisible; // ซ่อน column

        @if (Auth::user()->roles->contains('title', 'Staff') == true || Auth::user()->roles->contains('title', 'Admin') == true)
            columnVisible = true;
        @else
            columnVisible = false;
        @endif

        show_schedule();

        function show_schedule() {
            if (tbl_schedule) {
                tbl_schedule.destroy();
            }

            tbl_samples = $('#tbl_schedule').DataTable({
                destroy: true,
                pageLength: 10,
                responsive: true,
                ajax: "{{ route('admin.service_requests.list') }}",
                deferRender: true,
                columns: [
                    {
                        className: 'width-option-1 text-center',
                        data: 'id',
                        title: '<i class="fad fa-flag"></i>',
                        orderable: false,
                        visible: columnVisible,
                        render: function(data, type, row, meta) {
                            if(row.serviceFlag == 1){
                                return '<i class="fas fa-star fa-lg" style="color: #FFD43B;"></i>';
                            }else{
                                return '';
                            }
                        }
                    },
                    {
                        className: '',
                        data: 'serviceDateTime',
                        title: '<i class="fad fa-calendar-alt"></i> วันที่/เวลา',
                        orderable: false,
                    },
                    {
                        data: 'serviceName',
                        orderable: false,
                        title: '<i class="fad fa-hands-heart"></i> งานบริการ',
                        render: function(data, type, row, meta) {
                            @foreach ($service_assigns as $service_assign)
                                if (row.serviceName == '{{ $service_assign->id }}') {
                                    return '{{ $service_assign->serviceName }}';
                                }
                            @endforeach
                        }
                    },
                    {
                        className: '',
                        data: 'serviceDescription',
                        title: '<i class="fad fa-poll-people"></i> รายละเอียด',
                        orderable: false,
                    },
                    {
                        data: 'serviceRecipient',
                        orderable: false,
                        title: '<i class="fad fa-users"></i> ผู้ขอใช้บริการ',
                        render: function(data, type, row, meta) {
                            @foreach ($users as $user)
                                if (row.serviceRecipient == '{{ $user->username }}') {
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

                            return priorityStatus;
                        }
                    },
                    {
                        className: 'width-option-1 text-center',
                        width: '6%',
                        data: 'id',
                        orderable: false,
                        title: '',
                        render: function(data, type, row, meta) {
                            newdata = '';
                            url = "{{ route('admin.service_requests.edit') }}/" + row.id;
                            userUrl = "{{ route('admin.service_requests.view') }}/" + row.id;
                            @if (Auth::user()->roles->contains('title', 'Staff') == true || Auth::user()->roles->contains('title', 'Admin') == true)
                                // newdata +=
                                //     '<button class="btn btn-sm btn-warning btn-sm font-base mt-1" data-toggle="tooltip" data-placement="right" title="แก้ไข"  onclick="edit_schedule(' +
                                //     row.id + ')" type="button"><i class="fa fa-edit"></i></button> ';
                                newdata +=
                                    '<a class="btn btn-sm btn-info btn-sm font-base mt-1" href='+url+'><i class="fad fa-folder-plus"></i> ดู</a>';
                            @else
                                newdata +=
                                    '<a class="btn btn-sm btn-info btn-sm font-base mt-1" href='+userUrl+'><i class="fad fa-folder-plus"></i> ดู</a>';
                            @endif
                            return newdata;
                        }
                    },
                    {
                        className: 'width-option-1 text-center',
                        width: '6%',
                        data: 'id',
                        orderable: false,
                        title: '',
                        render: function(data, type, row, meta) {
                            newdata = '';
                            @if (Auth::user()->roles->contains('title', 'Staff') == true || Auth::user()->roles->contains('title', 'Admin') == true)
                                newdata +='<span class="right badge badge-danger">ใหม่</span>';
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

        $("#updateBtn").on('click', function(e) {
            e.preventDefault();

            var serviceRequestId = $('#id').val();
            var serviceRequestNumber = $('#serviceRequestNumber').val();
            var serviceRequestNote = $('#serviceRequestNote').val();

            $.ajax({
                type: "POST",
                url: "{{ route('admin.service_requests.updateServiceRequestNote') }}",
                data: {
                    serviceRequestId: serviceRequestId,
                    serviceRequestNumber: serviceRequestNumber,
                    serviceRequestNote: serviceRequestNote,
                },
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
                    if (response.status == true) {
                        Swal.fire("Success", response.message, "success");
                        $('#modal_schedule_form').modal('hide');
                        // console.log(response);
                    } else {
                        // console.log(response);
                    }
                },
                error: function(error) {
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
                type: "GET",
                url: "{{ route('admin.service_requests.find') }}/" + id,
                data: {},
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
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
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function update_schedule(id) {
            $.ajax({
                type: "GET",
                url: "{{ route('admin.service_requests.find') }}/" + id,
                data: {},
                dataType: 'json',
                beforeSend: function() {},
                success: function(response) {
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
                },
                error: function(error) {
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
                        url: "{{ route('admin.service_requests.delete') }}/" + id,
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

        function service_status_change(serviceStatus, element) {
            // Find the nearest `span` element with `data-id` relative to `element`
            var dataId = $(element).closest('tr').find('span').data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('admin.service_requests.service_status_change') }}",
                data: {
                    serviceStatus: serviceStatus,
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

@extends('layouts.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-0">
            <div class="col-sm-6">
                {{-- <h3></h3> --}}
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">แดชบอร์ด</a></li>
                    <li class="breadcrumb-item active">ชั้นความเร็ว</li>
                </ol>
            </div>
        </div>
    </div>
</div>
    @can('status_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="javascript:void(0)" id="createNewDepartment"><i class="fad fa-folder-plus"></i>
                    เพิ่มสถานะ</a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            รายการ
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable">
                    <thead>
                        <tr width="10">
                            <th>#</th>
                            <th>สถานะ</th>
                            <th>ค่าสถานะ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fad fa-window-close" style="--fa-primary-color: #bd0000; --fa-secondary-color: #bd0000;"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="department_Form" name="department_Form" class="form-horizontal">
                        <input type="hidden" name="service_status_id" id="service_status_id">

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">สถานะ</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="serviceStatusName" name="serviceStatusName" placeholder=""
                                        placeholder="" value="" title="">
                                    </div>
                                    <p class="help-block" style="color: red;"><span id=""></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">ค่าสถานะ</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="serviceStatus" name="serviceStatus" placeholder=""
                                        placeholder="" value="" title="">
                                    </div>
                                    <p class="help-block" style="color: red;"><span id=""></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success" id="saveBtn" value="create"><i
                                    class="fad fa-save"></i> บันทึก
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript" src="{{ asset('js/sweetalert2@11.js') }}"></script>
    <script type="text/javascript">
        $(function() {

            /*------------------------------------------
             --------------------------------------------
             Pass Header Token
             --------------------------------------------
             --------------------------------------------*/
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /*------------------------------------------
            --------------------------------------------
            Render DataTable
            --------------------------------------------
            --------------------------------------------*/
            var table = $('.datatable').DataTable({
                processing: false,
                serverSide: false,
                ajax: "{{ route('admin.service_status.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id',
                    },
                    {
                        data: 'serviceStatusName',
                        name: 'serviceStatusName',
                    },
                    {
                        data: 'serviceStatus',
                        name: 'serviceStatus',
                    },
                    // {
                    //     data: 'serviceStatus',
                    //     render: function (data, type, row, meta) {
                    //         if(data === 'Normal'){
                    //             return '<div class="bg-success color-palette"><span>&nbsp;</span></div>';
                    //         }
                    //         else if(data === 'Urgent'){
                    //             return '<div class="bg-warning color-palette"><span>&nbsp;</span></div>';
                    //         }
                    //         else if(data === 'VeryUrgent'){
                    //             return '<div class="bg-orange color-palette"><span>&nbsp;</span></div>';
                    //         }
                    //         else if(data === 'MostUrgent'){
                    //             return '<div class="bg-danger color-palette"><span>&nbsp;</span></div>';
                    //         }
                    //     },
                    //     className: 'text-center',
                    // },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Button
            --------------------------------------------
            --------------------------------------------*/
            $('#createNewDepartment').click(function() {
                $('#saveBtn').val("บันทึก");
                $('#priority_id').val('');
                $('#department_Form').trigger("reset");
                $('#modelHeading').html("เพิ่มข้อมูล");
                $('#ajaxModel').modal('show');
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Edit Button
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.editDepartment', function() {
                var service_status_id = $(this).data('id');
                $.get("{{ route('admin.service_status.index') }}" + '/' + service_status_id + '/edit', function(
                    data) {
                    $('#modelHeading').html("แก้ไขข้อมูล");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModel').modal('show');
                    $('#service_status_id').val(data.id);
                    $('#serviceStatusName').val(data.serviceStatusName);
                    $('#serviceStatus').val(data.serviceStatus);
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create
            --------------------------------------------
            --------------------------------------------*/
            $("#department_Form").on('submit', function(e) {
                e.preventDefault();
                let url = $(this).attr('action');
                let formData = new FormData(this); // use 'this' directly to refer to the HTML form element
                if ($('#serviceStatusName').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุสถานะการใช้บริการ!",
                        icon: "warning"
                    });
                } else if ($('#serviceStatus').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุค่าสถานะการใช้บริการ!",
                        icon: "warning"
                    });
                }

                $.ajax({
                    data: $('#department_Form').serialize(),
                    url: "{{ route('admin.service_status.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {

                        $('#department_Form').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();

                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('บันทึก');
                    }
                });
            });

            /*------------------------------------------
            --------------------------------------------
            Delete
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.deleteDepartment', function() {

                var service_status_id = $(this).data("id");
                confirm("คุณแน่ใจหรือไม่ว่าต้องการลบ !");

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('admin.service_status.store') }}" + '/' + service_status_id,
                    success: function(data) {
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });

        });
    </script>
@endsection

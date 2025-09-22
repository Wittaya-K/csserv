@extends('layouts.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-0">
            <div class="col-sm-6">
                {{-- <h3>งานบริการ</h3> --}}
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">แดชบอร์ด</a></li>
                    <li class="breadcrumb-item active">งานบริการ</li>
                </ol>
            </div>
        </div>
    </div>
</div>
    @can('service_request_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="javascript:void(0)" id="createServiceRequest"><i class="fad fa-folder-plus"></i>
                    เพิ่มงานบริการ</a>
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
                            <th>งานบริการ</th>
                            <th>ผู้ให้บริการ</th>
                            <th>เลือก</th>
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
                    <form id="service_request_Form" name="service_request_Form" class="form-horizontal">
                        <input type="hidden" name="service_request_id" id="service_request_id">

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">งานบริการ</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="serviceName" name="serviceName" placeholder="กรุณาระบุชื่องานบริการ"
                                        placeholder="" value="" title="กรุณาระบุชื่องานบริการ">
                                    </div>
                                    <p class="help-block" style="color: red;"><span id=""></span></p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">ผู้ให้บริการ</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="" name="" placeholder="" placeholder="" value="{{ Auth::user()->name }}" title="" readonly>
                                        <input type="hidden" name="serviceProvider" id="serviceProvider" value="{{ Auth::user()->username }}">

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

            $("#department_id").select2({ width: '80%' });

            /*------------------------------------------
            --------------------------------------------
            Render DataTable
            --------------------------------------------
            --------------------------------------------*/
            var table = $('.datatable').DataTable({
                processing: false,
                serverSide: false,
                ajax: "{{ route('admin.service_assigns.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'serviceName',
                        name: 'serviceName'
                    },
                    {
                        data: 'serviceProvider',
                        render: function(data, type, row, meta) {
                            if(row.serviceProvider == "{{ Auth::user()->username }}"){
                                return "{{ Auth::user()->name }}";
                            }else{
                                return null;
                            }
                        }
                    },
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
            $('#createServiceRequest').click(function() {
                $('#saveBtn').val("บันทึก");
                $('#service_request_id').val('');
                $('#service_request_Form').trigger("reset");
                $('#modelHeading').html("เพิ่มงานบริการ");
                $('#ajaxModel').modal('show');
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Edit Button
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.editServiceRequest', function() {
                var service_request_id = $(this).data('id');
                $.get("{{ route('admin.service_assigns.index') }}" + '/' + service_request_id + '/edit', function(
                    data) {
                    $('#modelHeading').html("แก้ไขข้อมูล");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModel').modal('show');
                    $('#service_request_id').val(data.id);
                    $('#serviceName').val(data.serviceName);
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create Course Code
            --------------------------------------------
            --------------------------------------------*/
            $("#service_request_Form").on('submit', function(e) {
                e.preventDefault();
                let url = $(this).attr('action');
                let formData = new FormData(this); // use 'this' directly to refer to the HTML form element
                if ($('#serviceName').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุชื่องานบริการ!",
                        icon: "warning"
                    });
                } else if ($('#serviceProvider').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุผู้ให้บริการ!",
                        icon: "warning"
                    });
                }

                $.ajax({
                    data: $('#service_request_Form').serialize(),
                    url: "{{ route('admin.service_assigns.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {

                        $('#service_request_Form').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        // table.draw();
                        location.reload();

                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('บันทึก');
                    }
                });
            });

            /*------------------------------------------
            --------------------------------------------
            Delete Course Code
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.deleteServiceRequest', function() {

                var service_request_id = $(this).data("id");
                confirm("คุณแน่ใจหรือไม่ว่าต้องการลบ !");

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('admin.service_assigns.store') }}" + '/' + service_request_id,
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

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
                    <li class="breadcrumb-item active">หน่วยงาน</li>
                </ol>
            </div>
        </div>
    </div>
</div>
    @can('department_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="javascript:void(0)" id="createNewDepartment"><i class="fad fa-folder-plus"></i>
                    เพิ่มหน่วยงาน</a>
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
                            <th>ชื่อหน่วยงาน</th>
                            <th>ประเภทหน่วยงาน</th>
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
                        <input type="hidden" name="department_id" id="department_id">

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">ชื่อหน่วยงาน</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="departmentName" name="departmentName" placeholder="กรุณาระบุชื่อหน่วยงาน"
                                        placeholder="" value="" title="กรุณาระบุชื่อหน่วยงาน" required>
                                    </div>
                                    <p class="help-block" style="color: red;"><span id=""></span></p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">ประเภทหน่วยงาน</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <select name="departmentType" id="departmentType" class="form-control" required>
                                                <option value="">เลือก</option>
                                                <option value="ภายในคณะวิทยาศาสตร์">ภายในคณะวิทยาศาสตร์</option>
                                                <option value="ภายนอกคณะวิทยาศาสตร์">ภายนอกคณะวิทยาศาสตร์</option>
                                        </select>
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
                ajax: "{{ route('admin.departments.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'departmentName',
                        name: 'departmentName'
                    },
                    {
                        data: 'departmentType',
                        name: 'departmentType'
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
            $('#createNewDepartment').click(function() {
                $('#saveBtn').val("บันทึก");
                $('#department_id').val('');
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
                var department_id = $(this).data('id');
                $.get("{{ route('admin.departments.index') }}" + '/' + department_id + '/edit', function(
                    data) {
                    $('#modelHeading').html("แก้ไขข้อมูล");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModel').modal('show');
                    $('#department_id').val(data.id);
                    $('#departmentName').val(data.departmentName);
                    $('#departmentType').val(data.departmentType);
                    // $('#status').val(data.status);
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create
            --------------------------------------------
            --------------------------------------------*/
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                let url = $(this).attr('action');
                let formData = new FormData(this); // use 'this' directly to refer to the HTML form element
                if ($('#departmentName').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุชื่อหน่วยงาน!",
                        icon: "warning"
                    });
                } else if ($('#departmentType').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุประเภทหน่วยงาน!",
                        icon: "warning"
                    });
                }

                $.ajax({
                    data: $('#department_Form').serialize(),
                    url: "{{ route('admin.departments.store') }}",
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

                var department_id = $(this).data("id");
                confirm("คุณแน่ใจหรือไม่ว่าต้องการลบ !");

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('admin.departments.store') }}" + '/' + department_id,
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

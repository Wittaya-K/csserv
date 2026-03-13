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
                    <li class="breadcrumb-item active">ลิงก์หน่วยงานที่เกี่ยวข้อง</li>
                </ol>
            </div>
        </div>
    </div>
</div>
    @can('manage_link_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="javascript:void(0)" id="create"><i class="fad fa-folder-plus"></i>
                    เพิ่มลิงก์</a>
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
                            <th>ชื่อลิงก์</th>
                            <th>ลิงก์</th>
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
                    <form id="manage_link_Form" name="manage_link_Form" class="form-horizontal">
                        <input type="hidden" name="manage_link_id" id="manage_link_id">

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">ชื่อหน่วยงาน</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <select name="department_name" id="department_name" class="form-control select2" required>
                                            @foreach ($dataDepartment as $item)
                                                <option value="{{ $item->departmentName }}">{{ $item->departmentName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p class="help-block" style="color: red;"><span id=""></span></p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">ชื่อลิงก์</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="link_name" name="link_name" placeholder=""
                                        placeholder="" required>
                                    </div>
                                    <p class="help-block" style="color: red;"><span id=""></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">ลิงก์</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="link" name="link" placeholder=""
                                        placeholder="" required>
                                    </div>
                                    <p class="help-block" style="color: red;"><span id=""></span></p>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12">
                                <div class="col-lg-4 col-12">
                                    <div class="btcd-f-input">
                                        <label class="col-sm-12 control-label">ไฟล์</label>
                                        <input type="file" name="fileUpload" id="fileUpload">
                                        <div id="currentFile" class="mt-1"></div>
                                    </div>
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#department_name").select2({
                width: '85%'
            });

            var table = $('.datatable').DataTable({
                processing: false,
                serverSide: false,
                ajax: "{{ route('admin.manage_links.index') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'department_name',
                        name: 'department_name'
                    },
                    {
                        data: 'link_name',
                        name: 'link_name'
                    },
                    {
                        data: 'link',
                        name: 'link'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
            });

            $('#create').click(function() {
                $('#saveBtn').val("บันทึก");
                $('#manage_link_id').val('');
                $('#manage_link_Form').trigger("reset");
                $('#modelHeading').html("เพิ่มข้อมูล");
                $('#ajaxModel').modal('show');
            });

            $('body').on('click', '.edit', function() {
                var manage_link_id = $(this).data('id');
                $.get("{{ route('admin.manage_links.index') }}" + '/' + manage_link_id + '/edit', function(data) {
                    $('#modelHeading').html("แก้ไขข้อมูล");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModel').modal('show');
                    $('#manage_link_id').val(data.id);
                    $('#department_name').val(data.department_name).trigger('change');
                    // $('#department_name').val(data.department_name);
                    $('#link_name').val(data.link_name);
                    $('#link').val(data.link);

                    // แสดงไฟล์เดิม (ถ้ามี)
                    if (data.file_name) {
                        $('#currentFile').html(
                            '<small class="text-muted">ไฟล์ปัจจุบัน: <a href="/storage/manage_links/' + data.file_name + '" target="_blank">' + data.file_name + '</a></small>'
                        );
                    } else {
                        $('#currentFile').html('');
                    }
                });
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();

                let form = document.getElementById('manage_link_Form');
                let formData = new FormData(form);

                if ($('#department_name').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุชื่อหน่วยงาน!",
                        icon: "warning"
                    });
                    return;
                }

                if ($('#link_name').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุชื่อลิงก์!",
                        icon: "warning"
                    });
                    return;
                }

                if ($('#link').val() == '') {
                    Swal.fire({
                        title: "แจ้งเตือน!",
                        text: "กรุณาระบุลิงก์!",
                        icon: "warning"
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.manage_links.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,   // สำคัญมาก
                    contentType: false,   // สำคัญมาก
                    success: function(data) {
                        $('#manage_link_Form').trigger("reset");
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


            $('body').on('click', '.delete', function() {

                var manage_link_id = $(this).data("id");
                confirm("คุณแน่ใจหรือไม่ว่าต้องการลบ !");

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('admin.manage_links.store') }}" + '/' + manage_link_id,
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

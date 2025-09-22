@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="row mb-0">
            <div class="col-sm-6">
            </div>
            <div class="col-sm-6">
            </div>
        </div>
    </div>

    <form id="SearchForm" name="SearchForm" action="" method="GET">
        @csrf
        <div class="card">
            <div class="card shadow-lg">
                <div class="card-header bg-secondary">
                    <i class="fad fa-file-chart-line"></i> ค้นหารายการปฏิบัติงาน
                </div>

                <div class="card-body">
                    <div style="margin-bottom: 10px;" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">วันที่/เวลา</label>
                                <div class="col-sm-12">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-chevron-square-down"></i></span>
                                        </div>
                                        <select class="form-control" name="serviceDateTime" id="serviceDateTime" title="">
                                            <option value="">เลือก</option>
                                            @foreach ($serviceRequests as $serviceRequest)
                                                <option value="{{ $serviceRequest->serviceDateTime }}">
                                                    {{ $serviceRequest->serviceDateTime }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">ผู้ให้บริการ</label>
                                <div class="col-sm-12">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-clock"></i></span>
                                        </div>
                                        <select class="form-control" name="serviceProvider" id="serviceProvider" title="">
                                            <option value="">เลือก</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->username }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">หน่วยงาน</label>
                                <div class="col-sm-12">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-clock"></i></span>
                                        </div>
                                        <select class="form-control select2" name="serviceDepartment" id="serviceDepartment"
                                            title="">
                                            <option value="">เลือก</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}">{{ $department->departmentName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="col-sm-12 control-label">งานบริการ</label>
                                <div class="col-sm-12">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-clock"></i></span>
                                        </div>
                                        <select class="form-control select2" name="serviceName" id="serviceName" title="">
                                            <option value="">เลือก</option>
                                            @foreach ($serviceAssigns as $serviceAssign)
                                                <option value="{{ $serviceAssign->id }}">{{ $serviceAssign->serviceName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card shadow-lg">
            <div class="card-header bg-secondary">
                <i class="fad fa-th-list"></i> รายการ
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable">
                        <thead class="text-center">
                            <tr width="10">
                                <th><i class="fad fa-calendar-alt"></i> วันที่/เวลา</th>
                                <th><i class="fad fa-user-headset"></i> ผู้ให้บริการ</th>
                                <th><i class="fad fa-share-alt"></i> หน่วยงาน</th>
                                <th><i class="fad fa-hands-heart"></i> งานบริการ</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_datatable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script type="text/javascript">
        $(document).ready(function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).ready(function() { 
                $("#serviceName").select2({ width: '80%' });
                $("#serviceDepartment").select2({ width: '80%' });
            });

            $('#serviceDateTime').change(function() {
                var serviceDateTime = $('#serviceDateTime').val();

                $.ajax({
                    data: {
                        serviceDateTime: serviceDateTime,
                    },
                    url: "{{ route('admin.search.search') }}",
                    type: "GET",
                    dataType: 'json',
                    success: function(data, textStatus, XmlHttpRequest) {
                        if (XmlHttpRequest.status === 200) {
                            let htmlView = '';
                            if (data.length <= 0) {
                                htmlView += ``;
                            }
                            var serviceName = '';
                            var serviceDepartment = '';
                            $.each(data, function(i, value) {
                                    @foreach ($serviceAssigns as $serviceAssign)
                                        if(value.serviceDepartment == '{{ $serviceAssign->id }}'){
                                            serviceName = '{{ $serviceAssign->serviceName }}';
                                        }
                                    @endforeach
                                    @foreach ($departments as $department)
                                        if(value.serviceDepartment == '{{ $department->id }}'){
                                            serviceDepartment = '{{ $department->departmentName }}';
                                        }
                                    @endforeach
                                    @foreach ($users as $user)
                                        if (value.serviceProvider == "{{ $user->username }}") {
                                            htmlView += `
                                            <tr width="10">
                                                <td>` + value.serviceDateTime + `</td>
                                                <td>` + '{{ $user->name }}' + `</td>
                                                <td>` + serviceDepartment + `</td>
                                                <td>` + serviceName + `</td>
                                            </tr>`;
                                        }
                                    @endforeach
                            });
                            $('#tbody_datatable').html(htmlView);
                        }
                    },
                    error: function(data) {

                    }
                });
            });

            $('#serviceProvider').change(function() {
                var serviceProvider = $('#serviceProvider').val();

                $.ajax({
                    data: {
                        serviceProvider: serviceProvider,
                    },
                    url: "{{ route('admin.search.search') }}",
                    type: "GET",
                    dataType: 'json',
                    success: function(data, textStatus, XmlHttpRequest) {
                        if (XmlHttpRequest.status === 200) {
                            let htmlView = '';
                            if (data.length <= 0) {
                                htmlView += ``;
                            }
                            var serviceName = '';
                            var serviceDepartment = '';
                            $.each(data, function(i, value) {
                                    @foreach ($serviceAssigns as $serviceAssign)
                                        if(value.serviceDepartment == '{{ $serviceAssign->id }}'){
                                            serviceName = '{{ $serviceAssign->serviceName }}';
                                        }
                                    @endforeach
                                    @foreach ($departments as $department)
                                        if(value.serviceDepartment == '{{ $department->id }}'){
                                            serviceDepartment = '{{ $department->departmentName }}';
                                        }
                                    @endforeach
                                    @foreach ($users as $user)
                                        if (value.serviceProvider == "{{ $user->username }}") {
                                            htmlView += `
                                            <tr width="10">
                                                <td>` + value.serviceDateTime + `</td>
                                                <td>` + '{{ $user->name }}' + `</td>
                                                <td>` + serviceDepartment + `</td>
                                                <td>` + serviceName + `</td>
                                            </tr>`;
                                        }
                                    @endforeach
                            });
                            $('#tbody_datatable').html(htmlView);
                        }
                    },
                    error: function(data) {

                    }
                });
            });

            $('#serviceDepartment').change(function() {
                var serviceDepartment = $('#serviceDepartment').val();

                $.ajax({
                    data: {
                        serviceDepartment: serviceDepartment,
                    },
                    url: "{{ route('admin.search.search') }}",
                    type: "GET",
                    dataType: 'json',
                    success: function(data, textStatus, XmlHttpRequest) {
                        if (XmlHttpRequest.status === 200) {
                            let htmlView = '';
                            if (data.length <= 0) {
                                htmlView += ``;
                            }
                            var serviceName = '';
                            var serviceDepartment = '';
                            $.each(data, function(i, value) {
                                    @foreach ($serviceAssigns as $serviceAssign)
                                        if(value.serviceDepartment == '{{ $serviceAssign->id }}'){
                                            serviceName = '{{ $serviceAssign->serviceName }}';
                                        }
                                    @endforeach
                                    @foreach ($departments as $department)
                                        if(value.serviceDepartment == '{{ $department->id }}'){
                                            serviceDepartment = '{{ $department->departmentName }}';
                                        }
                                    @endforeach
                                    @foreach ($users as $user)
                                        if (value.serviceProvider == "{{ $user->username }}") {
                                            htmlView += `
                                            <tr width="10">
                                                <td>` + value.serviceDateTime + `</td>
                                                <td>` + '{{ $user->name }}' + `</td>
                                                <td>` + serviceDepartment + `</td>
                                                <td>` + serviceName + `</td>
                                            </tr>`;
                                        }
                                    @endforeach
                            });
                            $('#tbody_datatable').html(htmlView);
                        }
                    },
                    error: function(data) {

                    }
                });
            });

            $('#serviceName').change(function() {
                var serviceName = $('#serviceName').val();

                $.ajax({
                    data: {
                        serviceName: serviceName,
                    },
                    url: "{{ route('admin.search.search') }}",
                    type: "GET",
                    dataType: 'json',
                    success: function(data, textStatus, XmlHttpRequest) {
                        if (XmlHttpRequest.status === 200) {
                            let htmlView = '';
                            if (data.length <= 0) {
                                htmlView += ``;
                            }
                            var serviceName = '';
                            var serviceDepartment = '';
                            $.each(data, function(i, value) {
                                    @foreach ($serviceAssigns as $serviceAssign)
                                        if(value.serviceDepartment == '{{ $serviceAssign->id }}'){
                                            serviceName = '{{ $serviceAssign->serviceName }}';
                                        }
                                    @endforeach
                                    @foreach ($departments as $department)
                                        if(value.serviceDepartment == '{{ $department->id }}'){
                                            serviceDepartment = '{{ $department->departmentName }}';
                                        }
                                    @endforeach
                                    @foreach ($users as $user)
                                        if (value.serviceProvider == "{{ $user->username }}") {
                                            htmlView += `
                                            <tr width="10">
                                                <td>` + value.serviceDateTime + `</td>
                                                <td>` + '{{ $user->name }}' + `</td>
                                                <td>` + serviceDepartment + `</td>
                                                <td>` + serviceName + `</td>
                                            </tr>`;
                                        }
                                    @endforeach
                            });
                            $('#tbody_datatable').html(htmlView);
                        }
                    },
                    error: function(data) {

                    }
                });
            });
        });
    </script>
@endsection

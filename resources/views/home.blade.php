@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="container-fluid">

                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                {{-- <h1 class="m-0">แดชบอร์ด</h1> --}}
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">แดชบอร์ด</a></li>
                                    <li class="breadcrumb-item active">ปฏิทินปฏิบัติงาน</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
                @if (Auth::user()->roles->contains('title', 'Admin') == true || Auth::user()->roles->contains('title', 'Executive') == true)
                <div class="card card-row card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fad fa-users"></i> ผู้ให้บริการ
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">เลือกปี</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-chevron-square-down"></i></span>
                                        </div>
                                        <select name="selectYear" id="selectYear" class="form-control select2"
                                            required>
                                            <option value="">เลือก</option>
                                            @foreach ($serviceRequestYears as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">เลือกเดือน</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-chevron-square-down"></i></span>
                                        </div>
                                        <select name="selectMonth" id="selectMonth" class="form-control select2" required>
                                            <option value="">เลือก</option>
                                            @foreach ($monthObjects as $monthObject)
                                                <option value="{{ $monthObject->valMouths }}">{{ $monthObject->month }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <div class="form-group">
                                    <label class="col-sm-12 control-label">เลือกผู้ให้บริการ</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fad fa-chevron-square-down"></i></span>
                                        </div>
                                        <select name="selectServiceProvider" id="selectServiceProvider" class="form-control select2"
                                            required>
                                            <option value="">เลือก</option>
                                            @foreach ($userStaffs as $userStaff)
                                                <option value="{{ $userStaff->username }}">
                                                    {{ $userStaff->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="card card-row card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fad fa-file-chart-pie"></i> สรุปจำนวนคำขอบริการ
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $allServiceRequest }}</h3>
                                        <p>งานรอดำเนินการทั้งหมด</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-ios-list"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $todayServiceRequest }}</h3>
                                        <p>งานประจำวันนี้</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-ios-time"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $comingServiceRequest }}</h3>
                                        <p>งานที่กำลังจะมาถึง</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-ios-calendar"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- ./col -->
                            <div class="col-lg-3 col-6">
                                <!-- small box -->
                                <div class="small-box bg-purple">
                                    <div class="inner">
                                        <h3>{{ $countUsers }}</h3>
                                        <p>ผู้ใช้งานทั้งหมด</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fad fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- ./col -->
                        </div>
                    </div>
                </div>

                <div class="card card-row card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fad fa-file-chart-pie"></i> ปฏิทินคำขอใช้บริการ
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="container-fluid">
                            <section class="content">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card card-primary">
                                                <div class="card-body p-0">
                                                    <div id="external-events"></div>
                                                    <div id="calendar"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalServiceRequestForm" aria-hidden="true" data-keyboard="false" data-backdrop="static">
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
                <form method="POST" class="needs-validation" id="schedule_form" action="{{ route('admin.service_requests.save') }}"
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
                                                <input type="text" name="serviceRequestNumber" id="serviceRequestNumber" class="form-control" value="" readonly
                                                    required placeholder="" />
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
                                                <textarea type="textarea" name="serviceDescription" id="serviceDescription" class="form-control" required readonly
                                                    placeholder=""></textarea>
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
                                                <select name="serviceName" id="serviceName" class="form-control select2" required disabled>
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
                                                <select name="serviceDepartment" id="serviceDepartment" class="form-control select2" onchange="selectdepartments(this.value, this)" required disabled>
                                                    <option value="">เลือก</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}">
                                                            {{ $department->departmentName }}</option>
                                                    @endforeach
                                                    <option value="etc">อื่นๆ</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="departmentEtc" hidden>
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">ชื่อหน่วยงาน</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-warning"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <input type="text" name="departmentName" id="departmentName" class="form-control form-control is-warning" required placeholder="" readonly />
                                                <input type="hidden" name="departmentId" id="departmentId" class="form-control" required placeholder="" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">ประเภทหน่วยงาน</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-warning"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <select name="departmentType" id="departmentType" class="form-control form-control is-warning" required disabled>
                                                    <option value="">เลือก</option>
                                                    <option value="ภายในคณะวิทยาศาสตร์">ภายในคณะวิทยาศาสตร์</option>
                                                    <option value="ภายนอกคณะวิทยาศาสตร์">ภายนอกคณะวิทยาศาสตร์</option>
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
                                                <select name="servicePriority" id="servicePriority" class="form-control select2"
                                                    required disabled>
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
                                            <label class="col-sm-12 control-label">ผู้ใช้บริการ</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <input type="text" value="{{ Auth()->user()->name }}"
                                                    class="form-control" required placeholder="" readonly/>
                                                <input type="hidden" name="serviceRecipient" id="serviceRecipient" value="{{ Auth()->user()->username }}" class="form-control" required placeholder="" />
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
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">วันที่ต้องการ</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fad fa-keyboard"></i></span>
                                                </div>
                                                <input type="text" class="form-control" name="serviceDueDate"
                                                    id="serviceDueDate" readonly>
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
    <!-- Page specific script -->
    <script>
        // $(document).ready(function() {
        //     $("#serviceName").select2({ width: '90%' });
        //     $("#serviceDepartment").select2({ width: '90%' });
        //     $("#servicePriority").select2({ width: '90%' });
        //     $("#selectServiceProvider").select2({ width: '90%' });
        // });

        var events = []
        const baseDownloadUrl = "{{ route('admin.service_requests.download') }}";
        $(function() {

            /* initialize the external events
             -----------------------------------------------------------------*/
            function ini_events(ele) {
                ele.each(function() {

                    // create an Event Object (https://fullcalendar.io/docs/event-object)
                    // it doesn't need to have a start or end
                    var eventObject = {
                        title: $.trim($(this).text()) // use the element's text as the event title
                    }

                    // store the Event Object in the DOM element so we can get to it later
                    $(this).data('eventObject', eventObject)

                    // make the event draggable using jQuery UI
                    $(this).draggable({
                        zIndex: 1070,
                        revert: true, // will cause the event to go back to its
                        revertDuration: 0 //  original position after the drag
                    })

                })
            }

            ini_events($('#external-events div.external-event'))

            /* initialize the calendar
             -----------------------------------------------------------------*/
            //Date for the calendar events (dummy data)
            var date = new Date()
            var d = date.getDate(),
                m = date.getMonth(),
                y = date.getFullYear()

            var Calendar = FullCalendar.Calendar;
            var Draggable = FullCalendar.Draggable;

            var containerEl = document.getElementById('external-events');
            var checkbox = document.getElementById('drop-remove');
            var calendarEl = document.getElementById('calendar');

            // initialize the external events
            // -----------------------------------------------------------------

            new Draggable(containerEl, {
                itemSelector: '.external-event',
                eventData: function(eventEl) {
                    return {
                        title: eventEl.innerText,
                        backgroundColor: window.getComputedStyle(eventEl, null).getPropertyValue(
                            'background-color'),
                        borderColor: window.getComputedStyle(eventEl, null).getPropertyValue(
                            'background-color'),
                        textColor: window.getComputedStyle(eventEl, null).getPropertyValue('color'),
                    };
                }
            });

            function formatDate(date) { //แปลงวันที่
                var d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                return [year, month, day].join('-');
            }

            // console.log(formatDate('2023-06-23 16:00:00'));

            let today = new Date().toISOString().slice(0, 10) //วันที่ปัจจุบัน
            // console.log(today)
            let Color;
            @if ($serviceRequests != null)
                @foreach ($serviceRequests as $serviceRequest)
                $('#serviceDueDate').val('{{ $serviceRequest->serviceDateTime }}');
                if (formatDate("{{ $serviceRequest->serviceDateTime }}") < today) {
                    Color = '#343a40';
                } else {
                    // Color = '#28a745';
                    @if ($serviceRequest->servicePriority == 'Normal')
                        Color = '#28a745';
                    @endif
                    @if ($serviceRequest->servicePriority == 'Urgent')
                        Color = '#ffc107';
                    @endif
                    @if ($serviceRequest->servicePriority == 'VeryUrgent')
                        Color = '#fd7e14';
                    @endif
                    @if ($serviceRequest->servicePriority == 'MostUrgent')
                        Color = '#dc3545';
                    @endif
                }
                var titleName = '';
                @foreach ($service_assigns as $service_assign)
                    @if ($serviceRequest->serviceName == $service_assign->id)
                        titleName = '{{ $service_assign->serviceName }}';
                    @endif
                @endforeach

                // @if ($serviceRequest->servicePriority == 'Normal')
                //     // Color = '#28a745';
                // @endif

                // // wittaya.kh
                // @if ($serviceRequest->serviceRecipient == 'wittaya.kh')
                //     if (formatDate("{{ $serviceRequest->serviceDateTime }}") < today) {
                //         Color = '#CB4335';
                //     } else {
                //         Color = '#27ae60';
                //     }
                // @endif
                // // sawalee.l
                // @if ($serviceRequest->serviceRecipient == 'sawalee.l')
                //     if (formatDate("{{ $serviceRequest->serviceDateTime }}") < today) {
                //         Color = '#CB4335';
                //     } else {
                //         Color = '#f1c40f';
                //     }
                // @endif
                // // nuengruethai.i
                // @if ($serviceRequest->serviceRecipient == 'nuengruethai.i')
                //     if (formatDate("{{ $serviceRequest->serviceDateTime }}") < today) {
                //         Color = '#CB4335';
                //     } else {
                //         Color = '#f1548d';
                //     }
                // @endif
                calendar.getEvents().forEach(event => event.remove());
                var event_item = {
                    id: "{{ $serviceRequest->id }}",
                    title: titleName,
                    start: "{{ $serviceRequest->serviceDateTime }}",
                    end: "{{ $serviceRequest->serviceDateTime }}",
                    backgroundColor: Color,
                    borderColor: Color,
                    allDay: true,
                }
                events.push(event_item)
                @endforeach
            @endif

            var calendar = new Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                themeSystem: 'bootstrap',
                //Random default events
                // events: [
                //   {
                //     title          : 'All Day Event',
                //     start          : new Date(y, m, 1),
                //     backgroundColor: '#28a745', //red
                //     borderColor    : '#28a745', //red
                //     allDay         : true
                //   },
                // ],
                events: events,
                editable: true,
                droppable: false, // this allows things to be dropped onto the calendar !!!
                locale: 'th',
                drop: function(info) {
                    // is the "remove after drop" checkbox checked?
                    if (checkbox.checked) {
                        // if so, remove the element from the "Draggable Events" list
                        info.draggedEl.parentNode.removeChild(info.draggedEl);
                    }
                },
                aspectRatio: 2,
                showNonCurrentDates: false, // แสดงที่ของเดือนอื่นหรือไม่
                displayEventTime: false, //ซ่อน Start-Time
                height: 'auto',
                eventClick: function(info) {
                    var id = info.event.id
                    $.ajax({
                        type: "GET",
                        url: "{{ route('admin.service_requests.find') }}/" + id,
                        data: {},
                        dataType: 'json',
                        beforeSend: function() {},
                        success: function(response) {
                            // console.log(response);
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
                            $('#modalServiceRequestForm').modal('show');
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                },
            });

            // ...existing code...
            $('#selectServiceProvider').change(function() {
                var selectYear = $('#selectYear').val();
                var selectMonth = $('#selectMonth').val();
                var selectServiceProvider = $('#selectServiceProvider').val();

                $.ajax({
                    data: {
                        selectYear: selectYear,
                        selectMonth: selectMonth,
                        selectServiceProvider: selectServiceProvider,
                    },
                    url: "{{ route('admin.search.serviceProviderSearch') }}",
                    type: "GET",
                    dataType: 'json',
                    success: function(response, textStatus, XmlHttpRequest) {
                        if (XmlHttpRequest.status === 200) {
                            let Color = '#28a745';
                            let serviceRequest = response.data;

                            // Remove all events from the calendar
                            calendar.getEvents().forEach(event => event.remove());

                            // Add new events
                            serviceRequest.forEach((value) => {
                                var event_item = {
                                    id: value.id, // Use a unique ID from your data
                                    title: value.serviceName, // Use a proper title
                                    start: value.serviceDateTime, // Use the correct date field
                                    end: value.serviceDateTime,     // Use the correct date field
                                    backgroundColor: Color,
                                    borderColor: Color,
                                    allDay: true,
                                }
                                calendar.addEvent(event_item);
                            });
                        }
                    },
                    error: function(data) {
                        // handle error
                    }
                });
            });
            // ...existing code...

            // $('#selectServiceProvider').change(function() {
            //     var selectServiceProvider = $('#selectServiceProvider').val();

            //     $.ajax({
            //         data: {
            //             selectServiceProvider: selectServiceProvider,
            //         },
            //         url: "{{ route('admin.search.serviceProviderSearch') }}",
            //         type: "GET",
            //         dataType: 'json',
            //         success: function(response, textStatus, XmlHttpRequest) {
            //             if (XmlHttpRequest.status === 200) {
            //                 // console.log(data);
            //                 let Color = '#343a40';
            //                 let serviceRequestNote = response.data;
            //                 serviceRequestNote.forEach((value) => {
            //                     var event_item = {
            //                         id: value.serviceProvider,
            //                         title: value.serviceProvider,
            //                         start: value.serviceProvider,
            //                         end: value.serviceProvider,
            //                         backgroundColor: Color,
            //                         borderColor: Color,
            //                         allDay: true,
            //                     }
            //                     events.push(event_item)
            //                 });
            //             }
            //         },
            //         error: function(data) {

            //         }
            //     });
            // });

            //รีเฟรชหน้าเพื่อรับลิงค์ไฟล์เอกสารใหม่
            $('#ajaxModel').on('hidden.bs.modal', function() {
                location.reload();
            });

            // เมื่อฟอร์มการเรียกใช้ evnet submit ข้อมูล
            $("#calendar_Form").on("submit", function(e) {
                e.preventDefault(); // ปิดการใช้งาน submit ปกติ เพื่อใช้งานผ่าน ajax

                // เตรียมข้อมูล form สำหรับส่งด้วย  FormData Object
                var formData = new FormData($(this)[0]);

                // ส่งค่าแบบ POST ไปยังไฟล์ show_data.php รูปแบบ ajax แบบเต็ม
            });

            calendar.render();
            // $('#calendar').fullCalendar()
        })
    </script>
@endsection

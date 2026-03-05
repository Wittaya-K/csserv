
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
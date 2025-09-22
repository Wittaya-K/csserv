@extends('layouts.admin')
@section('content')
    <style>
        /* ถ้าคุณวาง chart ไว้ใน div ขวาแล้วอยากให้ legend อยู่ซ้ายจัด layout ด้วย flex */
        #chartContainer {
            display: flex;
            align-items: flex-start;
        }
    </style>
    <div class="content">
        <div class="row">
            <div class="container-fluid">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                {{-- <h1 class="m-0">รายงานสรุป</h1> --}}
                            </div><!-- /.col -->
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="#">รายงานสรุป</a></li>
                                    <li class="breadcrumb-item active">รายงานสรุป</li>
                                </ol>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </div>
                @if (
                        Auth::user()->roles->contains('title', 'Admin') == true ||
                        Auth::user()->roles->contains('title', 'Executive') == true
                )
                    <div class="card card-row card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fad fa-list"></i> การค้นหา
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="search_form" name="search_form">
                                <!-- Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">เลือกปี</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fad fa-chevron-square-down"></i></span>
                                                </div>
                                                <select class="form-control select2" name="selectYear" id="selectYear">
                                                    <option>เลือก</option>
                                                    @foreach ($serviceRequestYears as $year)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">เลือกเดือน</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fad fa-chevron-square-down"></i></span>
                                                </div>
                                                <select class="form-control select2" name="selectMonth" id="selectMonth">
                                                    <option>เลือก</option>
                                                    @foreach ($monthObjects as $monthObject)
                                                        <option value="{{ $monthObject->valMouths }}">{{ $monthObject->month }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">เลือกผู้ให้บริการ</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i
                                                            class="fad fa-chevron-square-down"></i></span>
                                                </div>
                                                <select class="form-control select2" name="selectServiceProvider"
                                                    id="selectServiceProvider">
                                                    <option>เลือก</option>
                                                    @foreach ($staffUsers as $staffUser)
                                                        <option value="{{ $staffUser->username }}">{{ $staffUser->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">&nbsp;</label>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    {{-- <span class="input-group-text"><i
                                                            class="fad fa-chevron-square-down"></i></span> --}}
                                                </div>
                                                {{-- <input type="text" class="form-control" placeholder="ค้นหา..."> --}}
                                                <button type="submit" class="btn btn-primary" id="btnSearch"
                                                    value="btnSearch"><i class="fad fa-search"></i> ค้นหา</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="card card-row  card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fad fa-file-chart-pie"></i> สรุปผลการดำเนินการคำขอ
                        </h3>
                    </div>
                    <div class="card-body">

                        <!-- Summary Cards -->
                        <div class="row">
                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3 id="pending">0</h3>
                                        <p>รอดำเนินการ</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3 id="inprogress">0</h3>
                                        <p>กำลังดำเนินการ</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-spinner"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3 id="completed">0</h3>
                                        <p>ดำเนินการเสร็จสิ้น</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3 id="completed_and_forwarded">0</h3>
                                        <p>ยกเลิก</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-times"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fad fa-desktop"></i>
                                            คำขอตามประเภทงานเทคนิคและสนับสนุนด้านเทคโนโลยีสารสนเทศ</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="typeDistributionIt"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fad fa-user-headset"></i>
                                            คำขอตามประเภทงานสนับสนุนทางวิชาการและการบริหารจัดการ</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="typeDistributionCoordinate"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top 5 Requests -->
                        <div class="card" hidden>
                            <div class="card-header">
                                <h3 class="card-title">🔥 Top 5 ปัญหาที่ถูกร้องขอบ่อย</h3>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>ขอเปลี่ยนหมึกพิมพ์ (15 ครั้ง)</li>
                                    <li>ตั้งค่า Wi-Fi (12 ครั้ง)</li>
                                    <li>ซ่อมเก้าอี้ (10 ครั้ง)</li>
                                    <li>ขอเปิดสิทธิ์ใช้งานระบบ (9 ครั้ง)</li>
                                    <li>ตั้งค่าอีเมลใหม่ (7 ครั้ง)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Requests by Department and Staff -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fad fa-users"></i> คำขอตามหน่วยงาน</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="departmentChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card" hidden>
                                    <div class="card-header">
                                        <h3 class="card-title">👨‍🔧 ผู้รับผิดชอบที่มีงานมากที่สุด</h3>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="staffChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Requests Table -->
                        <div class="card" hidden>
                            <div class="card-header">
                                <h3 class="card-title">📋 คำขอล่าสุด</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>วันที่</th>
                                            <th>ประเภท</th>
                                            <th>ผู้ร้องขอ</th>
                                            <th>สถานะ</th>
                                            <th>ผู้รับผิดชอบ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>01/08</td>
                                            <td>ตั้งค่าอีเมล</td>
                                            <td>นาย ก</td>
                                            <td><span class="badge bg-warning">In Progress</span></td>
                                            <td>Staff A</td>
                                        </tr>
                                        <tr>
                                            <td>31/07</td>
                                            <td>เปลี่ยนหมึก</td>
                                            <td>น.ส. ข</td>
                                            <td><span class="badge bg-success">Completed</span></td>
                                            <td>Staff B</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <!-- Page specific script -->
    <script>
        $(document).ready(function () {
            $(function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const fullLabelsIt = [
                    'การดูแลและติดตั้งเครื่องคอมพิวเตอร์ส่วนบุคคลและอุปกรณ์ที่เกี่ยวข้อง',
                    'การดูแลและติดตั้งระบบเซิร์ฟเวอร์',
                    'การดูแลห้องปฏิบัติการสาขาวิทยาศาสตร์การคำนวณ',
                    'การให้บริการวิชาการ',
                    'งานพัฒนาระบบสารสนเทศและเครือข่าย',
                    'ปฏิบัติงานอื่น ๆ ตามที่ได้รับมอบหมาย'
                ];

                // labels แบบย่อ
                const shortLabelsIt = [
                    'ติดตั้ง PC',
                    'ติดตั้งเซิร์ฟเวอร์',
                    'ดูแลห้อง Lab',
                    'บริการวิชาการ',
                    'พัฒนาระบบ',
                    'อื่น ๆ'
                ];

                const pieChartIt = new Chart(document.getElementById('typeDistributionIt'), {
                    type: 'pie',
                    data: {
                        labels: shortLabelsIt,
                        datasets: [{
                            data: [0, 0, 0, 0, 0, 0],
                            backgroundColor: [
                                '#FFA2A2', '#FFB86A', '#FFD230', '#FFDF20',
                                '#BBF451', '#7BF1A8'
                            ]
                        }]
                    },
                    options: {
                        legend: {
                            display: true,
                            position: 'right', // หรือ 'bottom'
                            labels: {
                                // ใช้ label แบบสั้นใน legend
                                generateLabels: function (chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        return data.labels.map(function (label, i) {
                                            const meta = chart.getDatasetMeta(0);
                                            const ds = data.datasets[0];
                                            const arc = meta.data[i];
                                            const custom = arc && arc.custom || {};
                                            const valueAtIndexOrDefault = Chart.helpers
                                                .valueAtIndexOrDefault;
                                            const arcOpts = chart.options.elements
                                                .arc || {};
                                            const fill = custom.backgroundColor ? custom
                                                .backgroundColor :
                                                valueAtIndexOrDefault(ds
                                                    .backgroundColor, i, arcOpts
                                                    .backgroundColor);
                                            const stroke = custom.borderColor ? custom
                                                .borderColor :
                                                valueAtIndexOrDefault(ds.borderColor, i,
                                                    arcOpts.borderColor);
                                            const bw = custom.borderWidth ? custom
                                                .borderWidth :
                                                valueAtIndexOrDefault(ds.borderWidth, i,
                                                    arcOpts.borderWidth);

                                            return {
                                                text: '\u200E' + label,
                                                fillStyle: fill,
                                                strokeStyle: stroke,
                                                lineWidth: bw,
                                                hidden: isNaN(ds.data[i]) || meta.data[
                                                    i].hidden,
                                                index: i
                                            };
                                        });
                                    } else {
                                        return [];
                                    }
                                }
                            }
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    let index = tooltipItem.index;
                                    let dataset = data.datasets[tooltipItem.datasetIndex];
                                    let value = dataset.data[index];
                                    return fullLabelsIt[index] + ' : ' + value;
                                }
                            }
                        }
                    }
                });

                const fullLabels = [
                    'ประสานงานด้านการจัดการเรียนการสอนและพัฒนาหลักสูตร',
                    'ประสานงานด้านการประกันคุณภาพหลักสูตร',
                    'ประสานงานด้านการสนับสนุนการวิจัย',
                    'ประสานงานด้านการสนับสนุนบริการวิชาการ',
                    'ประสานงานด้านกายภาพและสิ่งสนับสนุนการเรียนรู้',
                    'ประสานงานด้านการจัดกิจกรรม/โครงการของสาขา',
                    'เลขานุการในที่ประชุม',
                    'งานด้านสารบรรณโดยรวมของสาขา',
                    'งานด้านการเงินและพัสดุ',
                    'งานด้านบริหารทรัพยากรมนุษย์',
                    'ประสานงานด้านงานพัฒนานักศึกษาและศิษย์เก่าสัมพันธ์',
                    'งานอื่นๆ ตามที่ได้รับมอบหมาย',
                    'คณะกรรมการงานสัปดาห์วิทย์และคณะกรรมการดำเนินงานชุดต่าง ๆ'
                ];

                // labels แบบย่อ
                const shortLabels = [
                    'จัดการเรียนฯ', 'ประกันฯ', 'สนับสนุนวิจัย', 'บริการวิชาการ', 'กายภาพ/สิ่งแวดล้อม',
                    'กิจกรรมสาขา', 'เลขานุการ', 'สารบรรณ', 'การเงิน', 'ทรัพยากรบุคคล',
                    'พัฒนานักศึกษา', 'อื่นๆ ตามที่ได้รับมอบหมาย', 'คณะกรรมการสัปดาห์วิทย์'
                ];

                const pieChartCoordinate = new Chart(document.getElementById(
                    'typeDistributionCoordinate'), {
                    type: 'pie',
                    data: {
                        labels: shortLabels,
                        datasets: [{
                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: [
                                '#FF6467', '#FF8904', '#FFB93B', '#FDC745', '#9AE630',
                                '#05DF72',
                                '#31D492', '#38D5BE', '#42D3F2', '#21BCFF', '#51A2FF',
                                '#7C86FF', '#A684FF'
                            ]
                        }]
                    },
                    options: {
                        legend: {
                            display: true,
                            position: 'right', // หรือ 'bottom'
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    let index = tooltipItem.index;
                                    let dataset = data.datasets[tooltipItem.datasetIndex];
                                    let value = dataset.data[index];
                                    return fullLabels[index] + ' : ' + value;
                                }
                            }
                        }
                    }
                });

                const departmentChart = new Chart(document.getElementById('departmentChart'), {
                    type: 'bar',
                    data: {
                        labels: ['คณิตศาสตร์', 'สถิติ', 'วิทยาการคอมพิวเตอร์',
                            'เทคโนโลยีสารสนเทศและการสื่อสาร'
                        ],
                        datasets: [{
                            label: 'คำขอ',
                            data: [0, 0, 0, 0],
                            backgroundColor: [
                                '#FF6384', // คณิตศาสตร์
                                '#36A2EB', // สถิติ
                                '#FFCE56', // วิทยาการคอมพิวเตอร์
                                '#4BC0C0', // เทคโนโลยีสารสนเทศและการสื่อสาร
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    min: 0,
                                    max: 5, // ปรับตามช่วงค่าของคุณ
                                    stepSize: 1 // ให้ค่าดูเข้าใจง่าย
                                }
                            }]
                        }
                    }
                });

                const staffChart = new Chart(document.getElementById('staffChart'), {
                    type: 'bar',
                    data: {
                        labels: ['Staff A', 'Staff B', 'Staff C'],
                        datasets: [{
                            label: 'จำนวนงาน',
                            data: [20, 15, 10],
                            backgroundColor: '#6f42c1'
                        }]
                    }
                });

                $('#btnSearch').click(function (e) {
                    e.preventDefault();

                    $.ajax({
                        data: $('#search_form').serialize(),
                        url: "{{ route('admin.report_summary.search') }}",
                        type: "POST",
                        dataType: 'json',
                        success: function (data) {
                            // console.log('Success:', data);

                            var countServiceStatus = data.countServiceStatus;
                            var countDepartmentName = data.countDepartmentName;
                            var countItServiceAssign = data.countItServiceAssign;
                            var countCoordinateServiceAssign = data.countCoordinateServiceAssign;

                            $('#pending').text(countServiceStatus.pending);
                            $('#inprogress').text(countServiceStatus.inprogress);
                            $('#completed').text(countServiceStatus.completed);
                            $('#completed_and_forwarded').text(countServiceStatus.completed_and_forwarded);

                            const itValues = Object.values(countItServiceAssign);
                            const coordinateValues = Object.values(countCoordinateServiceAssign);
                            const departmentValues = Object.values(countDepartmentName);

                            pieChartIt.data.datasets[0].data = itValues;
                            pieChartIt.update();

                            pieChartCoordinate.data.datasets[0].data = coordinateValues;
                            pieChartCoordinate.update();

                            departmentChart.data.datasets[0].data = departmentValues;
                            const maxDept = Math.max.apply(null, departmentValues);
                            departmentChart.options.scales.yAxes[0].ticks.max = Math.max(5, maxDept);
                            departmentChart.update();
                        },
                        error: function (data) {
                            // console.log('Error:', data);
                        }
                    });
                });
            })
        });
    </script>
@endsection
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans('global.system') }}</title>
    <link rel="stylesheet" href="{{ asset('dist/css/select2.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('dist/css/jquery.dataTables.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('dist/css/dataTables.bootstrap4.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('dist/css/select.dataTables.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('dist/css/buttons.dataTables.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap-datetimepicker.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/MultipleFileUpload.css') }}"/>
    <link rel="stylesheet" href="{{ asset('font-awesome-pro-5.15.4/css/all.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.min.css') }}">
    <!-- fullCalendar -->
    <link rel="stylesheet" href="{{ asset('plugins/fullcalendar/main.css') }}">
    <!-- datetimepicker -->
    <link rel="stylesheet" href="{{ asset('plugins/datetimepicker/jquery.datetimepicker.min.css') }}">
    <style>
        @font-face {
            font-family: PSU-Stidti-Regular;
            src: url(/font/PSU-Stidti-Regular.otf);
        }

        * {
            font-family: PSU-Stidti-Regular;
        }
    </style>
    @yield('styles')
</head>

{{-- <body class="sidebar-mini control-sidebar-slide-open sidebar-collapse" style="height: auto;"> --}}
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#"><strong> ผู้ใช้งาน: </strong> {{ Auth::user()->name }}</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user-cog"></i></a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" aria-labelledby="navbarDropdown"
                        style="max-width: 440px;">
                        <span class="dropdown-item dropdown-header"></span>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item">
                            <i class="fad fa-envelope"></i> เมล์: {{ Auth::user()->email }}
                        </a>
                        <div class="dropdown-divider"></div>
                        {{-- <a href="#" class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                            <i class="fad fa-sign-out"></i> ออกจากระบบ
                        </a> --}}
                        <a href="{{ url('/logout-azure') }}" class="dropdown-item">
                            <i class="fad fa-sign-out"></i> ออกจากระบบ
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        @include('partials.menu')
        <div class="content-wrapper" style="min-height: 917px;">
            <section class="content" style="padding-top: 20px">
                @yield('content')
            </section>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> {{ version()->get() }}
            </div>
            <strong> &copy;</strong>2024 พัฒนาโดย สาขาวิทยาศาสตร์การคำนวณ คณะวิทยาศาสตร์
        </footer>
        {{-- <aside class="control-sidebar control-sidebar-dark">
        </aside> --}}
        {{-- <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form> --}}
    </div>
    
    <script src="{{ asset('dist/js/jquery.min.js') }}"></script>
    <script src="{{ asset('dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('dist/js/jquery.dataTables.min.js') }}"></script>    
    <script src="{{ asset('dist/js/dataTables.bootstrap4.min.js') }}"></script>    
    <script src="{{ asset('dist/js/dataTables.buttons.min.js') }}"></script>    
    <script src="{{ asset('dist/js/dataTables.select.min.js') }}"></script>    
    <script src="{{ asset('dist/js/ckeditor.js') }}"></script>    
    <script src="{{ asset('dist/js/moment.min.js') }}"></script>
    <script src="{{ asset('dist/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="{{ asset('js/MultipleFileUpload.js') }}"></script>
    <script src="{{ asset('font-awesome-pro-5.15.4/js/pro.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('plugins/sparklines/sparkline.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('plugins/jqvmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <!-- jQuery UI -->
    <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- fullCalendar 2.2.5 -->
    <script src="{{ asset('plugins/fullcalendar/main.js') }}"></script>
    <script src="{{ asset('plugins/fullcalendar/locales/th.js') }}"></script>
    <!-- datetimepicker -->
    <script src="{{ asset('plugins/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
    <script>
        $(function() {
            $.fn.dataTable.ext.classes.sPageButton = '';
        });
    </script>
    @yield('scripts')
</body>

</html>

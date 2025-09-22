<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ trans('global.system') }}</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/css/adminltev3.css" rel="stylesheet" />
    <link href="{{ asset('font-awesome-pro-5.15.4/css/all.min.css') }}" rel="stylesheet" />
    <style> 
        @font-face {
           font-family: PSU-Stidti-Regular;
           src: url(/font/PSU-Stidti-Regular.otf);
        }
        
        * {
           font-family: PSU-Stidti-Regular;
        }
        </style>
</head>

<body>
    @yield('content')
</body>

</html>
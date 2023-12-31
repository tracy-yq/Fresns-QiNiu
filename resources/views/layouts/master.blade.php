<!doctype html>
<html lang="{{ $langTag }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="author" content="Fresns" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="{{ csrf_token() }}">
        <meta http-equiv="Cache-Control"content="no-cache"/>
        <title>Plugin QiNiu</title>
        <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap-icons.min.css') }}">
        <link rel="stylesheet" href="{{ @asset('/static/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ @asset('/static/css/select2-bootstrap-5-theme.min.css') }}">
        <style>
            .fs-7 {
                font-size: 0.9rem;
            }
        </style>
        @stack('css')
    </head>

    <body>
        @yield('content')

        <div class="fresns-tips"></div>

        <script src="https://cdn.staticfile.org/qiniu-js/3.4.1/qiniu.min.js"></script>
        <script src="//res.wx.qq.com/open/js/jweixin-1.6.0.js"></script>
        <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ @asset('/static/js/jquery.min.js') }}"></script>
        <script src="{{ @asset('/static/js/select2.min.js') }}"></script>
        <script src="{{ @asset('/assets/QiNiu/js/lodash.min.js') }}"></script>
        <script src="{{ @asset('/assets/QiNiu/js/app.js') }}"></script>
        @stack('script')
    </body>
</html>

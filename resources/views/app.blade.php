<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <meta name="viewport" content="width=device-width"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ВЭТС/ВЭССХТ</title>
{{--        <link rel="stylesheet"  href="{{ asset('build/assets/app-2990c851.css?v=1.3') }}" />--}}
</head>
<body>
<div id="app"></div>
@vite('resources/js/app.js')
{{--    <script type="module" src="{{ asset('build/assets/app-de3532b3.js?v=1.3') }}"></script>--}}
</body>
</html>

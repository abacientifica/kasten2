<!DOCTYPE html>
<html>
@include('sections.head')
<body class="hold-transition sidebar-mini">
    <div class="wrapper" id="app">
        <App ruta="{{route('basepath')}}"></App>
    </div>
</body>
@include('sections.script')
</html>
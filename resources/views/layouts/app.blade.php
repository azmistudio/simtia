<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'SIMTIA') }} {{ strtoupper(Session::get('institute')) }}</title>
    <!-- Scripts -->
    <script src="{{ asset('lib/jquery-easyui/jquery.min.js') }}"></script>
    <script src="{{ asset('lib/jquery-easyui/jquery.easyui.min.js') }}"></script>
    <script src="{{ asset('lib/jquery-easyui/locale/easyui-lang-id.js') }}"></script>
    <script src="{{ asset('lib/easyui-texteditor/jquery.texteditor.js') }}"></script>
    <script src="{{ asset('lib/waitMe/waitMe.min.js') }}"></script>
    <script src="{{ asset('js/datagrid-groupview.js') }}"></script>
    <script src="{{ asset('js/datagrid-cellediting.js') }}"></script>
    <script src="{{ asset('js/datagrid-filter.js') }}"></script>
    <script src="{{ asset('js/datagrid-detailview.js') }}"></script>
    <script src="{{ asset('js/jquery.edatagrid.js') }}"></script>
    <script src="{{ asset('js/jquery.form.js') }}"></script>
    <script src="{{ asset('js/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/highcharts.src.js') }}"></script>
    <script src="{{ asset('js/exporting.js') }}"></script>
    <script src="{{ asset('lib/js-year-calendar/js-year-calendar.min.js') }}"></script>
    <script src="{{ asset('lib/fullcalendar/lib/main.min.js') }}"></script>
    <script src="{{ asset('lib/fullcalendar/lib/locales/id.js') }}"></script>
    <script src="{{ asset('lib/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('lib/chartjs/package/dist/chart.min.js') }}"></script>
    <script src="{{ asset('js/pace.min.js') }}"></script>
    <script src="{{ asset('js/site.js') }}"></script>
    <script type="text/javascript">const Toast = Swal.mixin({ toast: true, position: 'top', showConfirmButton: false, timer: 1000 })</script>
    <!-- Styles -->
    <link href="{{ asset('lib/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/jquery-easyui/themes/metro/easyui.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/jquery-easyui/themes/icon.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/easyui-texteditor/texteditor.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/waitMe/waitMe.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/fullcalendar/lib/main.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/dropzone/basic.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/dropzone/dropzone.css') }}" rel="stylesheet">
    <link href="{{ asset('css/minimal.css') }}" rel="stylesheet">

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/simtia16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/simtia32.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="fade-out">
    <div class="">
        <main role="main" class="pb-3">
            @yield('content')
        </main>
    </div>
    <div id="error-w" class="easyui-window p-2" title="Terjadi Galat" style="width:530px;height:323px" data-options="iconCls:'',modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false">
        <div class="messager-icon messager-error"></div>
        <div style="line-height: 32px;"><b><span id="error-code"></span></b></div>
        <br/>
        <div class="mb-1">
            <input id="error-remark" class="easyui-textbox" style="width:500px;height:22px;" data-options="label:'Diagnosa',labelWidth:'125px'" />
        </div>
        <div class="mb-1">
            <input id="error-message" class="easyui-textbox" style="width:500px;height:50px;" data-options="label:'Pesan Galat',labelWidth:'125px',multiline:true" />
        </div>
        <div class="mb-2">
            <input id="error-detail" class="easyui-textbox" style="width:500px;height:100px;" data-options="label:'Rincian Galat',labelWidth:'125px',multiline:true" />
        </div>
        <div class="text-center">
            <a class="easyui-linkbutton" data-options="" href="javascript:void(0)" onclick="$('#error-w').window('close')" style="width:70px">Oke</a>
        </div>
    </div>
</body>
</html>
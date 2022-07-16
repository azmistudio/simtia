@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
    $ContentHeight = $InnerHeight - 326 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Kalender Akademik</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Calendar'" onclick="viewAcademicCalendar()">Lihat Kalender</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-academic-calendar" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-academic-calendar" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-academic-calendar" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($depts as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <select id="fcal-academic-calendar" class="easyui-combobox" style="width:285px;height:22px;" tabindex="1" data-options="label:'Kalender:',labelWidth:100,labelPosition:'before',panelHeight:68">
                        <option value="">---</option>
                        @foreach ($calendars as $calendar)
                        <option value="{{ $calendar->id }}">{{ $calendar->description }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterAcademicCalendar({fdept: $('#fdept-academic-calendar').val(),fcal: $('#fcal-academic-calendar').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-academic-calendar').form('reset');filterAcademicCalendar({})">Batal</a>
                </div>
            </form>
            <table id="tb-academic-calendar" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'calendar',width:175,resizeable:true,sortable:false">Kalender</th>
                        <th data-options="field:'activity',width:200,resizeable:true,sortable:false">Kegiatan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-academic-calendar" class="panel-top">
            <a id="newAcademicCalendar" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newAcademicCalendar()">Baru</a>
            <a id="editAcademicCalendar" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editAcademicCalendar()">Ubah</a>
            <a id="saveAcademicCalendar" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveAcademicCalendar()">Simpan</a>
            <a id="clearAcademicCalendar" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearAcademicCalendar()">Batal</a>
            <a id="deleteAcademicCalendar" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteAcademicCalendar()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-academic-calendar"></span>Kegiatan: <span id="title-academic-calendar"></span></h6>
        </div>
        <div id="page-academic-calendar" class="pt-3 pb-3">
            <form id="form-academic-calendar-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-5">
                            <input type="hidden" id="id-academic-calendar" name="id" value="-1" />
                            <div class="mb-1">
                                <input id="AcademicCalDeptId" class="easyui-textbox" style="width:380px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="AcademicCalSchoolYearId" class="easyui-textbox" style="width:380px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="AcademicCalPeriodId" class="easyui-textbox" style="width:380px;height:22px;" data-options="label:'Periode:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="calendar_id" id="AcademicCalId" class="easyui-combobox" style="width:318px;height:22px;" tabindex="10" data-options="label:'<b>*</b>Kalender Akademik:',labelWidth:'150px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'description'">
                                    <option value="">---</option>
                                    @foreach ($calendars as $calendar)
                                    <option value="{{ $calendar->id.'-'.$calendar->department.'-'.$calendar->school_year.'-'.$calendar->period }}">{{ $calendar->description }}</option>
                                    @endforeach
                                </select>
                                <a class="easyui-linkbutton small-btn" onclick="academicCalDialog()" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Add"></i></a>
                                <a class="easyui-linkbutton small-btn" onclick="reloadAcademicCal('AcademicCalId')" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Refresh"></i></a>
                            </div>
                            <div class="mb-1">
                                <input name="activity" id="AcademicCalActivityId" class="easyui-textbox" style="width:380px;height:44px;" data-options="label:'<b>*</b>Kegiatan:',labelWidth:'150px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="start" id="AcademicCalStart" class="easyui-datebox" style="width:260px;height:22px;" tabindex="6" data-options="label:'<b>*</b>Tanggal Mulai:',labelWidth:'150px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <input name="end" id="AcademicCalEnd" class="easyui-datebox" style="width:260px;height:22px;" tabindex="6" data-options="label:'<b>*</b>Tanggal Akhir:',labelWidth:'150px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="easyui-texteditor" id="AcademicCalDescId" title="Deskripsi Kegiatan" style="width:100%;height:{{ $ContentHeight  }};padding:20px" data-options="name:'description',toolbar:['bold','italic','strikethrough','underline','-','justifyleft','justifycenter','justifyright','justifyfull','-','insertorderedlist','insertunorderedlist','outdent','indent']"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- dialog --}}
<div id="academic-cal-w" class="easyui-window" title="Tambah Kalender Akademik" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--List'" style="width:500px;height:491px;padding:10px;">
    <form id="form-academic-calendar" method="post">
        @csrf
        <input type="hidden" name="id" id="academic-cal-id" value="-1" />
        <div class="mb-1">
            <div class="mb-1">
                <input class="easyui-textbox" id="academicsCalPeriodId" style="width:355px;height:22px;" data-options="label:'Periode:',labelWidth:'150px',readonly:true" />
            </div>
            <div class="mb-1">
                <select name="schoolyear_id" id="academicsCalSchoolYearId" class="easyui-combobox" style="width: 260px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'150px',panelHeight:100,valueField:'id',textField:'lesson_name'">
                    <option value="">---</option>
                    @foreach ($schoolyears as $schoolyear)
                    <option value="{{ $schoolyear->id .'-'. $schoolyear->start_date->format('d/m/Y') .'-'. $schoolyear->end_date->format('d/m/Y') }}">{{ $schoolyear->school_year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-1">
                <input name="description" class="easyui-textbox" style="width:100%;height:22px;" data-options="label:'<b>*</b>Kalender Akademik:',labelWidth:'150px'" />
            </div>
            <div class="mb-2">
                <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'150px',labelPosition:'before'" />
            </div>
            <div style="margin-left:150px;">
                <a href="javascript:void(0)" class="easyui-linkbutton small-btn" style="height:22px;" onclick="saveAcademicCal()">Simpan</a>
                <a href="javascript:void(0)" class="easyui-linkbutton small-btn" style="height:22px;" onclick="$('#form-academic-calendar').form('clear')">Batal</a>
            </div>
        </div>
    </form>
    <br/>
    <table id="tb-academic-cal" class="easyui-datagrid" style="width:100%;height:260px" data-options="singleSelect:true,method:'post',rownumbers:'true',toolbar:menubarActionAcademicCalendar,pagination:'true'">
        <thead>
            <tr>
                <th data-options="field:'department',width:100,resizeable:true,sortable:true">Departemen</th>
                <th data-options="field:'description',width:150,resizeable:true,sortable:true">Kalender</th>
                <th data-options="field:'period',width:170,resizeable:true,sortable:true">Periode</th>
            </tr>
        </thead>
    </table>
</div>
<div id="academic-cal-view-w" class="easyui-window" title="Kalendar Akademik" data-options="modal:true,closed:true,minimizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--List'" style="width:100%;height:98%;padding:10px;"></div>
<script type="text/javascript">
    var menuActionAcademicCalendar = document.getElementById("menu-act-academic-calendar").getElementsByTagName("a")
    var titleAcademicCalendar = document.getElementById("title-academic-calendar")
    var markAcademicCalendar = document.getElementById("mark-academic-calendar")
    var idAcademicCalendar = document.getElementById("id-academic-calendar")
    var dgAcademicCalendar = $("#tb-academic-calendar")
    var menubarActionAcademicCalendar = [{
        text: 'Hapus',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            let row = $('#tb-academic-cal').datagrid('getSelected')
            if (row != null) {
                $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kalender terpilih, tetap lanjutkan?", function (r) {
                    if (r) {
                        $.post("{{ url('academic/calendar/destroy') }}" + "/" + row.id, { _token: '{{ csrf_token() }}' }, function(response) {
                            $.messager.alert('Informasi', response.message)
                            $('#tb-academic-cal').datagrid('reload')
                            $("#AcademicCalId").combobox("setValue", "")
                        })
                    }
                })
            }
        }
    }]
    $(function () {
        sessionStorage.formData_Kalender_Akademik = "init"
        dgAcademicCalendar.datagrid({
            url: "{{ url('academic/calendar/activity/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formData_Kalender_Akademik == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleAcademicCalendar.innerText = row.activity
                    actionButtonAcademicCalendar("active",[2,3])
                    $("#form-academic-calendar-main").form("load", "{{ url('academic/calendar/activity/show') }}" + "/" + row.id)
                    $("#page-academic-calendar").waitMe("hide")
                }
            }
        })
        dgAcademicCalendar.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgAcademicCalendar.datagrid('getPager').pagination())
        actionButtonAcademicCalendar("{{ $ViewType }}", [])
        $("#AcademicCalActivityId").textbox("textbox").bind("keyup", function (e) {
            titleAcademicCalendar.innerText = $(this).val()
        })
        $("#academicsCalSchoolYearId").combobox({
            onSelect: function(record) {
                if (record.id != '') {
                    let values = record.id.split('-')
                    $("#academicsCalPeriodId").textbox('setValue', values[1] + " s.d " + values[2])
                } else {
                    $("#academicsCalPeriodId").textbox('setValue', '')
                }
            }
        })
        $("#tb-academic-cal").datagrid({
            url: "{{ url('academic/calendar/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
        })
        $("#AcademicCalId").combobox({
            onSelect: function(record) {
                if (record.id != '') {
                    let values = record.id.split('-')
                    $("#AcademicCalDeptId").textbox('setValue', values[1])
                    $("#AcademicCalSchoolYearId").textbox('setValue', values[2])
                    $("#AcademicCalPeriodId").textbox('setValue', values[3])
                    var periods = record.id.split("-")
                    var years = periods[3].split(" s.d ")
                    $("#AcademicCalStart").datebox().datebox('calendar').calendar({
                        validator: function(date){
                            var now = new Date();
                            let starts = years[0].split("/")
                            let ends = years[1].split("/")
                            var d1 = new Date(starts[2], parseInt(starts[1]) - 1, starts[0])
                            var d2 = new Date(ends[2], parseInt(ends[1]) - 1, ends[0])
                            return d1 <= date && date <= d2;
                        }
                    })
                    $("#AcademicCalEnd").datebox().datebox('calendar').calendar({
                        validator: function(date){
                            var now = new Date();
                            let starts = years[0].split("/")
                            let ends = years[1].split("/")
                            var d1 = new Date(starts[2], parseInt(starts[1]) - 1, starts[0])
                            var d2 = new Date(ends[2], parseInt(ends[1]) - 1, ends[0])
                            return d1 <= date && date <= d2;
                        }
                    })
                } else {
                    $("#AcademicCalDeptId").textbox('setValue', '')
                    $("#AcademicCalSchoolYearId").textbox('setValue', '')
                    $("#AcademicCalPeriodId").textbox('setValue', '')
                }
            }
        })
        $("#form-academic-calendar-main").form({
            onLoadSuccess: function(data) {
                $("#AcademicCalDeptId").textbox('setValue', data.department)
                $("#AcademicCalSchoolYearId").textbox('setValue', data.school_year)
                $("#AcademicCalPeriodId").textbox('setValue', data.period)
                $("#AcademicCalDescId").texteditor('setValue', data.description)
                $("#AcademicCalId").combobox('setValue', data.calendar_id+"-"+data.department+"-"+data.school_year+"-"+data.period) 
                $("#AcademicCalStart").datebox("setValue", data.start)
                $("#AcademicCalEnd").datebox("setValue", data.end)
                titleAcademicCalendar.innerText = data.activity  
            }
        })
        $("#page-academic-calendar").waitMe({effect:"none"})
    })
    function filterAcademicCalendar(params) {
        if (Object.keys(params).length > 0) {
            dgAcademicCalendar.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgAcademicCalendar.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newAcademicCalendar() {
        sessionStorage.formData_Kalender_Akademik = "active"
        $("#form-academic-calendar-main").form("reset")
        actionButtonAcademicCalendar("active", [0,1,4])
        markAcademicCalendar.innerText = "*"
        titleAcademicCalendar.innerText = ""
        idAcademicCalendar.value = "-1"
        $("#AcademicCalDescId").texteditor('setValue','')
        $("#page-academic-calendar").waitMe("hide")
    }
    function editAcademicCalendar() {
        sessionStorage.formData_Kalender_Akademik = "active"
        markAcademicCalendar.innerText = "*"
        actionButtonAcademicCalendar("active", [0,1,4])
    }
    function saveAcademicCalendar() {
        if (sessionStorage.formData_Kalender_Akademik == "active") {
            ajaxAcademicCalendar("academic/calendar/activity/store")
        }
    }
    function deleteAcademicCalendar() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kegiatan terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/calendar/activity/destroy') }}" +"/"+idAcademicCalendar.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAcademicCalendarResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxAcademicCalendar(route) {
        $("#form-academic-calendar-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-academic-calendar").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAcademicCalendarResponse(response)
                $("#page-academic-calendar").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-academic-calendar").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAcademicCalendarResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearAcademicCalendar()
            $("#tb-academic-calendar").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearAcademicCalendar() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearAcademicCalendar()
            }
        })
    }
    function actionButtonAcademicCalendar(viewType, idxArray) {
        for (var i = 0; i < menuActionAcademicCalendar.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionAcademicCalendar[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionAcademicCalendar[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionAcademicCalendar[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionAcademicCalendar[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearAcademicCalendar() {
        sessionStorage.formData_Kalender_Akademik = "init"
        $("#form-academic-calendar-main").form("reset")
        actionButtonAcademicCalendar("init", [])
        titleAcademicCalendar.innerText = ""
        markAcademicCalendar.innerText = ""
        idAcademicCalendar.value = "-1"
        $("#AcademicCalDescId").texteditor('setValue','')
        $("#page-academic-calendar").waitMe({effect:"none"})
    }
    function exportAcademicCalendar(document) {
        var dg = $("#tb-academic-calendar").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/calendar/export-') }}" + document,dg.rows,"Ekspor data Kalender Akademik ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function academicCalDialog() {
        $('#academic-cal-w').window('open')
    }
    function saveAcademicCal() {
        $("#form-academic-calendar").ajaxSubmit({
            url: "{{ url('academic/calendar/store') }}" + "/" + $("#academic-cal-id").val(),
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $.messager.alert('Informasi', response.message)
                    $("#form-academic-calendar").form("reset")
                    $("#tb-academic-cal").datagrid("reload")
                    reloadAcademicCal('AcademicCalId')
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            },
            error: function(xhr) {
                failResponse(xhr)
            }
        })
        return false
    }
    function reloadAcademicCal(id) {
        $('#'+id).combobox('reload','{{ url("academic/calendar/list") }}' + "?_token=" + "{{ csrf_token() }}")
    }
    function viewAcademicCalendar() {
        var dg = $("#tb-academic-calendar").datagrid('getSelected')
        if (dg != null) {
            $("#academic-cal-view-w").window('open')
            $('#academic-cal-view-w').window('refresh', '{{ url("academic/calendar/yearly") }}' + "/" + dg.calendar_id)
        } else {
            $.messager.alert('Peringatan', 'Silahkan pilih salah satu daftar Kalender Akademik di sebelah kiri', 'error')
        }
    }
</script>
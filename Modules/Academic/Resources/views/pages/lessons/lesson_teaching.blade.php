@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 13 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
    $TableHeight = $InnerHeight - 326 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Penyusunan Jadwal Guru dan Kelas</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-lesson-teaching" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',readonly:true,labelWidth:100" />
                        <input type="hidden" id="fdept-lesson-teaching" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-lesson-teaching" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-lesson-teaching" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Guru:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterLessonTeaching({fdept: $('#fdept-lesson-teaching').val(),fname: $('#fname-lesson-teaching').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-lesson-teaching').form('reset');filterLessonTeaching({})">Batal</a>
                </div>
            </form>
            <table id="tb-lesson-teaching" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'employee',width:150,resizeable:true,sortable:true">Guru</th>
                        <th data-options="field:'class_id',width:100,resizeable:true,sortable:true">Kelas</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-lesson-teaching" class="panel-top">
            <a id="newLessonTeaching" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newLessonTeaching()">Baru</a>
            <a id="editLessonTeaching" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editLessonTeaching()">Ubah</a>
            <a id="saveLessonTeaching" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveLessonTeaching()">Simpan</a>
            <a id="clearLessonTeaching" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearLessonTeaching()">Batal</a>
            <a id="deleteLessonTeaching" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteLessonTeaching()">Hapus</a>
            <a id="pdfTeacherLessonTeaching" class="easyui-linkbutton" style="width: 160px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfLessonTeaching('teacher')">Cetak Jadwal Guru</a>
            <a id="pdfClassLessonTeaching" class="easyui-linkbutton" style="width: 160px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfLessonTeaching('class')">Cetak Jadwal Kelas</a>
        </div>
        <div class="title">
            <h6><span id="mark-lesson-teaching"></span>Guru: <span id="title-lesson-teaching"></span></h6>
        </div>
        <div id="page-lesson-teaching">
            <form id="form-lesson-teaching-main" method="post">
                <div id="tt-lesson-teaching" class="easyui-tabs borderless" plain="true" narrow="true" style="height:{{ $TabHeight }}">
                    <div title="Umum" class="content-doc pt-3 pb-3">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-4">
                                    <input type="hidden" id="id-lesson-teaching" name="id" value="-1" />
                                    <input type="hidden" id="id-dept-lesson-teaching" name="department_id" value="-1" />
                                    <input type="hidden" id="id-schoolyear-lesson-teaching" name="schoolyear_id" value="-1" />
                                    <div class="mb-1">
                                        <input id="LessonTeachingDeptId" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <input id="LessonTeachingSchoolYearId" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="grade_id" id="LessonTeachingGradeId" class="easyui-textbox" style="width:310px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="class_id" id="LessonTeachingClassId" class="easyui-combogrid" style="width:310px;height:22px;" data-options="
                                            label:'<b>*</b>Kelas:',
                                            labelWidth:'125px',
                                            panelWidth: 570,
                                            idField: 'id',
                                            textField: 'class',
                                            fitColumns:true,
                                            columns: [[
                                                {field:'department',title:'Departemen',width:150},
                                                {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                                {field:'grade',title:'Tingkat',width:80,align:'center'},
                                                {field:'class',title:'Kelas',width:120},
                                                {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                            ]],
                                        ">
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <select name="employee_id" id="LessonTeachingEmployeeId" class="easyui-combogrid" style="width:310px;height:22px;" data-options="
                                            label:'<b>*</b>Guru:',
                                            labelWidth:'125px',
                                            panelWidth: 500,
                                            idField: 'employee_id',
                                            textField: 'employee',
                                            fitColumns:true,
                                            columns: [[
                                                {field:'employee_no',title:'NIP',width:80},
                                                {field:'employee',title:'Nama',width:250},
                                                {field:'status',title:'Status',width:230},
                                            ]],
                                        ">
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <select name="schedule_id" id="LessonTeachingScheduleId" class="easyui-combobox" style="width:248px;height:22px;" tabindex="10" data-options="label:'<b>*</b>Info Jadwal:',labelWidth:'125px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'description'">
                                            <option value="">---</option>
                                        </select>
                                        <a class="easyui-linkbutton small-btn" onclick="teachingScheduleDialog('id-schoolyear-lesson-teaching')" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Add"></i></a>
                                        <a class="easyui-linkbutton small-btn" onclick="reloadTeachingSchedule('LessonTeachingScheduleId')" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Refresh"></i></a>
                                    </div>
                                </div>
                                <div class="col-8 pl-0">
                                    <table id="tb-lesson-teaching-form" class="easyui-datagrid" style="width:100%;height:{{ $TableHeight }}" 
                                        data-options="method:'post',rownumbers:'true',toolbar:menubarLessonTeaching,singleSelect:true">
                                        <thead>
                                            <tr>
                                                <th data-options="field:'day',width:80,resizeable:true,align:'center'">Hari</th>
                                                <th data-options="field:'lesson_id',width:135,resizeable:true,">Pelajaran</th>
                                                <th data-options="field:'from_time',width:70,resizeable:true,align:'center'">Jam ke</th>
                                                <th data-options="field:'to_time',width:70,resizeable:true,align:'center'">Sampai</th>
                                                <th data-options="field:'teaching_status',width:100,resizeable:true,align:'center'">Status</th>
                                                <th data-options="field:'remark',width:190,resizeable:true">Keterangan</th>
                                                <th data-options="field:'day_id',width:50,hidden:true">DayId</th>
                                                <th data-options="field:'lesson',width:50,hidden:true">LessonId</th>
                                                <th data-options="field:'teaching',width:50,hidden:true">TeachingId</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="viewTeacher" title="Tabel Jadwal Guru" class="content-doc pt-3 pb-3" style="overflow-y: scroll;"></div>
                    <div id="viewClass" title="Tabel Jadwal Kelas" class="content-doc pt-3 pb-3" style="overflow-y: scroll;"></div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- dialog --}}
<div id="schedule-info-w" class="easyui-window" title="Info Jadwal" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--List'" style="width:600px;height:350px;padding:10px;"></div>
<div id="teaching-schedule-w" class="easyui-window" title="Tambah Jadwal Mengajar" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--List'" style="width:375px;height:440px;padding:10px;">
    <form id="form-teaching-schedule">
    <div class="mb-1">
        <input class="easyui-textbox" id="teachingScheduleTeacherId" style="width:335px;height:22px;" data-options="label:'Guru:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-1">
        <input class="easyui-textbox" id="teachingScheduleDeptId" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-1">
        <input class="easyui-textbox" id="teachingScheduleSchoolyearId" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-1">
        <input class="easyui-textbox" id="teachingScheduleGradeId" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-1">
        <input class="easyui-textbox" id="teachingScheduleClassId" style="width:335px;height:22px;" data-options="label:'Kelas:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-1">
        <input class="easyui-textbox" id="teachingScheduleScheduleId" style="width:335px;height:22px;" data-options="label:'Info Jadwal:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-1">
        <select name="day" class="easyui-combobox" id="teachingScheduleDayId" style="width: 335px;height:22px;" data-options="label:'Hari:',labelWidth:'125px',panelHeight:100">
            <option value="">---</option>
            <option value="1">Senin</option>
            <option value="2">Selasa</option>
            <option value="3">Rabu</option>
            <option value="4">Kamis</option>
            <option value="5">Jum'at</option>
            <option value="6">Sabtu</option>
            <option value="0">Ahad</option>
        </select>
    </div>
    <div class="mb-1">
        <select name="lesson_id" id="teachingScheduleLessonId" class="easyui-combobox" style="width: 335px;height:22px;" data-options="label:'Pelajaran:',labelWidth:'125px',panelHeight:100,valueField:'id',textField:'lesson'">
            <option value="">---</option>
        </select>
    </div>
    <div class="mb-1">
        <select name="" id="teachingScheduleLessonTimeStart" class="easyui-combobox" style="width: 335px;height:22px;" data-options="label:'Jam Ke:',labelWidth:'125px',panelHeight:100,valueField:'id',textField:'time'">
            <option value="">---</option>
        </select>
    </div>
    <div class="mb-1">
        <select name="" id="teachingScheduleLessonTimeEnd" class="easyui-combobox" style="width: 335px;height:22px;" data-options="label:'Sampai:',labelWidth:'125px',panelHeight:100,valueField:'id',textField:'time'">
            <option value="">---</option>
        </select>
    </div>
    <div class="mb-1">
        <select name="teaching_status" id="teachingScheduleStatusId" class="easyui-combobox" style="width: 335px;height:22px;" data-options="label:'Status:',labelWidth:'125px',panelHeight:90">
            <option value="">---</option>
            @foreach ($status as $stat)
            <option value="{{ $stat->id }}">{{ $stat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-1">
        <input name="remark" id="teachingScheduleRemarkId" class="easyui-textbox" style="width: 335px;height:50px;" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
    </div>
    </form>
    <div style="margin-left:125px;padding:5px 0">
        <a href="javascript:void(0)" class="easyui-linkbutton small-btn" style="height:22px;" 
        onclick="appendSchedule(
            $('#teachingScheduleDayId').combobox('getText'),
            $('#teachingScheduleLessonId').combobox('getText'),
            $('#teachingScheduleStatusId').combobox('getText'),
            $('#teachingScheduleDayId').combobox('getValue'),
            $('#teachingScheduleLessonId').combobox('getValue'),
            $('#teachingScheduleLessonTimeStart').combobox('getValue'),
            $('#teachingScheduleLessonTimeEnd').combobox('getValue'),
            $('#teachingScheduleStatusId').combobox('getValue'),
            $('#teachingScheduleRemarkId').textbox('getValue'),
        )">Tambah Jadwal</a>
    </div>
</div>
<script type="text/javascript">
    var menuActionLessonTeaching = document.getElementById("menu-act-lesson-teaching").getElementsByTagName("a")
    var titleLessonTeaching = document.getElementById("title-lesson-teaching")
    var markLessonTeaching = document.getElementById("mark-lesson-teaching")
    var idLessonTeaching = document.getElementById("id-lesson-teaching")
    var tabsLessonTeaching = $("#tt-lesson-teaching")
    var dgLessonTeaching = $("#tb-lesson-teaching")
    var rowIndex = undefined
    var menubarLessonTeaching = [{
        text: 'Tambah Jadwal',
        iconCls: 'ms-Icon ms-Icon--Add',
        handler: function() {
            addTeachingSchedule()
        }
    },'-',{
        text: 'Hapus Jadwal',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            if (rowIndex != undefined) {
                $("#tb-lesson-teaching-form").datagrid('deleteRow', rowIndex)
                rowIndex = undefined
            }
        }
    }]
    $(function () {
        sessionStorage.formJadwal_Guru = "init"
        sessionStorage.employeeId = 0
        dgLessonTeaching.datagrid({
            url: "{{ url('academic/lesson/schedule/teaching/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formJadwal_Guru == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleLessonTeaching.innerText = row.employee
                    actionButtonLessonTeaching("active",[2,3])
                    $("#form-lesson-teaching-main").form("load", "{{ url('academic/lesson/schedule/teaching/show') }}" + "/" + row.id) 
                    tabsLessonTeaching.tabs("select", 0)
                    $("#tt-lesson-teaching").waitMe("hide")
                }
            }
        })
        dgLessonTeaching.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgLessonTeaching.datagrid('getPager').pagination())
        actionButtonLessonTeaching("{{ $ViewType }}", [])
        actionTabLessonTeaching("{{ $ViewType }}")
        $("#LessonTeachingEmployeeId").combogrid({
            onClickRow: function(index, row) {
                titleLessonTeaching.innerText = row.employee
                $("#LessonTeachingEmployeeId").combogrid('hidePanel')
            }
        })
        $("#LessonTeachingClassId").combogrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#id-dept-lesson-teaching").val(row.department_id)
                $("#id-schoolyear-lesson-teaching").val(row.schoolyear_id)
                $("#LessonTeachingDeptId").textbox('setValue', row.department)
                $("#LessonTeachingSchoolYearId").textbox('setValue', row.school_year)
                $("#LessonTeachingGradeId").textbox('setValue', row.grade)
                $("#LessonTeachingEmployeeId").combogrid("grid").datagrid("load","{{ url('academic/teacher/combo-grid/group') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=" + row.department_id + "&grade_id=" + row.grade_id)
                $('#LessonTeachingScheduleId').combobox('reload','{{ url("academic/lesson/schedule/info/list") }}' + "/" + row.schoolyear_id + "?_token=" + "{{ csrf_token() }}")
                $("#LessonTeachingClassId").combogrid('hidePanel')
            }
        })
        $('#schedule-info-w').window({
            onLoad: function() {
                $("#scheduleInfoDeptId").textbox('setValue', $("#LessonTeachingDeptId").textbox('getValue'))
                $("#scheduleInfoSchoolYearId").textbox('setValue', $("#LessonTeachingSchoolYearId").textbox('getValue'))
                $("#id-schoolyear-schedule-info").val($("#id-schoolyear-lesson-teaching").val())
            }
        })
        $("#form-lesson-teaching-main").form({
            onLoadSuccess: function(data) {
                $("#id-lesson-teaching").val(data.main.id)
                $("#id-dept-lesson-teaching").val(data.main.department_id)
                $("#id-schoolyear-lesson-teaching").val(data.main.schoolyear_id)
                $("#LessonTeachingDeptId").textbox('setValue', data.main.department)
                $("#LessonTeachingSchoolYearId").textbox('setValue', data.main.school_year)
                $("#LessonTeachingGradeId").textbox('setValue', data.main.grade)
                $("#LessonTeachingClassId").combogrid('setValue', data.main.class_id)
                $("#LessonTeachingEmployeeId").combogrid("grid").datagrid("load","{{ url('academic/teacher/combo-grid/group') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=" + data.main.department_id + "&grade_id=" + data.main.get_grade_by_dept.id)
                $("#LessonTeachingEmployeeId").combogrid('setValue', data.main.employee_id)
                $('#LessonTeachingScheduleId').combobox('reload','{{ url("academic/lesson/schedule/info/list") }}' + "/" + data.main.schoolyear_id + "?_token=" + "{{ csrf_token() }}")
                $('#LessonTeachingScheduleId').combobox('setValue', data.main.schedule_id)
                actionTabLessonTeaching("active")
                $("#viewTeacher").tabs().panel({
                    href: "{{ url('academic/lesson/schedule/teaching/teacher') }}" + "/" + data.main.department_id + "/" + $("#LessonTeachingEmployeeId").combogrid('getValue')
                })
                $("#viewClass").tabs().panel({
                    href: "{{ url('academic/lesson/schedule/teaching/class') }}" + "/" + data.main.department_id 
                })
                
                $("#tb-lesson-teaching-form").datagrid({ data: data.schedules })
            }
        })
        $("#teaching-schedule-w").window({
            onOpen: function() {
                $("#teachingScheduleTeacherId").textbox('setValue', $("#LessonTeachingEmployeeId").combogrid('getText'))
                $("#teachingScheduleDeptId").textbox('setValue', $("#LessonTeachingDeptId").textbox('getText'))
                $("#teachingScheduleSchoolyearId").textbox('setValue', $("#LessonTeachingSchoolYearId").textbox('getText'))
                $("#teachingScheduleGradeId").textbox('setValue', $("#LessonTeachingGradeId").textbox('getText'))
                $("#teachingScheduleClassId").textbox('setValue', $("#LessonTeachingClassId").combogrid('getText'))
                $("#teachingScheduleScheduleId").textbox('setValue', $("#LessonTeachingScheduleId").combobox('getText'))
                $('#teachingScheduleLessonId').combobox('reload','{{ url("academic/teacher/list") }}' + "/" + $("#LessonTeachingEmployeeId").combogrid('getValue') + "/" + $("#id-dept-lesson-teaching").val() + "?_token=" + "{{ csrf_token() }}")
                $('#teachingScheduleLessonTimeStart').combobox('reload','{{ url("academic/lesson/schedule/time/combo-box") }}' + "/" + $("#id-dept-lesson-teaching").val() + "?_token=" + "{{ csrf_token() }}")
                $('#teachingScheduleLessonTimeEnd').combobox('reload','{{ url("academic/lesson/schedule/time/combo-box") }}' + "/" + $("#id-dept-lesson-teaching").val() + "?_token=" + "{{ csrf_token() }}")
            }
        })
        $("#tb-lesson-teaching-form").datagrid({
            onSelect: function(index, row) {
                rowIndex = index
            }
        })
        $("#tt-lesson-teaching").waitMe({effect:"none"})
    })
    function filterLessonTeaching(params) {
        if (Object.keys(params).length > 0) {
            $("#tb-lesson-teaching").datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            $("#tb-lesson-teaching").datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newLessonTeaching() {
        sessionStorage.formJadwal_Guru = "active"
        $("#form-lesson-teaching-main").form("reset")
        actionButtonLessonTeaching("active", [0,1,4,5,6])
        actionTabLessonTeaching("init")
        markLessonTeaching.innerText = "*"
        titleLessonTeaching.innerText = ""
        idLessonTeaching.value = "-1"
        $("#LessonTeachingClassId").combobox('textbox').focus()
        $("#tb-lesson-teaching-form").datagrid({data: []})
        tabsLessonTeaching.tabs("select", 0)
        $("#tt-lesson-teaching").waitMe("hide")
    }
    function editLessonTeaching() {
        sessionStorage.formJadwal_Guru = "active"
        markLessonTeaching.innerText = "*"
        actionButtonLessonTeaching("active", [0,1,4,5,6])
    }
    function saveLessonTeaching() {
        if (sessionStorage.formJadwal_Guru == "active") {
            if ($("#tb-lesson-teaching-form").datagrid('getRows').length < 1) {
                $.messager.alert('Peringatan', 'Minimal terdapat 1 hari jadwal mengajar.', 'error')
            } else {
                ajaxLessonTeaching("academic/lesson/schedule/teaching/store")
            }
        }
    }
    function deleteLessonTeaching() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Jadwal Guru terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/lesson/schedule/teaching/destroy') }}" +"/"+idLessonTeaching.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxLessonTeachingResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function pdfLessonTeaching(param) {
        if (idLessonTeaching.value != -1) {
            exportDocument("{{ url('academic/lesson/schedule/teaching/print') }}" + "/" + param, { 
                department_id: $("#id-dept-lesson-teaching").val(), 
                employee_id: $("#LessonTeachingEmployeeId").combogrid('getValue'),
                teacher: $("#LessonTeachingEmployeeId").combogrid('getText'),
                department: $("#LessonTeachingDeptId").textbox('getText'),
                school_year: $("#LessonTeachingSchoolYearId").textbox('getText'),
                schedule: $("#LessonTeachingScheduleId").combobox('getText'),
                class: $("#LessonTeachingClassId").combogrid('getText'),
            }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function ajaxLessonTeaching(route) {
        var dg = $("#tb-lesson-teaching-form").datagrid('getData')
        $("#form-lesson-teaching-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', dg: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-lesson-teaching").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxLessonTeachingResponse(response)
                $("#page-lesson-teaching").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-lesson-teaching").waitMe("hide")
            }
        })
        return false
    }
    function ajaxLessonTeachingResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearLessonTeaching()
            $("#tb-lesson-teaching").datagrid("reload")
        } else {
            showError(response)
        }
    }
    function clearLessonTeaching() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearLessonTeaching()
            }
        })
    }
    function actionButtonLessonTeaching(viewType, idxArray) {
        for (var i = 0; i < menuActionLessonTeaching.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionLessonTeaching[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionLessonTeaching[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionLessonTeaching[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionLessonTeaching[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionTabLessonTeaching(viewType) {
        if (viewType == "init") {
            tabsLessonTeaching.tabs('disableTab', 1)
            tabsLessonTeaching.tabs('disableTab', 2)
        } else {
            tabsLessonTeaching.tabs('enableTab', 1)
            tabsLessonTeaching.tabs('enableTab', 2)
        }
    }
    function actionClearLessonTeaching() {
        sessionStorage.formJadwal_Guru = "init"
        $("#form-lesson-teaching-main").form("reset")
        actionButtonLessonTeaching("init", [])
        actionTabLessonTeaching("init")
        titleLessonTeaching.innerText = ""
        markLessonTeaching.innerText = ""
        idLessonTeaching.value = "-1"
        $("#tb-lesson-teaching-form").datagrid({data: []})
        rowIndex = undefined
        $("#tt-lesson-teaching").waitMe({effect:"none"})
    }
    function exportLessonTeaching(document) {
        var dg = $("#tb-lesson-teaching").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/lesson/schedule/teaching/export-') }}" + document,dg.rows,"Ekspor data Jadwal Guru ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function teachingScheduleDialog(param) {
        if ($("#"+param).val() < 1)
        {
            $.messager.alert('Peringatan', 'Silahkan pilih Kelas terlebih dahulu', 'error')
        } else {
            $('#schedule-info-w').window('open')
            $('#schedule-info-w').window('refresh', '{{ url("academic/lesson/schedule/info") }}')
        }
    }
    function reloadTeachingSchedule(param) {
        if ($("#id-schoolyear-lesson-teaching").val() != '-1') {
            $('#'+param).combobox('reload','{{ url("academic/lesson/schedule/info/list") }}' + "/" + $("#id-schoolyear-lesson-teaching").val() + "?_token=" + "{{ csrf_token() }}")
        }
    }
    function addTeachingSchedule() {
        if (
            $("#LessonTeachingEmployeeId").combogrid('getValue') != '' && 
            $("#LessonTeachingClassId").combogrid('getValue') != '' &&
            $("#LessonTeachingScheduleId").combobox('getValue') != ''
        ) {
            $("#teaching-schedule-w").window('open')
        }
    }
    function appendSchedule(Day, Lesson, Status, ScheduleDayId, LessonId, LessonTimeStart, LessonTimeEnd, StatusId, RemarkId) {
        if (ScheduleDayId != '' && LessonId != '' && LessonTimeStart != '' && LessonTimeEnd != '' && StatusId != '') {
            var rows = $("#tb-lesson-teaching-form").datagrid("getRows")
            if (LessonTimeStart > LessonTimeEnd) {
                $.messager.alert("Peringatan", "Jam mulai lebih besar dari jam selesai.", "warning")
            } else {
                $('#teaching-schedule-w').waitMe({effect : 'facebook'})
                $.post('{{ url("academic/lesson/schedule/teaching/check") }}', $.param({ 
                    _token: "{{ csrf_token() }}",  
                    from_time: LessonTimeStart,
                    to_time: LessonTimeEnd,
                    day: ScheduleDayId,
                    employee: $("#LessonTeachingEmployeeId").combogrid('getValue'),
                    class: $("#LessonTeachingClassId").combogrid('getValue')
                }, true), function(response) {
                    if (response.success)
                    {
                        if (rows.length > 0)
                        {
                            let isValid = true
                            for (let i = 0; i < rows.length; i++) {
                                if (Day == rows[i].day && LessonTimeStart == rows[i].from_time) {
                                    isValid = false
                                    $.messager.alert("Peringatan", "Jadwal sudah ada di tabel, silahkan cek kembali.", "warning")
                                } 
                            }
                            if (isValid) {
                                appendScheduleToTable(Day, Lesson, Status, ScheduleDayId, LessonId, LessonTimeStart, LessonTimeEnd, StatusId, RemarkId)
                            }
                        } else {
                            appendScheduleToTable(Day, Lesson, Status, ScheduleDayId, LessonId, LessonTimeStart, LessonTimeEnd, StatusId, RemarkId)
                        }
                    } else {
                        $.messager.alert('Peringatan', response.message, 'error')
                    }
                    $('#teaching-schedule-w').waitMe('hide')
                })
            }
        }
    }
    function appendScheduleToTable(Day, Lesson, Status, ScheduleDayId, LessonId, LessonTimeStart, LessonTimeEnd, StatusId, RemarkId) {
        $('#tb-lesson-teaching-form').datagrid('appendRow',{
            day: Day,
            lesson_id: Lesson,
            from_time: LessonTimeStart,
            to_time: LessonTimeEnd,
            teaching_status: Status,
            remark: RemarkId,
            day_id: ScheduleDayId,
            lesson: LessonId,
            teaching: StatusId
        })
        $("#teaching-schedule-w").window('close')
        $("#form-teaching-schedule").form('reset')
    }
</script>
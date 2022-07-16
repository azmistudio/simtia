@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
    $SubGridHeight = $InnerHeight - 343 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Presensi Pelajaran</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportPresenceLesson('pdf')">Cetak Form Presensi Pelajaran</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-presence-lesson" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select name="class_id" id="fclass-presence-lesson" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
                        label:'Kelas:',
                        labelWidth:100,
                        panelWidth: 570,
                        idField: 'id',
                        textField: 'class',
                        url: '{{ url('academic/class/student/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        fitColumns:true,
                        queryParams: { _token: '{{ csrf_token() }}' },
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
                    <select id="flesson-presence-lesson" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
                        label:'Pelajaran:',
                        labelWidth:100,
                        panelWidth: 380,
                        idField: 'id',
                        textField: 'name',
                        url: '{{ url('academic/lesson/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        queryParams: { _token: '{{ csrf_token() }}' },
                        fitColumns:true,
                        columns: [[
                            {field:'department',title:'Departemen',width:150},
                            {field:'code',title:'Kode',width:80},
                            {field:'name',title:'Pelajaran',width:150},
                        ]],
                    ">
                    </select>
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterPresenceLesson({fclass: $('#fclass-presence-lesson').combobox('getValue'),flesson: $('#flesson-presence-lesson').combobox('getValue')})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-presence-lesson').form('reset');filterPresenceLesson({})">Batal</a>
                </div>
            </form>
            <table id="tb-presence-lesson" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'lesson',width:100,resizeable:true">Pelajaran</th>
                        <th data-options="field:'date',width:75,resizeable:true,sortable:true">Tanggal</th>
                        <th data-options="field:'lesson_schedule_id',width:90,resizeable:true">Jam</th>
                        <th data-options="field:'class_id',width:100,resizeable:true,sortable:true">Kelas</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-presence-lesson" class="panel-top">
            <a id="newPresenceLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newPresenceLesson()">Baru</a>
            <a id="editPresenceLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editPresenceLesson()">Ubah</a>
            <a id="savePresenceLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="savePresenceLesson()">Simpan</a>
            <a id="clearPresenceLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearPresenceLesson()">Batal</a>
            <a id="deletePresenceLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deletePresenceLesson()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-presence-lesson"></span>Presensi Pelajaran: <span id="title-presence-lesson"></span></h6>
        </div>
        <div class="pt-3 pb-3" id="page-presence-lesson">
            <form id="form-presence-lesson-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-5">
                            <input type="hidden" id="id-presence-lesson" name="id" value="-1" />
                            <input type="hidden" id="id-presence-lesson-semester" name="semester_id" value="-1" />
                            <input type="hidden" id="id-presence-lesson-employee" name="employee_id" value="-1" />
                            <input type="hidden" id="id-presence-lesson-class" name="class_id" value="-1" />
                            <input type="hidden" id="id-presence-lesson-teacher_type" name="teacher_type" value="-1" />
                            <input type="hidden" id="id-presence-lesson-schedule" name="lesson_schedule_id" value="-1" />
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceLessonDept" style="width:400px;height:22px;" data-options="label:'Departemen:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceLessonGrade" style="width:400px;height:22px;" data-options="label:'Tingkat - Tahun Ajaran:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceLessonSemester" style="width:400px;height:22px;" data-options="label:'Semester:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select id="PresenceLessonClass" class="easyui-combogrid" style="width:400px;height:22px;" data-options="
                                    label:'<b>*</b>Kelas:',
                                    labelWidth:'170px',
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
                                <input class="easyui-textbox" id="PresenceLessonTeacher" style="width:400px;height:22px;" data-options="label:'Guru:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceLessonStatus" style="width:400px;height:22px;" data-options="label:'Status Guru:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="lesson_id" id="PresenceLessonId" class="easyui-combogrid" style="width:400px;height:22px;" data-options="
                                    label:'<b>*</b>Pelajaran:',
                                    labelWidth:'170px',
                                    panelWidth: 500,
                                    idField: 'seq',
                                    textField: 'lesson',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'employee_name',title:'Guru',width:250},
                                        {field:'semester',title:'Semester',width:100},
                                        {field:'lesson',title:'Pelajaran',width:150},
                                    ]]
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <select id="PresenceLessonTime" class="easyui-combobox" style="width: 400px;height:22px;" data-options="label:'<b>*</b>Jam Belajar:',labelWidth:'170px',panelHeight:100,panelWidth: 230,valueField:'id',textField:'time',formatter:formatTime">
                                    <option value="">---</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="date[]" id="PresenceLessonDate" class="easyui-combobox" style="width: 400px;height:22px;" data-options="label:'<b>*</b>Tanggal:',labelWidth:'170px',panelHeight:100,panelWidth: 230,valueField:'id',textField:'text',multiple:true"></select>
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:400px;height:22px;" data-options="label:'Ket. Kehadiran Guru:',labelWidth:'170px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="subject" class="easyui-textbox" style="width:400px;height:44px;" data-options="label:'<b>*</b>Materi:',labelWidth:'170px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="plan" class="easyui-textbox" style="width:400px;height:44px;" data-options="label:'Materi Selanjutnya:',labelWidth:'170px',multiline:true" />
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="mb-1">
                                <input name="late" class="easyui-numberspinner" style="width:300px;height:22px;" value="0" data-options="label:'Keterlambatan:',labelWidth:'170px',min:0" />
                                <span class="ml-1">Menit</span>
                            </div>
                            <div class="mb-3">
                                <input name="teach_hour" id="PresenceLessonHourTeach" class="easyui-numberspinner" value="1" style="width:225px;height:22px;" data-options="label:'Jumlah Jam Mengajar:',labelWidth:'170px',min:1" />
                                <span class="mr-1"></span>
                                <span>:</span>
                                <span class="mr-1"></span>
                                <input name="teach_minute" id="PresenceLessonMinuteTeach" class="easyui-numberspinner" value="0" style="width:55px;height:22px;" data-options="min:0" />
                                <span class="mr-1"></span>
                                <span>(Jam:Menit)</span>
                            </div>
                            <div class="mb-1">
                                <table id="tb-presence-lesson-form" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}" 
                                    data-options="method:'post',rownumbers:'true', queryParams: { _token: '{{ csrf_token() }}' }">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'student_no',width:80,resizeable:true,align:'center'">NIS</th>
                                            <th data-options="field:'name',width:150,resizeable:true,align:'left'">Nama</th>
                                            <th data-options="field:'presence',width:100,resizeable:true,align:'center',editor:{type:'combobox',options:{valueField:'id',textField:'name',panelHeight:112,data:[{id:'Hadir',name:'Hadir'},{id:'Ijin',name:'Ijin'},{id:'Sakit',name:'Sakit'},{id:'Alpa',name:'Alpa'},{id:'Cuti',name:'Cuti'}]}}">Presensi</th>
                                            <th data-options="field:'remark',width:190,resizeable:true,align:'left',editor:'text'">Catatan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- dialog --}}
<div id="presence-lesson-form-w" class="easyui-window" title="Cetak Form Presensi Pelajaran" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--Print'" style="width:385px;padding:10px;">
    <form id="form-presence-lesson-form">
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceLessonFormDept" style="width:350px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceLessonFormGrade" style="width:350px;height:22px;" data-options="label:'Tingkat:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceLessonFormSchoolYear" style="width:350px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceLessonFormSemester" style="width:350px;height:22px;" data-options="label:'Semester:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceLessonFormPeriod" style="width:350px;height:22px;" data-options="label:'Periode:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <select id="presenceLessonFormClass" class="easyui-combogrid" style="width:350px;height:22px;" data-options="
                label:'<b>*</b>Kelas:',
                labelWidth:'150px',
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
            <select id="presenceLessonFormId" class="easyui-combobox" style="width:350px;height:22px;" data-options="label:'<b>*</b>Pelajaran:',labelWidth:'150px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'text'">
                <option value="">---</option>
            </select>   
        </div>
    </form>
    <div style="margin-left:150px;padding:5px 0">
        <a href="javascript:void(0)" class="easyui-linkbutton small-btn" onclick="printPresentLessonForm($('#presenceLessonFormClass').combobox('getValue'),$('#presenceLessonFormId').combobox('getText'))" style="height:22px;">Cetak Form</a>
    </div>
</div>
<script type="text/javascript">
    var menuActionPresenceLesson = document.getElementById("menu-act-presence-lesson").getElementsByTagName("a")
    var titlePresenceLesson = document.getElementById("title-presence-lesson")
    var markPresenceLesson = document.getElementById("mark-presence-lesson")
    var idPresenceLesson = document.getElementById("id-presence-lesson")
    var dgPresenceLesson = $("#tb-presence-lesson")
    $(function () {
        sessionStorage.formPresensi_Pelajaran = "init"
        sessionStorage.formPresenceLesson = "init"
        dgPresenceLesson.datagrid({
            url: "{{ url('academic/presence/lesson/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPresensi_Pelajaran == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titlePresenceLesson.innerText = row.lesson
                    actionButtonPresenceLesson("active",[2,3])
                    $("#form-presence-lesson-main").form("load", "{{ url('academic/presence/lesson/show') }}" + "/" + row.id)
                    $("#page-presence-lesson").waitMe("hide")
                }
            }
        })
        dgPresenceLesson.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgPresenceLesson.datagrid('getPager').pagination())
        actionButtonPresenceLesson("{{ $ViewType }}", [])
        $("#PresenceLessonClass").combogrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#id-presence-lesson-semester").val(row.semester_id)
                $("#id-presence-lesson-class").val(row.id)
                $("#PresenceLessonDept").textbox('setValue', row.department)
                $("#PresenceLessonGrade").textbox('setValue', row.grade + " - " + row.school_year)
                $("#PresenceLessonSemester").textbox('setValue', row.semester)
                $("#PresenceLessonTeacher").textbox("setValue", "")
                $("#PresenceLessonStatus").textbox("setValue", "")
                $("#PresenceLessonId").combogrid("setValue", "")
                $("#PresenceLessonTime").combobox("setValue", "")
                $("#PresenceLessonDate").combobox("setValue", "")
                $("#tb-presence-lesson-form").datagrid("load", "{{ url('academic/student/list') }}" + "?fclass=" + row.id)
                $("#PresenceLessonId").combogrid("grid").datagrid("load", "{{ url('academic/lesson/schedule/teaching/combo-grid') }}" + "?fclass=" + row.id + "&fdept=" + row.department_id + "&fsemester=" + row.semester_id + "&_token=" + "{{ csrf_token() }}")
            }
        })
        $("#PresenceLessonId").combogrid({
            onClickRow: function(index, row) {
                titlePresenceLesson.innerText = row.lesson
                $("#PresenceLessonTeacher").textbox("setValue", row.employee_name)
                $("#PresenceLessonStatus").textbox("setValue", row.status)
                $("#id-presence-lesson-employee").val(row.employee_id)
                $("#id-presence-lesson-teacher_type").val(row.teaching_status)
                $("#PresenceLessonId").combogrid("hidePanel")
                $("#PresenceLessonTime").combobox("setValue", "")
                $("#PresenceLessonTime").combobox("reload", "{{ url('academic/lesson/schedule/teaching/combo-box') }}" + "/" + row.seq + "?&_token=" + "{{ csrf_token() }}")
            }
        })
        $("#tb-presence-lesson-form").datagrid('enableCellEditing').datagrid('enableFilter')
        $("#form-presence-lesson-main").form({
            onLoadSuccess: function(data) {
                $("#PresenceLessonTime").combobox("reload", "{{ url('academic/lesson/schedule/teaching/combo-box') }}" + "/" + data.class_id+"-"+data.employee_id+"-"+data.department_id+"-"+data.lesson_id + "?&_token=" + "{{ csrf_token() }}")
                $("#id-presence-lesson-semester").val(data.semester_id)
                $("#id-presence-lesson-class").val(data.class_id)
                $("#PresenceLessonDept").textbox("setValue", data.department)
                $("#PresenceLessonGrade").textbox("setValue", data.grade)
                $("#PresenceLessonSchoolYear").textbox("setValue", data.school_year)
                $("#PresenceLessonSemester").textbox("setValue", data.semester)
                $("#PresenceLessonTeacher").textbox("setValue", data.teacher)
                $("#PresenceLessonStatus").textbox("setValue", data.status)
                $("#PresenceLessonClass").combogrid("setValue", data.class_id)
                $("#PresenceLessonClass").combogrid("readonly", true)
                $("#PresenceLessonId").combogrid("setValue", data.class_id+"-"+data.employee_id+"-"+data.department_id+"-"+data.lesson_id)
                $("#PresenceLessonId").combogrid("setText", data.lesson)
                $("#PresenceLessonId").combogrid("readonly", true)
                $("#PresenceLessonTime").combobox("readonly", true)
                $("#PresenceLessonTime").combobox("setValue", data.lesson_schedule_id)
                $("#PresenceLessonDate").combobox("readonly", true)
                $("#PresenceLessonDate").combobox("setValues", ['',data.p_date])
                $("#id-presence-lesson-schedule").val(data.lesson_schedule)
                let times_teach = data.times.split(":")
                $("#PresenceLessonHourTeach").numberspinner('setValue', parseInt(times_teach[0]))
                $("#PresenceLessonMinuteTeach").numberspinner('setValue', parseInt(times_teach[1]))
                $("#tb-presence-lesson-form").datagrid("load", "{{ url('academic/presence/lesson/list') }}" + "?id=" + data.id)
            }
        })
        $("#presenceLessonFormClass").combogrid('grid').datagrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#presenceLessonFormDept").textbox('setValue', row.department)
                $("#presenceLessonFormGrade").textbox('setValue', row.grade)
                $("#presenceLessonFormSchoolYear").textbox('setValue', row.school_year)
                $("#presenceLessonFormSemester").textbox('setValue', row.semester)
                let periods = row.period.slice(1,-1).split("|")
                $("#presenceLessonFormPeriod").textbox('setValue', parsingDate(periods[0]) + " s.d " + parsingDate(periods[1]))
                $("#presenceLessonFormId").combobox("setValue", "")
                $("#presenceLessonFormId").combobox("reload", "{{ url('academic/lesson/combo-box') }}" + "/" + row.department_id + "?&_token=" + "{{ csrf_token() }}")
                $("#presenceLessonFormClass").combogrid('hidePanel')
            }
        })
        $("#PresenceLessonTime").combobox({
            onSelect: function(record) {
                $("#PresenceLessonHourTeach").numberspinner("setValue", record.duration)
                $("#id-presence-lesson-schedule").val(record.id)
                if (record.id !== "") {
                    $("#PresenceLessonDate").combobox("reload", "{{ url('academic/lesson/schedule/teaching/day/combo-box') }}" + "?seq=" + record.id + "&_token=" + "{{ csrf_token() }}")
                }
            }
        })
        $("#page-presence-lesson").waitMe({effect:"none"})
    })
    function formatTime(row) {
        return "<span><b>"+row.time+"</b></span><br/><span>"+row.desc+"</span>"
    }
    function filterPresenceLesson(params) {
        if (Object.keys(params).length > 0) {
            dgPresenceLesson.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgPresenceLesson.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newPresenceLesson() {
        sessionStorage.formPresenceLesson = "new"
        sessionStorage.formPresensi_Pelajaran = "active"
        $("#form-presence-lesson-main").form("reset")
        actionButtonPresenceLesson("active", [0,1,4])
        clearPreview("photo-presence-lesson","preview-img-presence-lesson")
        markPresenceLesson.innerText = "*"
        titlePresenceLesson.innerText = ""
        idPresenceLesson.value = "-1"
        $("#id-presence-lesson-semester").val("-1")
        $("#id-presence-lesson-class").val("-1")
        $("#id-presence-lesson-schedule").val("-1")
        $("#tb-presence-lesson-form").datagrid("loadData", [])
        $("#PresenceLessonClass").combogrid("readonly", false)
        $("#PresenceLessonId").combogrid("readonly", false)
        $("#PresenceLessonTime").combogrid("readonly", false)
        $("#PresenceLessonDate").combobox("readonly", false)
        $("#page-presence-lesson").waitMe("hide")
    }
    function editPresenceLesson() {
        sessionStorage.formPresensi_Pelajaran = "active"
        sessionStorage.formPresenceLesson = "update"
        markPresenceLesson.innerText = "*"
        actionButtonPresenceLesson("active", [0,1,4])
    }
    function savePresenceLesson() {
        if (sessionStorage.formPresensi_Pelajaran == "active") {
            ajaxPresenceLesson("academic/presence/lesson/store")
        }
    }
    function deletePresenceLesson() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Presensi Pelajaran terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                var dg = $("#tb-presence-lesson-form").datagrid('getData')
                $.post("{{ url('academic/presence/lesson/destroy') }}", { 
                    _token: "{{ csrf_token() }}", 
                    id: idPresenceLesson.value, 
                    students: dg.rows, 
                    class_id: $("#id-presence-lesson-class").val(),
                    semester_id: $("#id-presence-lesson-semester").val(),
                    lesson_id: $("#PresenceLessonId").combogrid("getValue"),
                    employee_id: $("#id-presence-lesson-employee").val(),
                    dates: $("#PresenceLessonDate").combobox("getValues")
                }, "json").done(function( response ) {
                    ajaxPresenceLessonResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxPresenceLesson(route) {
        var dg = $("#tb-presence-lesson-form").datagrid('getData')
        $("#form-presence-lesson-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', students: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-presence-lesson").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxPresenceLessonResponse(response)
                $("#page-presence-lesson").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-presence-lesson").waitMe("hide")
            }
        })
        return false
    }
    function ajaxPresenceLessonResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearPresenceLesson()
            $("#tb-presence-lesson").datagrid("reload")
        } else {
            showError(response)
        }
    }
    function clearPresenceLesson() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearPresenceLesson()
            }
        })
    }
    function actionButtonPresenceLesson(viewType, idxArray) {
        for (var i = 0; i < menuActionPresenceLesson.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionPresenceLesson[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionPresenceLesson[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionPresenceLesson[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionPresenceLesson[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearPresenceLesson() {
        sessionStorage.formPresensi_Pelajaran = "init"
        sessionStorage.formPresenceLesson = "init"
        $("#form-presence-lesson-main").form("reset")
        actionButtonPresenceLesson("init", [])
        titlePresenceLesson.innerText = ""
        markPresenceLesson.innerText = ""
        idPresenceLesson.value = "-1"
        $("#id-presence-lesson-semester").val("-1")
        $("#id-presence-lesson-class").val("-1")
        $("#id-presence-lesson-schedule").val("-1")
        $("#tb-presence-lesson-form").datagrid("loadData", [])
        $("#PresenceLessonClass").combogrid("readonly", false)
        $("#PresenceLessonId").combogrid("readonly", false)
        $("#PresenceLessonTime").combogrid("readonly", false)
        $("#page-presence-lesson").waitMe({effect:"none"})
    }
    function exportPresenceLesson() {
        $("#presence-lesson-form-w").window("open")
    }
    function printPresentLessonForm(id, param) {
        if (id != '') {
            exportDocument("{{ url('academic/presence/lesson/print/form') }}", { id: id, lesson: param }, "Cetak Form Presensi Pelajaran", "{{ csrf_token() }}")
            $("#presence-lesson-form-w").window("close")
        }
    }
</script>
@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
    $SubGridHeight = $InnerHeight - 335 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Penilaian Pelajaran</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportAssessmentLesson('pdf')">Cetak Form Penilaian Pelajaran</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-assessment-lesson" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select id="fclass-assessment-lesson" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
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
                    <select id="flesson-assessment-lesson" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
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
                <div class="mb-1">
                    <input id="fcode-assessment-lesson" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Kode Ujian:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a class="easyui-linkbutton small-btn flist-box" onclick="filterAssessmentLesson({fclass: $('#fclass-assessment-lesson').combobox('getValue'),flesson: $('#flesson-assessment-lesson').combobox('getValue'),fcode: $('#fcode-assessment-lesson').val()})">Cari</a>
                    <a class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-assessment-lesson').form('reset');filterAssessmentLesson({})">Batal</a>
                </div>
            </form>
            <table id="tb-assessment-lesson" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'class_id',width:80,resizeable:true,sortable:true">Kelas</th>
                        <th data-options="field:'lesson',width:120,resizeable:true,sortable:true">Pelajaran</th>
                        <th data-options="field:'code',width:100,resizeable:true">Kode Uji</th>
                        <th data-options="field:'date',width:100,resizeable:true,sortable:true">Tanggal</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-assessment-lesson" class="panel-top">
            <a id="newAssessmentLesson" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newAssessmentLesson()">Baru</a>
            <a id="editAssessmentLesson" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editAssessmentLesson()">Ubah</a>
            <a id="saveAssessmentLesson" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveAssessmentLesson()">Simpan</a>
            <a id="clearAssessmentLesson" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearAssessmentLesson()">Batal</a>
            <a id="deleteAssessmentLesson" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteAssessmentLesson()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-assessment-lesson"></span>Pelajaran: <span id="title-assessment-lesson"></span></h6>
        </div>
        <div id="page-assessment-lesson">
            <form id="form-assessment-lesson-main" method="post">
                <div id="tt-assessment-lesson" class="easyui-tabs borderless" plain="true" narrow="true" style="height:{{ $TabHeight }}">
                    <div title="Umum" class="content-doc pt-2">
                        <div class="container-fluid">
                            <div class="row row-cols-auto">
                                <div class="col-5">
                                    <input type="hidden" id="id-assessment-lesson" name="id" value="-1" />
                                    <input type="hidden" id="id-assessment-lesson-semester" name="semester_id" value="-1" />
                                    <input type="hidden" id="id-assessment-lesson-employee" name="employee_id" value="-1" />
                                    <input type="hidden" id="id-assessment-lesson-id" name="lesson_id" value="-1" />
                                    <input type="hidden" id="id-assessment-lesson-class" name="class_id" value="-1" />
                                    <input type="hidden" id="id-assessment-lesson-teacher_type" name="status_id" value="-1" />
                                    <input type="hidden" id="id-assessment-lesson-score_aspect" name="score_aspect_id" value="-1" />
                                    <input type="hidden" id="id-assessment-lesson-exam" name="lesson_exam_id" value="-1" />
                                    <input type="hidden" id="id-assessment-lesson-start" name="start" value="" />
                                    <input type="hidden" id="id-assessment-lesson-end" name="end" value="" />
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="AssessmentLessonDept" style="width:395px;height:22px;" data-options="label:'Departemen:',labelWidth:'170px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="AssessmentLessonGrade" style="width:395px;height:22px;" data-options="label:'Tingkat - Tahun Ajaran:',labelWidth:'170px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="AssessmentLessonSemester" style="width:395px;height:22px;" data-options="label:'Semester:',labelWidth:'170px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="AssessmentLessonClass" style="width:395px;height:22px;" data-options="label:'Kelas:',labelWidth:'170px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="AssessmentLessonTeacher" style="width:395px;height:22px;" data-options="label:'Guru:',labelWidth:'170px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="AssessmentLessonStatus" style="width:395px;height:22px;" data-options="label:'Status Guru:',labelWidth:'170px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <select id="AssessmentLessonId" class="easyui-combogrid" style="width:395px;height:22px;" data-options="
                                            label:'<b>*</b>Pelajaran:',
                                            labelWidth:'170px',
                                            panelWidth: 830,
                                            idField: 'seq',
                                            textField: 'lesson',
                                            fitColumns:true,
                                            columns: [[
                                                {field:'department',title:'Departemen',width:150},
                                                {field:'grade',title:'Tingkat',width:80,align:'center',sortable:true},
                                                {field:'school_year',title:'Thn. Ajaran',width:120,align:'center',sortable:true},
                                                {field:'semester',title:'Semester',width:100,align:'center',sortable:true},
                                                {field:'class',title:'Kelas',width:100,sortable:true},
                                                {field:'lesson',title:'Pelajaran',width:200,sortable:true},
                                                {field:'employee_name',title:'Guru',width:200,sortable:true},
                                                {field:'status',title:'Status',width:120,sortable:true},
                                            ]],
                                        ">
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input class="easyui-textbox" id="AssessmentLessonScoreAspect" style="width:395px;height:22px;" data-options="label:'Aspek Penilaian:',labelWidth:'170px',readonly:true" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="assessment_id" class="easyui-combobox" id="AssessmentLessonExamType" style="width:395px;height:22px;" data-options="label:'<b>*</b>Jenis Ujian:',labelWidth:'170px',panelHeight:78,valueField:'id',textField:'text',groupField:'group'">
                                            <option value="">---</option>
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="code" class="easyui-textbox" style="width:395px;height:22px;" data-options="label:'<b>*</b>Kode Ujian:',labelWidth:'170px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="date" id="AssessmentLessonDate" class="easyui-datebox" style="width:280px;height:22px;" data-options="label:'<b>*</b>Tanggal Ujian:',labelWidth:'170px',formatter:dateFormatter,parser:dateParser" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="lesson_plan_id" id="AssessmentLessonPlan" class="easyui-combobox" style="width:395px;height:22px;" data-options="label:'<b>*</b>RPP:',labelWidth:'170px',panelHeight:113,valueField:'id',textField:'text'">
                                            <option value="">---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-7">
                                    <div class="mb-2">
                                        <input name="description" class="easyui-textbox" style="width:576px;height:22px;" data-options="label:'Materi:',labelWidth:'80px',multiline:true" />
                                    </div>
                                    <div class="mb-1">
                                        <table id="tb-assessment-lesson-form" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}" 
                                            data-options="method:'post',rownumbers:'true',queryParams: { _token: '{{ csrf_token() }}' }">
                                            <thead>
                                                <tr>
                                                    <th data-options="field:'id',width:50,hidden:true">ID</th>
                                                    <th data-options="field:'student_no',width:90,sortable:true,resizeable:true,align:'center'">NIS</th>
                                                    <th data-options="field:'name',width:190,sortable:true,resizeable:true,align:'left'">Nama</th>
                                                    <th data-options="field:'score',width:100,resizeable:true,align:'center',editor:{type:'numberbox',options:{precision:2}}">Nilai</th>
                                                    <th data-options="field:'remark',width:190,resizeable:true,align:'left',editor:'text'">Keterangan</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="viewScore" title="Tabel Nilai" class="content-doc pt-2"></div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- dialog --}}
<div id="assessment-lesson-form-w" class="easyui-window" title="Cetak Form Penilaian Pelajaran" style="width:820px;height:275px" data-options="iconCls:'ms-Icon ms-Icon--Print',modal:true,closed:true,maximizable:false,minimizable:false">
    <input type="hidden" id="id-assessment-lesson-form-department" name="department_id" value="-1" />
    <input type="hidden" id="id-assessment-lesson-form-semester" name="semester_id" value="-1" />
    <input type="hidden" id="id-assessment-lesson-form-employee" name="employee_id" value="-1" />
    <input type="hidden" id="id-assessment-lesson-form-teacher_type" name="status_id" value="-1" />
    <input type="hidden" id="id-assessment-lesson-form-score_aspect" name="score_aspect_id" value="-1" />
    <input type="hidden" id="id-assessment-lesson-form-exam" name="lesson_exam_id" value="-1" />
    <input type="hidden" id="id-assessment-lesson-form-class" name="class_id" value="-1" />
    <div class="p-3">
        <div class="mb-1">
            <select id="assessmentLessonFormId" class="easyui-combogrid" style="width:380px;height:22px;" data-options="
                label:'<b>*</b>Pelajaran:',
                labelWidth:'150px',
                panelWidth: 850,
                idField: 'seq',
                textField: 'lesson',
                fitColumns:true,
                columns: [[
                    {field:'department',title:'Departemen',width:150},
                    {field:'grade',title:'Tingkat',width:80,align:'center',sortable:true},
                    {field:'school_year',title:'Thn. Ajaran',width:120,align:'center',sortable:true},
                    {field:'semester',title:'Semester',width:100,align:'center',sortable:true},
                    {field:'class',title:'Kelas',width:150,sortable:true},
                    {field:'lesson',title:'Pelajaran',width:150,sortable:true},
                    {field:'employee_name',title:'Guru',width:200,sortable:true},
                    {field:'status',title:'Status',width:150,sortable:true},
                ]],
            ">
            </select>
            <span class="mr-2"></span>
            <input class="easyui-textbox" id="assessmentLessonFormClass" style="width:380px;height:22px;" data-options="label:'Kelas:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="assessmentLessonFormDept" style="width:380px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
            <span class="mr-2"></span>
            <input class="easyui-textbox" id="assessmentLessonFormTeacher" style="width:380px;height:22px;" data-options="label:'Guru:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="assessmentLessonFormGrade" style="width:380px;height:22px;" data-options="label:'Tingkat:',labelWidth:'150px',readonly:true" />
            <span class="mr-2"></span>
            <input class="easyui-textbox" id="assessmentLessonFormStatus" style="width:380px;height:22px;" data-options="label:'Status Guru:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="assessmentLessonFormSchoolYear" style="width:380px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'150px',readonly:true" />
            <span class="mr-2"></span>
            <input class="easyui-textbox" id="assessmentLessonFormScoreAspect" style="width:380px;height:22px;" data-options="label:'Aspek Penilaian:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="assessmentLessonFormSemester" style="width:380px;height:22px;" data-options="label:'Semester:',labelWidth:'150px',readonly:true" />
            <span class="mr-2"></span>
            <select class="easyui-combobox" id="assessmentLessonFormExamType" style="width:380px;height:22px;" data-options="label:'<b>*</b>Jenis Ujian:',labelWidth:'150px',panelHeight:78,valueField:'id',textField:'text',groupField:'group'">
                <option value="">---</option>
            </select>
        </div>
        <hr/>
        <div class="text-center">
            <div class="mb-2">
                <a href="javascript:void(0)" class="easyui-linkbutton small-btn text-left" data-options="iconCls:'ms-Icon ms-Icon--Print'" onclick="printAssessmentLessonFormScore()" style="height:22px;width: 280px;">Cetak Form Pengisian Nilai Santri</a>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton small-btn text-left" data-options="iconCls:'ms-Icon ms-Icon--Print'" onclick="printAssessmentLessonFormScoreFinal()" style="height:22px;width: 280px;">Cetak Form Pengisian Nilai Akhir Santri</a>
            </div>
            <div class="mb-2">
                <a href="javascript:void(0)" class="easyui-linkbutton small-btn text-left" data-options="iconCls:'ms-Icon ms-Icon--Print'" onclick="printAssessmentLessonFormScoreReport()" style="height:22px;width: 280px;">Cetak Form Pengisian Nilai Rapor Santri</a>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton small-btn text-left" data-options="iconCls:'ms-Icon ms-Icon--Print'" onclick="printAssessmentLessonFormReportComment()" style="height:22px;width: 280px;">Cetak Form Komentar Rapor Santri</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionAssessmentLesson = document.getElementById("menu-act-assessment-lesson").getElementsByTagName("a")
    var titleAssessmentLesson = document.getElementById("title-assessment-lesson")
    var markAssessmentLesson = document.getElementById("mark-assessment-lesson")
    var idAssessmentLesson = document.getElementById("id-assessment-lesson")
    var tabsAssessmentLesson = $("#tt-assessment-lesson")
    var dgAssessmentLesson = $("#tb-assessment-lesson")
    $(function () {
        sessionStorage.formPenilaian_Pelajaran = "init"
        dgAssessmentLesson.datagrid({
            url: "{{ url('academic/assessment/lesson/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPenilaian_Pelajaran == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleAssessmentLesson.innerText = row.lesson.toUpperCase()
                    actionButtonAssessmentLesson("active",[2,3])
                    $("#AssessmentLessonPlan").combobox("clear")
                    $("#form-assessment-lesson-main").form("load", "{{ url('academic/assessment/lesson/show') }}" + "/" + row.id)
                    tabsAssessmentLesson.tabs("select", 0)
                    $("#tt-assessment-lesson").waitMe("hide")
                }
            }
        })
        dgAssessmentLesson.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgAssessmentLesson.datagrid('getPager').pagination())
        actionButtonAssessmentLesson("{{ $ViewType }}", [])
        actionTabAssessmentLesson("{{ $ViewType }}")
        $("#AssessmentLessonId").combogrid('grid').datagrid({
            url: '{{ url('academic/lesson/schedule/teaching/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                titleAssessmentLesson.innerText = row.lesson
                $("#id-assessment-lesson-semester").val(row.semester_id)
                $("#id-assessment-lesson-employee").val(row.employee_id)
                $("#id-assessment-lesson-teacher_type").val(row.teaching_status)
                $("#id-assessment-lesson-id").val(row.seq)
                $("#id-assessment-lesson-class").val(row.id_class)
                var periods = row.period.slice(1,-1).split("|")
                $("#id-assessment-lesson-start").val(periods[0])
                $("#id-assessment-lesson-end").val(periods[1])
                $("#AssessmentLessonDept").textbox("setValue", row.department)
                $("#AssessmentLessonGrade").textbox("setValue", row.grade + " - " + row.school_year)
                $("#AssessmentLessonSemester").textbox("setValue", row.semester)
                $("#AssessmentLessonClass").textbox("setValue", row.class)
                $("#AssessmentLessonTeacher").textbox("setValue", row.employee_name)
                $("#AssessmentLessonStatus").textbox("setValue", row.status)
                $("#AssessmentLessonId").combogrid("hidePanel")
                $("#tb-assessment-lesson-form").datagrid("load", "{{ url('academic/student/list') }}" + "?fclass=" + row.id_class + "&_token=" + "{{ csrf_token() }}")
                $("#AssessmentLessonExamType").combobox("reload", "{{ url('academic/lesson/assessment/combo-box') }}" +"?lesson_id="+ row.lesson_id + "&employee_id=" + row.employee_id + "&_token=" + "{{ csrf_token() }}")
                $("#AssessmentLessonScoreAspect").textbox("setValue", "")
                $("#AssessmentLessonExamType").combobox("setValue", "")
                $("#AssessmentLessonDate").datebox().datebox("calendar").calendar({
                    validator: function(date){
                        var periods = row.period.slice(1,-1).split("|")
                        var now = new Date();
                        let starts = periods[0].split("-")
                        let ends = periods[1].split("-")
                        var d1 = new Date(starts[0], parseInt(starts[1]) - 1, starts[2])
                        var d2 = new Date(ends[0], parseInt(ends[1]) - 1, ends[2])
                        return d1<=date && date<=d2;
                    }
                })
                $("#AssessmentLessonPlan").combobox("clear")
                $("#AssessmentLessonPlan").combobox("reload", "{{ url('academic/lesson/plan/combo-box') }}" +"?department_id="+ row.department_id + "&grade_id=" + row.grade_id + "&semester_id=" + row.semester_id + "&lesson_id=" + row.lesson_id + "&_token=" + "{{ csrf_token() }}")
            }
        })
        $("#assessmentLessonFormId").combogrid('grid').datagrid({
            url: '{{ url('academic/lesson/schedule/teaching/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#id-assessment-lesson-form-department").val(row.department_id)
                $("#id-assessment-lesson-form-semester").val(row.semester_id)
                $("#id-assessment-lesson-form-employee").val(row.employee_id)
                $("#id-assessment-lesson-form-teacher_type").val(row.teaching_status)
                $("#id-assessment-lesson-form-class").val(row.id_class)
                $("#assessmentLessonFormDept").textbox("setValue", row.department)
                $("#assessmentLessonFormGrade").textbox("setValue", row.grade)
                $("#assessmentLessonFormSchoolYear").textbox("setValue", row.school_year)
                $("#assessmentLessonFormSemester").textbox("setValue", row.semester)
                $("#assessmentLessonFormClass").textbox("setValue", row.class)
                $("#assessmentLessonFormTeacher").textbox("setValue", row.employee_name)
                $("#assessmentLessonFormStatus").textbox("setValue", row.status)
                $("#assessmentLessonFormId").combogrid("hidePanel")
                $("#assessmentLessonFormExamType").combobox("reload", "{{ url('academic/lesson/assessment/combo-box') }}" +"?lesson_id="+ row.lesson_id + "&employee_id=" + row.employee_id + "&_token=" + "{{ csrf_token() }}")
                $("#assessmentLessonFormScoreAspect").textbox("setValue", "")
                $("#assessmentLessonFormExamType").combobox("setValue", "")
            }
        })
        $("#AssessmentLessonExamType").combobox({
            onSelect: function(record) {
                if (record.value != "") {
                    let ids = record.id.split("-")
                    $("#AssessmentLessonScoreAspect").textbox('setValue', record.group)
                    $("#id-assessment-lesson-exam").val(ids[1])
                    $("#id-assessment-lesson-score_aspect").val(ids[2])
                } 
            }
        })
        $("#assessmentLessonFormExamType").combobox({
            onSelect: function(record) {
                if (record.value != "") {
                    let ids = record.id.split("-")
                    $("#assessmentLessonFormScoreAspect").textbox('setValue', record.group)
                    $("#id-assessment-lesson-form-exam").val(ids[1])
                    $("#id-assessment-lesson-form-score_aspect").val(ids[2])
                } 
            }
        })
        $("#tb-assessment-lesson-form").datagrid("enableCellEditing").datagrid("enableFilter")
        $("#form-assessment-lesson-main").form({
            onLoadSuccess: function(data) {
                $("#id-assessment-lesson-semester").val(data.semester_id)
                $("#id-assessment-lesson-employee").val(data.employee_id)
                $("#id-assessment-lesson-id").val(data.class_id+"-"+data.employee_id+"-"+data.department_id+"-"+data.lesson_id)
                $("#id-assessment-lesson-teacher_type").val(data.status_id)
                $("#id-assessment-lesson-score_aspect").val(data.score_aspect_id)
                $("#id-assessment-lesson-exam").val(data.lesson_exam_id)
                $("#id-assessment-lesson-start").val(data.start_date)
                $("#id-assessment-lesson-end").val(data.end_date)
                $("#AssessmentLessonDept").textbox("setValue", data.department)
                $("#AssessmentLessonGrade").textbox("setValue", data.grade)
                $("#AssessmentLessonSchoolYear").textbox("setValue", data.school_year)
                $("#AssessmentLessonSemester").textbox("setValue", data.semester)
                $("#AssessmentLessonClass").textbox("setValue", data.class)
                $("#AssessmentLessonTeacher").textbox("setValue", data.teacher)
                $("#AssessmentLessonStatus").textbox("setValue", data.status)
                $("#AssessmentLessonId").combogrid("setValue", data.class_id+"-"+data.employee_id+"-"+data.department_id+"-"+data.lesson_id)
                $("#AssessmentLessonExamType").combobox("reload", "{{ url('academic/lesson/assessment/combo-box') }}" +"?lesson_id="+ data.lesson_id + "&employee_id=" + data.employee_id + "&_token=" + "{{ csrf_token() }}")
                $("#AssessmentLessonExamType").combobox("setValue", data.lesson_assessment_id + "-" + data.lesson_exam_id + "-" + data.score_aspect_id)
                $("#tb-assessment-lesson-form").datagrid("load", "{{ url('academic/assessment/lesson/list') }}" + "/" + data.id)
                $("#AssessmentLessonDate").datebox("setValue", data.date)
                $("#AssessmentLessonId").combogrid("readonly", true)
                $("#AssessmentLessonPlan").combobox("reload", "{{ url('academic/lesson/plan/combo-box') }}" +"?department_id="+ data.department_id + "&grade_id=" + data.grade_id + "&semester_id=" + data.semester_id + "&lesson_id=" + data.lesson_id + "&_token=" + "{{ csrf_token() }}")
                $("#AssessmentLessonPlan").combobox("setValue", data.lesson_plan_id)
                actionTabAssessmentLesson("active")
                $("#viewScore").tabs().panel({
                    href: "{{ url('academic/assessment/lesson/score') }}" + "/" + data.lesson_assessment_id + "-" + data.class_id + "-" + data.semester_id + "-" + data.id + "/" + window.innerHeight
                })
            }
        })
        $("#tt-assessment-lesson").waitMe({effect:"none"})
    })
    function filterAssessmentLesson(params) {
        if (Object.keys(params).length > 0) {
            dgAssessmentLesson.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgAssessmentLesson.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newAssessmentLesson() {
        sessionStorage.formPenilaian_Pelajaran = "active"
        $("#form-assessment-lesson-main").form("reset")
        actionButtonAssessmentLesson("active", [0,1,4])
        actionTabAssessmentLesson("init")
        markAssessmentLesson.innerText = "*"
        titleAssessmentLesson.innerText = ""
        idAssessmentLesson.value = "-1"
        $("#AssessmentLessonId").combogrid("readonly", false)
        $("#tb-assessment-lesson-form").datagrid("loadData", [])
        tabsAssessmentLesson.tabs("select", 0)
        $("#tt-assessment-lesson").waitMe("hide")
    }
    function editAssessmentLesson() {
        sessionStorage.formPenilaian_Pelajaran = "active"
        markAssessmentLesson.innerText = "*"
        actionButtonAssessmentLesson("active", [0,1,4])
    }
    function saveAssessmentLesson() {
        if (sessionStorage.formPenilaian_Pelajaran == "active") {
            ajaxAssessmentLesson("academic/assessment/lesson/store")
        }
    }
    function deleteAssessmentLesson() {
        var dg = $("#tb-assessment-lesson-form").datagrid('getData')
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Penilaian Pelajaran terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/assessment/lesson/destroy') }}" +"/"+idAssessmentLesson.value, { 
                    assessment_id: $("#AssessmentLessonExamType").combobox("getValue"), 
                    class_id: $("#id-assessment-lesson-class").val(), 
                    semester_id: $("#id-assessment-lesson-semester").val(), 
                    students: dg.rows, 
                    _token: "{{ csrf_token() }}" 
                }, "json").done(function( response ) {
                    ajaxAssessmentLessonResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxAssessmentLesson(route) {
        var dg = $("#tb-assessment-lesson-form").datagrid('getData')
        $("#form-assessment-lesson-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', students: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-assessment-lesson").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAssessmentLessonResponse(response)
                $("#page-assessment-lesson").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-assessment-lesson").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAssessmentLessonResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearAssessmentLesson()
            $("#tb-assessment-lesson").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearAssessmentLesson() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearAssessmentLesson()
            }
        })
    }
    function actionButtonAssessmentLesson(viewType, idxArray) {
        for (var i = 0; i < menuActionAssessmentLesson.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionAssessmentLesson[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionAssessmentLesson[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionAssessmentLesson[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionAssessmentLesson[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionTabAssessmentLesson(viewType) {
        if (viewType == "init") {
            tabsAssessmentLesson.tabs('disableTab', 1)
        } else {
            tabsAssessmentLesson.tabs('enableTab', 1)
        }
    }
    function actionClearAssessmentLesson() {
        sessionStorage.formPenilaian_Pelajaran = "init"
        $("#form-assessment-lesson-main").form("reset")
        actionButtonAssessmentLesson("init", [])
        actionTabAssessmentLesson("init")
        titleAssessmentLesson.innerText = ""
        markAssessmentLesson.innerText = ""
        idAssessmentLesson.value = "-1"
        $("#AssessmentLessonId").combogrid("readonly", false)
        $("#tb-assessment-lesson-form").datagrid("loadData", [])
        $("#tt-assessment-lesson").waitMe({effect:"none"})
    }
    function exportAssessmentLesson(document) {
        $("#assessment-lesson-form-w").window("open")
    }
    function printAssessmentLessonFormScore() {
        let lessonId = $("#assessmentLessonFormId").combobox('getValue')
        if (lessonId == '') {
            $.messager.alert('Peringatan', 'Pelajaran wajib dipilih.', 'warning')
        } else {
            $.messager.progress({ title: "Ekspor dokumen ke PDF", msg: "Mohon tunggu..." })
            $.post("{{ url('academic/assessment/lesson/form/score/export-pdf') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                department: $("#assessmentLessonFormDept").textbox('getText'),
                school_year: $("#assessmentLessonFormSchoolYear").textbox('getText'),
                semester: $("#assessmentLessonFormSemester").textbox('getText'),
                grade: $("#assessmentLessonFormGrade").textbox('getText'),
                class: $("#assessmentLessonFormClass").textbox('getText'),
                class_id: $("#id-assessment-lesson-form-class").val(),
                lesson: $("#assessmentLessonFormId").textbox('getText'),
                teacher: $("#assessmentLessonFormTeacher").textbox('getText'),
                employee_id : $("#id-assessment-lesson-form-employee").val(),
            }, true), function(response) {
                $.messager.progress("close")
                window.open("/storage/downloads/" + response)
            }).fail(function(){
                $.messager.progress('close')
                $.messager.alert('Peringatan', 'Terjadi gangguan pada Server, silahkan ulangi kembali.', 'error')
            })            
        }
    }
    function printAssessmentLessonFormScoreFinal() {
        let lessonId = $("#assessmentLessonFormId").combobox('getValue')
        let lessons = lessonId.split("-")
        let assessmentId = $("#assessmentLessonFormExamType").combobox('getValue')
        if (lessonId == '') {
            $.messager.alert('Peringatan', 'Pelajaran wajib dipilih.', 'warning')
        } else if (assessmentId == '') {
            $.messager.alert('Peringatan', 'Jenis Pengujian wajib dipilih.', 'warning')
        } else {
            $.messager.progress({ title: "Ekspor dokumen ke PDF", msg: "Mohon tunggu..." })
            $.post("{{ url('academic/assessment/lesson/form/score/final/export-pdf') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                department: $("#assessmentLessonFormDept").textbox('getText'),
                school_year: $("#assessmentLessonFormSchoolYear").textbox('getText'),
                semester: $("#assessmentLessonFormSemester").textbox('getText'),
                grade: $("#assessmentLessonFormGrade").textbox('getText'),
                class: $("#assessmentLessonFormClass").textbox('getText'),
                class_id: $("#id-assessment-lesson-form-class").val(),
                lesson: $("#assessmentLessonFormId").textbox('getText'),
                lesson_id: lessons[3],
                teacher: $("#assessmentLessonFormTeacher").textbox('getText'),
                aspect: $("#assessmentLessonFormScoreAspect").textbox('getText'),
                exam: $("#assessmentLessonFormExamType").combobox('getText'),
                semester_id : $("#id-assessment-lesson-form-semester").val(),
                department_id : $("#id-assessment-lesson-form-department").val(),
                employee_id : $("#id-assessment-lesson-form-employee").val(),
                status_id : $("#id-assessment-lesson-form-teacher_type").val(),
                score_aspect_id : $("#id-assessment-lesson-form-score_aspect").val(),
                lesson_exam_id : $("#id-assessment-lesson-form-exam").val(),
            }, true), function(response) {
                if (response.success) {
                    window.open("/storage/downloads/" + response.message)
                } else {
                    $.messager.alert('Peringatan', response.message, 'warning')
                }
                $.messager.progress("close")
            }).fail(function(){
                $.messager.progress('close')
                $.messager.alert('Peringatan', 'Terjadi gangguan pada Server, silahkan ulangi kembali.', 'error')
            })              
        }
    }
    function printAssessmentLessonFormScoreReport() {
        let lessonId = $("#assessmentLessonFormId").combobox('getValue')
        let lessons = lessonId.split("-")
        if (lessonId == '') {
            $.messager.alert('Peringatan', 'Pelajaran wajib dipilih.', 'warning')
        } else {
            $.messager.progress({ title: "Ekspor dokumen ke PDF", msg: "Mohon tunggu..." })
            $.post("{{ url('academic/assessment/lesson/form/score/report/export-pdf') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                department: $("#assessmentLessonFormDept").textbox('getText'),
                school_year: $("#assessmentLessonFormSchoolYear").textbox('getText'),
                semester: $("#assessmentLessonFormSemester").textbox('getText'),
                grade: $("#assessmentLessonFormGrade").textbox('getText'),
                class: $("#assessmentLessonFormClass").textbox('getText'),
                class_id: $("#id-assessment-lesson-form-class").val(),
                lesson: $("#assessmentLessonFormId").textbox('getText'),
                lesson_id: lessons[3],
                teacher: $("#assessmentLessonFormTeacher").textbox('getText'),
                semester_id : $("#id-assessment-lesson-form-semester").val(),
                department_id : $("#id-assessment-lesson-form-department").val(),
                employee_id : $("#id-assessment-lesson-form-employee").val(),
                score_aspect_id : $("#id-assessment-lesson-form-score_aspect").val(),
            }, true), function(response) {
                $.messager.progress("close")
                window.open("/storage/downloads/" + response)
            }).fail(function(){
                $.messager.progress('close')
                $.messager.alert('Peringatan', 'Terjadi gangguan pada Server, silahkan ulangi kembali.', 'error')
            })              
        }
    }
    function printAssessmentLessonFormReportComment() {
        let lessonId = $("#assessmentLessonFormId").combobox('getValue')
        if (lessonId == '') {
            $.messager.alert('Peringatan', 'Pelajaran wajib dipilih.', 'warning')
        } else {
            $.messager.progress({ title: "Ekspor dokumen ke PDF", msg: "Mohon tunggu..." })
            $.post("{{ url('academic/assessment/lesson/form/report/comment/export-pdf') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                department: $("#assessmentLessonFormDept").textbox('getText'),
                school_year: $("#assessmentLessonFormSchoolYear").textbox('getText'),
                semester: $("#assessmentLessonFormSemester").textbox('getText'),
                grade: $("#assessmentLessonFormGrade").textbox('getText'),
                class: $("#assessmentLessonFormClass").textbox('getText'),
                class_id: $("#id-assessment-lesson-form-class").val(),
                lesson: $("#assessmentLessonFormId").textbox('getText'),
                teacher: $("#assessmentLessonFormTeacher").textbox('getText'),
                employee_id : $("#id-assessment-lesson-form-employee").val(),
                department_id : $("#id-assessment-lesson-form-department").val(),
            }, true), function(response) {
                $.messager.progress("close")
                window.open("/storage/downloads/" + response)
            }).fail(function(){
                $.messager.progress('close')
                $.messager.alert('Peringatan', 'Terjadi gangguan pada Server, silahkan ulangi kembali.', 'error')
            })              
        }
    }
</script>
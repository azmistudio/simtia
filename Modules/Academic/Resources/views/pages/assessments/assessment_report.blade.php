@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
    $SubGridHeight = $InnerHeight - 356 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Perhitungan Nilai Rapor</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-assessment-report" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',readonly:true,labelWidth:100" />
                        <input type="hidden" id="fdept-assessment-report" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-assessment-report" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <select id="fclass-assessment-report" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
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
                    <select id="flesson-assessment-report" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
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
                    <a class="easyui-linkbutton small-btn flist-box" onclick="filterAssessmentReport({fdept: $('#fdept-assessment-report').val(),fclass: $('#fclass-assessment-report').combobox('getValue'),flesson: $('#flesson-assessment-report').combobox('getValue')})">Cari</a>
                    <a class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-assessment-report').form('reset');filterAssessmentReport({})">Batal</a>
                </div>
            </form>
            <table id="tb-assessment-report" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'class',width:75,resizeable:true,sortable:true">Kelas</th>
                        <th data-options="field:'lesson',width:100,resizeable:true">Pelajaran</th>
                        <th data-options="field:'score_aspect',width:150,resizeable:true">Aspek Nilai</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-assessment-report" class="panel-top">
            <a id="newAssessmentReport" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newAssessmentReport()">Baru</a>
            <a id="editAssessmentReport" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editAssessmentReport()">Ubah</a>
            <a id="saveAssessmentReport" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveAssessmentReport()">Simpan</a>
            <a id="clearAssessmentReport" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearAssessmentReport()">Batal</a>
            <a id="deleteAssessmentReport" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteAssessmentReport()">Hapus</a>
            <a id="excelAssessmentReport" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="excelAssessmentReport()">Excel</a>
        </div>
        <div class="title">
            <h6><span id="mark-assessment-report"></span>Pelajaran: <span id="title-assessment-report"></span></h6>
        </div>
        <div id="page-assessment-report" class="pt-3 pb-3">
            <form id="form-assessment-report-main" method="post">
                <div class="container-fluid">
                    <div class="row row-cols-auto">
                        <div class="col-12">
                            <input type="hidden" id="id-assessment-report" name="id" value="-1" />
                            <input type="hidden" id="id-assessment-report-semester" name="semester_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-employee" name="employee_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-class" name="class_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-id" name="lesson_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-exam-lesson" name="lesson_exam_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-grade" name="grade_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-score-aspect" name="score_aspect_id" value="-1" />
                            <div class="mb-1">
                                <input name="department" class="easyui-textbox" id="AssessmentReportDept" style="width:300px;height:22px;" data-options="label:'Departemen:',labelWidth:'120px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="grade" class="easyui-textbox" id="AssessmentReportGrade" style="width:300px;height:22px;" data-options="label:'Tingkat:',labelWidth:'120px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="school_year" class="easyui-textbox" id="AssessmentReportSchoolYear" style="width:300px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'120px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input name="semester" class="easyui-textbox" id="AssessmentReportSemester" style="width:300px;height:22px;" data-options="label:'Semester:',labelWidth:'120px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="class" class="easyui-textbox" id="AssessmentReportClass" style="width:300px;height:22px;" data-options="label:'Kelas:',labelWidth:'120px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="employee" class="easyui-textbox" id="AssessmentReportTeacher" style="width:300px;height:22px;" data-options="label:'Guru:',labelWidth:'120px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select id="AssessmentReportScoreAspect" class="easyui-combogrid" style="width:300px;height:22px;" data-options="
                                    label:'<b>*</b>Aspek Penilaian:',
                                    labelWidth:'120px',
                                    panelWidth: 852,
                                    idField: 'seq',
                                    textField: 'remark',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:110},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'semester',title:'Semester',width:90,align:'center'},
                                        {field:'class',title:'Kelas',width:120},
                                        {field:'lesson',title:'Pelajaran',width:180},
                                        {field:'employee',title:'Guru',width:200},
                                        {field:'remark',title:'Aspek Penilaian',width:200},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input name="lesson" class="easyui-textbox" id="AssessmentReportLesson" style="width:300px;height:22px;" data-options="label:'Pelajaran:',labelWidth:'120px',readonly:true" />
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div id="assessment-report-p" class="easyui-panel" style="width:100%;height:{{ $SubGridHeight }};border:none;"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionAssessmentReport = document.getElementById("menu-act-assessment-report").getElementsByTagName("a")
    var titleAssessmentReport = document.getElementById("title-assessment-report")
    var markAssessmentReport = document.getElementById("mark-assessment-report")
    var idAssessmentReport = document.getElementById("id-assessment-report")
    var dgAssessmentReport = $("#tb-assessment-report")
    $(function () {
        sessionStorage.formPerhitungan_Rapor = "init"
        dgAssessmentReport.datagrid({
            url: "{{ url('academic/assessment/report/formula/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPerhitungan_Rapor == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleAssessmentReport.innerText = row.lesson
                    actionButtonAssessmentReport("active",[2,3])
                    $("#form-assessment-report-main").form("load", "{{ url('academic/assessment/report/formula/show') }}" + "/" + row.id)
                    $("#page-assessment-report").waitMe("hide")
                }
            }
        })
        dgAssessmentReport.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgAssessmentReport.datagrid('getPager').pagination())
        actionButtonAssessmentReport("{{ $ViewType }}", [])
        $("#AssessmentReportScoreAspect").combogrid('grid').datagrid({
            url: '{{ url('academic/assessment/lesson/exam/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#id-assessment-report-semester").val(row.semester_id)
                $("#id-assessment-report-class").val(row.class_id)
                $("#id-assessment-report-employee").val(row.employee_id)
                $("#id-assessment-report-id").val(row.lesson_id)
                $("#id-assessment-report-exam-lesson").val(row.lesson_exam_id)
                $("#id-assessment-report-grade").val(row.grade_id)
                $("#id-assessment-report-score-aspect").val(row.score_aspect_id)
                $("#AssessmentReportDept").textbox("setValue", row.department)
                $("#AssessmentReportGrade").textbox("setValue", row.grade)
                $("#AssessmentReportSchoolYear").textbox("setValue", row.school_year)
                $("#AssessmentReportSemester").textbox("setValue", row.semester)
                $("#AssessmentReportClass").textbox("setValue", row.class)
                $("#AssessmentReportTeacher").textbox("setValue", row.employee)
                $("#AssessmentReportLesson").textbox("setValue", row.lesson)
                $("#AssessmentReportScoreAspect").combogrid("hidePanel")
                $("#assessment-report-p").panel("refresh", "{{ url('academic/assessment/report/formula/load') }}" 
                    + "?h=" + window.innerHeight 
                    + "&score_aspect=" + row.score_aspect_id 
                    + "&employee_id=" + row.employee_id
                    + "&grade_id=" + row.grade_id
                    + "&lesson_id=" + row.lesson_id 
                    + "&class_id=" + row.class_id
                    + "&semester_id=" + row.semester_id
                    + "&lesson_id=" + row.lesson_id
                    + "&exam_report_id=0"
                )
            }
        })
        $("#form-assessment-report-main").form({
            onLoadSuccess: function(data) {
                $("#id-assessment-report-semester").val(data.semester_id)
                $("#id-assessment-report-employee").val(data.employee_id)
                $("#id-assessment-report-id").val(data.lesson_id)    
                $("#AssessmentReportScoreAspect").combogrid("setValue", data.lesson_id.toString()+data.class_id.toString()+data.semester_id.toString()+data.employee_id.toString()+data.grade_id.toString()+data.score_aspect_id.toString())
                $("#assessment-report-p").panel("refresh", "{{ url('academic/assessment/report/formula/load') }}" 
                    + "?h=" + window.innerHeight 
                    + "&score_aspect=" + data.score_aspect_id 
                    + "&employee_id=" + data.employee_id
                    + "&grade_id=" + data.grade_id
                    + "&lesson_id=" + data.lesson_id 
                    + "&class_id=" + data.class_id
                    + "&semester_id=" + data.semester_id
                    + "&lesson_id=" + data.lesson_id
                    + "&exam_report_id=" + data.id
                )
            }
        })
        $("#page-assessment-report").waitMe({effect:"none"})
    })
    function filterAssessmentReport(params) {
        if (Object.keys(params).length > 0) {
            dgAssessmentReport.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgAssessmentReport.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newAssessmentReport() {
        sessionStorage.formPerhitungan_Rapor = "active"
        $("#form-assessment-report-main").form("reset")
        actionButtonAssessmentReport("active", [0,1,4,5])
        markAssessmentReport.innerText = "*"
        titleAssessmentReport.innerText = ""
        idAssessmentReport.value = "-1"
        $("#assessment-report-p").panel("clear")
        $("#page-assessment-report").waitMe("hide")
    }
    function editAssessmentReport() {
        sessionStorage.formPerhitungan_Rapor = "active"
        markAssessmentReport.innerText = "*"
        actionButtonAssessmentReport("active", [0,1,4,5])
    }
    function saveAssessmentReport() {
        if (sessionStorage.formPerhitungan_Rapor == "active") {
            ajaxAssessmentReport("academic/assessment/report/formula/store")
        }
    }
    function deleteAssessmentReport() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Perhitungan Nilai Rapor terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/assessment/report/formula/destroy') }}" +"/"+idAssessmentReport.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAssessmentReportResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxAssessmentReport(route) {
        var dg = $("#tb-assessment-report-score").datagrid('getData')
        $("#form-assessment-report-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', students: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-assessment-report").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAssessmentReportResponse(response)
                $("#page-assessment-report").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-assessment-report").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAssessmentReportResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearAssessmentReport()
            $("#tb-assessment-report").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearAssessmentReport() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearAssessmentReport()
            }
        })
    }
    function actionButtonAssessmentReport(viewType, idxArray) {
        for (var i = 0; i < menuActionAssessmentReport.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionAssessmentReport[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionAssessmentReport[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionAssessmentReport[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionAssessmentReport[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearAssessmentReport() {
        sessionStorage.formPerhitungan_Rapor = "init"
        $("#form-assessment-report-main").form("reset")
        actionButtonAssessmentReport("init", [])
        titleAssessmentReport.innerText = ""
        markAssessmentReport.innerText = ""
        idAssessmentReport.value = "-1"
        $("#assessment-report-p").panel("clear")
        $("#page-assessment-report").waitMe({effect:"none"})
    }
    function excelAssessmentReport() {
        $.messager.progress({ title: "Ekspor dokumen ke Excel", msg: "Mohon tunggu..." })
        var dg = $("#tb-assessment-report-score").datagrid('getData')
        $.post("{{ url('academic/assessment/report/formula/export-excel') }}", $.param({ 
            _token: "{{ csrf_token() }}", 
            scores: JSON.stringify(dg.rows),
            grade_id: $("#id-assessment-report-grade").val(),
            class: $("#AssessmentReportClass").textbox('getText'),
            class_id: $("#id-assessment-report-class").val(),
            lesson: $("#AssessmentReportLesson").textbox('getText'),
            lesson_id: $("#id-assessment-report-id").val(),
            semester_id: $("#id-assessment-report-semester").val(),
            employee_id: $("#id-assessment-report-employee").val(),
            score_aspect: $("#AssessmentReportScoreAspect").combobox('getText'),
            score_aspect_id: $("#id-assessment-report-score-aspect").val(),
        }, true), function(response) {
            $.messager.progress("close")
            window.open("/storage/downloads/" + response)
        })            
    }
</script>
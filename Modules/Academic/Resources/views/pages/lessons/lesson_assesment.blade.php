@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Aturan Perhitungan Nilai Rapor Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportLessonAssesment('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-lesson-assesment" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-lesson-assesment" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-lesson-assesment" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-lesson-assesment" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Guru:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="flesson-lesson-assesment" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Pelajaran:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterLessonAssesment({fdept: $('#fdept-lesson-assesment').val(),flesson: $('#flesson-lesson-assesment').val(),fname: $('#fname-lesson-assesment').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-lesson-assesment').form('clear');filterLessonAssesment({})">Batal</a>
                </div>
            </form>
            <table id="tb-lesson-assesment" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'grade_id',width:40,resizeable:true,sortable:true">Tingkat</th>
                        <th data-options="field:'employee_id',width:120,resizeable:true,sortable:true">Guru</th>
                        <th data-options="field:'lesson_id',width:100,resizeable:true,sortable:true">Pelajaran</th>
                        <th data-options="field:'score_aspect_id',width:140,resizeable:true,sortable:true">Aspek Penilaian</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-lesson-assesment" class="panel-top">
            <a id="newLessonAssesment" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newLessonAssesment()">Baru</a>
            <a id="editLessonAssesment" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editLessonAssesment()">Ubah</a>
            <a id="saveLessonAssesment" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveLessonAssesment()">Simpan</a>
            <a id="clearLessonAssesment" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearLessonAssesment()">Batal</a>
            <a id="deleteLessonAssesment" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteLessonAssesment()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-lesson-assesment"></span>Guru Pelajaran: <span id="title-lesson-assesment"></span></h6>
        </div>
        <div id="page-lesson-assesment" class="pt-3 pb-3">
            <form id="form-lesson-assesment-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-5">
                            <input type="hidden" id="id-lesson-assesment" name="id" value="-1" />
                            <input type="hidden" id="deptid-lesson-assesment" value="-1" />
                            <input type="hidden" name="lesson_id" id="lessonid-lesson-assesment" value="-1" />
                            <input type="hidden" name="employee_id" id="id-lesson-assesment-employee" value="-1" />
                            <input type="hidden" name="grade_id" id="id-lesson-assesment-grade" value="-1" />
                            <div class="mb-1">
                                <input id="LessonAssesmentDeptId" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="LessonAssesmentLessonId" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Pelajaran:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="LessonAssesmentGradeId" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Tingkat:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select id="LessonAssesmentEmployeeId" class="easyui-combogrid" style="width:375px;height:22px;" data-options="
                                    label:'<b>*</b>Guru Pelajaran:',
                                    labelWidth:'150px',
                                    panelWidth: 580,
                                    idField: 'seq',
                                    textField: 'employee',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:100,sortable:true},
                                        {field:'lesson',title:'Pelajaran',width:170},
                                        {field:'employee',title:'Guru',width:230},
                                        {field:'grade',title:'Tingkat',width:100,sortable:true},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="score_aspect_id" id="LessonAssesmentScoreId" class="easyui-combobox" style="width:375px;height:22px;" data-options="label:'<b>*</b>Aspek Penilaian:',labelWidth:'150px',labelPosition:'before',panelHeight:125">
                                    <option value="">---</option>
                                    @foreach ($scores as $score)
                                    <option value="{{ $score->id }}">{{ $score->remark }}</option>
                                    @endforeach
                                </select>  
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="" style="width:100%;">
                                <fieldset>
                                    <legend><b>Bobot Penilaian (%)</b></legend>
                                    <div id="LessonAssesmentScoreWeight"></div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionLessonAssesment = document.getElementById("menu-act-lesson-assesment").getElementsByTagName("a")
    var titleLessonAssesment = document.getElementById("title-lesson-assesment")
    var markLessonAssesment = document.getElementById("mark-lesson-assesment")
    var idLessonAssesment = document.getElementById("id-lesson-assesment")
    var dgLessonAssesment = $("#tb-lesson-assesment")
    var weights = []
    $(function () {
        sessionStorage.formAturan_Penilaian = "init"
        dgLessonAssesment.datagrid({
            url: "{{ url('academic/lesson/assessment/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formAturan_Penilaian == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleLessonAssesment.innerText = row.employee_id
                    actionButtonLessonAssesment("active",[2,3])
                    $("#id-lesson-assesment-grade").val(row.grade)
                    $("#LessonAssesmentEmployeeId").combogrid('setValue', row.seq)
                    $("#LessonAssesmentScoreId").combobox('setValue', row.score_aspect)
                    $('#LessonAssesmentDeptId').textbox('setValue', row.department)
                    $('#LessonAssesmentLessonId').textbox('setValue', row.lesson_id)
                    $('#LessonAssesmentGradeId').textbox('setValue', row.grade_id)
                    $("#form-lesson-assesment-main").form("load", "{{ url('academic/lesson/assessment/show') }}" + "/" + row.employee + "/" + row.grade + "/" + row.lesson + "/" + row.score_aspect)
                    $("#page-lesson-assesment").waitMe("hide")
                }
            }
        })
        dgLessonAssesment.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgLessonAssesment.datagrid('getPager').pagination())
        actionButtonLessonAssesment("{{ $ViewType }}", [])
        $("#LessonAssesmentEmployeeId").combogrid({
            url: '{{ url('academic/teacher/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, record) {
                titleLessonAssesment.innerText = record.employee
                $('#deptid-lesson-assesment').val(record.department_id)
                $("#id-lesson-assesment-employee").val(record.employee_id)
                $("#id-lesson-assesment-grade").val(record.grade_id)
                $('#lessonid-lesson-assesment').val(record.lesson_id)
                $('#LessonAssesmentDeptId').textbox('setValue', record.department)
                $('#LessonAssesmentLessonId').textbox('setValue', record.lesson)
                $("#LessonAssesmentGradeId").textbox('setValue', record.grade)
                $("#LessonAssesmentScoreWeight").html("")
            }
        })
        $("#LessonAssesmentScoreId").combobox({
            onSelect: function(record) {
                if (record.value != "") {
                    $("#LessonAssesmentScoreWeight").html("")
                    getLessonAssessmentScoreWeight($("#lessonid-lesson-assesment").val(), record.value, [])
                } else {
                    $("#LessonAssesmentScoreWeight").html("")
                }
            }
        })
        $("#form-lesson-assesment-main").form({
            onLoadSuccess: function(data) {
                idLessonAssesment.value = 1
                $("#id-lesson-assesment-employee").val(data[0].employee_id)
                $('#deptid-lesson-assesment').val(data[0].department_id)
                $('#lessonid-lesson-assesment').val(data[0].lesson_id)
                $("#LessonAssesmentScoreWeight").html("")
                weights = []
                for (var i = 0; i < data.length; i++) {
                    let weight = {
                        "exam_id": data[i].exam_id,
                        "id": data[i].id,
                        "value": data[i].value
                    }
                    weights.push(weight)
                }
                getLessonAssessmentScoreWeight(data[0].lesson_id, data[0].score_aspect_id, weights)
            }
        })
        $("#page-lesson-assesment").waitMe({effect:"none"})
    })
    function getLessonAssessmentScoreWeight(id, aspect_id, weights) {
        $.get("{{ url('academic/lesson/exam/list') }}" +"/"+ id + "/" + aspect_id, function (response, status) {
            for (let i = 0; i < response.length; i++) {
                $("#LessonAssesmentScoreWeight").append("<div class='mb-1'><input type='hidden' name='valueopts[]' value='"+response[i].id+"' /><input type='hidden' id='val"+response[i].id+"' name='assesment_id[]' value='' /><input name='weights[]' id='col"+response[i].id+"' class='easyui-numberbox' style='width:200px;height:22px;' label='"+response[i].subject.toUpperCase()+":' labelWidth='139px' /></div>")
                $("#col"+response[i].id).numberbox({ precision: 1 })
            }
            if (weights.length > 0) {
                for (var i = 0; i < weights.length; i++) {
                    $("#col"+weights[i].exam_id).numberbox("setValue", weights[i].value)
                    $("#val"+weights[i].exam_id).val(weights[i].id)

                }
            }
        })
    }
    function filterLessonAssesment(params) {
        if (Object.keys(params).length > 0) {
            dgLessonAssesment.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgLessonAssesment.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newLessonAssesment() {
        sessionStorage.formAturan_Penilaian = "active"
        $("#form-lesson-assesment-main").form("reset")
        actionButtonLessonAssesment("active", [0,1,4])
        markLessonAssesment.innerText = "*"
        titleLessonAssesment.innerText = ""
        idLessonAssesment.value = "-1"
        $("#LessonAssesmentScoreWeight").html("")
        $("#page-lesson-assesment").waitMe("hide")
    }
    function editLessonAssesment() {
        sessionStorage.formAturan_Penilaian = "active"
        markLessonAssesment.innerText = "*"
        actionButtonLessonAssesment("active", [0,1,4])
    }
    function saveLessonAssesment() {
        if (sessionStorage.formAturan_Penilaian == "active") {
            ajaxLessonAssesment("academic/lesson/assessment/store")
        }
    }
    function deleteLessonAssesment() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Aturan Penilaian terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                let employee_id = $("#id-lesson-assesment-employee").val()
                let grade_id = $("#LessonAssesmentGradeId").combobox('getValue')
                let lesson_id = $("#lessonid-lesson-assesment").val()
                let score_aspect_id = $("#LessonAssesmentScoreId").combobox('getValue')
                $.post("{{ url('academic/lesson/assessment/destroy') }}" +"/"+employee_id+"/"+grade_id+"/"+lesson_id+"/"+score_aspect_id, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxLessonAssesmentResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxLessonAssesment(route) {
        $("#form-lesson-assesment-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-lesson-assesment").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxLessonAssesmentResponse(response)
                $("#page-lesson-assesment").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-lesson-assesment").waitMe("hide")
            }
        })
        return false
    }
    function ajaxLessonAssesmentResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearLessonAssesment()
            $("#tb-lesson-assesment").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearLessonAssesment() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearLessonAssesment()
            }
        })
    }
    function actionButtonLessonAssesment(viewType, idxArray) {
        for (var i = 0; i < menuActionLessonAssesment.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionLessonAssesment[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionLessonAssesment[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionLessonAssesment[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionLessonAssesment[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearLessonAssesment() {
        sessionStorage.formAturan_Penilaian = "init"
        $("#form-lesson-assesment-main").form("reset")
        actionButtonLessonAssesment("init", [])
        titleLessonAssesment.innerText = ""
        markLessonAssesment.innerText = ""
        idLessonAssesment.value = "-1"
        $("#LessonAssesmentScoreWeight").html("")
        $("#page-lesson-assesment").waitMe({effect:"none"})
    }
    function exportLessonAssesment(document) {
        var dg = $("#tb-lesson-assesment").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/lesson/assessment/export-') }}" + document,dg.rows,"Ekspor data Aturan Penilaian ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
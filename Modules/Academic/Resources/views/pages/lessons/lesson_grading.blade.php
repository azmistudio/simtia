@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Aturan Grading Rapor Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportLessonGrading('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-lesson-grading" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-lesson-grading" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-lesson-grading" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-lesson-grading" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Guru:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="flesson-lesson-grading" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Pelajaran:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterLessonGrading({fdept: $('#fdept-lesson-grading').val(),flesson: $('#flesson-lesson-grading').val(),fname: $('#fname-lesson-grading').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-lesson-grading').form('reset');filterLessonGrading({})">Batal</a>
                </div>
            </form>
            <table id="tb-lesson-grading" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
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
        <div id="menu-act-lesson-grading" class="panel-top">
            <a id="newLessonGrading" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newLessonGrading()">Baru</a>
            <a id="editLessonGrading" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editLessonGrading()">Ubah</a>
            <a id="saveLessonGrading" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveLessonGrading()">Simpan</a>
            <a id="clearLessonGrading" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearLessonGrading()">Batal</a>
            <a id="deleteLessonGrading" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteLessonGrading()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-lesson-grading"></span>Guru Pelajaran: <span id="title-lesson-grading"></span></h6>
        </div>
        <div id="page-lesson-grading" class="pt-3 pb-3"> 
            <form id="form-lesson-grading-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-5">
                            <input type="hidden" id="id-lesson-grading" name="id" value="-1" />
                            <input type="hidden" id="deptid-lesson-grading" value="-1" />
                            <input type="hidden" name="lesson_id" id="lessonid-lesson-grading" value="-1" />
                            <input type="hidden" name="employee_id" id="id-lesson-grading-employee" value="-1" />
                            <input type="hidden" name="grade_id" id="id-lesson-grading-grade" value="-1" />
                            <div class="mb-1">
                                <input id="LessonGradingDeptId" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="LessonGradingLessonId" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Pelajaran:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="LessonGradingGradeId" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Tingkat:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select id="LessonGradingEmployeeId" class="easyui-combogrid" style="width:375px;height:22px;" data-options="
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
                                <select name="score_aspect_id" id="LessonGradingScoreId" class="easyui-combobox" style="width:375px;height:22px;" data-options="label:'<b>*</b>Aspek Penilaian:',labelWidth:'150px',labelPosition:'before',panelHeight:125">
                                    <option value="">---</option>
                                    @foreach ($scores as $score)
                                    <option value="{{ $score->id }}">{{ $score->remark }}</option>
                                    @endforeach
                                </select>       
                            </div>
                            <div class="mb-1" style="margin-left:150px;">                                 
                                <input name="is_all" id="LessonGradingScoreAll" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Semua Aspek Penilaian',labelWidth:'160px',labelPosition:'after'" />
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="mb-1">
                                <table id="tb-lesson-grading-form" class="easyui-datagrid" style="width:100%;height:300px"
                                       data-options="method:'post',rownumbers:'true',title:'Aturan Grading'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'id',width:50,hidden:true">ID</th>
                                            <th data-options="field:'min',width:100,resizeable:true,align:'center',editor:{type:'numberbox',options:{precision:1}}">Minimal</th>
                                            <th data-options="field:'max',width:100,resizeable:true,align:'center',editor:{type:'numberbox',options:{precision:1}}">Maksimal</th>
                                            <th data-options="field:'grade',width:100,resizeable:true,align:'center',editor:'text'">Grade</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div>
                                <ul class="well" style="font-size:13px;">
                                    <li><strong>Klik pada kolom untuk mengisi data</strong></li>
                                    <li><strong>Nilai desimal dipisahkan tanda titik (.)</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionLessonGrading = document.getElementById("menu-act-lesson-grading").getElementsByTagName("a")
    var titleLessonGrading = document.getElementById("title-lesson-grading")
    var markLessonGrading = document.getElementById("mark-lesson-grading")
    var idLessonGrading = document.getElementById("id-lesson-grading")
    var dgLessonGrading = $("#tb-lesson-grading")
    var initDataLessonGrading = [
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
        {id:'', min:'', max:'', grade: ''},
    ]
    $(function () {
        sessionStorage.formAturan_Grading = "init"
        dgLessonGrading.datagrid({
            url: "{{ url('academic/lesson/grading/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formAturan_Grading == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleLessonGrading.innerText = row.employee_id
                    actionButtonLessonGrading("active",[2,3])
                    $("#LessonGradingEmployeeId").combogrid("setValue", row.seq)
                    $("#LessonGradingScoreId").combobox("setValue", row.score_aspect)
                    $("#LessonGradingDeptId").textbox("setValue", row.department)
                    $("#LessonGradingLessonId").textbox("setValue", row.lesson_id)
                    $("#LessonGradingGradeId").textbox('setValue', row.grade_id)
                    $("#form-lesson-grading-main").form("load", "{{ url('academic/lesson/grading/show') }}" + "/" + row.employee + "/" + row.grade + "/" + row.lesson + "/" + row.score_aspect)
                    $("#page-lesson-grading").waitMe("hide")
                    $("#LessonGradingScoreAll").checkbox("disable")
                }
            }
        })
        dgLessonGrading.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgLessonGrading.datagrid('getPager').pagination())
        actionButtonLessonGrading("{{ $ViewType }}", [])
        $("#LessonGradingEmployeeId").combogrid({
            url: '{{ url('academic/teacher/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },                                
            onClickRow: function(index, record) {
                titleLessonGrading.innerText = record.employee
                $('#LessonGradingDeptId').textbox('setValue', record.department)
                $('#LessonGradingLessonId').textbox('setValue', record.lesson)
                $('#LessonGradingGradeId').textbox('setValue', record.grade)
                $('#deptid-lesson-grading').val(record.department_id)
                $('#id-lesson-grading-employee').val(record.employee_id)
                $('#id-lesson-grading-grade').val(record.grade_id)
                $('#lessonid-lesson-grading').val(record.lesson_id)
            }
        })
        $('#tb-lesson-grading-form').datagrid({ data: 
            [
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
            ]
        })
        $("#tb-lesson-grading-form").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 1,
            field: 'min'
        })
        $("#form-lesson-grading-main").form({
            onLoadSuccess: function(data) {
                idLessonGrading.value = 1
                $('#id-lesson-grading-employee').val(data[0].employee_id)
                $('#id-lesson-grading-grade').val(data[0].grade_id)
                $('#lessonid-lesson-grading').val(data[0].lesson_id)
                $("#deptid-lesson-grading").val(data[0].department_id)
                $('#tb-lesson-grading-form').datagrid({ data: 
                    [
                        {id:data[0].id, min:data[0].min, max:data[0].max, grade: data[0].grade},
                        {id:data[1].id, min:data[1].min, max:data[1].max, grade: data[1].grade},
                        {id:data[2].id, min:data[2].min, max:data[2].max, grade: data[2].grade},
                        {id:data[3].id, min:data[3].min, max:data[3].max, grade: data[3].grade},
                        {id:data[4].id, min:data[4].min, max:data[4].max, grade: data[4].grade},
                        {id:data[5].id, min:data[5].min, max:data[5].max, grade: data[5].grade},
                        {id:data[6].id, min:data[6].min, max:data[6].max, grade: data[6].grade},
                        {id:data[7].id, min:data[7].min, max:data[7].max, grade: data[7].grade},
                        {id:data[8].id, min:data[8].min, max:data[8].max, grade: data[8].grade},
                        {id:data[9].id, min:data[9].min, max:data[9].max, grade: data[9].grade},
                    ]
                })
            }
        })
        $("#page-lesson-grading").waitMe({effect:"none"})
    })
    function filterLessonGrading(params) {
        if (Object.keys(params).length > 0) {
            dgLessonGrading.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgLessonGrading.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newLessonGrading() {
        sessionStorage.formAturan_Grading = "active"
        $("#form-lesson-grading-main").form("reset")
        actionButtonLessonGrading("active", [0,1,4])
        markLessonGrading.innerText = "*"
        titleLessonGrading.innerText = ""
        idLessonGrading.value = "-1"
        $('#tb-lesson-grading-form').datagrid({ data: initDataLessonGrading })
        $("#page-lesson-grading").waitMe("hide")
        $("#LessonGradingScoreAll").checkbox("enable")
    }
    function editLessonGrading() {
        sessionStorage.formAturan_Grading = "active"
        markLessonGrading.innerText = "*"
        actionButtonLessonGrading("active", [0,1,4])
    }
    function saveLessonGrading() {
        if (sessionStorage.formAturan_Grading == "active") {
            ajaxLessonGrading("academic/lesson/grading/store")
        }
    }
    function deleteLessonGrading() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Aturan Grading terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                let employee_id = $('#id-lesson-grading-employee').val()
                let grade_id = $("#id-lesson-grading-grade").val()
                let lesson_id = $("#lessonid-lesson-grading").val()
                let score_aspect_id = $("#LessonGradingScoreId").combobox('getValue')
                $.post("{{ url('academic/lesson/grading/destroy') }}" +"/"+employee_id+"/"+grade_id+"/"+lesson_id+"/"+score_aspect_id, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxLessonGradingResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxLessonGrading(route) {
        var dg = $("#tb-lesson-grading-form").datagrid('getData')
        if (dg.rows[0].min != '' && dg.rows[0].max != '' && dg.rows[0].grade != '') {
            $("#form-lesson-grading-main").ajaxSubmit({
                url: route,
                data: { _token: '{{ csrf_token() }}', grades: dg.rows },
                beforeSubmit: function(formData, jqForm, options) {
                    $("#page-lesson-grading").waitMe({effect:"facebook"})
                },
                success: function(response) {
                    ajaxLessonGradingResponse(response)
                    $("#page-lesson-grading").waitMe("hide")
                },
                error: function(xhr) {
                    failResponse(xhr)
                    $("#page-lesson-grading").waitMe("hide")
                }
            })
        } else {
            $.messager.alert('Peringatan', 'Minimal mengisi satu Aturan Grading.', 'error')
        }
        return false
    }
    function ajaxLessonGradingResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearLessonGrading()
            $("#tb-lesson-grading").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearLessonGrading() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearLessonGrading()
            }
        })
    }
    function actionButtonLessonGrading(viewType, idxArray) {
        for (var i = 0; i < menuActionLessonGrading.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionLessonGrading[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionLessonGrading[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionLessonGrading[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionLessonGrading[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearLessonGrading() {
        sessionStorage.formAturan_Grading = "init"
        $("#form-lesson-grading-main").form("reset")
        actionButtonLessonGrading("init", [])
        titleLessonGrading.innerText = ""
        markLessonGrading.innerText = ""
        idLessonGrading.value = "-1"
        $('#tb-lesson-grading-form').datagrid({ data: 
            [
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
                {id:'', min:'', max:'', grade: ''},
            ]
        })
        $("#page-lesson-grading").waitMe({effect:"none"})
    }
    function exportLessonGrading(document) {
        var dg = $("#tb-lesson-grading").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/lesson/grading/export-') }}" + document,dg.rows,"Ekspor data Aturan Grading ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
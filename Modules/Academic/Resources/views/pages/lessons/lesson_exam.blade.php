@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Jenis Pengujian</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportLessonExam('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-lesson-exam" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-lesson-exam" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-lesson-exam" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fcode-lesson-exam" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Kode:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="fname-lesson-exam" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterLessonExam({fdept: $('#fdept-lesson-exam').val(),fcode: $('#fcode-lesson-exam').val(),fname: $('#fname-lesson-exam').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-lesson-exam').form('reset');filterLessonExam({})">Batal</a>
                </div>
            </form>
            <table id="tb-lesson-exam" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'lesson_id',width:120,resizeable:true,sortable:true">Pelajaran</th>
                        <th data-options="field:'code',width:60,resizeable:true,sortable:true">Kode</th>
                        <th data-options="field:'subject',width:120,resizeable:true,sortable:true">Materi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-lesson-exam" class="panel-top">
            <a id="newLessonExam" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newLessonExam()">Baru</a>
            <a id="editLessonExam" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editLessonExam()">Ubah</a>
            <a id="saveLessonExam" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveLessonExam()">Simpan</a>
            <a id="clearLessonExam" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearLessonExam()">Batal</a>
            <a id="deleteLessonExam" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteLessonExam()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-lesson-exam"></span>Jenis Pengujian: <span id="title-lesson-exam"></span></h6>
        </div>
        <div id="page-lesson-exam" class="pt-3 pb-3">
            <form id="form-lesson-exam-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-lesson-exam" name="id" value="-1" />
                            <input type="hidden" id="id-lesson-exam-dept" name="department_id" value="-1" />
                            <div class="mb-1">
                                <input id="LessonExamDeptId" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="lesson_id" id="LessonExamLessonId" class="easyui-combogrid" style="width:335px;height:22px;" data-options="
                                    label:'<b>*</b>Pelajaran:',
                                    labelWidth:'125px',
                                    panelWidth: 480,
                                    idField: 'id',
                                    textField: 'name',
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'code',title:'Kode',width:80},
                                        {field:'name',title:'Nama',width:270},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="score_aspect_id" id="" class="easyui-combobox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Aspek Penilaian:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                    <option value="">---</option>
                                    @foreach ($scores as $score)
                                    <option value="{{ $score->id }}">{{ $score->remark }}</option>
                                    @endforeach
                                </select>   
                            </div>
                            <div class="mb-1">
                                <input name="code" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Singkatan:',labelWidth:'125px'" />
                                <span class="mr-2"></span>
                                <input name="is_all" id="LessonExamAll" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Semua Pelajaran',labelWidth:'140px',labelPosition:'after'" />
                            </div>
                            <div class="mb-1">
                                <input name="subject" id="LessonExamSubjectId" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Jenis Pengujian:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:335px;height:40px;" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionLessonExam = document.getElementById("menu-act-lesson-exam").getElementsByTagName("a")
    var titleLessonExam = document.getElementById("title-lesson-exam")
    var markLessonExam = document.getElementById("mark-lesson-exam")
    var idLessonExam = document.getElementById("id-lesson-exam")
    var dgLessonExam = $("#tb-lesson-exam")
    $(function () {
        sessionStorage.formJenis_Pengujian = "init"
        dgLessonExam.datagrid({
            url: "{{ url('academic/lesson/exam/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formJenis_Pengujian == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleLessonExam.innerText = row.subject
                    actionButtonLessonExam("active",[2,3])
                    $("#LessonExamDeptId").textbox("setValue", row.department)    
                    $("#form-lesson-exam-main").form("load", "{{ url('academic/lesson/exam/show') }}" + "/" + row.id)
                    $("#LessonExamAll").checkbox("disable")
                    $("#page-lesson-exam").waitMe("hide")
                }
            }
        })
        dgLessonExam.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgLessonExam.datagrid('getPager').pagination())
        actionButtonLessonExam("{{ $ViewType }}", [])
        $("#LessonExamSubjectId").textbox("textbox").bind("keyup", function (e) {
            titleLessonExam.innerText = $(this).val()
        })        
        $("#LessonExamLessonId").combogrid({
            url: '{{ url('academic/lesson/combo-grid') }}',
            method: 'post',
            mode:'remote',
            fitColumns:true,
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function (index, row) {
                $("#id-lesson-exam-dept").val(row.department_id)
                $("#LessonExamDeptId").textbox("setValue", row.department)
            }
        })
        $("#page-lesson-exam").waitMe({effect:"none"})
    })
    function filterLessonExam(params) {
        if (Object.keys(params).length > 0) {
            dgLessonExam.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgLessonExam.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newLessonExam() {
        sessionStorage.formJenis_Pengujian = "active"
        $("#form-lesson-exam-main").form("reset")
        actionButtonLessonExam("active", [0,1,4])
        markLessonExam.innerText = "*"
        titleLessonExam.innerText = ""
        idLessonExam.value = "-1"
        $("#LessonExamAll").checkbox("enable")
        $("#page-lesson-exam").waitMe("hide")
    }
    function editLessonExam() {
        sessionStorage.formJenis_Pengujian = "active"
        markLessonExam.innerText = "*"
        actionButtonLessonExam("active", [0,1,4])
    }
    function saveLessonExam() {
        if (sessionStorage.formJenis_Pengujian == "active") {
            ajaxLessonExam("academic/lesson/exam/store")
        }
    }
    function deleteLessonExam() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Jenis Pengujian terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/lesson/exam/destroy') }}" +"/"+idLessonExam.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxLessonExamResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })   
            }
        })
    }
    function ajaxLessonExam(route) {
        $("#form-lesson-exam-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-lesson-exam").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxLessonExamResponse(response)
                $("#page-lesson-exam").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-lesson-exam").waitMe("hide")
            }
        })
        return false
    }
    function ajaxLessonExamResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearLessonExam()
            $("#tb-lesson-exam").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearLessonExam() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearLessonExam()
            }
        })
    }
    function actionButtonLessonExam(viewType, idxArray) {
        for (var i = 0; i < menuActionLessonExam.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionLessonExam[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionLessonExam[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionLessonExam[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionLessonExam[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearLessonExam() {
        sessionStorage.formJenis_Pengujian = "init"
        $("#form-lesson-exam-main").form("reset")
        actionButtonLessonExam("init", [])
        titleLessonExam.innerText = ""
        markLessonExam.innerText = ""
        idLessonExam.value = "-1"
        $("#page-lesson-exam").waitMe({effect:"none"})
    }
    function exportLessonExam(document) {
        var dg = $("#tb-lesson-exam").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/lesson/exam/export-') }}" + document,dg.rows,"Ekspor data Jenis Pengujian ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
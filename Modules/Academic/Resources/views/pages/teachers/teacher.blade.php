@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Guru Pelajaran</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportTeacher('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-teacher" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-teacher" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-teacher" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-teacher" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Guru:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="flesson-teacher" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Pelajaran:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterTeacher({fdept: $('#fdept-teacher').val(),fname: $('#fname-teacher').val(),flesson: $('#flesson-teacher').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-teacher').form('reset');filterTeacher({})">Batal</a>
                </div>
            </form>
            <table id="tb-teacher" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'employee',width:140,resizeable:true">Guru</th>
                        <th data-options="field:'lesson_id',width:100,resizeable:true,sortable:true">Pelajaran</th>
                        <th data-options="field:'status_id',width:130,resizeable:true,sortable:true">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-teacher" class="panel-top">
            <a id="newTeacher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newTeacher()">Baru</a>
            <a id="editTeacher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editTeacher()">Ubah</a>
            <a id="saveTeacher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveTeacher()">Simpan</a>
            <a id="clearTeacher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearTeacher()">Batal</a>
            <a id="deleteTeacher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteTeacher()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-teacher"></span>Nama: <span id="title-teacher"></span></h6>
        </div>
        <div id="page-teacher" class="pt-3 pb-3">
            <form id="form-teacher-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-teacher" name="id" value="-1" />
                            <div class="mb-1">
                                <input id="TeacherDeptId" class="easyui-textbox" style="width:350px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="lesson_id" id="TeacherLessonId" class="easyui-combogrid" style="width:350px;height:22px;" data-options="
                                    label:'<b>*</b>Pelajaran:',
                                    labelWidth:'125px',
                                    panelWidth: 480,
                                    idField: 'id',
                                    textField: 'name',
                                    fitColumns:true,
                                    queryParams: { _token: '{{ csrf_token() }}' },
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'code',title:'Kode',width:80},
                                        {field:'name',title:'Nama',width:270},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="employee_id" id="TeacherEmployeeId" class="easyui-combogrid" style="width:350px;height:22px;" data-options="
                                    label:'<b>*</b>Guru:',
                                    labelWidth:'125px',
                                    panelWidth: 500,
                                    idField: 'id',
                                    textField: 'name',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'employee_id',title:'NIP',width:80},
                                        {field:'name',title:'Nama',width:200},
                                        {field:'section',title:'Bagian',width:250},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="status_id" id="TeacherStatusId" class="easyui-combobox" style="width:350px;height:22px;" tabindex="10" data-options="label:'<b>*</b>Status Guru:',labelWidth:'125px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'name'">
                                    <option value="">---</option>
                                    @foreach ($status as $stat)
                                    <option value="{{ $stat->id }}">{{ $stat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:350px;height:50px;" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'125px',labelPosition:'before'" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionTeacher = document.getElementById("menu-act-teacher").getElementsByTagName("a")
    var titleTeacher = document.getElementById("title-teacher")
    var markTeacher = document.getElementById("mark-teacher")
    var idTeacher = document.getElementById("id-teacher")
    var dgTeacher = $("#tb-teacher")
    $(function () {
        sessionStorage.formGuru_Pelajaran = "init"
        dgTeacher.datagrid({
            url: "{{ url('academic/teacher/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formGuru_Pelajaran == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleTeacher.innerText = row.employee
                    $("#TeacherDeptId").textbox("setValue", row.department)
                    actionButtonTeacher("active",[2,3])
                    $("#form-teacher-main").form("load", "{{ url('academic/teacher/show') }}" + "/" + row.id)
                    $("#page-teacher").waitMe("hide")
                }
            }
        })
        dgTeacher.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgTeacher.datagrid('getPager').pagination())
        actionButtonTeacher("{{ $ViewType }}", [])
        $("#TeacherLessonId").combogrid({
            url: '{{ url('academic/lesson/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function (index, row) {
                $("#TeacherDeptId").textbox("setValue", row.department)
            }
        })
        $("#TeacherEmployeeId").combogrid({
            url: '{{ url('hr/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function (index, row) {
                titleTeacher.innerText = $("#TeacherEmployeeId").combobox("getText")
            }
        })
        $("#page-teacher").waitMe({effect:"none"})
    })
    function filterTeacher(params) {
        if (Object.keys(params).length > 0) {
            dgTeacher.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgTeacher.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newTeacher() {
        sessionStorage.formGuru_Pelajaran = "active"
        $("#form-teacher-main").form("reset")
        actionButtonTeacher("active", [0,1,4])
        markTeacher.innerText = "*"
        titleTeacher.innerText = ""
        idTeacher.value = "-1"
        $("#TeacherDeptId").combobox('textbox').focus()
        $("#page-teacher").waitMe("hide")
    }
    function editTeacher() {
        sessionStorage.formGuru_Pelajaran = "active"
        markTeacher.innerText = "*"
        actionButtonTeacher("active", [0,1,4])
    }
    function saveTeacher() {
        if (sessionStorage.formGuru_Pelajaran == "active") {
            ajaxTeacher("academic/teacher/store")
        }
    }
    function deleteTeacher() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Guru terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/teacher/destroy') }}" +"/"+idTeacher.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxTeacherResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })  
            }
        })
    }
    function ajaxTeacher(route) {
        $("#form-teacher-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-teacher").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxTeacherResponse(response)
                $("#page-teacher").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-teacher").waitMe("hide")
            }
        })
        return false
    }
    function ajaxTeacherResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearTeacher()
            $("#tb-teacher").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearTeacher() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearTeacher()
            }
        })
    }
    function actionButtonTeacher(viewType, idxArray) {
        for (var i = 0; i < menuActionTeacher.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionTeacher[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionTeacher[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionTeacher[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionTeacher[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearTeacher() {
        sessionStorage.formGuru_Pelajaran = "init"
        $("#form-teacher-main").form("reset")
        actionButtonTeacher("init", [])
        titleTeacher.innerText = ""
        markTeacher.innerText = ""
        idTeacher.value = "-1"
        $("#page-teacher").waitMe({effect:"none"})
    }
    function exportTeacher(document) {
        var dg = $("#tb-teacher").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/teacher/export-') }}" + document,dg.rows,"Ekspor data Guru ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
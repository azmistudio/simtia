@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Kamar Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportRoomStudent('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-room-student" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',readonly:true,labelWidth:100" />
                        <input type="hidden" id="fdept-room-student" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-room-student" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-room-student" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterRoomStudent({fdept: $('#fdept-room-student').val(),fname: $('#fname-room-student').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-room-student').form('reset');filterRoomStudent({})">Batal</a>
                </div>
            </form>
            <table id="tb-room-student" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'name',width:150,resizeable:true,sortable:true">Nama</th>
                        <th data-options="field:'capacity',width:100,resizeable:true,sortable:true">Kapasitas/Terisi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-room-student" class="panel-top">
            <a id="newRoomStudent" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newRoomStudent()">Baru</a>
            <a id="editRoomStudent" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editRoomStudent()">Ubah</a>
            <a id="saveRoomStudent" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveRoomStudent()">Simpan</a>
            <a id="clearRoomStudent" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearRoomStudent()">Batal</a>
            <a id="deleteRoomStudent" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteRoomStudent()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-room-student"></span>Kamar Santri: <span id="title-room-student"></span></h6>
        </div>
        <div id="page-room-student" class="pt-3 pb-3">
            <form id="form-room-student-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-room-student" name="id" value="-1" />
                            <input type="hidden" name="is_employee" value="0" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:400px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="RoomStudentDeptId" class="easyui-combobox" style="width:400px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'150px',labelPosition:'before',panelHeight:125">
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="name" id="RoomStudentName" class="easyui-textbox" style="width:400px;height:22px;" data-options="label:'<b>*</b>Nama:',labelWidth:'150px'" />
                            </div>
                            <div class="mb-1">
                                <select name="gender" class="easyui-combobox" style="width:260px;height:22px;" data-options="label:'<b>*</b>Status Kamar:',labelWidth:'150px',labelPosition:'before',panelHeight:46">
                                    <option value="1">Ikhwan</option>
                                    <option value="2">Akhwat</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="capacity" class="easyui-numberspinner" style="width:260px;height:22px;" data-options="label:'<b>*</b>Kapasitas:',labelWidth:'150px'" />
                            </div>
                            <div class="mb-1">
                                <select name="employee_id" class="easyui-combogrid" style="width:400px;height:22px;" data-options="
                                    label:'<b>*</b>Penanggung Jawab:',
                                    labelWidth:'150px',
                                    panelWidth: 440,
                                    idField: 'id',
                                    textField: 'name',
                                    url: '{{ url('hr/combo-grid') }}',
                                    method: 'post',
                                    queryParams: { _token: '{{ csrf_token() }}' },
                                    mode:'remote',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'employee_id',title:'NIP',width:80},
                                        {field:'name',title:'Nama',width:200},
                                        {field:'section',title:'Bagian',width:150},
                                    ]],
                                ">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionRoomStudent = document.getElementById("menu-act-room-student").getElementsByTagName("a")
    var titleRoomStudent = document.getElementById("title-room-student")
    var markRoomStudent = document.getElementById("mark-room-student")
    var idRoomStudent = document.getElementById("id-room-student")
    var dgRoomStudent = $("#tb-room-student")
    $(function () {
        sessionStorage.formKamar_Santri = "init"
        dgRoomStudent.datagrid({
            url: "{{ url('general/room/data') }}",
            queryParams: { _token: "{{ csrf_token() }}", is_employee: 0 },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formKamar_Santri == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleRoomStudent.innerText = row.name
                    actionButtonRoomStudent("active",[2,3])
                    $("#form-room-student-main").form("load", "{{ url('general/room/show') }}" + "/" + row.id)
                    $("#page-room-student").waitMe("hide")
                }
            }
        })
        dgRoomStudent.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgRoomStudent.datagrid('getPager').pagination())
        actionButtonRoomStudent("{{ $ViewType }}", [])
        $("#RoomStudentName").textbox("textbox").bind("keyup", function (e) {
            titleRoomStudent.innerText = $(this).val()
        })
        $("#page-room-student").waitMe({effect:"none"})
    })
    function filterRoomStudent(params) {
        if (Object.keys(params).length > 0) {
            dgRoomStudent.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgRoomStudent.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newRoomStudent() {
        sessionStorage.formKamar_Santri = "active"
        $("#form-room-student-main").form("reset")
        actionButtonRoomStudent("active", [0,1,4])
        markRoomStudent.innerText = "*"
        titleRoomStudent.innerText = ""
        idRoomStudent.value = "-1"
        $("#RoomStudentDeptId").combobox('textbox').focus()
        $("#page-room-student").waitMe("hide")
    }
    function editRoomStudent() {
        sessionStorage.formKamar_Santri = "active"
        markRoomStudent.innerText = "*"
        actionButtonRoomStudent("active", [0,1,4])
    }
    function saveRoomStudent() {
        if (sessionStorage.formKamar_Santri == "active") {
            ajaxRoomStudent("general/room/store")
        }
    }
    function deleteRoomStudent() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kamar Santri terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('general/room/destroy') }}" +"/"+idRoomStudent.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxRoomStudentResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })  
            }
        })
    }
    function pdfRoomStudent() {
        if (idRoomStudent.value != -1) {
            exportDocument("{{ url('general/room/print') }}", { id: idRoomStudent.value }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function ajaxRoomStudent(route) {
        $("#form-room-student-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-room-student").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxRoomStudentResponse(response)
                $("#page-room-student").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-room-student").waitMe("hide")
            }
        })
        return false
    }
    function ajaxRoomStudentResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearRoomStudent()
            $("#tb-room-student").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearRoomStudent() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearRoomStudent()
            }
        })
    }
    function actionButtonRoomStudent(viewType, idxArray) {
        for (var i = 0; i < menuActionRoomStudent.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionRoomStudent[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionRoomStudent[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionRoomStudent[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionRoomStudent[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearRoomStudent() {
        sessionStorage.formKamar_Santri = "init"
        $("#form-room-student-main").form("reset")
        actionButtonRoomStudent("init", [])
        titleRoomStudent.innerText = ""
        markRoomStudent.innerText = ""
        idRoomStudent.value = "-1"
        $("#page-room-student").waitMe({effect:"none"})
    }
    function exportRoomStudent(document) {
        var dg = $("#tb-room-student").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('general/room/export-') }}" + document,dg.rows,"Ekspor data Kamar Santri ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
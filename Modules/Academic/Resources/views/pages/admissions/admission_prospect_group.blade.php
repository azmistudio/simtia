@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Kelompok Calon Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportProspectiveGroup('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-prospective-group" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',readonly:true,labelWidth:100" />
                        <input type="hidden" id="fdept-prospective-group" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-prospective-group" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fgroup-prospective-group" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Kelompok:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterProspectiveGroup({fdept: $('#fdept-prospective-group').val(),fgroup: $('#fgroup-prospective-group').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-prospective-group').form('reset');filterProspectiveGroup({})">Batal</a>
                </div>
            </form>
            <table id="tb-prospective-group" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'admission_id',width:120,resizeable:true,sortable:true">Proses</th>
                        <th data-options="field:'group',width:100,resizeable:true,sortable:true">Kelompok</th>
                        <th data-options="field:'capacity',width:100,resizeable:true,sortable:true">Kapasitas/Terisi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-prospective-group" class="panel-top">
            <a id="newProspectiveGroup" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newProspectiveGroup()">Baru</a>
            <a id="editProspectiveGroup" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editProspectiveGroup()">Ubah</a>
            <a id="saveProspectiveGroup" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveProspectiveGroup()">Simpan</a>
            <a id="clearProspectiveGroup" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearProspectiveGroup()">Batal</a>
            <a id="deleteProspectiveGroup" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteProspectiveGroup()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-prospective-group"></span>Kelompok Calon Santri: <span id="title-prospective-group"></span></h6>
        </div>
        <div id="page-prospective-group" class="pt-3 pb-3">
            <form id="form-prospective-group-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-prospective-group" name="id" value="-1" />
                            <div class="mb-1">
                                <input class="easyui-textbox" id="ProspectGroupAdmissionDeptId" style="width:400px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="admission_id" id="ProspectGroupAdmissionId" class="easyui-combogrid" style="width:400px;height:22px;" data-options="
                                    label:'<b>*</b>Proses:',
                                    labelWidth:'125px',
                                    panelWidth: 450,
                                    idField: 'id',
                                    textField: 'name',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department_id',title:'Departemen',width:150},
                                        {field:'name',title:'Proses',width:300},
                                    ]],
                                ">
                                </select>
                                <a class="easyui-linkbutton small-btn" onclick="$('#ProspectGroupAdmissionId').combogrid('grid').datagrid('reload')" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Refresh"></i></a>
                            </div>
                            <div class="mb-1">
                                <input name="group" id="ProspectGroupName" class="easyui-textbox" style="width:400px;height:22px;" data-options="label:'<b>*</b>Kelompok:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input name="capacity" class="easyui-numberspinner" style="width:235px;height:22px;" data-options="label:'Kapasitas:',labelWidth:'125px',min:1" />
                            </div>
                            <div class="mb-1">
                                <input name="occupied" id="ProspectGroupOccupiedId" class="easyui-textbox" style="width:204px;height:22px;" data-options="label:'Terisi:',labelWidth:'125px',readonly:true" />
                                <a id="ProspectGroupOccupiedLookId" class="easyui-linkbutton small-btn" onclick="lookupProspectStudentDialog()" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Search"></i></a>
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:400px;height:50px;" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- window --}}
<div id="prospect-student-w" class="easyui-window" title="Daftar Calon Santri" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--List'" style="width:800px;height:500px;padding:10px;">
    <div class="mb-1">
        <input class="easyui-textbox" id="lookupDeptId" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-1">
        <input class="easyui-textbox" id="lookupProspectGroupAdmissionId" style="width:335px;height:22px;" data-options="label:'Proses:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-2">
        <input class="easyui-textbox" id="lookupGroupId" style="width:335px;height:22px;" data-options="label:'Kelompok:',labelWidth:'125px',readonly:true" />
    </div>
    <div>
        <table id="tb-lookup-prospective-student" class="easyui-datagrid" style="width:100%;height:355px" 
            data-options="method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
            <thead>
                <tr>
                    <th data-options="field:'registration_no',width:150,resizeable:true,sortable:true">No. Pendaftaran</th>
                    <th data-options="field:'name',width:250,resizeable:true,sortable:true">Nama</th>
                    <th data-options="field:'remark',width:300,resizeable:true,sortable:true">Keterangan</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    var menuActionProspectiveGroup = document.getElementById("menu-act-prospective-group").getElementsByTagName("a")
    var titleProspectiveGroup = document.getElementById("title-prospective-group")
    var markProspectiveGroup = document.getElementById("mark-prospective-group")
    var idProspectiveGroup = document.getElementById("id-prospective-group")
    var dgProspectiveGroup = $("#tb-prospective-group")
    $(function () {
        sessionStorage.formPSB_Calon = "init"
        dgProspectiveGroup.datagrid({
            url: "{{ url('academic/admission/prospective-group/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPSB_Calon == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleProspectiveGroup.innerText = row.group
                    actionButtonProspectiveGroup("active",[2,3])
                    $("#ProspectGroupAdmissionDeptId").textbox("setValue", row.department)
                    $("#form-prospective-group-main").form("load", "{{ url('academic/admission/prospective-group/show') }}" + "/" + row.id)
                    $("#ProspectGroupOccupiedLookId").linkbutton('enable')
                    $("#page-prospective-group").waitMe("hide")
                }
            }
        })
        dgProspectiveGroup.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgProspectiveGroup.datagrid('getPager').pagination())
        actionButtonProspectiveGroup("{{ $ViewType }}", [])
        $("#ProspectGroupName").textbox("textbox").bind("keyup", function (e) {
            titleProspectiveGroup.innerText = $(this).val()
        })
        $("#ProspectGroupAdmissionId").combogrid({
            url: '{{ url('academic/admission/combo-grid') }}',
            method: 'get',
            mode:'remote',
            onClickRow: function (index, row) {
                $("#ProspectGroupAdmissionDeptId").textbox("setValue", row.department_id)
            }
        })
        $("#ProspectGroupOccupiedLookId").tooltip({
            position: 'right',
            content: 'Lihat daftar Calon Santri'
        })
        $("#tb-lookup-prospective-student").datagrid({
            url: "{{ url('academic/admission/prospective-student/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
        })
        $('#prospect-student-w').window({
            onOpen:function(){
                $("#tb-lookup-prospective-student").datagrid('load', {
                    _token: "{{ csrf_token() }}",
                    fprospect_group: $('#id-prospective-group').val()
                })
            }
        })
        $("#page-prospective-group").waitMe({effect:"none"})
    })
    function filterProspectiveGroup(params) {
        if (Object.keys(params).length > 0) {
            dgProspectiveGroup.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgProspectiveGroup.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newProspectiveGroup() {
        sessionStorage.formPSB_Calon = "active"
        $("#form-prospective-group-main").form("reset")
        actionButtonProspectiveGroup("active", [0,1,4])
        markProspectiveGroup.innerText = "*"
        titleProspectiveGroup.innerText = ""
        idProspectiveGroup.value = "-1"
        $("#ProspectGroupAdmissionId").combobox('textbox').focus()
        $("#page-prospective-group").waitMe("hide")
    }
    function editProspectiveGroup() {
        sessionStorage.formPSB_Calon = "active"
        markProspectiveGroup.innerText = "*"
        actionButtonProspectiveGroup("active", [0,1,4])
    }
    function saveProspectiveGroup() {
        if (sessionStorage.formPSB_Calon == "active") {
            ajaxProspectiveGroup("academic/admission/prospective-group/store")
        }
    }
    function deleteProspectiveGroup() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kelompok Calon Santri terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/admission/prospective-group/destroy') }}" +"/"+idProspectiveGroup.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxProspectiveGroupResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                }) 
            }
        })
    }
    function ajaxProspectiveGroup(route) {
        $("#form-prospective-group-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-prospective-group").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxProspectiveGroupResponse(response)
                $("#page-prospective-group").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-prospective-group").waitMe("hide")
            }
        })
        return false
    }
    function ajaxProspectiveGroupResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearProspectiveGroup()
            dgProspectiveGroup.datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearProspectiveGroup() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearProspectiveGroup()
            }
        })
    }
    function actionButtonProspectiveGroup(viewType, idxArray) {
        for (var i = 0; i < menuActionProspectiveGroup.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionProspectiveGroup[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionProspectiveGroup[i].id).linkbutton({disabled: true})
                }
                $("#ProspectGroupOccupiedLookId").linkbutton('disable')
            } else {
                $("#" + menuActionProspectiveGroup[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionProspectiveGroup[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearProspectiveGroup() {
        sessionStorage.formPSB_Calon = "init"
        $("#form-prospective-group-main").form("reset")
        actionButtonProspectiveGroup("init", [])
        titleProspectiveGroup.innerText = ""
        markProspectiveGroup.innerText = ""
        idProspectiveGroup.value = "-1"
        $("#page-prospective-group").waitMe({effect:"none"})
    }
    function exportProspectiveGroup(document) {
        var dg = $("#tb-prospective-group").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/admission/prospective-group/export-') }}" + document,dg.rows,"Ekspor data Kelompok Calon ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function lookupProspectStudentDialog() {
        if ($('#ProspectGroupOccupiedId').textbox('getValue') > 0) {
            $('#prospect-student-w').window('open')
            $('#lookupDeptId').textbox('setValue', $('#ProspectGroupAdmissionDeptId').textbox('getValue'))
            $('#lookupProspectGroupAdmissionId').textbox('setValue', $('#ProspectGroupAdmissionId').combogrid('getText'))
            $('#lookupGroupId').textbox('setValue', $('#ProspectGroupName').textbox('getValue'))
            //
            $('#prospect-student-w').window('collapse')
            $('#prospect-student-w').window('expand')
        }
    }
</script>
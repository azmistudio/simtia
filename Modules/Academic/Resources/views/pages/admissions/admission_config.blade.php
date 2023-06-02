@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 214 . "px";
    $SubGridHeight = $InnerHeight - 377 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Konfigurasi Pendataan PSB</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div style="padding:5px;">
            <table id="tb-admission-config" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        @if (auth()->user()->getDepartment->is_all == 1)
                        <th data-options="field:'department_id',width:100,resizeable:true,sortable:true">Departemen</th>
                        @endif
                        <th data-options="field:'admission_id',width:180,resizeable:true,sortable:true">Proses</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-admission-config" class="panel-top">
            <a id="newAdmissionConfig" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newAdmissionConfig()">Baru</a>
            <a id="editAdmissionConfig" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editAdmissionConfig()">Ubah</a>
            <a id="saveAdmissionConfig" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveAdmissionConfig()">Simpan</a>
            <a id="clearAdmissionConfig" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearAdmissionConfig()">Batal</a>
            <a id="deleteAdmissionConfig" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteAdmissionConfig()">Hapus</a>
        </div>
        <div class="pt-3 pb-3" id="page-admission-config-main">
            <form id="form-admission-config-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-admission-config" name="id" value="-1" />
                            <div class="mb-1">
                                <input class="easyui-textbox" id="AdmissionConfigDeptId" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="admission_id" id="AdmissionConfigId" class="easyui-combogrid" style="width:450px;height:22px;" data-options="
                                    label:'<b>*</b>Proses Penerimaan:',
                                    labelWidth:'150px',
                                    panelWidth: 450,
                                    idField: 'id',
                                    textField: 'name',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department_id',title:'Departemen',width:120},
                                        {field:'name',title:'Proses',width:330},
                                    ]],
                                ">
                                </select>
                                <a class="easyui-linkbutton small-btn" onclick="$('#AdmissionConfigId').combogrid('grid').datagrid('reload')" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Refresh"></i></a>
                            </div>
                            <div class="mb-3" style="padding-left: 150px;">
                                <input name="is_clone" id="AdmissionConfigClone" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Salin dari periode sebelumnya',labelWidth:'200px',labelPosition:'after'" />
                            </div>
                            <div class="">
                                <table id="tb-admission-config-form" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                                       data-options="method:'post',rownumbers:'true'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'config',width:150,resizeable:true">Jenis</th>
                                            <th data-options="field:'code',width:150,resizeable:true,editor:'text'">Kode</th>
                                            <th data-options="field:'name',width:300,resizeable:true,editor:'text'">Nama</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="mt-1">
                                <ul class="well" style="font-size:13px;">
                                    <li><strong>Klik pada kolom Kode dan Nama untuk mengisi data.</strong></li>
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
    var menuActionAdmissionConfig = document.getElementById("menu-act-admission-config").getElementsByTagName("a")
    var idAdmissionConfig = document.getElementById("id-admission-config")
    var initDataAdmissionConfig = [
        {config:'Sumbangan #1', code:'', name: ''},
        {config:'Sumbangan #2', code:'', name: ''},
        {config:'Ujian #1', code:'', name: ''},
        {config:'Ujian #2', code:'', name: ''},
        {config:'Ujian #3', code:'', name: ''},
        {config:'Ujian #4', code:'', name: ''},
        {config:'Ujian #5', code:'', name: ''},
        {config:'Ujian #6', code:'', name: ''},
        {config:'Ujian #7', code:'', name: ''},
        {config:'Ujian #8', code:'', name: ''},
        {config:'Ujian #9', code:'', name: ''},
        {config:'Ujian #10', code:'', name: ''},
    ]
    var dgAdmissionConfig = $("#tb-admission-config")
    $(function () {
        sessionStorage.formPSB_Konfigurasi = "init"
        dgAdmissionConfig.datagrid({
            url: "{{ url('academic/admission/config/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPSB_Konfigurasi == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    actionButtonAdmissionConfig("active",[2,3])
                    $("#form-admission-config-main").form("load", "{{ url('academic/admission/config/show') }}" + "/" + row.id)
                    $("#AdmissionConfigDeptId").textbox("setValue", row.department_id)
                    $("#page-admission-config-main").waitMe("hide")
                    $("#AdmissionConfigId").combogrid("readonly")
                    $("#AdmissionConfigClone").checkbox("disable")
                }
            }
        })
        dgAdmissionConfig.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgAdmissionConfig.datagrid('getPager').pagination())
        actionButtonAdmissionConfig("{{ $ViewType }}", [])
        $('#tb-admission-config-form').datagrid({ data: initDataAdmissionConfig })
        $("#tb-admission-config-form").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 1,
            field: 'code'
        })
        $("#AdmissionConfigId").combogrid({
            url: '{{ url('academic/admission/combo-grid') }}',
            method: 'get',
            mode:'remote',
            onClickRow: function (index, row) {
                $("#AdmissionConfigDeptId").textbox("setValue", row.department_id)
            }
        })
        $("#form-admission-config-main").form({
            onLoadSuccess: function(data) {
                $('#tb-admission-config-form').datagrid({
                    data: [
                        {config:'Sumbangan #1', code:data.donate_code_1, name: data.donate_name_1},
                        {config:'Sumbangan #2', code:data.donate_code_2, name: data.donate_name_2},
                        {config:'Ujian #1', code:data.exam_code_01, name: data.exam_name_01},
                        {config:'Ujian #2', code:data.exam_code_02, name: data.exam_name_02},
                        {config:'Ujian #3', code:data.exam_code_03, name: data.exam_name_03},
                        {config:'Ujian #4', code:data.exam_code_04, name: data.exam_name_04},
                        {config:'Ujian #5', code:data.exam_code_05, name: data.exam_name_05},
                        {config:'Ujian #6', code:data.exam_code_06, name: data.exam_name_06},
                        {config:'Ujian #7', code:data.exam_code_07, name: data.exam_name_07},
                        {config:'Ujian #8', code:data.exam_code_08, name: data.exam_name_08},
                        {config:'Ujian #9', code:data.exam_code_09, name: data.exam_name_09},
                        {config:'Ujian #10', code:data.exam_code_10, name: data.exam_name_10},
                    ]
                })
            }
        })
        $("#page-admission-config-main").waitMe({effect:"none"})
    })
    function newAdmissionConfig() {
        sessionStorage.formPSB_Konfigurasi = "active"
        $("#form-admission-config-main").form("clear")
        actionButtonAdmissionConfig("active", [0,1,4])
        clearPreview("photo-admission-config","preview-img-admission-config")
        idAdmissionConfig.value = "-1"
        $('#tb-admission-config-form').datagrid({ data: initDataAdmissionConfig })
        $("#AdmissionConfigId").combogrid("readonly",false)
        $("#AdmissionConfigClone").checkbox("enable")
        $("#page-admission-config-main").waitMe("hide")
    }
    function editAdmissionConfig() {
        sessionStorage.formPSB_Konfigurasi = "active"
        actionButtonAdmissionConfig("active", [0,1,4])
    }
    function saveAdmissionConfig() {
        if (sessionStorage.formPSB_Konfigurasi == "active") {
            ajaxAdmissionConfig("academic/admission/config/store")
        }
    }
    function deleteAdmissionConfig() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data PSB Konfigurasi, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/admission/config/destroy') }}" +"/"+idAdmissionConfig.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAdmissionConfigResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxAdmissionConfig(route) {
        var dg = $("#tb-admission-config-form").datagrid('getData')
        $("#form-admission-config-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', configs: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-admission-config-main").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionConfigResponse(response)
                $("#page-admission-config-main").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-admission-config-main").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionConfigResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearAdmissionConfig()
            $("#tb-admission-config").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearAdmissionConfig() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearAdmissionConfig()
            }
        })
    }
    function actionButtonAdmissionConfig(viewType, idxArray) {
        for (var i = 0; i < menuActionAdmissionConfig.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionAdmissionConfig[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionAdmissionConfig[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionAdmissionConfig[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionAdmissionConfig[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearAdmissionConfig() {
        sessionStorage.formPSB_Konfigurasi = "init"
        $("#form-admission-config-main").form("clear")
        actionButtonAdmissionConfig("init", [])
        idAdmissionConfig.value = "-1"
        $('#tb-admission-config-form').datagrid({ data: initDataAdmissionConfig })
        $("#page-admission-config-main").waitMe({effect:"none"})
    }
</script>

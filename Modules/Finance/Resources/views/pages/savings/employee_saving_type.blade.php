@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Jenis Tabungan Pegawai</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportEmployeeSavingType('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-employee-saving-type" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-employee-saving-type" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-employee-saving-type" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-employee-saving-type" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterEmployeeSavingType({fdept: $('#fdept-employee-saving-type').combobox('getValue'),fname: $('#fname-employee-saving-type').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-employee-saving-type').form('reset');filterEmployeeSavingType({})">Batal</a>
                </div>
            </form>
            <table id="tb-employee-saving-type" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'department_id',width:110,resizeable:true,sortable:true">Departemen</th>
                        <th data-options="field:'name',width:150,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-employee-saving-type" class="panel-top">
            <a id="newEmployeeSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newEmployeeSavingType()">Baru</a>
            <a id="editEmployeeSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editEmployeeSavingType()">Ubah</a>
            <a id="saveEmployeeSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveEmployeeSavingType()">Simpan</a>
            <a id="clearEmployeeSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearEmployeeSavingType()">Batal</a>
            <a id="deleteEmployeeSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteEmployeeSavingType()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-employee-saving-type"></span>Jenis Tabungan Pegawai: <span id="title-employee-saving-type"></span></h6>
        </div>
        <div id="page-employee-saving-type" class="pt-3 pb-3">
            <form id="form-employee-saving-type-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-employee-saving-type" name="id" value="-1" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:550px;height:22px;" data-options="label:'Departemen:',labelWidth:'175px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="AccountingEmployeeSavingTypeDeptId" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'175px',labelPosition:'before',panelHeight:125">
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="name" id="AccountingEmployeeSavingType" class="easyui-textbox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Nama:',labelWidth:'175px'" />
                                <span class="mr-2"></span>
                            </div>
                            <div class="mb-1">
                                <select name="cash_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Rekening Kas:',labelWidth:'175px',labelPosition:'before',panelHeight:150">
                                    @foreach ($codes_cash as $code)
                                    <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="credit_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Rekening Utang:',labelWidth:'175px',labelPosition:'before',panelHeight:150">
                                    @foreach ($codes_credit as $code)
                                    <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:550px;height:50px;" data-options="label:'Keterangan:',labelWidth:'175px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'175px',labelPosition:'before'" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionEmployeeSavingType = document.getElementById("menu-act-employee-saving-type").getElementsByTagName("a")
    var titleEmployeeSavingType = document.getElementById("title-employee-saving-type")
    var markEmployeeSavingType = document.getElementById("mark-employee-saving-type")
    var idEmployeeSavingType = document.getElementById("id-employee-saving-type")
    var dgEmployeeSavingType = $("#tb-employee-saving-type")
    $(function () {
        sessionStorage.formJenis_TabPegawai = "init"
        dgEmployeeSavingType.datagrid({
            url: "{{ url('finance/saving/employee/type/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formJenis_TabPegawai == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleEmployeeSavingType.innerText = row.name
                    actionButtonEmployeeSavingType("active",[2,3])
                    $("#form-employee-saving-type-main").form("load", "{{ url('finance/saving/employee/type/show') }}" + "/" + row.id)
                    $("#page-employee-saving-type").waitMe("hide")
                }
            }
        })
        dgEmployeeSavingType.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgEmployeeSavingType.datagrid('getPager').pagination())
        actionButtonEmployeeSavingType("{{ $ViewType }}", [])
        $("#AccountingEmployeeSavingType").textbox("textbox").bind("keyup", function (e) {
            titleEmployeeSavingType.innerText = $(this).val()
        })
        $("#page-employee-saving-type").waitMe({effect:"none"})
    })
    function filterEmployeeSavingType(params) {
        if (Object.keys(params).length > 0) {
            dgEmployeeSavingType.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgEmployeeSavingType.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newEmployeeSavingType() {
        sessionStorage.formJenis_TabPegawai = "active"
        $("#form-employee-saving-type-main").form("reset")
        actionButtonEmployeeSavingType("active", [0,1,4])
        markEmployeeSavingType.innerText = "*"
        titleEmployeeSavingType.innerText = ""
        idEmployeeSavingType.value = "-1"
        $("#AccountingEmployeeSavingType").textbox('textbox').focus()
        $("#page-employee-saving-type").waitMe("hide")
    }
    function editEmployeeSavingType() {
        sessionStorage.formJenis_TabPegawai = "active"
        markEmployeeSavingType.innerText = "*"
        actionButtonEmployeeSavingType("active", [0,1,4])
    }
    function saveEmployeeSavingType() {
        if (sessionStorage.formJenis_TabPegawai == "active") {
            ajaxEmployeeSavingType("finance/saving/employee/type/store")
        }
    }
    function deleteEmployeeSavingType() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Jenis Tabungan Pegawai terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('finance/saving/employee/type/destroy') }}" +"/"+idEmployeeSavingType.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAdmissionResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })  
            }
        })
    }
    function ajaxEmployeeSavingType(route) {
        $("#form-employee-saving-type-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-employee-saving-type").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-employee-saving-type").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-employee-saving-type").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearEmployeeSavingType()
            $("#tb-employee-saving-type").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearEmployeeSavingType() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearEmployeeSavingType()
            }
        })
    }
    function actionButtonEmployeeSavingType(viewType, idxArray) {
        for (var i = 0; i < menuActionEmployeeSavingType.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionEmployeeSavingType[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionEmployeeSavingType[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionEmployeeSavingType[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionEmployeeSavingType[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearEmployeeSavingType() {
        sessionStorage.formJenis_TabPegawai = "init"
        $("#form-employee-saving-type-main").form("reset")
        actionButtonEmployeeSavingType("init", [])
        titleEmployeeSavingType.innerText = ""
        markEmployeeSavingType.innerText = ""
        idEmployeeSavingType.value = "-1"
        $("#page-employee-saving-type").waitMe({effect:"none"})
    }
    function exportEmployeeSavingType(document) {
        var dg = $("#tb-employee-saving-type").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('finance/saving/employee/type/export-') }}" + document,dg.rows,"Ekspor data Jenis Tab. Pegawai ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
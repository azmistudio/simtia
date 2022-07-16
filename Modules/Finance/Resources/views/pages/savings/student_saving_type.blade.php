@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Jenis Tabungan Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportStudentSavingType('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-student-saving-type" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-student-saving-type" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-student-saving-type" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-student-saving-type" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterStudentSavingType({fdept: $('#fdept-student-saving-type').combobox('getValue'),fname: $('#fname-student-saving-type').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-student-saving-type').form('reset');filterStudentSavingType({})">Batal</a>
                </div>
            </form>
            <table id="tb-student-saving-type" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
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
        <div id="menu-act-student-saving-type" class="panel-top">
            <a id="newStudentSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newStudentSavingType()">Baru</a>
            <a id="editStudentSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editStudentSavingType()">Ubah</a>
            <a id="saveStudentSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveStudentSavingType()">Simpan</a>
            <a id="clearStudentSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearStudentSavingType()">Batal</a>
            <a id="deleteStudentSavingType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteStudentSavingType()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-student-saving-type"></span>Jenis Tabungan Santri: <span id="title-student-saving-type"></span></h6>
        </div>
        <div id="page-student-saving-type" class="pt-3 pb-3">
            <form id="form-student-saving-type-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-student-saving-type" name="id" value="-1" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:550px;height:22px;" data-options="label:'Departemen:',labelWidth:'175px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="AccountingStudentSavingTypeDeptId" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'175px',labelPosition:'before',panelHeight:125">
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="name" id="AccountingStudentSavingType" class="easyui-textbox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Nama:',labelWidth:'175px'" />
                                <span class="mr-2"></span>
                                @if (auth()->user()->getDepartment->is_all == 1)
                                <input name="is_all" id="AccountingStudentSavingTypeAll" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Semua Departemen',labelWidth:'140px',labelPosition:'after'" />
                                @endif
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
    var menuActionStudentSavingType = document.getElementById("menu-act-student-saving-type").getElementsByTagName("a")
    var titleStudentSavingType = document.getElementById("title-student-saving-type")
    var markStudentSavingType = document.getElementById("mark-student-saving-type")
    var idStudentSavingType = document.getElementById("id-student-saving-type")
    var dgStudentSavingType = $("#tb-student-saving-type")
    $(function () {
        sessionStorage.formJenis_TabSantri = "init"
        dgStudentSavingType.datagrid({
            url: "{{ url('finance/saving/student/type/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formJenis_TabSantri == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleStudentSavingType.innerText = row.name
                    $("#AccountingStudentSavingTypeAll").checkbox("disable")
                    actionButtonStudentSavingType("active",[2,3])
                    $("#form-student-saving-type-main").form("load", "{{ url('finance/saving/student/type/show') }}" + "/" + row.id)
                    $("#page-student-saving-type").waitMe("hide")
                }
            }
        })
        dgStudentSavingType.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgStudentSavingType.datagrid('getPager').pagination())
        actionButtonStudentSavingType("{{ $ViewType }}", [])
        $("#AccountingStudentSavingType").textbox("textbox").bind("keyup", function (e) {
            titleStudentSavingType.innerText = $(this).val()
        })
        $("#page-student-saving-type").waitMe({effect:"none"})
    })
    function filterStudentSavingType(params) {
        if (Object.keys(params).length > 0) {
            dgStudentSavingType.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgStudentSavingType.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newStudentSavingType() {
        sessionStorage.formJenis_TabSantri = "active"
        $("#form-student-saving-type-main").form("reset")
        actionButtonStudentSavingType("active", [0,1,4])
        markStudentSavingType.innerText = "*"
        titleStudentSavingType.innerText = ""
        idStudentSavingType.value = "-1"
        $("#AccountingStudentSavingType").textbox('textbox').focus()
        $("#AccountingStudentSavingTypeAll").checkbox("enable")
        $("#page-student-saving-type").waitMe("hide")
    }
    function editStudentSavingType() {
        sessionStorage.formJenis_TabSantri = "active"
        markStudentSavingType.innerText = "*"
        actionButtonStudentSavingType("active", [0,1,4])
    }
    function saveStudentSavingType() {
        if (sessionStorage.formJenis_TabSantri == "active") {
            ajaxStudentSavingType("finance/saving/student/type/store")
        }
    }
    function deleteStudentSavingType() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Jenis Tabungan Santri terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('finance/saving/student/type/destroy') }}" +"/"+idStudentSavingType.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAdmissionResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })  
            }
        })
    }
    function ajaxStudentSavingType(route) {
        $("#form-student-saving-type-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-student-saving-type").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-student-saving-type").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-student-saving-type").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearStudentSavingType()
            $("#tb-student-saving-type").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearStudentSavingType() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearStudentSavingType()
            }
        })
    }
    function actionButtonStudentSavingType(viewType, idxArray) {
        for (var i = 0; i < menuActionStudentSavingType.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionStudentSavingType[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionStudentSavingType[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionStudentSavingType[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionStudentSavingType[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearStudentSavingType() {
        sessionStorage.formJenis_TabSantri = "init"
        $("#form-student-saving-type-main").form("reset")
        actionButtonStudentSavingType("init", [])
        titleStudentSavingType.innerText = ""
        markStudentSavingType.innerText = ""
        idStudentSavingType.value = "-1"
        $("#page-student-saving-type").waitMe({effect:"none"})
    }
    function exportStudentSavingType(document) {
        var dg = $("#tb-student-saving-type").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('finance/saving/student/type/export-') }}" + document,dg.rows,"Ekspor data Jenis Tab. Santri ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
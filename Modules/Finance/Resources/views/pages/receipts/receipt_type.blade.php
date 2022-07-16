@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Jenis Penerimaan</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportReceiptType('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-receipt-type" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-receipt-type" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-receipt-type" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-receipt-type" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterReceiptType({
                        fdept: @if (auth()->user()->getDepartment->is_all != 1) $('#fdept-receipt-type').val() @else $('#fdept-receipt-type').combobox('getValue') @endif,
                        fname: $('#fname-receipt-type').val()
                    })">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-receipt-type').form('reset');filterReceiptType({})">Batal</a>
                </div>
            </form>
            <table id="tb-receipt-type" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'department_id',width:100,resizeable:true,sortable:true">Departemen</th>
                        <th data-options="field:'name',width:150,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-receipt-type" class="panel-top">
            <a id="newReceiptType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newReceiptType()">Baru</a>
            <a id="editReceiptType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editReceiptType()">Ubah</a>
            <a id="saveReceiptType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveReceiptType()">Simpan</a>
            <a id="clearReceiptType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearReceiptType()">Batal</a>
            <a id="deleteReceiptType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteReceiptType()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-receipt-type"></span>Jenis Penerimaan: <span id="title-receipt-type"></span></h6>
        </div>
        <div id="page-receipt-type" class="pt-3 pb-3">
            <form id="form-receipt-type-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-receipt-type" name="id" value="-1" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:550px;height:22px;" data-options="label:'Departemen:',labelWidth:'175px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="AccountingReceiptTypeDeptId" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'175px',labelPosition:'before',panelHeight:125">
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <select name="category_id" id="AccountingReceiptTypeCategory" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Kategori:',labelWidth:'175px',labelPosition:'before',panelHeight:100">
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="name" id="AccountingReceiptType" class="easyui-textbox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Nama:',labelWidth:'175px'" />
                                <span class="mr-2"></span>
                                @if (auth()->user()->getDepartment->is_all == 1)
                                <input name="is_all" id="AccountingReceiptTypeAll" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Semua Departemen',labelWidth:'140px',labelPosition:'after'" />
                                @endif
                            </div>
                            <div class="mb-1">
                                <select name="cash_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Rekening Kas:',labelWidth:'175px',labelPosition:'before',panelHeight:150">
                                    @foreach ($codes_activa as $code)
                                    <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="receipt_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Rekening Pendapatan:',labelWidth:'175px',labelPosition:'before',panelHeight:150">
                                    @foreach ($codes_receipt as $code)
                                    <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="receivable_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Rekening Piutang:',labelWidth:'175px',labelPosition:'before',panelHeight:150">
                                    @foreach ($codes_activa as $code)
                                    <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="discount_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Rekening Diskon:',labelWidth:'175px',labelPosition:'before',panelHeight:150">
                                    @foreach ($codes_receipt as $code)
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
    var menuActionReceiptType = document.getElementById("menu-act-receipt-type").getElementsByTagName("a")
    var titleReceiptType = document.getElementById("title-receipt-type")
    var markReceiptType = document.getElementById("mark-receipt-type")
    var idReceiptType = document.getElementById("id-receipt-type")
    var dgReceiptType = $("#tb-receipt-type")
    $(function () {
        sessionStorage.formJenis_Penerimaan = "init"
        dgReceiptType.datagrid({
            url: "{{ url('finance/receipt/type/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formJenis_Penerimaan == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleReceiptType.innerText = row.name
                    $("#AccountingReceiptTypeAll").checkbox("disable")
                    actionButtonReceiptType("active",[2,3])
                    $("#form-receipt-type-main").form("load", "{{ url('finance/receipt/type/show') }}" + "/" + row.id)
                    $("#page-receipt-type").waitMe("hide")
                }
            }
        })
        dgReceiptType.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgReceiptType.datagrid('getPager').pagination())
        actionButtonReceiptType("{{ $ViewType }}", [])
        $("#AccountingReceiptType").textbox("textbox").bind("keyup", function (e) {
            titleReceiptType.innerText = $(this).val()
        })
        $("#page-receipt-type").waitMe({effect:"none"})
    })
    function filterReceiptType(params) {
        if (Object.keys(params).length > 0) {
            dgReceiptType.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgReceiptType.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newReceiptType() {
        sessionStorage.formJenis_Penerimaan = "active"
        $("#form-receipt-type-main").form("clear")
        actionButtonReceiptType("active", [0,1,4])
        markReceiptType.innerText = "*"
        titleReceiptType.innerText = ""
        idReceiptType.value = "-1"
        $("#AccountingReceiptType").textbox('textbox').focus()
        $("#AccountingReceiptTypeAll").checkbox("enable")
        $("#page-receipt-type").waitMe("hide")
    }
    function editReceiptType() {
        sessionStorage.formJenis_Penerimaan = "active"
        markReceiptType.innerText = "*"
        actionButtonReceiptType("active", [0,1,4])
    }
    function saveReceiptType() {
        if (sessionStorage.formJenis_Penerimaan == "active") {
            ajaxReceiptType("finance/receipt/type/store")
        }
    }
    function deleteReceiptType() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Jenis Penerimaan terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('finance/receipt/type/destroy') }}" +"/"+idReceiptType.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAdmissionResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })  
            }
        })
    }
    function ajaxReceiptType(route) {
        $("#form-receipt-type-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-receipt-type").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-receipt-type").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-receipt-type").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearReceiptType()
            $("#tb-receipt-type").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearReceiptType() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearReceiptType()
            }
        })
    }
    function actionButtonReceiptType(viewType, idxArray) {
        for (var i = 0; i < menuActionReceiptType.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionReceiptType[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionReceiptType[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionReceiptType[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionReceiptType[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearReceiptType() {
        sessionStorage.formJenis_Penerimaan = "init"
        $("#form-receipt-type-main").form("clear")
        actionButtonReceiptType("init", [])
        titleReceiptType.innerText = ""
        markReceiptType.innerText = ""
        idReceiptType.value = "-1"
        $("#page-receipt-type").waitMe({effect:"none"})
    }
    function exportReceiptType(document) {
        var dg = $("#tb-receipt-type").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('finance/receipt/type/export-') }}" + document,dg.rows,"Ekspor data Jenis Penerimaan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
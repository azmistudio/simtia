@php
    $WindowHeight = $InnerHeight - 142 . "px";
    $WindowWidth = $InnerWidth - 73 . "px";
    $GridHeight = $InnerHeight - 275 . "px";
    $TabHeight = $InnerHeight - 224 . "px";
    $ViewType = $ViewType;
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Jenis Pengeluaran</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportExpenditureType('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:250px">
        <div class="p-1">
            <form id="ff-expenditure-type" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select id="fdept-expenditure-type" class="easyui-combobox" style="width:235px;height:22px;" data-options="label:'Departemen:',panelHeight:68">
                        <option value="">---</option>
                        @foreach ($depts as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <input id="fname-expenditure-type" class="easyui-textbox" style="width:235px;height:22px;" data-options="label:'Nama:'">
                </div>
                <div style="margin-left:80px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterExpenditureType({fdept: $('#fdept-expenditure-type').combobox('getValue'),fname: $('#fname-expenditure-type').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-expenditure-type').form('reset');filterExpenditureType({})">Batal</a>
                </div>
            </form>
            <table id="tb-expenditure-type" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'deptid',width:110,resizeable:true,sortable:true">Departemen</th>
                        <th data-options="field:'name',width:120,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-expenditure-type" class="panel-top">
            <a id="newExpenditureType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newExpenditureType()">Baru</a>
            <a id="editExpenditureType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editExpenditureType()">Ubah</a>
            <a id="saveExpenditureType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveExpenditureType()">Simpan</a>
            <a id="clearExpenditureType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearExpenditureType()">Batal</a>
            <a id="deleteExpenditureType" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteExpenditureType()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-expenditure-type"></span>Jenis Pengeluaran: <span id="title-expenditure-type"></span></h6>
        </div>
        <div>
            <form id="form-expenditure-type-main" method="post">
                <div id="tt-expenditure-type" class="easyui-tabs borderless" plain="true" narrow="true" style="height:{{ $TabHeight }}">
                    <div title="Umum" class="content-doc pt-3 pb-3">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <input type="hidden" id="id-expenditure-type" name="id" value="-1" />
                                    <div class="mb-1">
                                        <select name="deptid" id="AccountingExpenditureTypeDeptId" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'175px',labelPosition:'before',panelHeight:68">
                                            @foreach ($depts as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="name" id="AccountingExpenditureType" class="easyui-textbox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Nama:',labelWidth:'175px'" />
                                        <span class="mr-2"></span>
                                        <input name="is_all" id="AccountingExpenditureTypeAll" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Semua Departemen',labelWidth:'140px',labelPosition:'after'" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="debit_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Rekening Kas:',labelWidth:'175px',labelPosition:'before',panelHeight:150">
                                            @foreach ($codes_cash as $code)
                                            <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <select name="credit_account" class="easyui-combobox" style="width:550px;height:22px;" data-options="label:'<b>*</b>Rekening Beban:',labelWidth:'175px',labelPosition:'before',panelHeight:150">
                                            @foreach ($codes_expense as $code)
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
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionExpenditureType = document.getElementById("menu-act-expenditure-type").getElementsByTagName("a")
    var titleExpenditureType = document.getElementById("title-expenditure-type")
    var markExpenditureType = document.getElementById("mark-expenditure-type")
    var idExpenditureType = document.getElementById("id-expenditure-type")
    var dgExpenditureType = $("#tb-expenditure-type")
    $(function () {
        sessionStorage.formJenis_Pengeluaran = "init"
        dgExpenditureType.datagrid({
            url: "{{ url('accounting/expenditure/type/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formJenis_Pengeluaran == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleExpenditureType.innerText = row.name
                    $("#AccountingExpenditureTypeAll").checkbox("disable")
                    actionButtonExpenditureType("active",[2,3])
                    $("#form-expenditure-type-main").form("load", "{{ url('accounting/expenditure/type/show') }}" + "/" + row.id)
                    $("#tt-expenditure-type").waitMe("hide")
                }
            }
        })
        dgExpenditureType.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        var pagerExpenditureType = dgExpenditureType.datagrid('getPager').pagination()
        pagerExpenditureType[0].children[0].style.width = "100%"
        pagerExpenditureType[0].children[1].style.width = "100%"
        pagerExpenditureType[0].children[1].style.margin = "0"
        pagerExpenditureType[0].children[1].style.textAlign = "center"
        actionButtonExpenditureType("{{ $ViewType }}", [])
        $("#AccountingExpenditureType").textbox("textbox").bind("keyup", function (e) {
            titleExpenditureType.innerText = $(this).val()
        })
        $("#tt-expenditure-type").waitMe({effect:"none"})
    })
    function filterExpenditureType(params) {
        if (Object.keys(params).length > 0) {
            dgExpenditureType.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgExpenditureType.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newExpenditureType() {
        sessionStorage.formJenis_Pengeluaran = "active"
        $("#form-expenditure-type-main").form("clear")
        actionButtonExpenditureType("active", [0,1,4])
        markExpenditureType.innerText = "*"
        titleExpenditureType.innerText = ""
        idExpenditureType.value = "-1"
        $("#AccountingExpenditureType").textbox('textbox').focus()
        $("#AccountingExpenditureTypeAll").checkbox("enable")
        $("#tt-expenditure-type").waitMe("hide")
    }
    function editExpenditureType() {
        sessionStorage.formJenis_Pengeluaran = "active"
        markExpenditureType.innerText = "*"
        actionButtonExpenditureType("active", [0,1,4])
    }
    function saveExpenditureType() {
        if (sessionStorage.formJenis_Pengeluaran == "active") {
            ajaxExpenditureType("accounting/expenditure/type/store")
        }
    }
    function deleteExpenditureType() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Jenis Pengeluaran terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('accounting/expenditure/type/destroy') }}" +"/"+idExpenditureType.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAdmissionResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })  
            }
        })
    }
    function ajaxExpenditureType(route) {
        $("#form-expenditure-type-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                ajaxAdmissionResponse(response)
            },
            error: function(xhr) {
                failResponse(xhr)
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            $.messager.alert('Informasi', response.message)
            actionClearExpenditureType()
            $("#tb-expenditure-type").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearExpenditureType() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearExpenditureType()
            }
        })
    }
    function actionButtonExpenditureType(viewType, idxArray) {
        for (var i = 0; i < menuActionExpenditureType.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionExpenditureType[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionExpenditureType[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionExpenditureType[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionExpenditureType[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearExpenditureType() {
        sessionStorage.formJenis_Pengeluaran = "init"
        $("#form-expenditure-type-main").form("clear")
        actionButtonExpenditureType("init", [])
        titleExpenditureType.innerText = ""
        markExpenditureType.innerText = ""
        idExpenditureType.value = "-1"
        $("#tt-expenditure-type").waitMe({effect:"none"})
    }
    function exportExpenditureType(document) {
        var dg = $("#tb-expenditure-type").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('accounting/expenditure/type/export-') }}" + document,dg.rows,"Ekspor data Jenis Pengeluaran ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
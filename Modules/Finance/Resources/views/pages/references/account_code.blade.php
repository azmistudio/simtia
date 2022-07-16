@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 13 . "px";
    $GridHeight = $InnerHeight - 270 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Kode Akun Perkiraan (COA)</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div id="" data-options="region:'center'">
        <div id="menu-act-accounting-code" class="panel-top">
            <a id="newAccountingCode" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newAccountingCode()">Baru</a>
            <a id="editAccountingCode" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editAccountingCode()">Ubah</a>
            <a id="saveAccountingCode" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveAccountingCode()">Simpan</a>
            <a id="clearAccountingCode" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearAccountingCode()">Batal</a>
            <a id="deleteAccountingCode" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteAccountingCode()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-accounting-code"></span>Nama Akun: <span id="title-accounting-code"></span></h6>
        </div>
        <div class="pt-3">
            <form id="form-accounting-code-main" method="post">
                <input type="hidden" id="id-accounting-code" name="id" value="-1" />
                <input type="hidden" id="" name="prefix" value="" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-5">
                            <div id="page-accounting-code">
                                <div class="mb-1">
                                    <select name="category_id" id="AccountingCodeCategory" class="easyui-combobox" style="width:500px;height:22px;" data-options="label:'<b>*</b>Kategori Akun:',labelWidth:'150px',labelPosition:'before',panelHeight:100">
                                        @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <select name="parent" id="AccountingCodeParent" class="easyui-combobox" style="width:500px;height:22px;" data-options="label:'<b>*</b>Kode Akun Induk:',labelWidth:'150px',labelPosition:'before',panelHeight:100,valueField:'id',textField:'name'">
                                        <option value="0">---</option>
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <input name="code" id="AccountingCode" class="easyui-numberbox" style="width:300px;height:22px;" data-options="label:'<b>*</b>Kode Akun:',labelWidth:'150px',min:1" />
                                </div>
                                <div class="mb-1">
                                    <input name="name" id="AccountingCodeName" class="easyui-textbox" style="width:500px;height:22px;" data-options="label:'<b>*</b>Nama Akun:',labelWidth:'150px'" />
                                </div>
                                <div class="mb-1">
                                    <input name="remark" class="easyui-textbox" style="width:500px;height:66px;" data-options="label:'Keterangan:',labelWidth:'150px',multiline:true" />
                                </div>
                            </div>
                        </div>
                        <div class="col-7 pl-0">
                            <div class="mb-1" >
                                <table id="tb-accounting-code" style="width:100%;height:{{ $GridHeight }}" class="easyui-treegrid"
                                    data-options="animate:true,idField:'id',treeField:'name',lines: true,toolbar:toolbarCOA">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'name'" width="400">Nama Akun</th>
                                            <th data-options="field:'code',align:'center'" width="150">Kode Akun</th>
                                            <th data-options="field:'balance',align:'right',styler:cellStyler" width="150">Saldo Normal</th>
                                        </tr>  
                                    </thead>                                  
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- dialog --}}
<div id="dlg-accounting-code-balance" class="easyui-window" title="Set Saldo Awal Akun" style="width:1200px;height:550px;padding:10px" data-options="modal: true, closed:true, minimizable:false, maximizable:false, iconCls: 'ms-Icon ms-Icon--Add', footer: '#footer-accounting-code-balance'"></div>
<div id="footer-accounting-code-balance" style="padding:5px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 pr-0 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Save" onclick="saveAccountingCodeBalance()">Simpan</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Cancel" onclick="$('#dlg-accounting-code-balance').window('close')">Batal</a>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarCOA">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3"><span style="line-height: 25px;"><b>Daftar Kode Akun</b></span></div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Database" plain="true" onclick="openAccountingCodeBalance()">Saldo Awal</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Refresh" plain="true" onclick="refreshAccountingCode()">Muat ulang</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAccountingCode('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAccountingCode('excel')">Ekspor XLS</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionAccountingCode = document.getElementById("menu-act-accounting-code").getElementsByTagName("a")
    var markAccountingCode = document.getElementById("mark-accounting-code")
    var titleAccountingCode = document.getElementById("title-accounting-code")
    var idAccountingCode = document.getElementById("id-accounting-code")
    $(function () {
        sessionStorage.formKode_COA = "init"
        actionButtonAccountingCode("{{ $ViewType }}", [])
        $("#AccountingCodeName").textbox("textbox").bind("keyup", function (e) {
            titleAccountingCode.innerText = $(this).val()
        })
        //
        $("#AccountingCodeCategory").combobox({
            onClick: function (record) {
                $("#AccountingCodeParent").combobox("setValue", 0)
                $("#AccountingCodeParent").combobox("reload", "{{ url('finance/coa/combo-box') }}" + "/" + record.value + "?_token=" + "{{ csrf_token() }}")
            }
        })
        $("#AccountingCodeParent").combobox({
            onClick: function (record) {
                var prefixs = record.name.split(" | ")
            }
        })
        $("#tb-accounting-code").treegrid({
            url:'{{ url('finance/coa/data') }}',
            method:'get',
            onDblClickRow: function(row) {
                if (sessionStorage.formKode_COA == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    if (row.id > 0) {
                        titleAccountingCode.innerText = row.name
                        $("#form-accounting-code-main").form("load", "{{ url('finance/coa/show') }}" + "/" + row.id)
                        $("#page-accounting-code").waitMe("hide")
                    } else {
                        $("#form-accounting-code-main").form("reset")
                    }
                }
            }
        })
        $("#form-accounting-code-main").form({
            onLoadSuccess: function(data) {
                $("#AccountingCodeParent").combobox("reload", "{{ url('finance/coa/combo-box') }}" + "/" + data.category_id + "?_token=" + "{{ csrf_token() }}")
                var codes = data.code.split("-")
                $("#AccountingCode").numberbox("setValue", parseInt(codes[1]))
                $("#AccountingCodeName").textbox("setValue", data.name)
                if (data.locked == 1) {
                    $("#AccountingCodeCategory").combobox("readonly", true)
                    $("#AccountingCodeParent").combobox("readonly", true)
                    $("#AccountingCode").textbox("readonly", true)
                } else {
                    $("#AccountingCodeCategory").combobox("readonly", false)
                    $("#AccountingCodeParent").combobox("readonly", false)
                    $("#AccountingCode").textbox("readonly", false)
                }
                actionButtonAccountingCode("active",[2,3])
            }
        })
        $("#page-accounting-code").waitMe({effect:"none"})
        
    })
    function newAccountingCode() {
        sessionStorage.formKode_COA = "active"
        $("#form-accounting-code-main").form("reset")
        actionButtonAccountingCode("active", [0,1,4])
        markAccountingCode.innerText = "*"
        titleAccountingCode.innerText = ""
        idAccountingCode.value = "-1"
        $("#AccountingCode").numberbox('textbox').focus()
        $("#AccountingCodeCategory").combobox("readonly", false)
        $("#AccountingCodeParent").combobox("readonly", false)
        $("#AccountingCode").textbox("readonly", false)
        $("#page-accounting-code").waitMe("hide")
    }
    function editAccountingCode() {
        sessionStorage.formKode_COA = "active"
        markAccountingCode.innerText = "*"
        actionButtonAccountingCode("active", [0, 1, 4])
    }
    function saveAccountingCode() {
        if (sessionStorage.formKode_COA == "active") {
            ajaxAccountingCode("finance/coa/store")
        }
    }
    function deleteAccountingCode() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kode Akun terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('finance/coa/destroy') }}" +"/"+idAccountingCode.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAccountingCodeResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxAccountingCode(route) {
        $("#form-accounting-code-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-accounting-code").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAccountingCodeResponse(response)
                $("#page-accounting-code").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-accounting-code").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAccountingCodeResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearAccountingCode()
            $("#tb-accounting-code").treegrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearAccountingCode() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearAccountingCode()
            }
        })
    }
    function actionButtonAccountingCode(viewType, idxArray) {
        for (var i = 0; i < menuActionAccountingCode.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionAccountingCode[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionAccountingCode[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionAccountingCode[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionAccountingCode[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearAccountingCode() {
        sessionStorage.formKode_COA = "init"
        $("#form-accounting-code-main").form("reset")
        actionButtonAccountingCode("init", [])
        titleAccountingCode.innerText = ""
        markAccountingCode.innerText = ""
        idAccountingCode.value = "-1"
        $("#page-accounting-code").waitMe({effect:"none"})
    }
    function refreshAccountingCode() {
        $("#tb-accounting-code").treegrid("reload")
    }
    function openAccountingCodeBalance() {
        $("#dlg-accounting-code-balance").window("open")
        $("#dlg-accounting-code-balance").window("refresh", "{{ url('finance/coa/balance') }}")
    }
    function saveAccountingCodeBalance() {
        var dgActiva = $("#tb-accounting-code-balance-activa").datagrid("getData")
        var dgPassiva = $("#tb-accounting-code-balance-passiva").datagrid("getData")
        var dgActivaTotal = $("#tb-accounting-code-balance-activa").datagrid("getFooterRows")
        var dgPassivaTotal = $("#tb-accounting-code-balance-passiva").datagrid("getFooterRows")
        if (dgActivaTotal[0].total !== dgPassivaTotal[0].total) {
            $.messager.confirm({
                title: 'Konfirmasi',
                msg: 'Jumlah Harta dan Kewajiban + Modal tidak sama, selisih akan disimpan ke akun Ekuitas Saldo Awal, lanjutkan?',
                fn: function(r){
                    if (r){
                        storeAccountingCodeBalance(dgActiva, dgPassiva)
                    }
                }
            })
        } else {
            storeAccountingCodeBalance(dgActiva, dgPassiva)
        }
        return false
    }
    function storeAccountingCodeBalance(dgActiva, dgPassiva) {
        $("#form-accounting-code-balance").ajaxSubmit({
            url: "{{ url('finance/coa/balance/store') }}",
            data: { _token: '{{ csrf_token() }}', activa: dgActiva.rows, passiva: dgPassiva.rows },
            success: function(response) {
                ajaxAccountingCodeResponse(response)
                $("#dlg-accounting-code-balance").window("close")
            },
            error: function(xhr) {
                failResponse(xhr)
            }
        })
    }
    function exportAccountingCode(document) {
        exportDocument("{{ url('finance/coa/export-') }}" + document,[],"Ekspor Kode Akun (COA) ke "+ document.toUpperCase(),"{{ csrf_token() }}")
    }
    function cellStyler(value,row,index) {
        if (row.parent == 0) {
            return 'font-weight:bold;';
        }
    }
</script>
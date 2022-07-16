@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 353 . "px";
    $SubGridHeight = $InnerHeight - 326 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Transaksi Jurnal Umum</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportJournalVoucher('pdf')">Ekspor PDF</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportJournalVoucher('excel')">Ekspor Excel</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-journal-voucher" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-journal-voucher" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-journal-voucher" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fdate-from-journal-voucher" class="easyui-datebox" style="width:210px;height:22px;" data-options="label:'Dari Tgl:',labelWidth:100,formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                </div>
                <div class="mb-1">
                    <input id="fdate-to-journal-voucher" class="easyui-datebox" style="width:210px;height:22px;" data-options="label:'Sampai Tgl:',labelWidth:100,formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                </div>
                <div class="mb-1">
                    <input id="fname-journal-voucher" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'No.Jurnal:',labelWidth:100" />
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterJournalVoucher({fdept: $('#fdept-journal-voucher').combobox('getValue'),fstart: $('#fdate-from-journal-voucher').datebox('getValue'),fend: $('#fdate-to-journal-voucher').datebox('getValue'),fname: $('#fname-journal-voucher').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-journal-voucher').form('reset');filterJournalVoucher({})">Batal</a>
                </div>
            </form>
            <table id="tb-journal-voucher" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'department',width:100,resizeable:true,sortable:true">Departemen</th>
                        <th data-options="field:'date_journal',width:80,resizeable:true,sortable:true,align:'center'">Tanggal</th>
                        <th data-options="field:'cash_no',width:80,resizeable:true,sortable:true,align:'center'">No. Jurnal</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-journal-voucher" class="panel-top">
            <a id="newJournalVoucher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newJournalVoucher()">Baru</a>
            <a id="editJournalVoucher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editJournalVoucher()">Ubah</a>
            <a id="saveJournalVoucher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveJournalVoucher()">Simpan</a>
            <a id="clearJournalVoucher" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearJournalVoucher()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-journal-voucher"></span>Transaksi Jurnal Umum: <span id="title-journal-voucher"></span></h6>
        </div>
        <div id="page-journal-voucher" class="pl-2 pt-3 pr-2">
            <form id="form-journal-voucher-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-4">
                            <input type="hidden" id="id-journal-voucher" name="id" value="-1" />
                            <input type="hidden" name="bookyear_id" value="{{ $bookyear->id }}" />
                            <input type="hidden" id="id-journal-voucher-dept" value="-1" />
                            <input type="hidden" name="journal_id" id="id-journal-voucher-journal" value="-1" />
                            <div class="mb-1">
                                <input class="easyui-textbox" style="width:240px;height:22px;" data-options="label:'Tahun Buku:',labelWidth:'125px',readonly:true" value="{{ $bookyear->book_year }}" />
                            </div>
                            <div class="mb-1">
                                <input name="trans_date" id="AccountingJournalVoucherDate" class="easyui-datebox" style="width:240px;height:22px;" data-options="label:'<b>*</b>Tanggal:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" value="{{ date('d/m/Y') }}" />
                            </div>
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="AccountingJournalVoucherDept" class="easyui-combobox" style="width:300px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="purpose" id="AccountingJournalVoucherPurpose" class="easyui-textbox" style="width:300px;height:66px;" data-options="label:'<b>*</b>Keperluan:',labelWidth:'125px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="remark" id="AccountingJournalVoucherRemark" class="easyui-textbox" style="width:300px;height:66px;" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                            </div>
                            <div class="mb-1 d-none" id="AccountingJournalVoucherReason">
                                <input name="reason" class="easyui-textbox" style="width:300px;height:66px;" data-options="label:'<b>*</b>Alasan Ubah Data:',labelWidth:'125px',multiline:true" />
                            </div>
                        </div>
                        <div class="col-8 pl-0">
                            <div class="mb-1">
                                <select id="AccountingJournalVoucherCode" class="easyui-combogrid" style="width:625px;height:22px;" data-options="
                                    label:'Pilih Akun utk menambah/Klik kanan utk menghapus.',
                                    labelPosition: 'after',
                                    labelWidth:'400px',
                                    panelWidth: 500,
                                    idField: 'code',
                                    textField: 'name',
                                    fitColumns:true,
                                    pagination:'true',
                                    pageSize:50,pageList:[10,25,50,75,100],
                                    columns: [[
                                        {field:'category_id',title:'Kategori',width:150},
                                        {field:'code',title:'Kode Akun',width:100,align:'center'},
                                        {field:'name',title:'Nama Akun',width:250},
                                    ]],
                                ">
                                </select>
                            </div>
                            <table id="tb-journal-voucher-detail" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}" 
                                data-options="method:'post',rownumbers:'true',showFooter:'true'">
                                <thead>
                                    <tr>
                                        <th data-options="field:'id',width:50,hidden:'true'">ID</th>
                                        <th data-options="field:'code',width:100,align:'center'">Kode Akun</th>
                                        <th data-options="field:'name',width:230">Nama Akun</th>
                                        <th data-options="
                                            field:'debit',
                                            width:150,
                                            align:'right',
                                            editor:{type:'numberbox',options:{min:0,precision:2}},
                                            formatter:function(value,row){return calculateTotalJournalVoucher(value)}">Debit</th>
                                        <th data-options="
                                            field:'credit',
                                            width:150,
                                            align:'right',
                                            editor:{type:'numberbox',options:{min:0,precision:2}},
                                            formatter:function(value,row){return calculateTotalJournalVoucher(value)}">Kredit</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="table-filter mt-1" style="border:solid 1px #d5d5d5;">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td style="width:30px;"></td>
                                            <td style="width:100px;"></td>
                                            <td style="width:230px;text-align: right;"><b>Total</b></td>
                                            <td style="width:150px;text-align: right;"><b><span id="AccountingJournalVoucherTotalDebit">Rp0</span></b></td>
                                            <td style="width:150px;text-align: right;"><b><span id="AccountingJournalVoucherTotalCredit">Rp0</span></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- journal context menu --}}
<div id="tb-journal-voucher-detail-ctxmenu" class="easyui-menu" style="width:120px;">
    <div>Hapus</div>
</div>
<script type="text/javascript">
    var menuActionJournalVoucher = document.getElementById("menu-act-journal-voucher").getElementsByTagName("a")
    var titleJournalVoucher = document.getElementById("title-journal-voucher")
    var markJournalVoucher = document.getElementById("mark-journal-voucher")
    var idJournalVoucher = document.getElementById("id-journal-voucher")
    var dgJournalVoucher = $("#tb-journal-voucher")
    var totalDebit = 0
    var totalCredit = 0
    $(function () {
        sessionStorage.formJurnal_Umum = "init"
        dgJournalVoucher.datagrid({
            url: "{{ url('finance/journal/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formJurnal_Umum == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleJournalVoucher.innerText = row.cash_no
                    actionButtonJournalVoucher("active",[2,3])
                    $("#form-journal-voucher-main").form("reset")
                    idJournalVoucher.value = row.id
                    $("#id-journal-voucher-dept").val(row.department_id)
                    $("#id-journal-voucher-journal").val(row.journal_id)
                    $("#AccountingJournalVoucherBookYear").textbox("setValue", row.book_year)
                    $("#AccountingJournalVoucherDept").combobox("setValue", row.department_id)
                    $("#AccountingJournalVoucherDate").textbox("setValue", row.date_journal)
                    $("#AccountingJournalVoucherPurpose").textbox("setValue", row.purpose)
                    $("#AccountingJournalVoucherRemark").textbox("setValue", row.remark)
                    $("#tb-journal-voucher-detail").datagrid("reload", "{{ url('finance/journal/data/detail') }}" + "?_token=" + "{{ csrf_token() }}" + "&journal_id=" + row.journal_id)
                    $("#AccountingJournalVoucherReason").removeClass("d-none")
                    $("#page-journal-voucher").waitMe("hide")
                }
            }
        })
        dgJournalVoucher.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        var pagerJournalVoucher = dgJournalVoucher.datagrid('getPager').pagination()
        pagingGrid(dgJournalVoucher.datagrid('getPager').pagination())
        actionButtonJournalVoucher("{{ $ViewType }}", [])
        $("#AccountingJournalVoucherPurpose").textbox("textbox").bind("keyup", function (e) {
            titleJournalVoucher.innerText = $(this).val()
        })
        $("#AccountingJournalVoucherCode").combogrid({
            url: "{{ url('finance/coa/combo-grid') }}",
            method: 'post',
            mode:'remote',
            queryParams: { _token: "{{ csrf_token() }}", category: 0 },
            onClickRow: function(index, row) {
                $("#tb-journal-voucher-detail").datagrid("appendRow",{
                    id: row.id,
                    code: row.code,
                    name: row.name,
                })
            }
        })
        $("#tb-journal-voucher-detail").datagrid('enableCellEditing').datagrid({
            onRowContextMenu: function(e,index,row) {
                e.preventDefault()
                $("#tb-journal-voucher-detail-ctxmenu").menu("show", {
                    left: e.pageX,
                    top: e.pageY
                })
                $("#tb-journal-voucher-detail-ctxmenu").menu({
                    onClick: function(item) {
                        if (index > -1) {
                            $("#tb-journal-voucher-detail").datagrid("deleteRow", index)
                            calculateTotalDebitCredit()
                        }
                    }
                })
            },
            onEndEdit: function(index,row,changes) {
                calculateTotalDebitCredit()
            },
            onLoadSuccess: function(data) {
                calculateTotalDebitCredit()
            }
        })
        $("#page-journal-voucher").waitMe({effect:"none"})
    })
    function calculateTotalDebitCredit() {
        var dgDetail = $("#tb-journal-voucher-detail").datagrid("getData")
        var TotalDebit = 0
        var TotalCredit = 0
        if (dgDetail.rows.length > 0) {
            for (var i = 0; i < dgDetail.rows.length; i++) {
                if (typeof dgDetail.rows[i].debit !== "undefined") {
                    if (!isNaN(parseFloat(dgDetail.rows[i].debit))) {
                        TotalDebit += parseFloat(dgDetail.rows[i].debit)
                    }
                }
                if (typeof dgDetail.rows[i].credit !== "undefined") {
                    if (!isNaN(parseFloat(dgDetail.rows[i].credit))) {
                        TotalCredit += parseFloat(dgDetail.rows[i].credit)
                    }
                }
            }
        }
        $("#AccountingJournalVoucherTotalDebit").text(currencyFormat(TotalDebit))
        $("#AccountingJournalVoucherTotalCredit").text(currencyFormat(TotalCredit))
    }
    function calculateTotalJournalVoucher(value) {
        let rupiahIDLocale = Intl.NumberFormat('id-ID')
        if (typeof(value) !== "undefined") {
            return 'Rp' + rupiahIDLocale.format(value)
        } else {
            return 'Rp0'
        }
    }
    function filterJournalVoucher(params) {
        if (Object.keys(params).length > 0) {
            dgJournalVoucher.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgJournalVoucher.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newJournalVoucher() {
        sessionStorage.formJurnal_Umum = "active"
        $("#form-journal-voucher-main").form("reset")
        actionButtonJournalVoucher("active", [0,1])
        markJournalVoucher.innerText = "*"
        titleJournalVoucher.innerText = ""
        idJournalVoucher.value = "-1"
        $("#id-journal-voucher-bookyear").val("-1")
        $("#id-journal-voucher-dept").val("-1")
        $("#id-journal-voucher-journal").val("-1")
        $("#AccountingJournalVoucherReason").addClass("d-none")
        $("#tb-journal-voucher-detail").datagrid("loadData",[])
        $("#AccountingJournalVoucherTotalDebit").text(currencyFormat())
        $("#AccountingJournalVoucherTotalCredit").text(currencyFormat())
        totalDebit = 0
        totalCredit = 0
        $("#page-journal-voucher").waitMe("hide")
    }
    function editJournalVoucher() {
        sessionStorage.formJurnal_Umum = "active"
        markJournalVoucher.innerText = "*"
        actionButtonJournalVoucher("active", [0,1])
    }
    function saveJournalVoucher() {
        if (sessionStorage.formJurnal_Umum == "active") {
            ajaxJournalVoucher("finance/journal/store")
        }
    }
    function ajaxJournalVoucher(route) {
        var dg = $("#tb-journal-voucher-detail").datagrid("getData")
        if (dg.rows.length > 0)
        {
            $("#form-journal-voucher-main").ajaxSubmit({
                url: route,
                data: { _token: '{{ csrf_token() }}', rows: dg.rows, totalDebit: $("#AccountingJournalVoucherTotalDebit").text().replace("Rp",""), totalCredit: $("#AccountingJournalVoucherTotalCredit").text().replace("Rp","") },
                beforeSubmit: function(formData, jqForm, options) {
                    $("#page-journal-voucher").waitMe({effect:"facebook"})
                },
                success: function(response) {
                    ajaxAdmissionResponse(response)
                    $("#page-journal-voucher").waitMe("hide")
                },
                error: function(xhr) {
                    failResponse(xhr)
                    $("#page-journal-voucher").waitMe("hide")
                }
            })
        }
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearJournalVoucher()
            $("#tb-journal-voucher").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearJournalVoucher() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearJournalVoucher()
            }
        })
    }
    function actionButtonJournalVoucher(viewType, idxArray) {
        for (var i = 0; i < menuActionJournalVoucher.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionJournalVoucher[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionJournalVoucher[i].id).linkbutton({disabled: true})
                }
            } else if (viewType == "subactive") {
                $("#" + menuActionJournalVoucher[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionJournalVoucher[idxArray[j]].id).linkbutton({ disabled: true })
                }
            } else {
                $("#" + menuActionJournalVoucher[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionJournalVoucher[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearJournalVoucher() {
        sessionStorage.formJurnal_Umum = "init"
        $("#form-journal-voucher-main").form("reset")
        actionButtonJournalVoucher("init", [])
        titleJournalVoucher.innerText = ""
        markJournalVoucher.innerText = ""
        idJournalVoucher.value = "-1"
        $("#AccountingJournalVoucherReason").addClass("d-none")
        $("#tb-journal-voucher-detail").datagrid("loadData", [])
        $("#AccountingJournalVoucherTotalDebit").text(currencyFormat())
        $("#AccountingJournalVoucherTotalCredit").text(currencyFormat())
        totalDebit = 0
        totalCredit = 0
        $("#page-journal-voucher").waitMe({effect:"none"})
    }
    function exportJournalVoucher(document) {
        var dg = $("#tb-journal-voucher").datagrid('getData')
        if (dg.total > 0) {
            var payload = {rows: dg.rows, start: $("#fdate-from-journal-voucher").datebox("getValue"), end: $("#fdate-to-journal-voucher").datebox("getValue")}
            exportDocument("{{ url('finance/journal/export-') }}" + document,payload,"Ekspor data Jurnal Umum ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
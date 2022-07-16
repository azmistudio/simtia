@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 353 . "px";
    $SubGridHeight = $InnerHeight - 318 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Transaksi Pengeluaran</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportExpenditures('pdf')">Ekspor PDF</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportExpenditures('excel')">Ekspor Excel</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-expenditure" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-expenditure" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-expenditure" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fdate-from-expenditure" class="easyui-datebox" style="width:210px;height:22px;" data-options="label:'Dari Tgl:',labelWidth:100,formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                </div>
                <div class="mb-1">
                    <input id="fdate-to-expenditure" class="easyui-datebox" style="width:210px;height:22px;" data-options="label:'Sampai Tgl:',labelWidth:100,formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                </div>
                <div class="mb-1">
                    <input id="fjournal-expenditure" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'No. Jurnal:',labelWidth:100"/>
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterExpenditure({fdept: $('#fdept-expenditure').combobox('getValue'),fstart: $('#fdate-from-expenditure').datebox('getValue'),fend: $('#fdate-to-expenditure').datebox('getValue'),fjournal: $('#fjournal-expenditure').textbox('getValue')})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-expenditure').form('reset');filterExpenditure({})">Batal</a>
                </div>
            </form>
            <table id="tb-expenditure" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'department',width:100,resizeable:true,sortable:true">Departemen</th>
                        <th data-options="field:'cash_no',width:80,resizeable:true,sortable:true,align:'center'">Jurnal</th>
                        <th data-options="field:'total',width:100,resizeable:true,sortable:true,align:'right'">Total</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-expenditure" class="panel-top">
            <a id="newExpenditure" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newExpenditure()">Baru</a>
            <a id="editExpenditure" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editExpenditure()">Ubah</a>
            <a id="saveExpenditure" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveExpenditure()">Simpan</a>
            <a id="clearExpenditure" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearExpenditure()">Batal</a>
            <a id="pdfExpenditure" class="easyui-linkbutton" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfExpenditure()">Cetak</a>
        </div>
        <div class="title">
            <h6><span id="mark-expenditure"></span>No. Jurnal: <span id="title-expenditure"></span></h6>
        </div>
        <div id="page-expenditure" class="pl-2 pt-3 pr-2">
            <form id="form-expenditure-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-4 pr-0">
                            <input type="hidden" id="id-expenditure" name="id" value="-1" />
                            <input type="hidden" id="id-expenditure-employee" name="employee_id" value="-1" />
                            <input type="hidden" id="id-expenditure-student" name="student_id" value="-1" />
                            <input type="hidden" id="id-expenditure-other" name="requested_id" value="-1" />
                            <input type="hidden" id="id-expenditure-journal" name="journal_id" value="-1" />
                            <input type="hidden" id="id-expenditure-dept" value="-1" />
                            <input type="hidden" id="id-expenditure-department" value="" />
                            <div class="mb-1">
                                <input id="AccountingExpenditureBookYear" class="easyui-textbox" style="width:260px;height:22px;" data-options="label:'Tahun Buku:',labelWidth:'150px',readonly:true" value="{{ $bookyear->book_year }}" />
                            </div>
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:320px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="AccountingExpenditureDept" class="easyui-combobox" style="width:320px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'150px',labelPosition:'before',panelHeight:125">
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-3">
                                <input name="trans_date" class="easyui-datebox" style="width:260px;height:22px;" data-options="label:'<b>*</b>Tanggal Transaksi:',labelWidth:'150px',formatter:dateFormatter,parser:dateParser" value="{{ date('d/m/Y') }}" />
                            </div>
                            <div class="mb-2">
                                <label class="mb-1" style="width:146px;"><b>*</b>Dibayar dari Kas/Bank:</label><br/>
                                <select name="debit_account" id="AccountingExpenditureDebit" class="easyui-combobox" style="width:320px;height:22px;" data-options="panelHeight:150">
                                    @foreach ($codes_debit as $code)
                                    <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="textbox-label textbox-label-before" style="text-align: left; width: 170px; height: 22px; line-height: 22px;"><b>*</b>Pemohon:</label><br/>
                                <input name="requested_by" class="easyui-radiobutton requested_by" value="1" data-options="label:'Pegawai',labelPosition:'after'" checked="checked" />
                                <input name="requested_by" class="easyui-radiobutton requested_by" value="2" data-options="label:'Santri',labelPosition:'after'" />
                                <input name="requested_by" class="easyui-radiobutton requested_by" value="3" data-options="label:'Lainnya',labelPosition:'after'" />
                            </div>
                            <div class="mb-2">
                                <input name="requested_name" id="AccountingExpenditureReceived" class="easyui-textbox" style="width:285px;height:22px;" data-options="readonly:true" />
                                <span class="mr-1"></span>
                                <a class="easyui-linkbutton small-btn" onclick="getRequestedPerson()" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Search"></i></a>
                            </div>
                            <div class="mb-2">
                                <label class="mb-1" style="width:146px;"><b>*</b>Penerima:</label><br/>
                                <input name="received_name" id="AccountingExpenditureReceivedName" class="easyui-textbox" style="width:320px;height:22px;" />
                            </div>
                            <div class="mb-2">
                                <label class="mb-1" style="width:146px;">Keterangan/Referensi:</label><br/>
                                <input name="remark" class="easyui-textbox" style="width:320px;height:22px;" />
                            </div>
                            <div class="mb-1 d-none" id="AccountingExpenditureReason">
                                <label class="mb-1" style="width:146px;"><b>*</b>Alasan Ubah Data:</label><br/>
                                <input name="reason" class="easyui-textbox" style="width:320px;height:34px;" data-options="multiline:true" />
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="mb-1">
                                <select id="AccountingExpenditureCode" class="easyui-combogrid" style="width:625px;height:22px;" data-options="
                                    label:'Pilih Akun utk menambah/Klik kanan utk menghapus.',
                                    labelPosition: 'after',
                                    labelWidth:'400px',
                                    panelWidth: 500,
                                    idField: 'code',
                                    textField: 'name',
                                    fitColumns:true,
                                    pagination:'true',
                                    pageSize:30,
                                    columns: [[
                                        {field:'category_id',title:'Kategori',width:150},
                                        {field:'code',title:'Kode Akun',width:100,align:'center'},
                                        {field:'name',title:'Nama Akun',width:250},
                                    ]],
                                ">
                                </select>
                            </div>
                            <table id="tb-expenditure-detail" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}" 
                                data-options="method:'post',rownumbers:'true',showFooter:'true'">
                                <thead>
                                    <tr>
                                        <th data-options="field:'id',width:50,hidden:'true'">ID</th>
                                        <th data-options="field:'code',width:100,align:'center'">Kode Akun</th>
                                        <th data-options="field:'name',width:200">Nama Akun</th>
                                        <th data-options="field:'remark',width:220,editor:'text'">Deskripsi</th>
                                        <th data-options="
                                            field:'credit',
                                            width:100,
                                            align:'right',
                                            editor:{type:'numberbox',options:{min:0,precision:2}},
                                            formatter:function(value,row){return calculateTotalExpenditure(value)}">Jumlah</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="table-filter mt-1" style="border:solid 1px #d5d5d5;">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td style="width:30px;"></td>
                                            <td style="width:100px;"></td>
                                            <td style="width:200px;text-align: right;"><b>Total</b></td>
                                            <td style="width:320px;text-align: right;"><b><span id="AccountingExpenditureTotal">Rp0</span></b></td>
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
{{-- context menu --}}
<div id="tb-expenditure-ctxmenu" class="easyui-menu" style="width:120px;">
    <div>Hapus</div>
</div>
<script type="text/javascript">
    var menuActionExpenditure = document.getElementById("menu-act-expenditure").getElementsByTagName("a")
    var titleExpenditure = document.getElementById("title-expenditure")
    var markExpenditure = document.getElementById("mark-expenditure")
    var idExpenditure = document.getElementById("id-expenditure")
    var dgExpenditure = $("#tb-expenditure")
    $(function () {
        sessionStorage.formTransaksi_Pengeluaran = "init"
        dgExpenditure.datagrid({
            url: "{{ url('finance/expenditure/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTransaksi_Pengeluaran == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleExpenditure.innerText = row.cash_no
                    actionButtonExpenditure("active",[2,3])
                    $("#form-expenditure-main").form("reset")
                    $("#id-expenditure-dept").val(row.department_id)
                    $("#id-expenditure-department").val(row.department)
                    $("#form-expenditure-main").form("load", "{{ url('finance/expenditure/show/') }}" + "/" + row.id)
                    $("#tb-expenditure-detail").datagrid("reload", "{{ url('finance/expenditure/data/journal') }}" + "/" + row.id + "?_token=" + "{{ csrf_token() }}")
                    $("#AccountingExpenditureTotal").text(row.total)
                    $("#AccountingExpenditureReason").removeClass("d-none")
                    $("#page-expenditure").waitMe("hide")
                }
            }
        })
        dgExpenditure.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgExpenditure.datagrid('getPager').pagination())
        actionButtonExpenditure("{{ $ViewType }}", [])
        $("#form-expenditure-main").form({
            onLoadSuccess: function(data) {
                $("#AccountingExpenditureBookYear").textbox("setValue", data.book_year)
            }
        })
        $("#AccountingExpenditureCode").combogrid({
            url: "{{ url('finance/coa/combo-grid') }}",
            method: 'post',
            mode:'remote',
            queryParams: { _token: "{{ csrf_token() }}", category: 5 },
            onClickRow: function(index, row) {
                $("#tb-expenditure-detail").datagrid("appendRow",{
                    id: row.id,
                    code: row.code,
                    name: row.name,
                })
            }
        })
        $("#tb-expenditure-detail").datagrid('enableCellEditing').datagrid({
            onRowContextMenu: function(e,index,row) {
                e.preventDefault()
                $("#tb-expenditure-ctxmenu").menu("show", {
                    left: e.pageX,
                    top: e.pageY
                })
                $("#tb-expenditure-ctxmenu").menu({
                    onClick: function(item) {
                        if (index > -1) {
                            $("#tb-expenditure-detail").datagrid("deleteRow", index)
                            calculateTotal()
                        }
                    }
                })
            },
            onEndEdit: function(index,row,changes) {
                calculateTotal()
            }
        })
        $("#page-expenditure").waitMe({effect:"none"})
    })
    function filterExpenditure(params) {
        if (Object.keys(params).length > 0) {
            dgExpenditure.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgExpenditure.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newExpenditure() {
        sessionStorage.formTransaksi_Pengeluaran = "active"
        $("#form-expenditure-main").form("reset")
        actionButtonExpenditure("active", [0,1,4])
        markExpenditure.innerText = "*"
        titleExpenditure.innerText = ""
        idExpenditure.value = "-1"
        $("#AccountingExpenditureReason").addClass("d-none")
        $("#id-expenditure-employee").val(-1)
        $("#id-expenditure-student").val(-1)
        $("#id-expenditure-other").val(-1)
        $("#id-expenditure-bookyear").val(-1)
        $("#id-expenditure-journal").val(-1)
        $("#id-expenditure-dept").val(-1)
        $("#tb-expenditure-detail").datagrid("loadData", [])
        $("#AccountingExpenditureTotal").text(currencyFormat())
        $("#page-expenditure").waitMe("hide")
    }
    function editExpenditure() {
        sessionStorage.formTransaksi_Pengeluaran = "active"
        markExpenditure.innerText = "*"
        actionButtonExpenditure("active", [0,1,4])
    }
    function saveExpenditure() {
        if (sessionStorage.formTransaksi_Pengeluaran == "active") {
            ajaxExpenditure("finance/expenditure/store")
        }
    }
    function ajaxExpenditure(route) {
        var dg = $("#tb-expenditure-detail").datagrid("getData")
        if (dg.rows.length > 0)
        {
            $("#form-expenditure-main").ajaxSubmit({
                url: route,
                data: { _token: '{{ csrf_token() }}', rows: dg.rows, totalCredit: $("#AccountingExpenditureTotal").text().replace("Rp","") },
                beforeSubmit: function(formData, jqForm, options) {
                    $("#page-expenditure").waitMe({effect:"facebook"})
                },
                success: function(response) {
                    ajaxAdmissionResponse(response)
                    $("#page-expenditure").waitMe("hide")
                },
                error: function(xhr) {
                    failResponse(xhr)
                    $("#page-expenditure").waitMe("hide")
                }
            })
        }
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearExpenditure()
            $("#tb-expenditure").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearExpenditure() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearExpenditure()
            }
        })
    }
    function actionButtonExpenditure(viewType, idxArray) {
        for (var i = 0; i < menuActionExpenditure.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionExpenditure[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionExpenditure[i].id).linkbutton({disabled: true})
                }
            } else if (viewType == "subactive") {
                $("#" + menuActionExpenditure[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionExpenditure[idxArray[j]].id).linkbutton({ disabled: true })
                }
            } else {
                $("#" + menuActionExpenditure[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionExpenditure[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearExpenditure() {
        sessionStorage.formTransaksi_Pengeluaran = "init"
        $("#form-expenditure-main").form("reset")
        actionButtonExpenditure("init", [])
        titleExpenditure.innerText = ""
        markExpenditure.innerText = ""
        idExpenditure.value = "-1"
        $("#AccountingExpenditureReason").addClass("d-none")
        $("#tb-expenditure-detail").datagrid("loadData", [])
        $("#AccountingExpenditureTotal").text(currencyFormat())
        $("#page-expenditure").waitMe({effect:"none"})
    }
    function exportExpenditures(document) {
        var dg = $("#tb-expenditure").datagrid('getData')
        if (dg.total > 0) {
            var payload = {rows: dg.rows, start: $("#fdate-from-expenditure").datebox("getValue"), end: $("#fdate-to-expenditure").datebox("getValue"),}
            exportDocument("{{ url('finance/expenditure/export-') }}" + document,payload,"Ekspor data Pengeluaran ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function pdfExpenditure() {
        exportDocument("{{ url('finance/expenditure/print/receipt') }}", { 
            id: idExpenditure.value,
        }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
    function getRequestedPerson() {
        var requested_by = $("input[name='requested_by']:checked").val()
        var title
        if (requested_by == 2) {
            title = "Data Santri"
        } else if (requested_by == 3) {
            title = "Data Pemohon Lainnya"
        } else {
            title = "Data Pegawai"
        }
        var id_department = {{ auth()->user()->department_id }}
        @if (auth()->user()->getDepartment->is_all == 1)
            id_department = $("#AccountingExpenditureDept").combobox("getValue")
        @endif 
        $("#receipt-w").window("open")
        $("#receipt-w").window("setTitle", title)
        $("#receipt-w").window("refresh", "{{ url('finance/expenditure/requested') }}" + "?requested_by=" + requested_by + "&department_id=" + id_department)
    }
    function calculateTotal() {
        var dgDetail = $("#tb-expenditure-detail").datagrid("getData")
        var TotalCredit = 0
        if (dgDetail.rows.length > 0) {
            for (var i = 0; i < dgDetail.rows.length; i++) {
                if (typeof dgDetail.rows[i].credit !== "undefined") {
                    if (!isNaN(parseFloat(dgDetail.rows[i].credit))) {
                        TotalCredit += parseFloat(dgDetail.rows[i].credit)
                    }
                }
            }
        }
        $("#AccountingExpenditureTotal").text(currencyFormat(TotalCredit))
    }
    function calculateTotalExpenditure(value) {
        let rupiahIDLocale = Intl.NumberFormat('id-ID')
        if (typeof(value) !== "undefined") {
            return 'Rp' + rupiahIDLocale.format(value)
        } else {
            return 'Rp0'
        }
    }
</script>
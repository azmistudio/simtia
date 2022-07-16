@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 326 . "px";
    $SubGridHeight = $InnerHeight - 427 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Transaksi Tabungan Santri</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:250px">
        <div class="p-1">
            <form id="ff-student-saving" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select id="fclass-student-saving" class="easyui-combogrid" style="width:235px;height:22px;" data-options="
                        label:'Kelas:',
                        panelWidth: 570,
                        idField: 'id',
                        textField: 'class',
                        url: '{{ url('academic/class/student/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        fitColumns:true,
                        queryParams: { _token: '{{ csrf_token() }}' },
                        columns: [[
                            {field:'department',title:'Departemen',width:150},
                            {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                            {field:'grade',title:'Tingkat',width:80,align:'center'},
                            {field:'class',title:'Kelas',width:120},
                            {field:'capacity',title:'Kapasitas/Terisi',width:120},
                        ]],
                    ">
                    </select>
                </div>
                <div class="mb-1">
                    <input id="fnis-student-saving" class="easyui-textbox" style="width:235px;height:22px;" label="NIS">
                </div>
                <div class="mb-1">
                    <input id="fname-student-saving" class="easyui-textbox" style="width:235px;height:22px;" data-options="label:'Nama:'">
                </div>
                <div style="margin-left:80px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterStudentSaving({fclass: $('#fclass-student-saving').combogrid('getValue'),fnis: $('#fnis-student-saving').textbox('getValue'),fname: $('#fname-student-saving').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-student-saving').form('reset');filterStudentSaving({})">Batal</a>
                </div>
            </form>
            <table id="tb-student-saving" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'student_no',width:80,resizeable:true,sortable:true">NIS</th>
                        <th data-options="field:'name',width:120,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-student-saving" class="panel-top">
            <a id="newStudentSaving" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newStudentSaving()">Baru</a>
            <a id="editStudentSaving" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editStudentSaving()">Ubah</a>
            <a id="saveStudentSaving" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveStudentSaving()">Simpan</a>
            <a id="clearStudentSaving" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearStudentSaving()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-student-saving"></span>Jenis Tabungan: <span id="title-student-saving"></span></h6>
        </div>
        <div class="pl-2 pt-3 pr-2" id="page-student-saving-main">
            <div class="container-fluid">
                <div class="row">
                    <div id="page-student-saving" class="col-4">
                        <form id="form-student-saving-main" method="post">
                        <input type="hidden" id="id-student-saving" name="id" value="-1" />
                        <input type="hidden" id="id-student-saving-studentid" name="student_id" value="-1" />
                        <input type="hidden" id="id-student-saving-bookyear" name="bookyear_id" value="{{ $bookyear->id }}" />
                        <input type="hidden" id="id-student-saving-journal" name="journal_id" value="-1" />
                        <input type="hidden" id="id-student-saving-dept" name="department_id" value="-1" />
                        <input type="hidden" name="is_employee" value="0" />
                        <div class="mb-1">
                            <input name="student_no" class="easyui-textbox" id="AccountingStudentSavingStudentNo" style="width:300px;height:22px;" data-options="labelWidth:'125px',readonly:'true'" label="NIS" />
                        </div>
                        <div class="mb-1">
                            <input name="student_name" class="easyui-textbox" id="AccountingStudentSavingStudentName" style="width:300px;height:22px;" data-options="label:'Nama:',labelWidth:'125px',readonly:'true'" />
                        </div>
                        <div class="mb-1">
                            <input name="class_name" class="easyui-textbox" id="AccountingStudentSavingClass" style="width:300px;height:22px;" data-options="labelWidth:'125px',readonly:'true'" label="Tingkat/Kelas" />
                        </div>
                        <div class="mb-1">
                            <input id="AccountingStudentSavingDept" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                        </div>
                        <div class="mb-1">
                            <input class="easyui-textbox" style="width:200px;height:22px;" data-options="label:'Tahun Buku:',labelWidth:'125px',readonly:true" value="{{ $bookyear->book_year }}" />
                        </div>
                        <div class="mb-1">
                            <select name="saving_id" id="AccountingStudentSavingTypes" class="easyui-combogrid" style="width:300px;height:22px;" data-options="
                                label:'<b>*</b>Jenis Tabungan:',
                                labelWidth:'125px',
                                panelWidth: 400,
                                idField: 'id',
                                textField: 'name',
                                fitColumns:true,
                                mode: 'remote',
                                columns: [[
                                    {field:'department',title:'Departemen',width:150},
                                    {field:'name',title:'Nama',width:250},
                                ]],
                            ">
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="textbox-label textbox-label-before" style="text-align: left; width: 120px; height: 22px; line-height: 22px;"><b>*</b>Jenis Transaksi:</label>
                            <input name="transaction_type" class="easyui-radiobutton ttype" value="credit" data-options="label:'SETOR',labelWidth:'60px',labelPosition:'after'" checked="checked" />
                            <input name="transaction_type" class="easyui-radiobutton ttype" value="debit" data-options="label:'TARIK',labelWidth:'60px',labelPosition:'after'" />
                        </div>
                        <div class="mb-1">
                            <input name="amount" id="AccountingStudentSavingAmount" class="easyui-numberbox" style="width:300px;height:22px;" data-options="label:'<b>*</b>Jumlah:',labelWidth:'125px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" value="0" />
                        </div>
                        <div class="mb-2">
                            <input name="trans_date" class="easyui-datebox" style="width:240px;height:22px;" data-options="label:'<b>*</b>Tanggal Transaksi:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" value="{{ date('d/m/Y') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="mb-1" style="width:121px;">*Rekening Kas:</label>
                            <select name="cash_account" id="AccountingStudentSavingAccount" class="easyui-combobox" style="width:300px;height:22px;" data-options="panelHeight:150">
                                @foreach ($codes_cash as $code)
                                <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="mb-1" style="width:121px;">Keterangan:</label>
                            <input name="remark" id="AccountingStudentSavingRemark" class="easyui-textbox" style="width:300px;height:22px;" />
                        </div>
                        <div id="student-saving-reason" class="mb-1 d-none">
                            <label class="mb-1" style="width:121px;">*Alasan Ubah Data:</label>
                            <input name="reason" id="AccountingStudentSavingReason" class="easyui-textbox" style="width:300px;height:22px;" />
                        </div>
                        </form>
                    </div>
                    <div class="col-8 pl-0">
                        <div class="mb-2" style="padding-left:11px;">
                            <select id="AccountingStudentSavingTypeFilter" class="easyui-combobox" style="width:600px;height:22px;" data-options="label:'<b>Filter Jenis Tabungan</b>:',labelWidth:'160px',panelHeight:150,valueField:'id',textField:'text'"></select>
                        </div>
                        <fieldset class="mb-2">
                            <legend><b>Informasi Tabungan</b></legend>
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td width="25%"><b>Saldo</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingStudentSavingBalance"></span></b></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b>Jumlah Setoran</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingStudentSavingDebit"></span></b></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b>Setoran Terakhir</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingStudentSavingLast"></span></b></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b>Jumlah Penarikan</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingStudentSavingCredit"></span></b></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b>Penarikan Terakhir</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingStudentSavingLastCredit"></span></b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        <table id="tb-student-saving-list" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                            data-options="singleSelect:true,method:'post',rownumbers:true,toolbar:'#toolbarStudentSavingTrans'">
                            <thead>
                                <tr>
                                    <th data-options="field:'id',width:80,hidden:true">ID</th>
                                    <th data-options="field:'journal',width:180,resizeable:true,align:'center'">No. Jurnal/Tgl.</th>
                                    <th data-options="field:'debit',width:120,resizeable:true,align:'right'">Debit</th>
                                    <th data-options="field:'credit',width:120,resizeable:true,align:'right'">Kredit</th>
                                    <th data-options="field:'remark',width:200,resizeable:true">Keterangan</th>
                                    <th data-options="field:'logged',width:200,resizeable:true,align:'center'">Petugas</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarStudentSavingTrans">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Transaksi</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportStudentSaving('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Print" plain="true" onclick="printStudentSavingReceipt()">Cetak Kuitansi</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionStudentSaving = document.getElementById("menu-act-student-saving").getElementsByTagName("a")
    var titleStudentSaving = document.getElementById("title-student-saving")
    var markStudentSaving = document.getElementById("mark-student-saving")
    var idStudentSaving = document.getElementById("id-student-saving")
    var dgStudentSaving = $("#tb-student-saving")
    $(function () {
        sessionStorage.formTabungan_Santri = "init"
        dgStudentSaving.datagrid({
            url: "{{ url('finance/receipt/payment/major/student') }}",
            queryParams: { _token: "{{ csrf_token() }}", is_prospect: 0 },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTabungan_Santri == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    actionButtonStudentSaving("active",[1,2,3])
                    $("#form-student-saving-main").form("reset")
                    $("#id-student-saving-dept").val(row.department_id)
                    $("#id-student-saving-studentid").val(row.student_id)
                    $("#AccountingStudentSavingDept").textbox("setValue", row.department)
                    $("#AccountingStudentSavingStudentNo").textbox("setValue", row.student_no)
                    $("#AccountingStudentSavingStudentName").textbox("setValue", row.name)
                    $("#AccountingStudentSavingClass").textbox("setValue", row.grade + " / " + row.class_name)
                    $("#AccountingStudentSavingTypes").combogrid("grid").datagrid("reload", "{{ url('finance/saving/student/type/combo-grid') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=" + row.department_id + "&is_employee=" + 0)
                    $("#AccountingStudentSavingTypeFilter").combobox("enable")
                    $("#AccountingStudentSavingTypeFilter").combobox("reload", "{{ url('finance/saving/student/type/combo-box') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=" + row.department_id)
                }
            }
        })
        dgStudentSaving.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgStudentSaving.datagrid('getPager').pagination())
        actionButtonStudentSaving("{{ $ViewType }}", [])
        $("#AccountingStudentSavingTypeFilter").combobox("disable")
        $("#AccountingStudentSavingTypeFilter").combobox({
            onLoadSuccess: function(data) {
                $("#AccountingStudentSavingTypeFilter").combobox("setValue", data[0].id)
                reloadStudentSavingDetail(data[0].id)
            },
            onSelect: function(record) {
                reloadStudentSavingDetail(record.id)
            }
        })
        $("#AccountingStudentSavingTypes").combogrid({
            onClickRow: function (index, row) {
                titleStudentSaving.innerText = row.name
                $("#AccountingStudentSavingBookYear").textbox("setValue", row.book_year)
            }
        })
        $("#tb-student-saving-list").datagrid({
            onDblClickRow: function (index, row) {
                actionButtonStudentSaving("subactive",[0,2,3])
                $("#form-student-saving-main").form("load", "{{ url('finance/saving/student/show') }}" + "/" + row.id)
                $("#student-saving-reason").removeClass("d-none")
                $("#page-student-saving").waitMe("hide")
            }
        })
        $("#form-student-saving-main").form({
            onLoadSuccess: function(data) {
                if (data.transaction_type === 'credit') {
                    $("#AccountingStudentSavingAmount").numberbox("setValue", data.credit)
                } else {
                    $("#AccountingStudentSavingAmount").numberbox("setValue", data.debit)
                }
                $("#AccountingStudentSavingBookYear").textbox("setValue", data.book_year)
                titleStudentSaving.innerText = $("#AccountingStudentSavingTypes").combogrid("getText")
            }
        })
        $("#page-student-saving").waitMe({effect:"none"})
    })
    function reloadStudentSavingDetail(saving_type) {
        $("#tb-student-saving-list").datagrid("reload", "{{ url('finance/saving/student/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&student_id=" + $("#id-student-saving-studentid").val() + "&employee_id=0" + "&bookyear_id=" + $("#id-student-saving-bookyear").val() + "&saving_type=" + saving_type)
        // get saving info
        $.getJSON("{{ url('finance/saving/student/info/') }}" + "?bookyear_id=" + $("#id-student-saving-bookyear").val() + "&person_id=" + $("#id-student-saving-studentid").val() + "&saving_type=" + saving_type, function(result) {
            $("#AccountingStudentSavingBalance").text(result.balance)
            $("#AccountingStudentSavingDebit").text(result.total_deposit)
            $("#AccountingStudentSavingLast").text(result.last_deposit +" "+ result.last_deposit_date)
            $("#AccountingStudentSavingCredit").text(result.total_withdraw)
            $("#AccountingStudentSavingLastCredit").text(result.last_withdraw +" "+ result.last_withdraw_date)
        })
    }
    function filterStudentSaving(params) {
        if (Object.keys(params).length > 0) {
            dgStudentSaving.datagrid("load", { params, _token: "{{ csrf_token() }}", is_prospect: 0 })
        } else {
            dgStudentSaving.datagrid("load", { _token: "{{ csrf_token() }}", is_prospect: 0 })
        }
    }
    function newStudentSaving() {
        sessionStorage.formTabungan_Santri = "active"
        actionButtonStudentSaving("active", [0,1])
        markStudentSaving.innerText = "*"
        titleStudentSaving.innerText = ""
        idStudentSaving.value = "-1"
        $("#student-saving-reason").addClass("d-none")
        $("#AccountingStudentSavingTypeFilter").combobox("disable")
        $("#page-student-saving").waitMe("hide")
    }
    function editStudentSaving() {
        sessionStorage.formTabungan_Santri = "active"
        markStudentSaving.innerText = "*"
        actionButtonStudentSaving("active", [0,1])
    }
    function saveStudentSaving() {
        if (sessionStorage.formTabungan_Santri == "active") {
            ajaxStudentSaving("finance/saving/student/store")
        }
    }
    function ajaxStudentSaving(route) {
        $("#form-student-saving-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-student-saving-main").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-student-saving-main").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-student-saving-main").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            reloadStudentSavingDetail($("#AccountingStudentSavingTypes").combogrid("getValue"))
            actionDataStudentSaving()
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearStudentSaving() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearStudentSaving()
            }
        })
    }
    function actionButtonStudentSaving(viewType, idxArray) {
        for (var i = 0; i < menuActionStudentSaving.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionStudentSaving[i].id).linkbutton({disabled: true})
            } else {
                $("#" + menuActionStudentSaving[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionStudentSaving[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearStudentSaving() {
        sessionStorage.formTabungan_Santri = "init"
        $("#form-student-saving-main").form("reset")
        actionButtonStudentSaving("init", [])
        titleStudentSaving.innerText = ""
        markStudentSaving.innerText = ""
        idStudentSaving.value = "-1"
        $("#student-saving-reason").addClass("d-none")
        $("#AccountingStudentSavingBalance").text("")
        $("#AccountingStudentSavingDebit").text("")
        $("#AccountingStudentSavingLast").text("")
        $("#AccountingStudentSavingCredit").text("")
        $("#AccountingStudentSavingLastCredit").text("")
        $("#page-student-saving").waitMe({effect:"none"})
        $("#tb-student-saving-list").datagrid("loadData", [])
    }
    function actionDataStudentSaving() {
        sessionStorage.formTabungan_Santri = "init"
        actionButtonStudentSaving("active",[1,2,3])
        $("#page-student-saving").waitMe({effect:"none"})
        titleStudentSaving.innerText = ""
        markStudentSaving.innerText = ""
        $("#student-saving-reason").addClass("d-none")
        $("#AccountingStudentSavingBookYear").textbox("clear")
        $("#AccountingStudentSavingTypes").combogrid("clear")
        $("#AccountingStudentSavingAmount").numberbox("setValue", "0,00")
        $("#AccountingStudentSavingAccount").combobox("clear")
        $("#AccountingStudentSavingRemark").textbox("clear")
        $(".ttype").radiobutton("reset")
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
        $("#receipt-w").window("open")
        $("#receipt-w").window("setTitle", title)
        $("#receipt-w").window("refresh", "{{ url('finance/saving/student/requested') }}" + "?requested_by=" + requested_by)
    }
    function exportStudentSaving(document) {
        var dg = $("#tb-student-saving-list").datagrid("getData")
        if (dg.total > 0) {
            var payload = {
                rows: dg.rows, 
                is_employee: 0,
                department: $("#AccountingStudentSavingDept").textbox("getText"),
                bookyear_id: $("#id-student-saving-bookyear").val(),
                saving_type: $("#AccountingStudentSavingTypeFilter").combobox("getValue"),
                person_id: $("#id-student-saving-studentid").val(),
                person_no: $("#AccountingStudentSavingStudentNo").textbox("getText"),
                person_name: $("#AccountingStudentSavingStudentName").textbox("getText"),
                person_info: $("#AccountingStudentSavingClass").textbox("getText")
            }
            exportDocument("{{ url('finance/saving/student/print') }}" + "/" + document,payload,"Ekspor data Tabungan Santri ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function printStudentSavingReceipt() {
        var dg = $("#tb-student-saving-list").datagrid("getSelected")
        if (dg !== null) {
            $.messager.progress({ title: "Cetak Kuitansi Pembayaran", msg:'Mohon tunggu...' })
            var payload = {
                _token: '{{ csrf_token() }}', 
                transaction_id: dg.id,
                is_employee: 0,
                person_id: $("#id-student-saving-studentid").val(),
                person_no: $("#AccountingStudentSavingStudentNo").textbox("getText"),
                person_name: $("#AccountingStudentSavingStudentName").textbox("getText"),
                person_info: $("#AccountingStudentSavingClass").textbox("getText")
            }
            $.post("{{ url('finance/saving/student/print/receipt') }}", $.param(payload, true), function(response) {
                $.messager.progress('close')
                $("#receipt-w").window("open")
                $("#receipt-w").window("setTitle", "Tanda Bukti Pembayaran")
                $("#receipt-w").window("refresh", "{{ url('finance/receipt/data/show') }}" + "?url=" + response)
            })           
        }
    }
</script>
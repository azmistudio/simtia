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
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Transaksi Tabungan Pegawai</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:250px">
        <div class="p-1">
            <form id="ff-employee-saving" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select id="fsection-employee-saving" class="easyui-combobox" style="width:235px;height:22px;" data-options="label:'Bagian:',panelHeight:68">
                        <option value="">---</option>
                        @foreach ($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <input id="fnip-employee-saving" class="easyui-textbox" style="width:235px;height:22px;" data-options="label:'NIP:'">
                </div>
                <div class="mb-1">
                    <input id="fname-employee-saving" class="easyui-textbox" style="width:235px;height:22px;" data-options="label:'Nama:'">
                </div>
                <div style="text-align:right;padding:5px 0">
                    <a class="easyui-linkbutton small-btn flist-box" onclick="filterEmployeeSaving({fsection: $('#fsection-employee-saving').val(),fnip: $('#fnip-employee-saving').val(),fname: $('#fname-employee-saving').val()})">Cari</a>
                    <a class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-employee-saving').form('clear');filterEmployeeSaving({})">Batal</a>
                </div>
            </form>
            <table id="tb-employee-saving" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'employee_id',width:60,resizeable:true,sortable:true">NIP</th>
                        <th data-options="field:'name',width:150,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-employee-saving" class="panel-top">
            <a id="newEmployeeSaving" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newEmployeeSaving()">Baru</a>
            <a id="editEmployeeSaving" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editEmployeeSaving()">Ubah</a>
            <a id="saveEmployeeSaving" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveEmployeeSaving()">Simpan</a>
            <a id="clearEmployeeSaving" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearEmployeeSaving()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-employee-saving"></span>Jenis Tabungan: <span id="title-employee-saving"></span></h6>
        </div>
        <div class="pl-2 pt-3 pr-2" id="page-employee-saving-main">
            <div class="container-fluid">
                <div class="row">
                    <div id="page-employee-saving" class="col-4">
                        <form id="form-employee-saving-main" method="post">
                        <input type="hidden" id="id-employee-saving" name="id" value="-1" />
                        <input type="hidden" id="id-employee-saving-employeeid" name="employee_id" value="-1" />
                        <input type="hidden" id="id-employee-saving-bookyear" name="bookyear_id" value="{{ $bookyear->id }}" />
                        <input type="hidden" id="id-employee-saving-journal" name="journal_id" value="-1" />
                        <input type="hidden" id="id-employee-saving-dept" name="department_id" value="-1" />
                        <input type="hidden" id="id-employee-saving-deptname" value="" />
                        <input type="hidden" name="is_employee" value="0" />
                        <div class="mb-1">
                            <input name="employee_no" class="easyui-textbox" id="AccountingEmployeeSavingNo" style="width:300px;height:22px;" data-options="labelWidth:'125px',readonly:'true'" label="NIP" />
                        </div>
                        <div class="mb-1">
                            <input name="name" class="easyui-textbox" id="AccountingEmployeeSavingName" style="width:300px;height:22px;" data-options="label:'Nama:',labelWidth:'125px',readonly:'true'" />
                        </div>
                        <div class="mb-1">
                            <input name="section" class="easyui-textbox" id="AccountingEmployeeSavingSections" style="width:300px;height:22px;" data-options="labelWidth:'125px',readonly:'true'" label="Bagian" />
                        </div>
                        <div class="mb-1">
                            <input id="AccountingEmployeeSavingDept" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                        </div>
                        <div class="mb-1">
                            <input class="easyui-textbox" style="width:200px;height:22px;" data-options="label:'Tahun Buku:',labelWidth:'125px',readonly:true" value="{{ $bookyear->book_year }}" />
                        </div>
                        <div class="mb-1">
                            <select name="saving_id" id="AccountingEmployeeSavingTypes" class="easyui-combogrid" style="width:300px;height:22px;" data-options="
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
                            <input name="transaction_type" class="easyui-radiobutton ettype" value="credit" data-options="label:'SETOR',labelWidth:'60px',labelPosition:'after'" checked="checked" />
                            <input name="transaction_type" class="easyui-radiobutton ettype" value="debit" data-options="label:'TARIK',labelWidth:'60px',labelPosition:'after'" />
                        </div>
                        <div class="mb-1">
                            <input name="amount" id="AccountingEmployeeSavingAmount" class="easyui-numberbox" style="width:300px;height:22px;" data-options="label:'<b>*</b>Jumlah:',labelWidth:'125px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" value="0" />
                        </div>
                        <div class="mb-2">
                            <input name="trans_date" class="easyui-datebox" style="width:240px;height:22px;" data-options="label:'<b>*</b>Tanggal Transaksi:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" value="{{ date('d/m/Y') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="mb-1" style="width:121px;">*Rekening Kas:</label>
                            <select name="cash_account" id="AccountingEmployeeSavingAccount" class="easyui-combobox" style="width:300px;height:22px;" data-options="panelHeight:150">
                                @foreach ($codes_cash as $code)
                                <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="mb-1" style="width:121px;">Keterangan:</label>
                            <input name="remark" id="AccountingEmployeeSavingRemark" class="easyui-textbox" style="width:300px;height:22px;" data-options="multiline:true" />
                        </div>
                        <div id="employee-saving-reason" class="mb-1 d-none">
                            <label class="mb-1" style="width:121px;">*Alasan Ubah Data:</label>
                            <input name="reason" id="AccountingEmployeeSavingReason" class="easyui-textbox" style="width:300px;height:22px;" data-options="multiline:true" />
                        </div>
                        </form>
                    </div>
                    <div class="col-8 pl-0">
                        <div class="mb-2" style="padding-left:11px;">
                            <select id="AccountingEmployeeSavingTypeFilter" class="easyui-combobox" style="width:600px;height:22px;" data-options="label:'<b>Filter Jenis Tabungan</b>:',labelWidth:'160px',panelHeight:150,valueField:'id',textField:'text'"></select>
                        </div>
                        <fieldset class="mb-2">
                            <legend><b>Informasi Tabungan</b></legend>
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td width="25%"><b>Saldo</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingEmployeeSavingBalance"></span></b></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b>Jumlah Setoran</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingEmployeeSavingDebit"></span></b></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b>Setoran Terakhir</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingEmployeeSavingLast"></span></b></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b>Jumlah Penarikan</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingEmployeeSavingCredit"></span></b></td>
                                    </tr>
                                    <tr>
                                        <td width="25%"><b>Penarikan Terakhir</b></td>
                                        <td width="2%">:</td>
                                        <td><b><span id="AccountingEmployeeSavingLastCredit"></span></b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>
                        <table id="tb-employee-saving-list" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                            data-options="singleSelect:true,method:'post',rownumbers:true,toolbar:'#toolbarEmployeeSavingTrans'">
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
<div id="toolbarEmployeeSavingTrans">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Transaksi</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportEmployeeSaving('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Print" plain="true" onclick="printEmployeeSavingReceipt()">Cetak Kuitansi</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionEmployeeSaving = document.getElementById("menu-act-employee-saving").getElementsByTagName("a")
    var titleEmployeeSaving = document.getElementById("title-employee-saving")
    var markEmployeeSaving = document.getElementById("mark-employee-saving")
    var idEmployeeSaving = document.getElementById("id-employee-saving")
    var dgEmployeeSaving = $("#tb-employee-saving")
    $(function () {
        sessionStorage.formTabungan_Pegawai = "init"
        dgEmployeeSaving.datagrid({
            url: "{{ url('hr/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTabungan_Pegawai == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    actionButtonEmployeeSaving("active",[1,2,3])
                    $("#form-employee-saving-main").form("reset")
                    $("#id-employee-saving-employeeid").val(row.id)
                    $("#AccountingEmployeeSavingNo").textbox("setValue", row.employee_id)
                    $("#AccountingEmployeeSavingName").textbox("setValue", row.name)
                    $("#AccountingEmployeeSavingSections").textbox("setValue", row.section)
                    $("#AccountingEmployeeSavingTypeFilter").combobox("enable")
                    $("#AccountingEmployeeSavingTypeFilter").combobox("reload", "{{ url('finance/saving/employee/type/combo-box') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=0&is_employee=1")
                }
            }
        })
        dgEmployeeSaving.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgEmployeeSaving.datagrid('getPager').pagination())
        actionButtonEmployeeSaving("{{ $ViewType }}", [])
        $("#AccountingEmployeeSavingTypeFilter").combobox("disable")
        $("#AccountingEmployeeSavingTypeFilter").combobox({
            onLoadSuccess: function(data) {
                var texts = data[0].text.split(" / ")
                $("#AccountingEmployeeSavingTypeFilter").combobox("setValue", data[0].id)
                $("#id-employee-saving-deptname").val(texts[1])
                reloadEmployeeSavingDetail(data[0].id)
            },
            onSelect: function(record) {
                var texts = record.text.split(" / ")
                $("#id-employee-saving-deptname").val(texts[1])
                reloadEmployeeSavingDetail(record.id)
            }
        })
        $("#AccountingEmployeeSavingTypes").combogrid({
            url: "{{ url('finance/saving/employee/type/combo-grid') }}",
            queryParams: { _token: "{{ csrf_token() }}", is_dept: 0, is_employee: 1 },
            onClickRow: function (index, row) {
                titleEmployeeSaving.innerText = row.name
                $("#id-employee-saving-dept").val(row.department_id)
                $("#AccountingEmployeeSavingDept").textbox("setValue", row.department)
                $("#AccountingEmployeeSavingBookYear").textbox("setValue", row.book_year)
            }
        })
        $("#tb-employee-saving-list").datagrid({
            onDblClickRow: function (index, row) {
                actionButtonEmployeeSaving("subactive",[0,2,3])
                $("#form-employee-saving-main").form("load", "{{ url('finance/saving/employee/show') }}" + "/" + row.id)
                $("#employee-saving-reason").removeClass("d-none")
                $("#page-employee-saving").waitMe("hide")
            }
        })
        $("#form-employee-saving-main").form({
            onLoadSuccess: function(data) {
                $("#id-employee-saving-dept").val(data.department_id)
                if (data.transaction_type === 'credit') {
                    $("#AccountingEmployeeSavingAmount").numberbox("setValue", data.credit)
                } else {
                    $("#AccountingEmployeeSavingAmount").numberbox("setValue", data.debit)
                }
                $("#AccountingEmployeeSavingDept").textbox("setValue", data.department)
                titleEmployeeSaving.innerText = $("#AccountingEmployeeSavingTypes").combogrid("getText")
            }
        })
        $("#page-employee-saving").waitMe({effect:"none"})
    })
    function reloadEmployeeSavingDetail(saving_type) {
        $("#tb-employee-saving-list").datagrid("reload", "{{ url('finance/saving/employee/data') }}" 
            + "?_token=" + "{{ csrf_token() }}" 
            + "&employee_id=" + $("#id-employee-saving-employeeid").val() 
            + "&student_id=0" 
            + "&bookyear_id=" + {{ $bookyear->id }}
            + "&saving_type=" + saving_type
        )
        // get saving info
        $.getJSON("{{ url('finance/saving/employee/info/') }}" + "?bookyear_id=" + {{ $bookyear->id }} + "&person_id=" + $("#id-employee-saving-employeeid").val() + "&saving_type=" + saving_type, function(result) {
            $("#AccountingEmployeeSavingBalance").text(result.balance)
            $("#AccountingEmployeeSavingDebit").text(result.total_deposit)
            $("#AccountingEmployeeSavingLast").text(result.last_deposit +" "+ result.last_deposit_date)
            $("#AccountingEmployeeSavingCredit").text(result.total_withdraw)
            $("#AccountingEmployeeSavingLastCredit").text(result.last_withdraw +" "+ result.last_withdraw_date)
        })
    }
    function filterEmployeeSaving(params) {
        if (Object.keys(params).length > 0) {
            dgEmployeeSaving.datagrid("load", { params, _token: "{{ csrf_token() }}", is_prospective: 0 })
        } else {
            dgEmployeeSaving.datagrid("load", { _token: "{{ csrf_token() }}", is_prospective: 0 })
        }
    }
    function newEmployeeSaving() {
        sessionStorage.formTabungan_Pegawai = "active"
        actionButtonEmployeeSaving("active", [0,1])
        markEmployeeSaving.innerText = "*"
        titleEmployeeSaving.innerText = ""
        idEmployeeSaving.value = "-1"
        $("#employee-saving-reason").addClass("d-none")
        $("#AccountingEmployeeSavingTypeFilter").combobox("disable")
        $("#page-employee-saving").waitMe("hide")
    }
    function editEmployeeSaving() {
        sessionStorage.formTabungan_Pegawai = "active"
        markEmployeeSaving.innerText = "*"
        actionButtonEmployeeSaving("active", [0,1])
    }
    function saveEmployeeSaving() {
        if (sessionStorage.formTabungan_Pegawai == "active") {
            ajaxEmployeeSaving("finance/saving/employee/store")
        }
    }
    function ajaxEmployeeSaving(route) {
        $("#form-employee-saving-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-employee-saving-main").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-employee-saving-main").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-employee-saving-main").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            reloadEmployeeSavingDetail($("#AccountingEmployeeSavingTypes").combogrid("getValue"), $("#id-employee-saving-bookyear").val())
            actionDataEmployeeSaving()
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearEmployeeSaving() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearEmployeeSaving()
            }
        })
    }
    function actionButtonEmployeeSaving(viewType, idxArray) {
        for (var i = 0; i < menuActionEmployeeSaving.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionEmployeeSaving[i].id).linkbutton({disabled: true})
            } else {
                $("#" + menuActionEmployeeSaving[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionEmployeeSaving[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearEmployeeSaving() {
        sessionStorage.formTabungan_Pegawai = "init"
        $("#form-employee-saving-main").form("reset")
        actionButtonEmployeeSaving("init", [])
        titleEmployeeSaving.innerText = ""
        markEmployeeSaving.innerText = ""
        idEmployeeSaving.value = "-1"
        $("#employee-saving-reason").addClass("d-none")
        $("#tb-employee-saving-list").datagrid("loadData", [])
        $("#AccountingEmployeeSavingBalance").text("")
        $("#AccountingEmployeeSavingDebit").text("")
        $("#AccountingEmployeeSavingLast").text("")
        $("#AccountingEmployeeSavingCredit").text("")
        $("#AccountingEmployeeSavingLastCredit").text("")
        $("#page-employee-saving").waitMe({effect:"none"})
    }
    function actionDataEmployeeSaving() {
        sessionStorage.formTabungan_Pegawai = "init"
        actionButtonEmployeeSaving("active",[1,2,3])
        $("#page-employee-saving").waitMe({effect:"none"})
        titleEmployeeSaving.innerText = ""
        markEmployeeSaving.innerText = ""
        $("#employee-saving-reason").addClass("d-none")
        $("#AccountingEmployeeSavingBookYear").textbox("clear")
        $("#AccountingEmployeeSavingTypes").combogrid("clear")
        $("#AccountingEmployeeSavingAmount").numberbox("setValue", "0,00")
        $("#AccountingEmployeeSavingAccount").combobox("clear")
        $("#AccountingEmployeeSavingRemark").textbox("clear")
        $(".ettype").radiobutton("reset")
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
        $("#receipt-w").window("refresh", "{{ url('finance/saving/employee/requested') }}" + "?requested_by=" + requested_by)
    }
    function exportEmployeeSaving(document) {
        var dg = $("#tb-employee-saving-list").datagrid("getData")
        if (dg.total > 0) {
            var payload = {
                rows: dg.rows, 
                is_employee: 1,
                department: $("#id-employee-saving-deptname").val(),
                bookyear_id: $("#id-employee-saving-bookyear").val(),
                saving_type: $("#AccountingEmployeeSavingTypeFilter").combobox("getValue"),
                person_id: $("#id-employee-saving-employeeid").val(),
                person_no: $("#AccountingEmployeeSavingNo").textbox("getText"),
                person_name: $("#AccountingEmployeeSavingName").textbox("getText"),
                person_info: $("#AccountingEmployeeSavingSections").textbox("getText")
            }
            exportDocument("{{ url('finance/saving/employee/print') }}" + "/" + document,payload,"Ekspor data Tabungan Pegawai ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function printEmployeeSavingReceipt() {
        var dg = $("#tb-employee-saving-list").datagrid("getSelected")
        if (dg !== null) {
            $.messager.progress({ title: "Cetak Kuitansi Pembayaran", msg:'Mohon tunggu...' })
            var payload = {
                _token: '{{ csrf_token() }}', 
                transaction_id: dg.id,
                is_employee: 1,
                person_id: $("#id-employee-saving-employeeid").val(),
                person_no: $("#AccountingEmployeeSavingNo").textbox("getText"),
                person_name: $("#AccountingEmployeeSavingName").textbox("getText"),
                person_info: $("#AccountingEmployeeSavingSections").textbox("getText")
            }
            $.post("{{ url('finance/saving/employee/print/receipt') }}", $.param(payload, true), function(response) {
                $.messager.progress('close')
                $("#receipt-w").window("open")
                $("#receipt-w").window("setTitle", "Tanda Bukti Pembayaran")
                $("#receipt-w").window("refresh", "{{ url('finance/receipt/data/show') }}" + "?url=" + response)
            })
        }
    }
</script>
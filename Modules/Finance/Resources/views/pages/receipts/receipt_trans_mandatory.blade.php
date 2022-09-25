@php
    $WindowHeight = $InnerHeight;
    $WindowWidth = $InnerWidth;
    $GridHeight = str_replace('px', '', $InnerHeight) - 158 . "px";
    $SubGridHeight = str_replace('px', '', $InnerHeight) - 220 . "px";
@endphp
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar Santri'" style="width:250px">
        <div class="p-1">
            <form id="ff-receipt-trans" method="post" class="mb-1">
            @csrf
                @if ($payload['category_id'] == 1)
                    <div class="mb-1">
                @else
                    <div class="mb-1 d-none">
                @endif
                    <select id="fclass-receipt-trans" class="easyui-combogrid" style="width:235px;height:22px;" data-options="
                        label:'Kelas:',
                        panelWidth: 570,
                        idField: 'id',
                        textField: 'class',
                        url: '{{ url('academic/class/student/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        fitColumns:true,
                        pagination:true,
                        queryParams: { _token: '{{ csrf_token() }}', department_id: {{ $payload['department_id'] }} },
                        columns: [[
                            {field:'department',title:'Departemen',width:110},
                            {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                            {field:'grade',title:'Tingkat',width:80,align:'center'},
                            {field:'class',title:'Kelas',width:120},
                            {field:'capacity',title:'Kapasitas/Terisi',width:120},
                        ]],
                    ">
                    </select>
                </div>
                @if ($payload['category_id'] == 1)
                    <div class="mb-1 d-none">
                @else
                    <div class="mb-1">
                @endif
                    <select id="fgroup-receipt-trans" class="easyui-combogrid" style="width:235px;height:22px;" data-options="
                        label:'Kelompok:',
                        panelWidth: 570,
                        idField: 'id',
                        textField: 'group',
                        url: '{{ url('academic/admission/prospective-group/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        fitColumns:true,
                        pagination:true,
                        queryParams: { _token: '{{ csrf_token() }}', department_id: '{{ $payload['department_id'] }}' },
                        columns: [[
                            {field:'department',title:'Departemen',width:110},
                            {field:'admission_id',title:'Proses',width:230},
                            {field:'group',title:'Kelompok',width:150},
                            {field:'quota',title:'Kapasitas/Terisi',width:120},
                        ]],
                    ">
                    </select>
                </div>
                <div class="mb-1">
                    <input id="fnis-receipt-trans" class="easyui-textbox" style="width:235px;height:22px;" label="{{ $payload['category_id'] == 1 ? 'NIS' : 'No.Daftar' }}">
                </div>
                <div class="mb-1">
                    <input id="fname-receipt-trans" class="easyui-textbox" style="width:235px;height:22px;" data-options="label:'Nama:'">
                </div>
                <div style="margin-left:80px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterReceiptTrans({ fnis: $('#fnis-receipt-trans').val(),fname: $('#fname-receipt-trans').val(),fdept: {{ $payload['department_id'] }} })">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-receipt-trans').form('reset');filterReceiptTrans({fdept: {{ $payload['department_id'] }}})">Batal</a>
                </div>
            </form>
            <table id="tb-receipt-trans" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'student_no',width:100,resizeable:true,sortable:true,align:'center'">{{ $payload['category_id'] == 1 ? 'NIS' : 'No.Daftar' }}</th>
                        <th data-options="field:'name',width:120,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-receipt-trans" class="panel-top">
            <a id="newReceiptTrans" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newReceiptTrans()">Baru</a>
            <a id="editReceiptTrans" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editReceiptTrans()">Ubah</a>
            <a id="saveReceiptTrans" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveReceiptTrans()">Simpan</a>
            <a id="clearReceiptTrans" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearReceiptTrans()">Batal</a>
            <a id="printReceiptTrans" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Print'" onclick="printReceiptTrans()">Cetak</a>
        </div>
        <div class="pl-1 pt-3 pr-1" id="page-trans-mandatory">
            <div class="container-fluid">
                <div class="row">
                    <div id="page-receipt-trans" class="col-4">
                        <form id="form-receipt-trans-main" method="post">
                        <input type="hidden" id="id-receipt-trans" name="id" value="-1" />
                        <input type="hidden" id="id-receipt-trans-receipt" name="receipt_id" value="{{ $payload['receipt_id'] }}" />
                        <input type="hidden" id="id-receipt-trans-dept" name="department_id" value="-1" />
                        <input type="hidden" id="id-receipt-trans-student" name="student_id" value="-1" />
                        <input type="hidden" name="category_id" value="{{ $payload['category_id'] }}" />
                        <input type="hidden" name="bookyear_id" value="{{ $book_year->id }}" />
                        <input type="hidden" name="is_prospect" value="{{ $payload['category_id'] == 1 ? 0 : 1 }}" />
                        <input type="hidden" id="id-payment-major" name="major_id" value="-1" />
                        <div class="mb-1">
                            <input name="student_no" class="easyui-textbox" id="AccountingReceiptTransStudentNo" style="width:300px;height:22px;" data-options="labelWidth:'125px',readonly:'true'" label="{{ $payload['category_id'] == 1 ? 'NIS' : 'No.Daftar' }}" />
                        </div>
                        <div class="mb-1">
                            <input name="student_name" class="easyui-textbox" id="AccountingReceiptTransStudentName" style="width:300px;height:22px;" data-options="label:'Nama:',labelWidth:'125px',readonly:'true'" />
                        </div>
                        <div class="mb-1">
                            <input name="class_name" class="easyui-textbox" id="AccountingReceiptTransClass" style="width:300px;height:56px;" data-options="labelWidth:'125px',readonly:'true',multiline:'true'" label="{{ $payload['category_id'] == 1 ? 'Kelas' : 'Kelompok' }}" />
                        </div>
                        <div class="mb-1">
                            <input name="instalment" id="AccountingReceiptTransInstalment" class="easyui-numberbox" style="width:300px;height:22px;" data-options="label:'<b>*</b>Cicilan:',labelWidth:'125px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" />
                        </div>
                        <div class="mb-1">
                            <input name="discount" id="AccountingReceiptTransDiscount" class="easyui-numberbox" style="width:300px;height:22px;" data-options="label:'<b>*</b>Diskon:',labelWidth:'125px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" value="0" />
                        </div>
                        <div class="mb-1">
                            <input name="amount_pay" id="AccountingReceiptTransPay" class="easyui-numberbox" style="width:300px;height:22px;" data-options="label:'Jumlah Bayar:',labelWidth:'125px',min:0,precision:2,groupSeparator:'.',decimalSeparator:',',readonly:'true'" />
                        </div>
                        <div class="mb-3">
                            <input name="journal_date" class="easyui-datebox" style="width:240px;height:22px;" data-options="label:'Tanggal:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" value="{{ date('d/m/Y') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="mb-1" style="width:121px;">*Rekening Kas:</label>
                            <select name="cash_account" class="easyui-combobox" style="width:300px;height:22px;" data-options="panelHeight:150">
                                @foreach ($codes_cash as $code)
                                <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="mb-1" style="width:121px;">Keterangan:</label>
                            <input name="remark" id="AccountingReceiptTransRemark" class="easyui-textbox" style="width:300px;height:22px;" data-options="" />
                        </div>
                        <div id="receipt-trans-reason" class="mb-1 d-none">
                            <label class="mb-1" style="width:121px;">*Alasan Ubah Data:</label>
                            <input name="reason" id="AccountingReceiptTransReason" class="easyui-textbox" style="width:300px;height:34px;" data-options="multiline:true" />
                        </div>
                        </form>
                    </div>
                    <div class="col-8 pl-0">
                        <form id="form-receipt-trans-paid" method="post">
                        <input type="hidden" id="id-payment-major-paid" name="id" value="-1">
                        <input type="hidden" id="id-student-paid" name="student_id" value="-1" />
                        <input type="hidden" id="id-student-no-paid" name="student_no" value="" />
                        <input type="hidden" id="id-student-name-paid" name="student_name" value="" />
                        <input type="hidden" id="id-receipt-paid-dept" name="department_id" value="-1" />
                        <input type="hidden" id="id-receipt-paid" name="receipt_id" value="{{ $payload['receipt_id'] }}" />
                        <input type="hidden" name="bookyear_id" value="{{ $book_year->id }}" />
                        <input type="hidden" name="category_id" value="1-JTT" />
                        <fieldset class="mb-3" id="panel-receipt-trans-paid">
                            <legend><b>Pembayaran yang harus dilunasi</b></legend>
                            <div class="row">
                                <div class="col-7">
                                    <div class="mb-1">
                                        @if ($payload['category_id'] == 1)
                                        <select name="period_payment" id="AccountingReceiptTransAmountPeriod" class="easyui-combobox" style="width:384px;height:22px;" data-options="label:'<b>*</b>Periode Bayar:',labelWidth:'125px',panelHeight:150,valueField:'id',textField:'text'"></select>
                                        @else 
                                        <input name="period_payment" id="AccountingReceiptTransAmountPeriodYear" class="easyui-textbox" style="width:384px;height:22px;" data-options="label:'*Periode Bayar:',labelWidth:'125px',readonly:'true'" />
                                        @endif
                                    </div>
                                    <div class="mb-1">
                                        <input name="amount" id="AccountingReceiptTransAmountPaid" class="easyui-numberbox" style="width:225px;height:22px;" data-options="label:'<b>*</b>Jumlah Bayaran:',labelWidth:'125px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" />
                                        <span class="mr-1"></span>
                                        <input name="instalment" id="AccountingReceiptTransInstalmentPaid" class="easyui-numberbox" style="width:151px;height:22px;" data-options="label:'<b>*</b>Cicilan:',labelWidth:'60px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="journal_paid" id="AccountingReceiptTransJournalPaid" class="easyui-textbox" style="width:225px;height:22px;" data-options="label:'Tanggal Jurnal:',labelWidth:'125px',readonly:'true'" />
                                        <span class="mr-1"></span>
                                        <input id="AccountingReceiptTransStatus" class="easyui-textbox" style="width:151px;height:22px;" data-options="label:'Status:',labelWidth:'60px',readonly:'true'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="remark_paid" id="AccountingReceiptTransRemarkPaid" class="easyui-textbox" style="width:384px;height:22px;" data-options="label:'Keterangan:',labelWidth:'125px'" />
                                    </div>
                                </div>
                                <div class="col-5 pl-0">
                                    <div class="mb-1">
                                        <input name="reason_paid" id="AccountingReceiptTransInstalmentPaidReason" class="easyui-textbox" style="width:280px;height:75px;" data-options="label:'*Alasan Perubahan<br/>Data:',labelWidth:'125px',multiline:'true'" />
                                    </div>
                                    <div style="margin-left:125px;">
                                        <a href="javascript:void(0)" class="easyui-linkbutton" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Save'" onclick="storePaymentMajor()">Ubah Simpan</a>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        </form>
                        <table id="tb-receipt-trans-instalment" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                            data-options="singleSelect:true,method:'post',rownumbers:true,showFooter:true,toolbar:'#menubarReceiptTrans'">
                            <thead>
                                <tr>
                                    <th data-options="field:'id',width:80,hidden:true">ID</th>
                                    <th data-options="field:'journal',width:130,resizeable:true,align:'center'">No. Jurnal/Tgl.</th>
                                    <th data-options="field:'account',width:170,resizeable:true">Rek. Kas</th>
                                    <th data-options="field:'total',width:100,resizeable:true,align:'right'">Besar</th>
                                    <th data-options="field:'discount',width:100,resizeable:true,align:'right'">Diskon</th>
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
<div id="menubarReceiptTrans">
    <div class="container-fluid">
        <div class="row">
            <div class="col-4"><span style="line-height: 25px;"><b>Pembayaran Cicilan</b></span></div>
            <div class="col-8 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Refresh" plain="true" onclick="refreshInstalment()">Muat Ulang</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Print" plain="true" onclick="printReceipt()">Cetak Kuitansi</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionReceiptTrans = document.getElementById("menu-act-receipt-trans").getElementsByTagName("a")
    var dgReceiptTrans = $("#tb-receipt-trans")
    $(function () {
        sessionStorage.formTransaksi_Penerimaan = "init"
        dgReceiptTrans.datagrid({
            url: "{{ url('finance/receipt/payment/major/student') }}",
            queryParams: { _token: "{{ csrf_token() }}", department_id: {{ $payload['department_id'] }}, is_prospect: {{ $payload['category_id'] == 1 ? 0 : 1 }} },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTransaksi_Penerimaan == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    actionButtonReceiptTrans("active", [1,2,3])
                    $("#id-receipt-trans").val(-1)
                    $("#receipt-trans-reason").addClass("d-none")
                    $("#id-receipt-trans-dept").val(row.department_id)
                    $("#id-receipt-trans-student").val(row.student_id)
                    $("#AccountingReceiptTransStudentNo").textbox("setValue", row.student_no)
                    $("#AccountingReceiptTransStudentName").textbox("setValue", row.name)
                    $("#id-receipt-paid-dept").val(row.department_id)
                    $("#id-student-paid").val(row.student_id)
                    $("#id-student-no-paid").val(row.student_no)
                    $("#id-student-name-paid").val(row.name)
                    @if ($payload['category_id'] == 1)
                        $("#AccountingReceiptTransClass").textbox("setValue", row.grade + " - " + row.class_name)
                        $.getJSON("{{ url('finance/receipt/data/period') }}", $.param({
                            _token: "{{ csrf_token() }}",
                            department_id: row.department_id,
                            receipt_id: $("#AccountingReceiptTransPayment").combogrid("getValue"),
                            student_id: row.student_id,
                        }), function(response) {
                            $("#AccountingReceiptTransAmountPeriod").combobox("loadData", response).combobox("setValue", response[0].id)
                        })
                    @else
                        $("#AccountingReceiptTransClass").textbox("setValue", row.admission_name + " - " + row.admission_group)
                        $.getJSON("{{ url('finance/receipt/data/payment') }}", $.param({ 
                            _token: "{{ csrf_token() }}",
                            department_id: row.department_id,
                            receipt_id: $("#AccountingReceiptTransPayment").combogrid("getValue"),
                            student_id: row.student_id,
                        }, true), function(response) {
                            $("#AccountingReceiptTransInstalment").numberbox("setValue", response.instalment)
                            $("#AccountingReceiptTransAmountPeriodYear").textbox("setValue", response.period)
                            $("#AccountingReceiptTransPay").numberbox("setValue", response.instalment)
                            $("#AccountingReceiptTransAmountPaid").numberbox("setValue", response.amount)
                            $("#AccountingReceiptTransInstalmentPaid").numberbox("setValue", response.instalment)
                            $("#AccountingReceiptTransJournalPaid").textbox("setValue", response.journal_date)
                            if (response.is_paid > 0) {
                                $("#AccountingReceiptTransStatus").textbox("setValue", "Lunas")
                            } else {
                                $("#AccountingReceiptTransStatus").textbox("setValue", "Belum Lunas")
                            }
                            $("#AccountingReceiptTransRemarkPaid").textbox("setValue", response.remark)
                            $("#id-payment-major").val(response.payment_major_id)
                            $("#id-payment-major-paid").val(response.payment_major_id)
                            $("#tb-receipt-trans-instalment").datagrid("reload", "{{ url('finance/receipt/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&payment_major_id=" + response.payment_major_id + "&amount=" + $("#AccountingReceiptTransAmountPaid").numberbox("getValue"))
                        })
                    @endif
                    $("#AccountingReceiptTransInstalmentPaidReason").textbox("setValue","")
                }
            }
        })
        dgReceiptTrans.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgReceiptTrans.datagrid('getPager').pagination())
        actionButtonReceiptTrans("{{ $ViewType }}",[])
        $("#AccountingReceiptTransInstalment").numberbox({
            onChange: function (newValue,oldValue) {
                setAmountPay(newValue, $("#AccountingReceiptTransDiscount").numberbox("getValue"))
            }
        })
        $("#AccountingReceiptTransDiscount").numberbox({
            onChange: function (newValue,oldValue) {
                setAmountPay($("#AccountingReceiptTransInstalment").numberbox("getValue"), newValue)
            }
        })
        $("#tb-receipt-trans-instalment").datagrid({
            url: "{{ url('finance/receipt/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTransaksi_Penerimaan == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    actionButtonReceiptTrans("active", [0,2,3,4])
                    $("#AccountingReceiptTransAmountPeriod").combobox("readonly")
                    $("#receipt-trans-reason").removeClass("d-none")
                    $("#form-receipt-trans-main").form("load", "{{ url('finance/receipt/show') }}" + "/" + row.id + "/" + {{ $payload['category_id'] }})
                }
            }
        })
        $("#page-receipt-trans").waitMe({effect:"none"})
        $("#AccountingReceiptTransAmountPeriod").combobox({
            onSelect: function(record) {
                getPaymentData($("#id-receipt-paid-dept").val(), $("#AccountingReceiptTransPayment").combogrid("getValue"), record.id)
            }
        })
    })
    function getPaymentData(department_id, receipt_id, payment_major_id) {
        $.getJSON("{{ url('finance/receipt/data/payment') }}", $.param({
            _token: "{{ csrf_token() }}",
            department_id: department_id,
            receipt_id: receipt_id,
            payment_major_id: payment_major_id,
            student_id: $("#id-receipt-trans-student").val()
        }), function(response) {
            $("#id-payment-major").val(payment_major_id)
            $("#id-payment-major-paid").val(payment_major_id)
            $("#AccountingReceiptTransInstalment").numberbox("setValue", response.instalment)
            $("#AccountingReceiptTransAmountPaid").numberbox("setValue", response.amount)
            $("#AccountingReceiptTransInstalmentPaid").numberbox("setValue", response.instalment)
            $("#AccountingReceiptTransJournalPaid").textbox("setValue", response.journal_date)
            if (response.is_paid > 0) {
                $("#AccountingReceiptTransStatus").textbox("setValue", "Lunas")
            } else {
                $("#AccountingReceiptTransStatus").textbox("setValue", "Belum Lunas")
            }
            $("#tb-receipt-trans-instalment").datagrid("reload", "{{ url('finance/receipt/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&payment_major_id=" + payment_major_id + "&amount=" + response.amount)
        })
    }
    function setAmountPay(instalment, discount) {
        var total = parseFloat(instalment) - parseFloat(discount)
        $("#AccountingReceiptTransPay").numberbox("setValue", total)
    }
    function filterReceiptTrans(params) {
        var is_prospect = {{ $payload['category_id'] == 1 ? 0 : 1 }}
        if (Object.keys(params).length > 0) {
            var category_id = {{ $payload['category_id'] }}
            if (category_id === 1) {
                dgReceiptTrans.datagrid("load", { params, is_prospect: is_prospect, fclass: $('#fclass-receipt-trans').combogrid('getValue'), _token: "{{ csrf_token() }}" })
            } else {
                dgReceiptTrans.datagrid("load", { params, is_prospect: is_prospect, fclass: $('#fgroup-receipt-trans').combogrid('getValue'), _token: "{{ csrf_token() }}" })
            }
        } else {
            dgReceiptTrans.datagrid("load", { is_prospect: is_prospect, _token: "{{ csrf_token() }}" })
        }
    }
    function newReceiptTrans() {
        sessionStorage.formTransaksi_Penerimaan = "active"
        actionButtonReceiptTrans("active", [0,1,4])
        $("#AccountingReceiptTransRemark").textbox("setValue","")
        $("#page-receipt-trans").waitMe("hide")
    }
    function editReceiptTrans() {
        sessionStorage.formTransaksi_Penerimaan = "active"
        actionButtonReceiptTrans("active", [0,1,4])
        $("#page-receipt-trans").waitMe("hide")
    }
    function saveReceiptTrans() {
        if (sessionStorage.formTransaksi_Penerimaan == "active") {
            $.messager.confirm("Konfirmasi", "Apakah data sudah benar?", function (r) {
                if (r) {
                    ajaxReceiptTrans("finance/receipt/store")
                }
            })
        }
    }
    function ajaxReceiptTrans(route) {
        $("#form-receipt-trans-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-trans-mandatory").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-trans-mandatory").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-trans-mandatory").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearReceiptTrans()
            getPaymentData($("#id-receipt-trans-dept").val(), $("#AccountingReceiptTransPayment").combogrid("getValue"), $("#id-payment-major").val())
            if (response.params !== 0) {
                $("#AccountingReceiptTransStatus").textbox("setValue", "Lunas")
            } else {
                $("#AccountingReceiptTransStatus").textbox("setValue", "Belum Lunas")
            }
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearReceiptTrans() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearReceiptTransForm()
            }
        })
    }
    function actionButtonReceiptTrans(viewType, idxArray) {
        for (var i = 0; i < menuActionReceiptTrans.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionReceiptTrans[i].id).linkbutton({disabled: true})
            } else {
                $("#" + menuActionReceiptTrans[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionReceiptTrans[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearReceiptTransForm() {
        sessionStorage.formTransaksi_Penerimaan = "init"
        $("#form-receipt-trans-main").form("reset")
        $("#form-receipt-trans-paid").form("reset")
        actionButtonReceiptTrans("init", [0,1])
        $("#id-receipt-trans").val(-1)
        $("#id-receipt-trans-dept").val(-1)
        $("#id-receipt-trans-student").val(-1)
        $("#id-payment-major").val(-1)
        $("#id-payment-major-paid").val(-1)
        $("#id-student-paid").val(-1)
        $("#id-student-no-paid").val("")
        $("#id-student-name-paid").val("")
        $("#id-receipt-paid-dept").val(-1)
        $("#receipt-trans-reason").addClass("d-none")
        $("#tb-receipt-trans-instalment").datagrid("loadData", [])
        $("#AccountingReceiptTransAmountPeriod").combobox("loadData", []).combobox("readonly",false)
        $("#page-receipt-trans").waitMe({effect:"none"})
    }
    function actionClearReceiptTrans() {
        sessionStorage.formTransaksi_Penerimaan = "init"
        actionButtonReceiptTrans("active", [1,2,3])
        $("#AccountingReceiptTransAmountPeriod").combobox("readonly",false)
        $("#AccountingReceiptTransInstalmentPaidReason").textbox("setValue","")
        $("#receipt-trans-reason").addClass("d-none")
        $("#page-receipt-trans").waitMe({effect:"none"})
    }
    function refreshInstalment() {
        $("#tb-receipt-trans-instalment").datagrid("reload", "{{ url('finance/receipt/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&payment_major_id=" + $("#id-payment-major").val() + "&amount=" + $("#AccountingReceiptTransAmountPaid").numberbox("getValue"))
    }
    function storePaymentMajor() {
        $("#form-receipt-trans-paid").ajaxSubmit({
            url: "{{ url('finance/receipt/payment/major/store') }}",
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#panel-receipt-trans-paid").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#panel-receipt-trans-paid").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#panel-receipt-trans-paid").waitMe("hide")
            }
        })
        return false
    }
    function printReceiptTrans() {
        exportDocument("{{ url('finance/receipt/data/print') }}", { 
            department_id: $("#id-receipt-trans-dept").val(), 
            receipt_id: $("#AccountingReceiptTransPayment").combogrid("getValue"),
            student_id: $("#id-receipt-trans-student").val(),
            is_prospect: {{ $payload['category_id'] == 1 ? 0 : 1 }},
            student_no: $("#AccountingReceiptTransStudentNo").textbox("getValue"),
            student_name: $("#AccountingReceiptTransStudentName").textbox("getValue"),
            class: $("#AccountingReceiptTransClass").textbox("getValue"),
            category_id: {{ $payload['category_id'] }},
            bookyear_id: {{ $payload['bookyear_id'] }}
        }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
    function printReceipt() {
        var dg = $("#tb-receipt-trans-instalment").datagrid("getSelected")
        if (dg !== null) {
            $.messager.progress({ title: "Cetak Kuitansi Pembayaran", msg:'Mohon tunggu...' })
            var payload = {
                _token: '{{ csrf_token() }}', 
                receipt_id: dg.id,
                student_id: $("#id-receipt-trans-student").val(),
                is_prospect: {{ $payload['category_id'] == 1 ? 0 : 1 }},
                student_no: $("#AccountingReceiptTransStudentNo").textbox("getValue"),  
                student_name: $("#AccountingReceiptTransStudentName").textbox("getValue"),
                class: $("#AccountingReceiptTransClass").textbox("getValue"),
                category_id: {{ $payload['category_id'] }}
            }
            $.post("{{ url('finance/receipt/data/print/receipt') }}", $.param(payload, true), function(response) {
                $.messager.progress('close')
                $("#receipt-w").window("open")
                $("#receipt-w").window("setTitle", "Kuitansi Pembayaran")
                $("#receipt-w").window("refresh", "{{ url('finance/receipt/data/show') }}" + "?url=" + response)
            })
        }
    }
</script>
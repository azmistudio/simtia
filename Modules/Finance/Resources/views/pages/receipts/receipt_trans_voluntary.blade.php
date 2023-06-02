@php
    $WindowHeight = $InnerHeight;
    $WindowWidth = $InnerWidth;
    $GridHeight = str_replace('px', '', $InnerHeight) - 158 . "px";
    $SubGridHeight = str_replace('px', '', $InnerHeight) - 67 . "px";
@endphp
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar Santri'" style="width:250px">
        <div class="p-1">
            <form id="ff-receipt-trans-voluntary" method="post" class="mb-1">
            @csrf
                @if ($payload['category_id'] == 2)
                    <div class="mb-1">
                @else
                    <div class="mb-1 d-none">
                @endif
                    <select id="fclass-receipt-trans-voluntary" class="easyui-combogrid" style="width:235px;height:22px;" data-options="
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
                            {field:'department',title:'Departemen',width:150},
                            {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                            {field:'grade',title:'Tingkat',width:80,align:'center'},
                            {field:'class',title:'Kelas',width:120},
                            {field:'capacity',title:'Kapasitas/Terisi',width:120},
                        ]],
                    ">
                    </select>
                </div>
                @if ($payload['category_id'] == 2)
                    <div class="mb-1 d-none">
                @else
                    <div class="mb-1">
                @endif
                    <select id="fgroup-receipt-trans-voluntary" class="easyui-combogrid" style="width:235px;height:22px;" data-options="
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
                            {field:'department',title:'Departemen',width:150},
                            {field:'admission_id',title:'Proses',width:100},
                            {field:'group',title:'Kelompok',width:200},
                            {field:'quota',title:'Kapasitas/Terisi',width:110},
                        ]],
                    ">
                    </select>
                </div>
                <div class="mb-1">
                    <input id="fnis-receipt-trans-voluntary" class="easyui-textbox" style="width:235px;height:22px;" label="{{ $payload['category_id'] == 2 ? 'NIS' : 'No.Daftar' }}">
                </div>
                <div class="mb-1">
                    <input id="fname-receipt-trans-voluntary" class="easyui-textbox" style="width:235px;height:22px;" data-options="label:'Nama:'">
                </div>
                <div style="margin-left:80px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterReceiptTransVoluntary({ fnis: $('#fnis-receipt-trans-voluntary').val(),fname: $('#fname-receipt-trans-voluntary').val(),fdept: {{ $payload['department_id'] }} })">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-receipt-trans-voluntary').form('reset');filterReceiptTransVoluntary({})">Batal</a>
                </div>
            </form>
            <table id="tb-receipt-trans-voluntary" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'student_no',width:110,resizeable:true,sortable:true,align:'center'">{{ $payload['category_id'] == 2 ? 'NIS' : 'No.Daftar' }}</th>
                        <th data-options="field:'name',width:120,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-receipt-trans-voluntary" class="panel-top">
            <a id="newReceiptTransVoluntary" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newReceiptTransVoluntary()">Baru</a>
            <a id="editReceiptTransVoluntary" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editReceiptTransVoluntary()">Ubah</a>
            <a id="saveReceiptTransVoluntary" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveReceiptTransVoluntary()">Simpan</a>
            <a id="clearReceiptTransVoluntary" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearReceiptTransVoluntary()">Batal</a>
            <a id="printReceiptTransVoluntary" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Print'" onclick="printReceiptTransVoluntary()">Cetak</a>
        </div>
        <div class="pl-1 pt-3 pr-1" id="page-trans-voluntary">
            <div class="container-fluid">
                <div class="row">
                    <div id="page-receipt-trans-voluntary" class="col-4">
                        <form id="form-receipt-trans-voluntary-main" method="post">
                        <input type="hidden" id="id-receipt-trans-voluntary" name="id" value="-1" />
                        <input type="hidden" id="id-receipt-trans-voluntary-receipt" name="receipt_id" value="{{ $payload['receipt_id'] }}" />
                        <input type="hidden" id="id-receipt-trans-voluntary-dept" name="department_id" value="-1" />
                        <input type="hidden" id="id-receipt-trans-voluntary-student" name="student_id" value="-1" />
                        <input type="hidden" name="category_id" value="{{ $payload['category_id'] }}" />
                        <input type="hidden" name="bookyear_id" value="{{ $book_year->id }}" />
                        <input type="hidden" name="is_prospect" value="{{ $payload['category_id'] == 2 ? 0 : 1 }}" />
                        <div class="mb-1">
                            <input name="student_no" class="easyui-textbox" id="AccountingReceiptTransVoluntaryStudentNo" style="width:300px;height:22px;" data-options="labelWidth:'125px',readonly:'true'" label="{{ $payload['category_id'] == 2 ? 'NIS' : 'No.Daftar' }}" />
                        </div>
                        <div class="mb-1">
                            <input name="student_name" class="easyui-textbox" id="AccountingReceiptTransVoluntaryStudentName" style="width:300px;height:22px;" data-options="label:'Nama:',labelWidth:'125px',readonly:'true'" />
                        </div>
                        <div class="mb-1">
                            <input name="class_name" class="easyui-textbox" id="AccountingReceiptTransVoluntaryClass" style="width:300px;height:66px;" data-options="labelWidth:'125px',readonly:'true',multiline:'true'" label="{{ $payload['category_id'] == 2 ? 'Kelas' : 'Kelompok' }}" />
                        </div>
                        <div class="mb-1">
                            <input name="amount" id="AccountingReceiptTransVoluntaryAmount" class="easyui-numberbox" style="width:300px;height:22px;" data-options="label:'<b>*</b>Jumlah:',labelWidth:'125px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" value="0" />
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
                            <input name="remark" class="easyui-textbox" style="width:300px;height:44px;" data-options="multiline:true" />
                        </div>
                        <div id="receipt-trans-voluntary-reason" class="mb-1 d-none">
                            <label class="mb-1" style="width:121px;">*Alasan Ubah Data:</label>
                            <input name="reason" id="AccountingReceiptTransVoluntaryReason" class="easyui-textbox" style="width:300px;height:44px;" data-options="multiline:true" />
                        </div>
                        </form>
                    </div>
                    <div class="col-8 pl-0">
                        <table id="tb-receipt-trans-voluntary-instalment" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                            data-options="singleSelect:true,method:'post',rownumbers:true,showFooter:true,toolbar:'#menubarReceiptTrans'">
                            <thead>
                                <tr>
                                    <th data-options="field:'id',width:80,hidden:true">ID</th>
                                    <th data-options="field:'journal',width:130,resizeable:true,align:'center'">No. Jurnal/Tgl.</th>
                                    <th data-options="field:'account',width:250,resizeable:true">Rek. Kas</th>
                                    <th data-options="field:'total',width:100,resizeable:true,align:'right'">Jumlah</th>
                                    <th data-options="field:'remark',width:150,resizeable:true">Keterangan</th>
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
            <div class="col-4"><span style="line-height: 25px;"><b>Daftar Transaksi</b></span></div>
            <div class="col-8 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Refresh" plain="true" onclick="refreshPaymentVoluntary()">Muat Ulang</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Print" plain="true" onclick="printPaymentReceiptVoluntary()">Cetak Kuitansi</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionReceiptTrans = document.getElementById("menu-act-receipt-trans-voluntary").getElementsByTagName("a")
    var dgReceiptTrans = $("#tb-receipt-trans-voluntary")
    $(function () {
        sessionStorage.formTransaksi_Penerimaan = "init"
        dgReceiptTrans.datagrid({
            url: "{{ url('finance/receipt/payment/major/student') }}",
            queryParams: { _token: "{{ csrf_token() }}", department_id: {{ $payload['department_id'] }}, is_prospect: {{ $payload['category_id'] == 2 ? 0 : 1 }} },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTransaksi_Penerimaan == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    $("#form-receipt-trans-voluntary-main").form("reset")
                    actionButtonReceiptTransVoluntary("active", [1,2,3])
                    $("#id-receipt-trans-voluntary").val(-1)
                    $("#receipt-trans-voluntary-reason").addClass("d-none")
                    $("#id-receipt-trans-voluntary-dept").val(row.department_id)
                    $("#id-receipt-trans-voluntary-student").val(row.student_id)
                    $("#AccountingReceiptTransVoluntaryStudentNo").textbox("setValue", row.student_no)
                    $("#AccountingReceiptTransVoluntaryStudentName").textbox("setValue", row.name)
                    @if ($payload['category_id'] == 2)
                        $("#AccountingReceiptTransVoluntaryClass").textbox("setValue", row.grade + " - " + row.class_name)
                    @else
                        $("#AccountingReceiptTransVoluntaryClass").textbox("setValue", row.admission_name + " - " + row.admission_group)
                    @endif
                    var is_prospect = {{ $payload['category_id'] == 2 ? 0 : 1 }}
                    $("#tb-receipt-trans-voluntary-instalment").datagrid("reload", "{{ url('finance/receipt/data/voluntary') }}" + "?_token=" + "{{ csrf_token() }}" + "&is_prospect=" + is_prospect + "&student_id=" + row.student_id)
                }
            }
        })
        dgReceiptTrans.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgReceiptTrans.datagrid('getPager').pagination())
        actionButtonReceiptTransVoluntary("{{ $ViewType }}",[])
        $("#tb-receipt-trans-voluntary-instalment").datagrid({
            url: "{{ url('finance/receipt/data/voluntary') }}" + "?_token=" + "{{ csrf_token() }}" + "&is_prospect=" + {{ $payload['category_id'] == 2 ? 0 : 1 }} + "&student_id=0",
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTransaksi_Penerimaan == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    actionButtonReceiptTransVoluntary("active", [0,2,3,4])
                    $("#receipt-trans-voluntary-reason").removeClass("d-none")
                    $("#form-receipt-trans-voluntary-main").form("load", "{{ url('finance/receipt/show') }}" + "/" + row.id + "/" + {{ $payload['category_id'] }})
                }
            }
        })
        $("#page-receipt-trans-voluntary").waitMe({effect:"none"})
    })
    function filterReceiptTransVoluntary(params) {
        var is_prospect = {{ $payload['category_id'] == 2 ? 0 : 1 }}
        if (Object.keys(params).length > 0) {
            var category_id = {{ $payload['category_id'] }}
            if (category_id === 2) {
                dgReceiptTrans.datagrid("load", { params, is_prospect: is_prospect, fclass: $('#fclass-receipt-trans-voluntary').combogrid('getValue'), _token: "{{ csrf_token() }}" })
            } else {
                dgReceiptTrans.datagrid("load", { params, is_prospect: is_prospect, fclass: $('#fgroup-receipt-trans-voluntary').combogrid('getValue'), _token: "{{ csrf_token() }}" })
            }
        } else {
            dgReceiptTrans.datagrid("load", { is_prospect: is_prospect, _token: "{{ csrf_token() }}" })
        }
    }
    function newReceiptTransVoluntary() {
        sessionStorage.formTransaksi_Penerimaan = "active"
        actionButtonReceiptTransVoluntary("active", [0,1,4])
        $("#page-receipt-trans-voluntary").waitMe("hide")
    }
    function editReceiptTransVoluntary() {
        sessionStorage.formTransaksi_Penerimaan = "active"
        actionButtonReceiptTransVoluntary("active", [0,1,4])
        $("#page-receipt-trans-voluntary").waitMe("hide")
    }
    function saveReceiptTransVoluntary() {
        if (sessionStorage.formTransaksi_Penerimaan == "active") {
            $.messager.confirm("Konfirmasi", "Apakah data sudah benar?", function (r) {
                if (r) {
                    ajaxReceiptTransVoluntary("finance/receipt/store")
                }
            })
        }
    }
    function ajaxReceiptTransVoluntary(route) {
        $("#form-receipt-trans-voluntary-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-trans-voluntary").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-trans-voluntary").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-trans-voluntary").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearReceiptTransVoluntary()
            refreshPaymentVoluntary()
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearReceiptTransVoluntary() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearReceiptTransVoluntaryForm()
            }
        })
    }
    function actionButtonReceiptTransVoluntary(viewType, idxArray) {
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
    function actionClearReceiptTransVoluntaryForm() {
        sessionStorage.formTransaksi_Penerimaan = "init"
        $("#form-receipt-trans-voluntary-main").form("reset")
        actionButtonReceiptTransVoluntary("init", [0,1])
        $("#id-receipt-trans-voluntary").val(-1)
        $("#id-receipt-trans-voluntary-dept").val(-1)
        $("#id-receipt-trans-voluntary-student").val(-1)
        $("#receipt-trans-voluntary-reason").addClass("d-none")
        $("#tb-receipt-trans-voluntary-instalment").datagrid("loadData", [])
        $("#page-receipt-trans-voluntary").waitMe({effect:"none"})
    }
    function actionClearReceiptTransVoluntary() {
        sessionStorage.formTransaksi_Penerimaan = "init"
        actionButtonReceiptTransVoluntary("active", [1,2,3])
        $("#receipt-trans-voluntary-reason").addClass("d-none")
        $("#page-receipt-trans-voluntary").waitMe({effect:"none"})
    }
    function refreshPaymentVoluntary() {
        var is_prospect = {{ $payload['category_id'] == 2 ? 0 : 1 }}
        $("#tb-receipt-trans-voluntary-instalment").datagrid("reload", "{{ url('finance/receipt/data/voluntary') }}" + "?_token=" + "{{ csrf_token() }}" + "&is_prospect=" + is_prospect + "&student_id=" + $("#id-receipt-trans-voluntary-student").val())
    }
    function storePaymentMajor() {
        $("#form-receipt-trans-voluntary-paid").ajaxSubmit({
            url: "{{ url('finance/receipt/payment/major/store') }}",
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
    function printReceiptTransVoluntary() {
        exportDocument("{{ url('finance/receipt/data/print') }}", {
            department_id: $("#id-receipt-trans-voluntary-dept").val(),
            student_id: $("#id-receipt-trans-voluntary-student").val(),
            is_prospect: {{ $payload['category_id'] == 2 ? 0 : 1 }},
            student_no: $("#AccountingReceiptTransVoluntaryStudentNo").textbox("getValue"),
            student_name: $("#AccountingReceiptTransVoluntaryStudentName").textbox("getValue"),
            class: $("#AccountingReceiptTransVoluntaryClass").textbox("getValue"),
            category_id: {{ $payload['category_id'] }}
        }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
    function printPaymentReceiptVoluntary() {
        var dg = $("#tb-receipt-trans-voluntary-instalment").datagrid("getSelected")
        if (dg !== null) {
            $.messager.progress({ title: "Cetak Kuitansi Pembayaran", msg:'Mohon tunggu...' })
            var payload = {
                _token: '{{ csrf_token() }}',
                receipt_id: dg.id,
                is_prospect: {{ $payload['category_id'] == 2 ? 0 : 1 }},
                student_id: $("#id-receipt-trans-voluntary-student").val(),
                student_no: $("#AccountingReceiptTransVoluntaryStudentNo").textbox("getValue"),
                student_name: $("#AccountingReceiptTransVoluntaryStudentName").textbox("getValue"),
                class: $("#AccountingReceiptTransVoluntaryClass").textbox("getValue"),
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

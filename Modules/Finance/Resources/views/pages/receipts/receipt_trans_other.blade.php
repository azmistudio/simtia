@php
    $WindowHeight = $InnerHeight;
    $WindowWidth = $InnerWidth;
    $GridHeight = str_replace('px', '', $InnerHeight) - 158 . "px";
    $SubGridHeight = str_replace('px', '', $InnerHeight) - 67 . "px";
@endphp
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'center'">
        <div id="menu-act-receipt-trans-other" class="panel-top">
            <a id="newReceiptTransOther" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newReceiptTransOther()">Baru</a>
            <a id="editReceiptTransOther" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editReceiptTransOther()">Ubah</a>
            <a id="saveReceiptTransOther" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveReceiptTransOther()">Simpan</a>
            <a id="clearReceiptTransOther" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearReceiptTransOther()">Batal</a>
        </div>
        <div class="pl-1 pt-3 pr-1" id="page-trans-other">
            <div class="container-fluid">
                <div class="row">
                    <div id="page-receipt-trans-other" class="col-4">
                        <form id="form-receipt-trans-other-main" method="post">
                        <input type="hidden" id="id-receipt-trans-other" name="id" value="-1" />
                        <input type="hidden" id="id-receipt-trans-other-receipt" name="receipt_id" value="{{ $payload['receipt_id'] }}" />
                        <input type="hidden" id="id-receipt-trans-other-dept" name="department_id" value="{{ $payload['department_id'] }}" />
                        <input type="hidden" name="category_id" value="{{ $payload['category_id'] }}" />
                        <input type="hidden" name="bookyear_id" value="{{ $book_year->id }}" />
                        <div class="mb-1">
                            <input name="source" class="easyui-textbox" style="width:390px;height:22px;" data-options="label:'*Sumber Penerimaan',labelWidth:'150px'" />
                        </div>
                        <div class="mb-1">
                            <input name="amount" id="AccountingReceiptTransOtherAmount" class="easyui-numberbox" style="width:390px;height:22px;" data-options="label:'<b>*</b>Jumlah:',labelWidth:'150px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" value="0" />
                        </div>
                        <div class="mb-3">
                            <input name="journal_date" class="easyui-datebox" style="width:265px;height:22px;" data-options="label:'<b>*</b>Tanggal:',labelWidth:'150px',formatter:dateFormatter,parser:dateParser" value="{{ date('d/m/Y') }}" />
                        </div>
                        <div class="mb-1">
                            <label class="mb-1" style="width:146px;">*Rekening Kas:</label>
                            <select name="cash_account" class="easyui-combobox" style="width:390px;height:22px;" data-options="panelHeight:150">
                                @foreach ($codes_cash as $code)
                                <option value="{{ $code['id'] }}">{{ $code['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="mb-1" style="width:146px;">Keterangan:</label>
                            <input name="remark" class="easyui-textbox" style="width:390px;height:44px;" data-options="multiline:true" />
                        </div>
                        <div id="receipt-trans-other-reason" class="mb-1 d-none">
                            <label class="mb-1" style="width:146px;">*Alasan Ubah Data:</label>
                            <input name="reason" id="AccountingReceiptTransOtherReason" class="easyui-textbox" style="width:390px;height:44px;" data-options="multiline:true" />
                        </div>
                        </form>
                    </div>
                    <div class="col-8 pl-0">
                        <table id="tb-receipt-trans-other" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                            data-options="singleSelect:true,method:'post',rownumbers:true,showFooter:true,toolbar:'#menubarReceiptTrans'">
                            <thead>
                                <tr>
                                    <th data-options="field:'id',width:80,hidden:true">ID</th>
                                    <th data-options="field:'journal',width:130,resizeable:true,align:'center'">No. Jurnal/Tgl.</th>
                                    <th data-options="field:'source',width:130,resizeable:true">Sumber.</th>
                                    <th data-options="field:'account',width:200,resizeable:true">Rek. Kas</th>
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
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--Print" plain="true" onclick="printPaymentReceiptOther()">Cetak Kuitansi</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionReceiptTransOther = document.getElementById("menu-act-receipt-trans-other").getElementsByTagName("a")
    $(function () {
        sessionStorage.formTransaksi_Penerimaan = "init"
        actionButtonReceiptTransOther("{{ $ViewType }}",[])
        $("#tb-receipt-trans-other").datagrid({
            url: "{{ url('finance/receipt/data/other') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTransaksi_Penerimaan == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    actionButtonReceiptTransOther("active", [0,2,3])
                    $("#receipt-trans-other-reason").removeClass("d-none")
                    $("#form-receipt-trans-other-main").form("load", "{{ url('finance/receipt/show') }}" + "/" + row.id + "/" + {{ $payload['category_id'] }})
                    $("#page-receipt-trans-other").waitMe("hide")
                }
            }
        })
        $("#page-receipt-trans-other").waitMe({effect:"none"})
    })
    function newReceiptTransOther() {
        sessionStorage.formTransaksi_Penerimaan = "active"
        actionButtonReceiptTransOther("active", [0,1])
        $("#page-receipt-trans-other").waitMe("hide")
    }
    function editReceiptTransOther() {
        sessionStorage.formTransaksi_Penerimaan = "active"
        actionButtonReceiptTransOther("active", [0,1])
    }
    function saveReceiptTransOther() {
        if (sessionStorage.formTransaksi_Penerimaan == "active") {
            $.messager.confirm("Konfirmasi", "Apakah data sudah benar?", function (r) {
                if (r) {
                    ajaxReceiptTransOther("finance/receipt/store")
                }
            })
        }
    }
    function ajaxReceiptTransOther(route) {
        $("#form-receipt-trans-other-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-trans-other").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-trans-other").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-trans-other").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearReceiptTransOther()
            $("#tb-receipt-trans-other").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearReceiptTransOther() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearReceiptTransOther()
            }
        })
    }
    function actionButtonReceiptTransOther(viewType, idxArray) {
        for (var i = 0; i < menuActionReceiptTransOther.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionReceiptTransOther[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionReceiptTransOther[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionReceiptTransOther[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionReceiptTransOther[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearReceiptTransOther() {
        sessionStorage.formTransaksi_Penerimaan = "init"
        $("#form-receipt-trans-other-main").form("reset")
        actionButtonReceiptTransOther("init", [0,1])
        $("#id-receipt-trans-other").val(-1)
        $("#receipt-trans-other-reason").addClass("d-none")
        $("#page-receipt-trans-other").waitMe({effect:"none"})
    }
    function printPaymentReceiptOther() {
        var dg = $("#tb-receipt-trans-other").datagrid("getSelected")
        if (dg !== null) {
            $.messager.progress({ title: "Cetak Kuitansi Pembayaran", msg:'Mohon tunggu...' })
            var payload = {
                _token: '{{ csrf_token() }}', 
                receipt_id: dg.id, 
                student_name: dg.source,
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
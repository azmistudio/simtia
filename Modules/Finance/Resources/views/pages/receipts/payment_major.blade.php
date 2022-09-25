@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Besar Pembayaran</h5>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'center'">
        <div id="menu-act-payment-major" class="panel-top">
            <a id="newPaymentMajor" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newPaymentMajor()">Baru</a>
            <a id="savePaymentMajor" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="savePaymentMajor()">Simpan</a>
            <a id="clearPaymentMajor" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearPaymentMajor()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-payment-major"></span>Jenis Penerimaan: <span id="title-payment-major"></span></h6>
        </div>
        <div id="page-payment-major" class="pt-3 pl-1">
            <form id="form-payment-major-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-payment-major" name="id" value="-1" />
                            <input type="hidden" id="id-payment-major-dept" name="department_id" value="-1" />
                            <div class="mb-1">
                                <select name="category_id" id="AccountingPaymentMajorCategory" class="easyui-combobox" style="width:400px;height:22px;" data-options="label:'<b>*</b>Kategori:',labelWidth:'175px',labelPosition:'before',panelHeight:68">
                                    <option value="">---</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id .'-'. $category->code }}">{{ $category->category }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input id="AccountingPaymentMajorDeptId" class="easyui-textbox" style="width:400px;height:22px;" data-options="label:'Departemen:',labelWidth:'175px',readonly:'true'" />
                            </div>
                            <div class="mb-1">
                                <select name="receipt_id" id="AccountingPaymentMajorReceiptId" class="easyui-combogrid" style="width:400px;height:22px;" data-options="
                                    label:'<b>*</b>Jenis Penerimaan:',
                                    labelWidth:'175px',
                                    panelWidth: 350,
                                    mode: 'remote',
                                    idField: 'id',
                                    textField: 'name',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:120},
                                        {field:'name',title:'Nama',width:200},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="amount" class="easyui-numberbox" style="width:400px;height:22px;" data-options="label:'<b>*</b>Besar Total Pembayaran:',labelWidth:'175px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" value="0" />
                                <span class="mr-2"></span>
                                <label class="mb-0"><i>besar total pembayaran yang harus dilunasi</i></label>
                            </div>
                            <div class="mb-1">
                                <input name="instalment" class="easyui-numberbox" style="width:400px;height:22px;" data-options="label:'<b>*</b>Besar Cicilan:',labelWidth:'175px',min:0,precision:2,groupSeparator:'.',decimalSeparator:','" value="0" />
                                <span class="mr-2"></span>
                                <label class="mb-0"><i>besar cicilan pembayaran yang dibayarkan ketika membayar</i></label>
                            </div>
                            <div id="AccountingPaymentMajorMonth" class="mb-1">
                                <select name="period_month" class="easyui-combobox" style="width:330px;height:22px;" data-options="label:'<b>*</b>Periode Bayar:',labelWidth:'175px',labelPosition:'before',panelHeight:100">
                                    <option value="01">Januari</option>
                                    <option value="02">Pebruari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">Nopember</option>
                                    <option value="12">Desember</option>
                                </select>
                                <span class="mr-2"></span>
                                <input name="period_year" class="easyui-textbox" style="width:58px;height:22px;" data-options="readonly:'true'" value="{{ date('Y') }}" />
                            </div>
                            <div class="mb-1">
                                <input name="first_instalment" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Cicilan Pertama:',labelWidth:'175px',labelPosition:'before'" />
                                <span class="mr-1"></span>
                                <label class="mb-0">Set cicilan pertama Rp 0</label>
                                <span class="mr-2"></span>
                                <label class="mb-0" style="margin-left:50px;"><i>set cicilan pertama Rp 0, supaya muncul di Laporan Tunggakan </i></label>
                            </div>
                            <div class="mb-1" id="OptionClass">
                                <select name="class[]" id="AccountingPaymentMajorClass" class="easyui-combogrid" style="width:400px;height:22px;" data-options="
                                    label:'<b>*</b>Kelas:',
                                    labelWidth:'175px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    multiple: true,
                                    mode: 'remote',
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'class',title:'Kelas',width:120},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <label class="mb-0"><i>dapat dipilih lebih dari 1 kelas</i></label>
                            </div>
                            <div class="mb-1" id="OptionGroup">
                                <select name="group[]" id="AccountingPaymentMajorGroup" class="easyui-combogrid" style="width:400px;height:22px;" data-options="
                                    label:'<b>*</b>Kelompok:',
                                    labelWidth:'175px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'group',
                                    fitColumns:true,
                                    multiple: true,
                                    mode: 'remote',
                                    columns: [[
                                        {field:'department',title:'Departemen',width:100},
                                        {field:'admission_id',title:'Proses',width:200},
                                        {field:'group',title:'Kelompok',width:100},
                                        {field:'quota',title:'Kapasitas/Terisi',width:110},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <label class="mb-0"><i>dapat dipilih lebih dari 1 kelompok</i></label>
                            </div>
                            <div class="well mt-3">
                                <label class="mb-0"><i><b>Santri/calon santri yang sudah terdata besar pembayarannya, tidak akan di data ulang besar pembayarannya</b></i>.</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionPaymentMajor = document.getElementById("menu-act-payment-major").getElementsByTagName("a")
    var titlePaymentMajor = document.getElementById("title-payment-major")
    var markPaymentMajor = document.getElementById("mark-payment-major")
    var idPaymentMajor = document.getElementById("id-payment-major")
    $(function () {
        sessionStorage.formBesar_Pembayaran = "init"
        actionButtonPaymentMajor("{{ $ViewType }}", [])
        $("#AccountingPaymentMajorCategory").combobox({
            onClick: function (record) {
                var payload = 0
                if (record.value !== "") {
                    var records = record.value.split("-")
                    if (records[1] == "JTT") {
                        $("#OptionClass").removeClass("d-none")
                        $("#OptionGroup").addClass("d-none")
                        $("#AccountingPaymentMajorMonth").removeClass("d-none")
                    } else {
                        $("#OptionClass").addClass("d-none")
                        $("#OptionGroup").removeClass("d-none")
                        $("#AccountingPaymentMajorMonth").addClass("d-none")
                    }
                    payload = records[0]
                } else {
                    $("#OptionGroup").addClass("d-none")
                    $("#OptionClass").addClass("d-none")
                    $("#AccountingPaymentMajorMonth").addClass("d-none")
                    $("#id-payment-major-dept").val(-1)
                    $("#AccountingPaymentMajorDeptId").textbox("setValue", "")
                    $("#AccountingPaymentMajorReceiptId").combogrid("setValue", "")
                }
                $("#AccountingPaymentMajorDeptId").textbox("setValue", "")
                $("#AccountingPaymentMajorReceiptId").combogrid("setValue", "")
                $("#AccountingPaymentMajorReceiptId").combogrid("grid").datagrid("load", "{{ url('finance/receipt/type/combo-grid') }}" + "?_token=" + "{{ csrf_token() }}" + "&category_id=" + payload)
            }
        })
        $("#AccountingPaymentMajorReceiptId").combogrid({
            onClickRow: function (index, row) {
                $("#id-payment-major-dept").val(row.department_id)
                $("#AccountingPaymentMajorDeptId").textbox("setValue", row.department)
                $("#AccountingPaymentMajorClass").combogrid("setValue", "").combogrid("grid").datagrid("reload", "{{ url('academic/class/student/combo-grid') }}" + "?department_id=" + row.department_id + "&_token=" + "{{ csrf_token() }}")
                $("#AccountingPaymentMajorGroup").combogrid("setValue", "").combogrid("grid").datagrid("reload", "{{ url('academic/admission/prospective-group/combo-grid') }}" + "?department_id=" + row.department_id + "&_token=" + "{{ csrf_token() }}")
            }
        })
        $("#page-payment-major").waitMe({effect:"none"})
    })
    function newPaymentMajor() {
        sessionStorage.formBesar_Pembayaran = "active"
        $("#form-payment-major-main").form("reset")
        actionButtonPaymentMajor("active", [0])
        markPaymentMajor.innerText = "*"
        titlePaymentMajor.innerText = ""
        idPaymentMajor.value = "-1"
        $("#AccountingPaymentMajorMonth").addClass("d-none")
        $("#OptionGroup").addClass("d-none")
        $("#OptionClass").addClass("d-none")
        $("#page-payment-major").waitMe("hide")
    }
    function savePaymentMajor() {
        if (sessionStorage.formBesar_Pembayaran == "active") {
            ajaxPaymentMajor("finance/receipt/payment/major/store")
        }
    }
    function ajaxPaymentMajor(route) {
        $("#form-payment-major-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-payment-major").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-payment-major").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-payment-major").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearPaymentMajor()
            $("#tb-payment-major").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearPaymentMajor() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearPaymentMajor()
            }
        })
    }
    function actionButtonPaymentMajor(viewType, idxArray) {
        for (var i = 0; i < menuActionPaymentMajor.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionPaymentMajor[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionPaymentMajor[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionPaymentMajor[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionPaymentMajor[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearPaymentMajor() {
        sessionStorage.formBesar_Pembayaran = "init"
        $("#form-payment-major-main").form("reset")
        actionButtonPaymentMajor("init", [])
        titlePaymentMajor.innerText = ""
        markPaymentMajor.innerText = ""
        idPaymentMajor.value = "-1"
        $("#page-payment-major").waitMe({effect:"none"})
    }
</script>
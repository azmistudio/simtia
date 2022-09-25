@php
    $WindowHeight = $InnerHeight - 216 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $PanelHeight = $InnerHeight - 201 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Transaksi Penerimaan</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
        <div class="col-12 p-0">
            <div class="mb-1 mt-2">
                <input id="AccountingReceiptTransBookYear" class="easyui-textbox" style="width:150px;height:22px;" data-options="label:'Tahun Buku:',labelWidth:'100px',readonly:'true'" value="{{ $bookyear->book_year }}" />
                <span class="mr-2"></span>
                <input id="AccountingReceiptTransDept" class="easyui-textbox" style="width:250px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:'true'" />
                <input type="hidden" id="id-receipt-dept" value="-1" />
                <span class="mr-2"></span>
                <input id="AccountingReceiptTransCategory" class="easyui-textbox" style="width:280px;height:22px;" data-options="label:'Kategori:',labelWidth:'80px',readonly:'true'" />
                <input type="hidden" id="AccountingReceiptTransCategoryId" value="-1" />
                <span class="mr-2"></span>
                <select id="AccountingReceiptTransPayment" class="easyui-combogrid" style="width:375px;height:22px;" data-options="
                    label:'Penerimaan:',
                    labelWidth:'100px',
                    panelWidth: 700,
                    idField: 'id',
                    textField: 'name',
                    fitColumns:true,
                    columns: [[
                        {field:'department',title:'Departemen',width:150},
                        {field:'category',title:'Kategori',width:200},
                        {field:'name',title:'Tipe Penerimaan',width:250},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'" onclick="showReceiptTransForm($('#id-receipt-dept').val(), $('#AccountingReceiptTransPayment').combogrid('getValue'), $('#AccountingReceiptTransCategoryId').val())">Tampilkan Form</a>
            </div>
        </div>
        <div class="col-12 p-0 mt-1">
            <div id="p-receipt-trans-form" class="easyui-panel" style="height:{{ $PanelHeight }};border: none !important;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReceiptTransPayment").combogrid({
            url: '{{ url('finance/receipt/type/payment/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#id-receipt-dept").val(row.department_id)
                $("#AccountingReceiptTransDept").textbox("setValue", row.department)
                $("#AccountingReceiptTransCategory").textbox("setValue", row.category)
                $("#AccountingReceiptTransCategoryId").val(row.category_id)
                $("#AccountingReceiptTransPayment").combogrid('hidePanel')
            }
        })
    })
    function reloadAccountingReceiptTransPayment(department_id) {
        $("#AccountingReceiptTransPayment").combogrid("grid").datagrid("reload", "{{ url('finance/receipt/type/payment/combo-grid') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=" + department_id)                                
    }
    function showReceiptTransForm(department_id, receipt_id, category_id) {
        if ($("#id-receipt-dept").val() !== "-1") {
            var page
            if (category_id == 1 || category_id == 3) {
                page = "mandatory"
            } else if (category_id == 2 || category_id == 4) {
                page = "voluntary"
            } else {
                page = "other"
            }
            $("#p-receipt-trans-form").panel("refresh", "{{ url('finance/receipt') }}" + "/" + page + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" + "&department_id=" + department_id + "&receipt_id=" + receipt_id + "&category_id=" + category_id + "&bookyear_id=" + {{ $bookyear->id }});
        } 
    } 
</script>
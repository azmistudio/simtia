@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
    $PanelHeight = $InnerHeight - 325 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:110px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Tingkat/Semester:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:90px;">Tahun Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:170px;">Jns.Penerimaan</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Pembayaran</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Status</label>
            </div>
            <form id="form-accounting-report-class">
            <div class="col-12">
                <input type="hidden" id="id-report-class-department" value="-1" /> 
                <input type="hidden" id="id-report-class-schoolyear" value="-1" /> 
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:110px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportGrade" class="easyui-textbox tbox" style="width:120px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportSchoolYear" class="easyui-textbox tbox" style="width:90px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportClass" class="easyui-combogrid cgrd" style="width:120px;height:22px;" data-options="
                    panelWidth: 450,
                    idField: 'id',
                    textField: 'class',
                    fitColumns:true,
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                        {field:'class',title:'Kelas',width:150},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportReceiptCategory" class="easyui-combobox cbox" style="width:170px;height:22px;" data-options="panelHeight:90">
                    <option value="0">---</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportPayment" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:90,valueField:'id',textField:'name'"></select>
                <span class="mr-2"></span>
                <select id="AccountingReportStatus" class="easyui-combobox cbox" style="width:120px;height:22px;" data-options="panelHeight:90">
                    <option value="-1">-- Semua --</option>
                    <option value="0">Belum Lunas</option>
                    <option value="1">Lunas</option>
                    <option value="2">Gratis</option>
                </select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportClass(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-class').form('reset');filterAccountingReportClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
            </div>
            <div class="col-12 mt-1">
                <label class="mb-1" style="width:242px;">Periode Bayar</label>
            </div>
            <div class="col-12">
                 <select id="AccountingReportPeriod" class="easyui-combobox cbox" style="width:242px;height:22px;" data-options="panelHeight:150,valueField:'id',textField:'text'"></select>
            </div>
            </form>
            <div class="col-12 mt-2">
                <div id="p-report-receipt" class="easyui-panel pnel" style="height:{{ $PanelHeight }};border:none !important;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportClass").combogrid('grid').datagrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#AccountingReportPayment").combobox("clear")
                $("#AccountingReportDept").textbox('setText', row.department)
                $("#AccountingReportGrade").textbox('setText', row.grade + "/" + row.semester)
                $("#AccountingReportSchoolYear").textbox('setText', row.school_year)
                $("#AccountingReportClass").combogrid('hidePanel')
                $("#id-report-class-department").val(row.department_id)
                $("#id-report-class-schoolyear").val(row.school_year)
            }
        })
        $("#AccountingReportReceiptCategory").combobox({
            onSelect: function(record) {
                if (record.value !== "0" && $("#id-report-class-department").val() !== "-1") {
                    $("#AccountingReportPayment").combobox("clear").combobox("reload", "{{ url('finance/receipt/type/combo-box') }}" + "/" + record.value + "/" + $("#id-report-class-department").val() + "?_token=" + "{{ csrf_token() }}")
                }
            }
        })
        $("#AccountingReportPayment").combobox({
            onSelect: function(record) {
                if (record.id !== 0 && $("#id-report-class-department").val() !== "-1" && $("#AccountingReportReceiptCategory").combobox("getValue") !== 0)
                $("#AccountingReportPeriod").combobox("clear").combobox("reload", "{{ url('finance/receipt/payment/major/period/combo-box') }}" 
                    + "?_token=" + "{{ csrf_token() }}"
                    + "&department_id=" + $("#id-report-class-department").val()
                    + "&category_id=" + $("#AccountingReportReceiptCategory").combobox("getValue")
                    + "&receipt_id=" + record.id
                    + "&schoolyear=" + $("#id-report-class-schoolyear").val()
                ).combobox({
                    onLoadSuccess: function(response) {
                        $(this).combobox("setValue", response[0])
                    }
                })
            }
        })
    })
    function filterAccountingReportClass(val) {
        if (val > 0) {
            if ($("#AccountingReportPayment").combobox("getValue") > 0) {
                $("#p-report-receipt").panel("refresh", "{{ url('finance/report/receipt/class/view') }}" 
                    + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                    + "&class_id=" + $("#AccountingReportClass").combogrid("getValue") 
                    + "&class=" + $("#AccountingReportClass").combogrid("getText") 
                    + "&department_id=" + $("#id-report-class-department").val()
                    + "&department=" + $("#AccountingReportDept").combogrid("getText") 
                    + "&grade=" + $("#AccountingReportGrade").combogrid("getText") 
                    + "&category_id=" + $("#AccountingReportReceiptCategory").combobox("getValue") 
                    + "&category=" + $("#AccountingReportReceiptCategory").combobox("getText") 
                    + "&payment=" + $("#AccountingReportPayment").combobox("getValue") 
                    + "&status=" + $("#AccountingReportStatus").combobox("getValue") 
                    + "&payment_name=" + $("#AccountingReportPayment").combobox("getText") 
                    + "&schoolyear=" + $("#AccountingReportSchoolYear").textbox("getText")
                    + "&period=" + $("#AccountingReportPeriod").combobox("getValue")
                )
            }
        } else {
            $("#p-report-receipt").panel("clear")
        }
    }
</script>
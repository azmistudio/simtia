@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
    $PanelHeight = $InnerHeight - 271 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:150px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Tingkat/Semester:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:90px;">Tahun Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Pembayaran</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Telat Bayar</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-class">
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:150px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportGrade" class="easyui-textbox tbox" style="width:120px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportSchoolYear" class="easyui-textbox tbox" style="width:90px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportClass" class="easyui-combogrid cgrd" style="width:150px;height:22px;" data-options="
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
                <select id="AccountingReportPayment" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:90,valueField:'id',textField:'name'"></select>
                <span class="mr-2"></span>
                <input id="AccountingReportDuration" class="easyui-numberbox nbox" style="width:40px;height:22px;text-align: right;" value="30" />
                <span class="ml-2 mr-2">hari, dari</span>
                <input id="AccountingReportDateDelay" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportClass(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-class').form('reset');filterAccountingReportClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
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
                $("#AccountingReportPayment").combobox("reload", "{{ url('finance/receipt/type/combo-box') }}" + "/1/" + row.department_id + "?_token=" + "{{ csrf_token() }}")
            }
        })
    })
    function filterAccountingReportClass(val) {
        if (val > 0) {
            if ($("#AccountingReportPayment").combobox("getValue") > 0) {
                $("#p-report-receipt").panel("refresh", "{{ url('finance/report/receipt/student/arrear/view') }}" 
                    + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                    + "&class_id=" + $("#AccountingReportClass").combogrid("getValue") 
                    + "&class=" + $("#AccountingReportClass").combogrid("getText") 
                    + "&department=" + $("#AccountingReportDept").combogrid("getText") 
                    + "&grade=" + $("#AccountingReportGrade").combogrid("getText") 
                    + "&payment=" + $("#AccountingReportPayment").combobox("getValue") 
                    + "&status=0" 
                    + "&payment_name=" + $("#AccountingReportPayment").combobox("getText") 
                    + "&duration=" + $("#AccountingReportDuration").numberbox("getValue") 
                    + "&date_delay=" + $("#AccountingReportDateDelay").datebox("getValue") 
                    + "&schoolyear=" + $("#AccountingReportSchoolYear").textbox("getText"))
            }
        } else {
            $("#p-report-receipt").panel("clear")
        }
    }
</script>
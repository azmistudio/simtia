<div class="p-2">
    <div class="table-filter" style="width:870px !important;">
        <form id="fg-list-employee" method="post">
        @csrf
        <table>
            <tbody>
                <tr>
                    <td style="width:30px;"></td>
                    <td style="width:100px;">
                        <input class="easyui-textbox" id="fg-list-employee-nip" style="width:95px;height:22px;" />
                    </td>
                    <td style="width:150px;">
                        <input class="easyui-textbox" id="fg-list-employee-name" style="width:145px;height:22px;" />
                    </td>
                    <td style="width:250px;">
                        <select id="fg-list-employee-section" class="easyui-combobox" style="width:245px;height:22px;" data-options="panelHeight:68">
                            <option value="">---</option>
                            @foreach ($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="width:100px;border-right: 0;text-align: left;">
                       <a href="javascript:void(0)" class="easyui-linkbutton" onclick="filterListEmployee({fnip: $('#fg-list-employee-nip').val(), fname: $('#fg-list-employee-name').val(), fsection: $('#fg-list-employee-section').combobox('getValue') })" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                       <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#fg-list-employee').form('reset');filterListEmployee({});" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
    </div>
    <table id="tb-list-employee" class="easyui-datagrid" style="width:870px;height:315px" 
        data-options="method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
        <thead>
            <tr>
                <th data-options="field:'id',width:150,hidden:true">ID</th>
                <th data-options="field:'employee_id',width:100,align:'center'">NIP</th>
                <th data-options="field:'name',width:150,sortable:true">Nama</th>
                <th data-options="field:'section',width:250,sortable:true">Bagian</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-list-employee").datagrid({
            url: "{{ url('hr/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                $("#id-expenditure-employee").val(row.id)
                $("#id-expenditure-student").val(-1)
                $("#id-expenditure-other").val(-1)
                $("#AccountingExpenditureReceived").textbox("setValue", row.employee_id + "-" + row.name)
                $("#AccountingExpenditureReceivedName").textbox("setValue", row.name)
                $("#receipt-w").window("close")
            }
        })
    })
    function filterListEmployee(params) {
        if (Object.keys(params).length > 0) {
            $("#tb-list-employee").datagrid("reload", { params, _token: "{{ csrf_token() }}" })
        } else {
            $("#tb-list-employee").datagrid("reload", { _token: "{{ csrf_token() }}" })
        }
    }
</script>
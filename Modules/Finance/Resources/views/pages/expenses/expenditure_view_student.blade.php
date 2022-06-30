<div class="p-2">
    <div class="table-filter" style="width:870px !important;">
        <form id="fg-list-student" method="post">
        @csrf
        <table>
            <tbody>
                <tr>
                    <td style="width:30px;"></td>
                    <td style="width:100px;"></td>
                    <td style="width:150px;"></td>
                    <td style="width:60px;"></td>
                    <td style="width:100px;"></td>
                    <td style="width:100px;">
                        <input class="easyui-textbox" id="fg-list-student-nis" style="width:95px;height:22px;" />
                    </td>
                    <td style="width:220px;">
                        <input class="easyui-textbox" id="fg-list-student-name" style="width:215px;height:22px;" />
                    </td>
                    <td style="width:100px;border-right: 0;text-align: left;">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="filterListStudent({
                            fnis: $('#fg-list-student-nis').val(), 
                            fname: $('#fg-list-student-name').val(), 
                        })" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#fg-list-student').form('reset');filterListStudent({});" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
    </div>
    <table id="tb-list-student" class="easyui-datagrid" style="width:870px;height:315px" 
        data-options="method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
        <thead>
            <tr>
                <th data-options="field:'id',width:150,hidden:true">ID</th>
                <th data-options="field:'school_year',width:100,align:'center'">Thn. Ajaran</th>
                <th data-options="field:'department',width:150,align:'center'">Departemen</th>
                <th data-options="field:'grade',width:60,align:'center'">Tingkat</th>
                <th data-options="field:'class',width:100,align:'center'">Kelas</th>
                <th data-options="field:'student_no',width:100,align:'center',sortable:true">NIS</th>
                <th data-options="field:'name',width:220,sortable:true">Nama</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript">
    $(function () {
        var params = {fdept: "{{ $requests['department_id'] }}"}
        $("#tb-list-student").datagrid({
            url: "{{ url('academic/student/data') }}",
            queryParams: { _token: "{{ csrf_token() }}", params },
            onDblClickRow: function (index, row) {
                $("#id-expenditure-employee").val(-1)
                $("#id-expenditure-student").val(row.id)
                $("#id-expenditure-other").val(-1)
                $("#AccountingExpenditureReceived").textbox("setValue", row.student_no + " - " + row.name)
                $("#AccountingExpenditureReceivedName").textbox("setValue", row.name)
                $("#receipt-w").window("close")
            }
        })
    })
    function filterListStudent(params) {
        if (Object.keys(params).length > 0) {
            $("#tb-list-student").datagrid("reload", { params, _token: "{{ csrf_token() }}" })
        } else {
            $("#tb-list-student").datagrid("reload", { _token: "{{ csrf_token() }}" })
        }
    }
</script>
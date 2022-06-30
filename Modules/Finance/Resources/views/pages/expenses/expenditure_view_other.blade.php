<div class="p-2">
    <table id="tb-list-requested" class="easyui-datagrid" style="width:870px;height:315px" 
        data-options="idField:'id',rownumbers:'true',toolbar:toolbarListRequested">
        <thead>
            <tr>
                <th data-options="field:'name',width:250,sortable:true,editor:{type:'validatebox',options:{required:true}}">Nama</th>
                <th data-options="field:'remark',width:250,editor:{type:'validatebox',options:{required:false}}">Keterangan</th>
            </tr>
        </thead>
    </table>
</div>
<script type="text/javascript">
    var toolbarListRequested = [
    {
        text: 'Ambil Terpilih',
        iconCls: 'ms-Icon ms-Icon--Clicked',
        handler: function() {
            var row = $("#tb-list-requested").datagrid("getSelected")
            $("#id-expenditure-employee").val(-1)
            $("#id-expenditure-student").val(-1)
            if (row !== null && row.hasOwnProperty('id')) {
                $("#id-expenditure-other").val(row.id)
                $("#AccountingExpenditureReceived").textbox("setValue", row.name)
                $("#AccountingExpenditureReceivedName").textbox("setValue", row.name)
                $("#receipt-w").window("close")
            }
        }
    },'-',{
    	text: 'Tambah',
        iconCls: 'ms-Icon ms-Icon--Add',
        handler: function() {
            $("#tb-list-requested").edatagrid("addRow")
        }
   	},{
    	text: 'Simpan',
        iconCls: 'ms-Icon ms-Icon--Save',
        handler: function() {
            $("#tb-list-requested").edatagrid("saveRow")
        }
    },{
    	text: 'Batal',
        iconCls: 'ms-Icon ms-Icon--Clear',
        handler: function() {
            $("#tb-list-requested").edatagrid("cancelRow")
        }
    },{
    	text: 'Hapus',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            $("#tb-list-requested").edatagrid("destroyRow")
        }
    }]
    $(function () {
        $('#tb-list-requested').edatagrid({
            url: "{{ url('finance/expenditure/requested/data') }}" + "?_token=" + "{{ csrf_token() }}",
            saveUrl: "{{ url('finance/expenditure/requested/store') }}" + "?_token=" + "{{ csrf_token() }}",
            updateUrl: "{{ url('finance/expenditure/requested/store') }}" + "?_token=" + "{{ csrf_token() }}",
            destroyUrl: "{{ url('finance/expenditure/requested/destroy') }}" + "?_token=" + "{{ csrf_token() }}",
        })
        $("#tb-list-requested").edatagrid({
            onSave: function(index, row) {
                if (row.success) {
                    $.messager.alert('Informasi', row.message)
                } else {
                    $.messager.alert('Peringatan', row.message, 'error')
                }
                $(this).datagrid('reload')
            },
            onDestroy: function(index, row) {
                if (row.success) {
                    $.messager.alert('Informasi', row.message)
                    $("#id-expenditure-other").val("-1")
                    $("#AccountingExpenditureReceived").textbox("setValue","")
                } else {
                    $.messager.alert('Peringatan', row.message, 'error')
                }
                $(this).datagrid('reload')
            },
        })
    })
    function filterListRequested(params) {
        if (Object.keys(params).length > 0) {
            $("#tb-list-requested").datagrid("reload", { params, _token: "{{ csrf_token() }}" })
        } else {
            $("#tb-list-requested").datagrid("reload", { _token: "{{ csrf_token() }}" })
        }
    }
</script>
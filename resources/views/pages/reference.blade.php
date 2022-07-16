@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $GridHeight = $InnerHeight - 251 . "px";
    $ViewType = $ViewType;
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Referensi Sistem</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};">
    <div class="pt-3 pl-1 pr-1 pb-3" data-options="region:'center'">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <label class="mb-1" style="width:200px;">Kategori:</label>
                </div>
                <div class="col-12">
                    <select id="ReferenceCategory" class="easyui-combobox" style="width:200px;height:22px;" data-options="panelHeight:125">
                        @foreach ($references as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 mt-2">
                    <table id="tb-reference" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                        data-options="method:'post',rownumbers:'true',toolbar:menubarReference,singleSelect:true,pagination:'true',pageSize:10,pageList:[10,25,50,75,100]">
                        <thead>
                            <tr>
                                <th data-options="field:'id',width:50,hidden:true">ID</th>
                                <th data-options="field:'code',width:100,resizeable:true,align:'center',editor:'textbox'">Kode</th>
                                <th data-options="field:'name',width:200,resizeable:true,editor:{type:'validatebox',options:{required:true}}">Nama Referensi</th>
                                <th data-options="field:'remark',width:300,resizeable:true,editor:'textbox'">Keterangan</th>
                                <th data-options="field:'category',width:100,hidden:true,editor:'textbox'">Kategori</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var dg = $("#tb-reference")
    var menubarReference = [{
        text: 'Tambah',
        iconCls: 'ms-Icon ms-Icon--Add',
        handler: function() {
            dg.edatagrid('addRow')
        }
    },'-',{
        text: 'Simpan',
        iconCls: 'ms-Icon ms-Icon--Cancel',
        handler: function() {
            dg.edatagrid('saveRow')
        }
    },'-',{
        text: 'Batal',
        iconCls: 'ms-Icon ms-Icon--Cancel',
        handler: function() {
            dg.edatagrid('cancelRow')
        }
    },'-',{
        text: 'Hapus',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            var row = dg.datagrid("getSelected")
            if (row !== null) {
                $.post("{{ url('reference/destroy')}}" + "/" + row.id, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    if (response.success) {
                        Toast.fire({icon:"success",title:response.message})
                        dg.datagrid("reload")
                    } else {
                        $.messager.alert('Peringatan', response.message, 'error')
                    }
                }).fail(function(xhr) {
                    failResponse(xhr)
                })                  
            }
        }
    }]
    $(function () {
        dg.datagrid({
            url: "{{ url('reference/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
        })
        $("#ReferenceCategory").combobox({
            onClick: function(record) {
                dg.datagrid("reload", "{{ url('reference/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&fcategory=" + record.value)
            }            
        })
        dg.edatagrid({
            saveUrl: "{{ url('reference/store') }}" + "/-1" + "?_token=" + "{{ csrf_token() }}",
            updateUrl: "{{ url('reference/store') }}" + "/-1" + "?_token=" + "{{ csrf_token() }}",
            onAdd: function(index, row) {
                var ed = dg.edatagrid('getEditor', {index:index, field:'category'})
                ed.target.textbox("setValue", $("#ReferenceCategory").combobox("getValue"))
            },
            onSave: function(index, row) {
                if (typeof row.success !== "undefined") {
                    if (row.success) {
                        Toast.fire({icon:"success",title:response.message})
                    } else {
                        $.messager.alert('Peringatan', row.message, 'error')
                    }
                } else {
                    $.messager.alert('Informasi', "Data Referensi berhasil disimpan.")                    
                }
                $(this).datagrid('reload')
            },
        })
    })
</script>
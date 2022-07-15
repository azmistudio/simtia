@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 251 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Log Aplikasi</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div class="pt-3 pl-1 pr-1 pb-3" data-options="region:'center'">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                    <span class="mr-2"></span>
                    <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
                    <span class="mr-2"></span>
                    <label class="mb-1" style="width:250px;">Departemen:</label>
                    <span class="mr-2"></span>
                    <label class="mb-1" style="width:200px;">Pengguna:</label>
                </div>
                <div class="col-12">
                    <form id="AuditLogForm">
                    <input value="{{ date('Y-m-d', strtotime('-1 day')) }}" id="AuditLogFromDate" class="easyui-datebox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" />
                    <span class="mr-2"></span>
                    <input value="{{ date('Y-m-d') }}" id="AuditLogToDate" class="easyui-datebox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" />
                    <span class="mr-2"></span>
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:250px;height:22px;" data-options="readonly:true" />
                        <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select name="department_id" id="AuditLogDeptId" class="easyui-combobox" style="width:250px;height:22px;" data-options="panelHeight:125">
                            <option value="">---</option>    
                            @foreach ($depts as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    @endif
                    <span class="mr-2"></span>
                    <select id="AuditLogUser" class="easyui-combobox" style="width:200px;height:22px;" data-options="panelHeight:125,valueField:'user',textField:'name'">
                        <option value="">---</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->user }}">{{ ucwords($user->name) }}</option>
                        @endforeach
                    </select>
                    <span class="mr-2"></span>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--Search'" style="height:22px;width: auto;" onclick="filterAuditLog()">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--Clear'" style="height:22px;width: auto;" onclick="resetFilterAuditLog()">Batal</a>
                    </form>
                </div>
                <div class="col-12 mt-2">
                    <table id="tb-audit-log" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                        data-options="method:'post',url: '{{ url('audit/log/data') }}',queryParams: { _token: '{{ csrf_token() }}', fstart: $('#AuditLogFromDate').datebox('getValue'), fend: $('#AuditLogToDate').datebox('getValue') },pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                        <thead>
                            <tr>
                                <th data-options="field:'user',width:150">Email</th>
                                <th data-options="field:'created',width:150,resizeable:true,align:'center'">Waktu</th>
                                <th data-options="field:'ip',width:100,resizeable:true,align:'center'">Alamat IP</th>
                                <th data-options="field:'remark',width:300,resizeable:true">Aktivitas</th>
                                <th data-options="field:'before',width:300,resizeable:true">Sebelum</th>
                                <th data-options="field:'after',width:300,resizeable:true">Sesudah</th>
                                <th data-options="field:'browser',width:640,resizeable:true">Browser</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function filterAuditLog() {
        $('#tb-audit-log').datagrid('load', { 
            _token: '{{ csrf_token() }}', 
            @if (auth()->user()->getDepartment->is_all == 1)
            fdepartment: $("#AuditLogDeptId").combobox('getValue'),
            @endif
            fuser: $("#AuditLogUser").combobox('getValue'),
            fstart: $('#AuditLogFromDate').datebox('getValue'), 
            fend: $('#AuditLogToDate').datebox('getValue') 
        })
    }
    function resetFilterAuditLog() {
        $('#AuditLogForm').form('reset')
        $('#tb-audit-log').datagrid('load', { 
            _token: '{{ csrf_token() }}', 
            fstart: $('#AuditLogFromDate').datebox('getValue'), 
            fend: $('#AuditLogToDate').datebox('getValue') 
        })
    }
</script>
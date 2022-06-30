<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<input type="hidden" id="id-schoolyear-schedule-info" value="" />
			<div class="mb-1">
                <input name="deptid" id="scheduleInfoDeptId" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
            </div>
            <div class="mb-3">
                <input name="schoolyear_id" id="scheduleInfoSchoolYearId" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
            </div>
            <div>
		        <table id="tb-schedule-info" style="width:100%;height:215px" 
		            data-options="idField:'id',rownumbers:'true',toolbar:'#toolbar-schedule-info',singleSelect:'true'">
		            <thead>
		                <tr>
		                    <th data-options="field:'description',width:250,resizeable:true,sortable:true,editor:{type:'validatebox',options:{required:true}}">Info Jadwal</th>
		                    <th data-options="field:'schoolyear_id',width:100,hidden:true">Tahun Ajaran</th>
		                    <th data-options="field:'is_active',width:100,align:'center',resizeable:true,sortable:true,editor:{type:'checkbox',options:{on:'Ya',off:'Tidak'}}">Non Aktif</th>
		                </tr>
		            </thead>
		        </table>
		    </div>
		    <div id="toolbar-schedule-info">
		        <a class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="javascript:$('#tb-schedule-info').edatagrid('addRow')">Baru</a>
		        <a class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="javascript:$('#tb-schedule-info').edatagrid('saveRow')">Simpan</a>
		        <a class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="javascript:$('#tb-schedule-info').edatagrid('cancelRow')">Batal</a>
		        <a class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="javascript:$('#tb-schedule-info').edatagrid('destroyRow')">Hapus</a>
		    </div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function () {
		$('#tb-schedule-info').edatagrid({
            url: "{{ url('academic/lesson/schedule/info/data') }}" + "/" + $('#id-schoolyear-schedule-info').val() + "?_token=" + "{{ csrf_token() }}",
            saveUrl: "{{ url('academic/lesson/schedule/info/store') }}" + "/" + $('#id-schoolyear-schedule-info').val() + "?_token=" + "{{ csrf_token() }}",
            updateUrl: "{{ url('academic/lesson/schedule/info/store') }}" + "/" + $('#id-schoolyear-schedule-info').val() + "?_token=" + "{{ csrf_token() }}",
            destroyUrl: "{{ url('academic/lesson/schedule/info/destroy') }}" + "?_token=" + "{{ csrf_token() }}",
        })
        $('#tb-schedule-info').edatagrid({
            onSave: function(index, row) {
                if (row.success) {
                    $.messager.alert('Informasi', row.message)
                    $('#lessonTeachingScheduleId').combobox('reload','{{ url("academic/lesson/schedule/info/list") }}' + "/" + $("#id-schoolyear-schedule-info").val() + "?_token=" + "{{ csrf_token() }}")
                } else {
                    $.messager.alert('Peringatan', row.message, 'error')
                }
                $(this).datagrid('reload')
            },
            onDestroy: function(index, row) {
                if (row.success) {
                    $.messager.alert('Informasi', row.message)
                    $('#lessonTeachingScheduleId').combobox('reload','{{ url("academic/lesson/schedule/info/list") }}' + "/" + $("#id-schoolyear-schedule-info").val() + "?_token=" + "{{ csrf_token() }}")
                } else {
                    $.messager.alert('Peringatan', row.message, 'error')
                }
                $(this).datagrid('reload')
            },
        })
	})
</script>
@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GrapHeight = $InnerHeight > 671 ? $InnerHeight - 530 . "px" : $InnerHeight - 456 . "px";
    $GridHeight = $InnerHeight > 671 ? $InnerHeight - 496 . "px" : $InnerHeight - 520 . "px";
@endphp
<div class="container-fluid pt-2">
	<div class="row">
		<div class="col-12">
            <label class="mb-1" style="width:200px;">Departemen:</label>
            <span class="mr-2"></span>
            <label class="mb-1" style="width:150px;">Proses:</label>
            <span class="mr-2"></span>
            <label class="mb-1" style="width:200px;">Berdasarkan:</label>
        </div>
		<div class="col-8 mb-2">
			<form id="form-admission-stat">
				<div class="mb-1">
                    <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:200px;height:22px;" data-options="readonly:true" />
					<span class="mr-2"></span>
                    <select id="AcademicReportProcess" class="easyui-combogrid cgrd" style="width:150px;height:22px;" data-options="
                        panelWidth: 350,
                        idField: 'id',
                        textField: 'name',
                        url: '{{ url('academic/admission/combo-grid') }}',
                        method: 'get',
                        mode:'remote',
                        fitColumns:true,
                        columns: [[
                            {field:'department_id',title:'Departemen',width:150},
                            {field:'name',title:'Proses',width:200},
                        ]],
                    ">
                    </select>
                    <span class="mr-2"></span>
                    <select id="AcademicReportCategory" class="easyui-combobox cbox" style="width:200px;height:22px;" data-options="panelHeight:125,valueField:'id',textField:'name'">
                        <option value="blood_type">Golongan Darah</option>
                        <option value="gender">Jenis Kelamin</option>
                        <option value="tribe">Suku</option>
                        <option value="born">Tahun Lahir</option>
                        <option value="age">Usia</option>
                    </select>
                    <span class="mr-2"></span>
                    <a id="fbtn-admission-stat" href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAdmissionStat()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                </div>
			</form>
		</div>
		<div class="col-4 text-right">
            <a id="pdf-admission-stat" class="easyui-linkbutton lbtn" data-options="iconCls:'ms-Icon ms-Icon--Print'" style="height:22px;" onclick="exportAdmissionStat('pdf')">Pratinjau Cetak</a>
        </div>
		<div class="col-8 mb-3">
			<div class="" id="bar-admission-stat" style="height: {{ $GrapHeight }};width: 100%;border: solid 1px #d5d5d5;"></div>
		</div>
		<div class="col-4">
			<div class="" id="pie-admission-stat" style="height: {{ $GrapHeight }};width: 100%;border: solid 1px #d5d5d5;"></div>
		</div>
		<div class="col-5">
			<table id="tb-admission-stat" style="width:100%;height:{{ $GridHeight }}"></table>
		</div>
		<div class="col-7">
			<table id="tb-admission-stat-detail" style="width:100%;height:{{ $GridHeight }}"></table>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function () {
    	$('#tb-admission-stat').datagrid({
    		onClickRow: function(index, row) {
    			detailDataGrid($('#AcademicReportProcess').combogrid('getValue'), $('#AcademicReportCategory').combobox('getValue'), row.id)
    		}
    	})
    	$("#AcademicReportProcess").combogrid({
    		onClickRow: function(index, row) {
    			$("#AcademicReportDept").textbox("setValue", row.department_id)
    		}
    	})
    })
    function filterAdmissionStat() {
    	getDataView($('#AcademicReportProcess').combogrid('getValue'), $('#AcademicReportCategory').combobox('getValue'))
    	$('#tb-admission-stat-detail').datagrid({data: [{}]})
    }
    function getDataView(param1, param2) {
    	var grapTitle = "Asal Sekolah"
    	switch(param2) {
			case "blood_type":
				grapTitle = "Golongan Darah"
				break;
			case "gender":
				grapTitle = "Jenis Kelamin"
				break;
			case "tribe":
				grapTitle = "Suku"
				break;
			case "born":
				grapTitle = "Tahun Lahir"
				break;
			case "age":
				grapTitle = "Usia"
				break;
			default:
				
		} 
		var dg = $('#tb-admission-stat').datagrid({
			singleSelect: true,
			columns:[[
    			{field:'id',width:150,hidden:true,title:'ID'},
    			{field:'subject',width:150,resizeable:true,sortable:true,title:grapTitle},
    			{field:'total',width:100,resizeable:true,sortable:true,title:'Jumlah',align:'center'},
    			{field:'percent',width:100,resizeable:true,sortable:true,title:'Persentase',align:'right'}
    		]],
    	})  			
		$.post("{{ url('academic/report/admission/stat/data') }}", {admission_id: param1, category: param2, _token: '{{ csrf_token() }}'}, function(response) {
	        var cats = []
	        var data = []
	        var total = 0
	        if (response.success) {
	            for (var i = 0; i < response.data.length; i++)
		        {
		        	total += response.data[i].y
		        }
		        for (var j = 0; j < response.data.length; j++)
		        {
		            cats.push(response.data[j].label)
		            data.push({id: response.data[j].id,subject: response.data[j].label, total: response.data[j].y, percent: ((response.data[j].y / total) * 100).toFixed(2)})
		        }
	            graphBarAdmissionStat(grapTitle, response.data, cats)
	            graphPieAdmissionStat(grapTitle, response.data, cats)
	            //
	            dg.datagrid({
	            	data: data
	            })
	        } else {
	            $.messager.alert('Peringatan', response.message, 'error')
	        }
	    })
    }
    function graphBarAdmissionStat(title, result, category) {
    	$('#bar-admission-stat').highcharts({
	        chart: { type: 'column' },
	        title: {
	            text: '<b>Berdasarkan '+title+'</b>',
	            style: { fontSize: '14px' }
	        },
	        xAxis: { categories: category },
	        yAxis: {
	            min: 0,
	            title: {
	                text: 'Jumlah',
	                align: 'high'
	            },
		        labels: { overflow: 'justify' },
		        tickWidth: 1,
	        },
	        plotOptions: {
	            column: {
	                dataLabels: { enabled: true },
	                showInLegend: true
	            },
	            series: { cursor: 'pointer' }
	        },
	        exporting: {
	        	enabled: false
	        },
	        series: [{
	            name: "Jumlah",
	            data: result
	        }]
	    })
    }
    function graphPieAdmissionStat(title, result, category) {
    	$('#pie-admission-stat').highcharts({
	        chart: { type: 'pie' },
	        title: {
	            text: '<b>% '+title+'</b>',
	            style: { fontSize: '14px' }
	        },
	        tooltip: { pointFormat: '{point.label}: <b>{point.y}</b>' },
	        plotOptions: {
	            pie: {
	                allowPointSelect: true,
	                cursor: 'pointer',
	                dataLabels: {
	                    enabled: true,
	                    format: '{point.percentage:.1f} %<br/>({point.label})',
	                },
	            },
	            series: { cursor: 'pointer' }
	        },
	        exporting: {
	        	enabled: false
	        },
	        series: [{
	            name: "Jumlah",
	            colorByPoint: true,
	            data: result
	        }]
	    })		
    }
    function detailDataGrid(param1, param2, param3) {
    	var dg = $('#tb-admission-stat-detail').datagrid({
    				columns:[[
		    			{field:'registration_no',width:150,resizeable:true,sortable:true,title:'No. Pendaftaran',align:'center'},
		    			{field:'name',width:200,resizeable:true,sortable:true,title:'Nama'},
		    			{field:'groups',width:150,resizeable:true,sortable:true,title:'Kelompok',align:'center'},
		    		]],
		    	})
    	$.post("{{ url('academic/report/admission/stat/data/detail') }}", {admission_id: param1, category: param2, id: param3, _token: '{{ csrf_token() }}'}, function(response) {
	        var data = []
	        if (response.success) {
		        for (var j = 0; j < response.data.length; j++)
		        {
		            data.push({registration_no: response.data[j].registration_no, nisn: response.data[j].nisn, name: response.data[j].name, groups: response.data[j].groups, })
		        }
	            //
	            dg.datagrid({
	            	data: data
	            })
	        } else {
	            $.messager.alert('Peringatan', response.message, 'error')
	        }
	    })
    }
    function exportAdmissionStat(doctype) {
    	if ($('#AcademicReportProcess').combogrid('getValue') != '') {
            reportDocument("{{ url('academic/report/admission/stat/print') }}", { 
            	admission_id: $('#AcademicReportProcess').combogrid('getValue'), 
            	admission: $('#AcademicReportProcess').combogrid('getText'), 
            	department: $('#AcademicReportDept').textbox('getValue'), 
            	category: $('#AcademicReportCategory').combobox('getValue'), 
            }, "Ekspor data ke " + doctype.toUpperCase(), "{{ csrf_token() }}")
        }
    }
</script>
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
            <label class="mb-1" style="width:150px;">Angkatan:</label>
            <span class="mr-2"></span>
            <label class="mb-1" style="width:200px;">Berdasarkan:</label>
        </div>
		<div class="col-8 mb-2">
			<form id="form-student-stat">
				<div class="mb-1">
					<input class="easyui-textbox tbox" id="AcademicReportDept" style="width:200px;height:22px;" data-options="readonly:true" />
					<span class="mr-2"></span>
                    <select id="AcademicReportSchoolYear" class="easyui-combogrid cgrd" style="width:150px;height:22px;" data-options="
                        panelWidth: 450,
                        idField: 'id',
                        textField: 'school_year',
                        url: '{{ url('academic/school-year/combo-grid') }}',
                        queryParams: { _token: '{{ csrf_token() }}' },
                        mode:'remote',
                        fitColumns:true,
                        pagination:true,
                        columns: [[
                            {field:'department',title:'Departemen',width:150},
                            {field:'school_year',title:'Angkatan',width:100,sortable:true},
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
                    <a id="fbtn-student-stat" href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterStudentStat()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                </div>
			</form>
		</div>
		<div class="col-4 text-right">
            <a id="pdf-student-stat" class="easyui-linkbutton lbtn" data-options="iconCls:'ms-Icon ms-Icon--Print'" style="height:22px;" onclick="exportStudentStat('pdf')">Pratinjau Cetak</a>
        </div>
		<div class="col-8 mb-3">
			<div class="" id="bar-student-stat" style="height: {{ $GrapHeight }};width: 100%;border: solid 1px #d5d5d5;"></div>
		</div>
		<div class="col-4">
			<div class="" id="pie-student-stat" style="height: {{ $GrapHeight }};width: 100%;border: solid 1px #d5d5d5;"></div>
		</div>
		<div class="col-5">
			<table id="tb-student-stat" style="width:100%;height:{{ $GridHeight }}"></table>
		</div>
		<div class="col-7">
			<table id="tb-student-stat-detail" style="width:100%;height:{{ $GridHeight }}"></table>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function () {
    	$('#tb-student-stat').datagrid({
    		onClickRow: function(index, row) {
    			detailDataGrid($('#AcademicReportSchoolYear').combogrid('getText'), $('#AcademicReportCategory').combobox('getValue'), row.id, $('#AcademicReportSchoolYear').combogrid('grid').datagrid('getSelected').department_id)
    		}
    	})
    	$("#AcademicReportSchoolYear").combogrid({
    		onClickRow: function(index, row) {
    			$("#AcademicReportDept").textbox("setValue", row.department)
    		}
    	})
    })
    function filterStudentStat() {
    	getDataView($('#AcademicReportSchoolYear').combogrid('getText'), $('#AcademicReportCategory').combobox('getValue'), $('#AcademicReportSchoolYear').combogrid('grid').datagrid('getSelected').department_id)
    	$('#tb-student-stat-detail').datagrid({data: [{}]})
    }
    function getDataView(param1, param2, param3) {
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
		var dg = $('#tb-student-stat').datagrid({
			singleSelect: true,
			columns:[[
    			{field:'id',width:150,hidden:true,title:'ID'},
    			{field:'subject',width:150,resizeable:true,sortable:true,title:grapTitle},
    			{field:'total',width:100,resizeable:true,sortable:true,title:'Jumlah',align:'center'},
    			{field:'percent',width:100,resizeable:true,sortable:true,title:'Persentase',align:'right'}
    		]],
    	})  			
		$.post("{{ url('academic/report/student/stat/data') }}", {generation_id: param1, category: param2, department_id: param3, _token: '{{ csrf_token() }}'}, function(response) {
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
		            cats.push(response.data[j].subject)
		            data.push({id: response.data[j].id,subject: response.data[j].subject, total: response.data[j].y, percent: ((response.data[j].y / total) * 100).toFixed(2)})
		        }
	            graphBarStudentStat(grapTitle, response.data, cats)
	            graphPieStudentStat(grapTitle, response.data, cats)
	            //
	            dg.datagrid({
	            	data: data
	            })
	        } else {
	            $.messager.alert('Peringatan', response.message, 'error')
	        }
	    })
    }
    function graphBarStudentStat(title, result, category) {
    	$('#bar-student-stat').highcharts({
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
    function graphPieStudentStat(title, result, category) {
    	$('#pie-student-stat').highcharts({
	        chart: { type: 'pie' },
	        title: {
	            text: '<b>% '+title+'</b>',
	            style: { fontSize: '14px' }
	        },
	        tooltip: { pointFormat: '{series.name}: <b>{point.y}</b>' },
	        plotOptions: {
	            pie: {
	                allowPointSelect: true,
	                cursor: 'pointer',
	                dataLabels: {
	                    enabled: true,
	                    format: '{point.percentage:.1f} %<br/>({point.subject})',
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
    function detailDataGrid(param1, param2, param3, param4) {
    	var dg = $('#tb-student-stat-detail').datagrid({
    				columns:[[
		    			{field:'student_no',width:150,resizeable:true,sortable:true,title:'No. Santri',align:'center'},
		    			{field:'name',width:200,resizeable:true,sortable:true,title:'Nama'},
		    			{field:'generation',width:150,resizeable:true,sortable:true,title:'Angkatan',align:'center'},
		    		]],
		    	})
    	$.post("{{ url('academic/report/student/stat/data/detail') }}", {generation_id: param1, category: param2, id: param3, department_id: param4, _token: '{{ csrf_token() }}'}, function(response) {
	        var data = []
	        if (response.success) {
		        for (var j = 0; j < response.data.length; j++)
		        {
		            data.push({student_no: response.data[j].student_no, name: response.data[j].name, generation: response.data[j].year_entry, })
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
    function exportStudentStat(doctype) {
    	if ($('#AcademicReportSchoolYear').combogrid('getValue') != '') {
            reportDocument("{{ url('academic/report/student/stat/print') }}", { 
            	generation_id: $('#AcademicReportSchoolYear').combogrid('getText'), 
            	department: $('#AcademicReportDept').textbox('getValue'), 
            	department_id: $('#AcademicReportSchoolYear').combogrid('grid').datagrid('getSelected').department_id,
            	category: $('#AcademicReportCategory').combobox('getValue') 
            }, "Ekspor data ke " + doctype.toUpperCase(), "{{ csrf_token() }}")
        }
    }
</script>
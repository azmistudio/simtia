@inject('savingEloquent', 'Modules\Finance\Repositories\Saving\SavingEloquent')
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 pl-2">
            <h6>Data Tabungan: <br/><b>{{ $requests['employee_no'] }} - {{ $requests['employee'] }}</b></h6>
        </div>
        <div class="col-4 p-2 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportReportSavingEmployee('pdf')">Ekspor PDF</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportReportSavingEmployee('excel')">Ekspor Excel</a>
        </div>
    </div>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 p-2">
			<input type="hidden" id="report-saving-employee-data" value="{{ count($savings) }}" />
			<table border="1" style="width:100%;border-collapse:collapse">
				<tbody>
					{{-- savings --}}
					@foreach ($savings as $saving)
					@php 
						$savingDetail = $savingEloquent->dataSavingDetailInfo(1, $requests['employee_id'], $requests['bookyear_id'], $requests['start_date'], $requests['end_date'], $saving->saving_id);
					@endphp
					<tr height="35">
						<td colspan="5" bgcolor="#CCCFFF">&nbsp;<b>{{ $saving->saving_type }}</b></td>
					</tr>
					<tr height="25">
						<td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Jumlah Setoran</strong></td>
				        <td width="15%" bgcolor="#FFFFFF" align="right"><b>{{ $savingDetail['deposit'] }}</b>&nbsp;</td>
				        <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Total Setoran</strong></td>
				        <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Total Tarikan</strong></td>
				        <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Saldo Tabungan</strong></td>
					</tr>
					<tr height="25">
						<td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Setoran Terakhir</strong></td>
				        <td width="15%" bgcolor="#FFFFFF" align="right">{!! $savingDetail['last_deposit'] !!}&nbsp;</td>
				        <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h5>{{ $savingDetail['total_deposit'] }}</h5></b>&nbsp;</td>
				        <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h5>{{ $savingDetail['total_withdraw'] }}</h5></b>&nbsp;</td>
				        <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h5>{{ $savingDetail['total_balance'] }}</h5></b>&nbsp;</td>
					</tr>
					<tr height="25">
						<td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Jumlah Tarikan</strong></td>
				        <td width="15%" bgcolor="#FFFFFF" align="right"><b>{{ $savingDetail['withdraw'] }}</b>&nbsp;</td>
					</tr>
					<tr height="25">
						<td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Tarikan Terakhir</strong></td>
				        <td width="15%" bgcolor="#FFFFFF" align="right">{!! $savingDetail['last_withdraw'] !!}&nbsp;</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
    function exportReportSavingEmployee(document) {
    	if ($("#report-saving-employee-data").val() > 0) {
	       	var payload = {
	       		bookyear_id: {{ $requests['bookyear_id'] }},
	       		employee_id: {{ $requests['employee_id'] }},
	       		employee: "{{ $requests['employee'] }}",
	       		employee_no: "{{ $requests['employee_no'] }}",
	            start_date: "{{ $requests['start_date'] }}",
	            end_date: "{{ $requests['end_date'] }}",
	        }
	        exportDocument("{{ url('finance/report/saving/employee/export-') }}" + document,payload,"Ekspor Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>
@inject('savingEloquent', 'Modules\Finance\Repositories\Saving\SavingEloquent')
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 pl-2">
            <h6>Data Tabungan: <br/><b>{{ $requests['student_no'] }} - {{ $requests['student'] }}</b></h6>
        </div>
        <div class="col-4 p-2 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportReportSavingStudent('pdf')">Ekspor PDF</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportReportSavingStudent('excel')">Ekspor Excel</a>
        </div>
    </div>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 p-2">
			<table border="1" style="width:100%;border-collapse:collapse">
				<tbody>
					{{-- savings --}}
					@foreach ($savings as $saving)
					@php 
						$savingDetail = $savingEloquent->dataSavingDetailInfo(0, $requests['student_id'], $requests['bookyear_id'], $requests['start_date'], $requests['end_date'], $saving->saving_id);
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
    function exportReportSavingStudent(document) {
       var payload = {
       		student_id: {{ $requests['student_id'] }},
       		student: "{{ $requests['student'] }}",
       		student_no: "{{ $requests['student_no'] }}",
            department: "{{ $requests['department'] }}",
            grade: "{{ $requests['grade'] }}",
            bookyear_id: {{ $requests['bookyear_id'] }}, 
            schoolyear: "{{ $requests['schoolyear'] }}",
            start_date: "{{ $requests['start_date'] }}",
            end_date: "{{ $requests['end_date'] }}",
            class: "{{ $requests['class'] }}",
        }
        exportDocument("{{ url('finance/report/saving/student/export-') }}" + document,payload,"Ekspor data Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
    }
</script>
@php
	$total_deposit = 0;
	$total_withdraw = 0;
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 pl-2"><h6>Rekapitulasi Tabungan Pegawai</div>
        <div class="col-4 p-2 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportReportSavingEmployeeRecap('pdf')">Ekspor PDF</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportReportSavingEmployeeRecap('excel')">Ekspor Excel</a>
        </div>
    </div>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-12 p-2">
			<table border="1" style="width:100%;border-collapse:collapse">
				<thead>
					<tr height="35" bgcolor="#CCCFFF">
						<th class="text-center" width="5%">No.</th>
						<th class="text-center">Tabungan</th>
						<th class="text-center" width="20%">Jumlah Setoran</th>
						<th class="text-center" width="20%">Jumlah Tarikan</th>
						<th class="text-center" width="20%">Jumlah Saldo</th>
					</tr>
				</thead>
				<tbody>
					@php $x = 1; @endphp
					@foreach ($savings as $saving)
					@php
						$total_deposit += $saving->total_credit;
						$total_withdraw += $saving->total_debit;
					@endphp
					<tr height="25">
						<td class="text-center">{{ $x++ }}</td>
						<td>&nbsp;{{ $saving->saving_type }}</td>
						<td class="text-right"><a href="#" onclick="detailSavingEmployee({{ $saving->saving_id }},'{{ $saving->saving_type }}','credit')">Rp{{ number_format($saving->total_credit,2) }}</a>&nbsp;</td>
						<td class="text-right"><a href="#" onclick="detailSavingEmployee({{ $saving->saving_id }},'{{ $saving->saving_type }}','debit')">Rp{{ number_format($saving->total_debit,2) }}</a>&nbsp;</td>
						<td class="text-right">Rp{{ number_format($saving->total_credit - $saving->total_debit,2) }}&nbsp;</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr height="30" bgcolor="#CCFFFF">
						<th colspan="2" class="text-center"><b>TOTAL</b></th>
						<th class="text-right"><b>Rp{{ number_format($total_deposit,2) }}&nbsp;</b></th>
						<th class="text-right"><b>Rp{{ number_format($total_withdraw,2) }}&nbsp;</b></th>
						<th class="text-right"><b>Rp{{ number_format($total_deposit - $total_withdraw,2) }}&nbsp;</b></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	function detailSavingEmployee(saving_id, saving_type, type) {
		$("#report-saving-employee-recap-w").window("open").window('refresh', "{{ url('finance/report/saving/employee/recap/detail') }}" 
            + "?saving_id=" + saving_id
            + "&saving_type=" + saving_type
            + "&type=" + type
            + "&employee=" + "{{ $requests['employee'] }}"
            + "&employee_id=" + {{ $requests['employee_id'] }}
            + "&start_date=" + "{{ $requests['start_date'] }}"
            + "&end_date=" + "{{ $requests['end_date'] }}"
        )
	}
    function exportReportSavingEmployeeRecap(document) {
       var payload = {
            start_date: "{{ $requests['start_date'] }}",
            end_date: "{{ $requests['end_date'] }}",
            employee: "{{ $requests['employee'] }}",
            employee_id: {{ $requests['employee_id'] }},
        }
        exportDocument("{{ url('finance/report/saving/employee/recap/export-') }}" + document,payload,"Ekspor Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
    }
</script>
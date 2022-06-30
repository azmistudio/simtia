@inject('reference', 'Modules\Finance\Http\Controllers\FinanceController')
<div class="container-fluid mt-1 mb-1">
	<div class="row">
		<div class="col-8">
			<h6><b>Perubahan Data Iuran Sukarela Santri</b></h6>
			<span>Departemen: {{ $requests['department'] }}</span> - <span>Tahun Buku: {{ $requests['bookyear'] }}</span> - <span>Periode {{ $requests['start_date'] }} s.d {{ $requests['end_date'] }}</span>
		</div>
		<div class="col-4 text-right">
			<a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="exportAccountingReportAudit('pdf')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--PDF'">Ekspor PDF</a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="exportAccountingReportAudit('excel')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'">Ekspor Excel</a>
		</div>
		<div class="col-12">
			<br/>
			<table class="table table-bordered table-sm">
				<thead>
					<tr>
						<th class="text-center">No.</th>
						<th class="text-center">Status Data</th>
						<th class="text-center">Tanggal</th>
						<th class="text-center">Jumlah</th>
						<th class="text-center">Keterangan</th>
						<th class="text-center">Petugas</th>
					</tr>
				</thead>
				<tbody>
					@php $no = 1; @endphp
					@foreach ($audits as $audit)
					<tr>
						<td rowspan="4" class="text-center">{{ $no }}</td>
						<td colspan="6">Perubahan dilakukan oleh {{ $audit->employee }} tanggal {{ $reference->formatDate($audit->audit_date,'isotime') }}</td>
					</tr>
					<tr>
						<td colspan="6">
							<b>No. Jurnal</b>: {{ $audit->cash_no }} &nbsp;&nbsp; <b>Alasan</b>: {{ $audit->remark }}<br/>
							<b>Transaksi</b>: {{ $audit->transaction }}
						</td>
					</tr>
					@foreach ($detail_audits as $detail)
					@if ($audit->id == $detail->audit_id)
					<tr>
						<td>{{ $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan' }}</td>
						<td class="text-center">{{ $reference->formatDate($audit->audit_date,'timeiso') }}</td>
						<td class="text-right">Rp{{ number_format($detail->total,2) }}</td>
						<td>{{ $detail->remark }}</td>
						<td class="text-center">{{ $detail->employee }}</td>
					</tr>
					@endif
					@endforeach
					@php $no++; @endphp
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
    function exportAccountingReportAudit(document) {
    	var payload = {
    		department_id: "{{ $requests['department_id'] }}",
            department: "{{ $requests['department'] }}",
            bookyear_id: "{{ $requests['bookyear_id'] }}",
            bookyear: "{{ $requests['bookyear'] }}",
            start_date: "{{ $requests['start_date'] }}", 
            end_date: "{{ $requests['end_date'] }}",
            source: "receipt_skr"
        }
        exportDocument("{{ url('finance/report/audit/export-') }}" + document,payload,"Ekspor data Audit ke "+ document.toUpperCase(),"{{ csrf_token() }}")
    }
</script>
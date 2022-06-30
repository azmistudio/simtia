<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head>
        <title>JURNAL UMUM</title>
        <style type="text/css">
            body { margin: 0; padding: 0; font-family: "Calibri", "Open Sans", serif !important; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            table.no-border, table.no-border th, table.no-border td { border: none; }
            table { border-collapse: collapse; border: 1px solid #000; }
            th, td { border-top: 1px solid #000; }
            .title { font-size: 13pt; font-weight: bold; }
            .subtitle { font-size: 11pt; font-weight: bold; }
        </style>
    </head>
    <body>
    	<table class="no-border">
    		<tbody>
    			<tr>
    				<td class="title" colspan="7" nowrap="nowrap">{{ strtoupper(Session::get('institute')) }}</td>
    			</tr>
    			<tr>
    				<td class="title" colspan="7" nowrap="nowrap">TRANSAKSI JURNAL UMUM</td>
    			</tr>
    		</tbody>
    	</table>
    	<br/>
    	@foreach ($departments as $department)
    	<table class="no-border">
    		<tbody>
    			<tr>
    				<td class="subtitle" colspan="7" nowrap="nowrap">DEPARTEMEN : {{ $department->department }}</td>
    			</tr>
    			<tr>
    				<td class="subtitle" colspan="7" nowrap="nowrap">TAHUN BUKU : {{ $department->book_year }}</td>
    			</tr>
    			<tr>
    				<td class="subtitle" colspan="7" nowrap="nowrap">TANGGAL : {{ $requests->start }} s.d {{ $requests->end }}</td>
    			</tr>
    		</tbody>
    	</table>
    	<br/>
		<table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
			<thead>
				<tr style="background-color:#CCFFFF;">
					<th class="subtitle" align="center" nowrap="nowrap">No.</th>
		            <th class="subtitle" align="center">No. Jurnal/Tanggal</th>
		            <th class="subtitle" align="center">Transaksi</th>
		            <th class="subtitle" align="center">Detil Transaksi</th>
				</tr>
			</thead>
			<tbody>
				@php $x = 1; @endphp
				@foreach ($journals as $journal)
		          @if ($journal->deptid == $department->deptid)
		            <tr>
		              <td class="text-center" rowspan="2" nowrap="nowrap" style="width:10px;">{{ $x }}</td>
		              <td class="text-center" width="20%"><b>{{ $journal->cash_no }}</b> / {{ $journal->date_journal }}</td>
		              <td width="30%">{{ $journal->transaction }}</td>
		              <td rowspan="2">
		                <table width="100%">
		                  <tbody>
		                    @foreach ($journal_details as $detail)
		                      @if ($detail->journal_id == $journal->id)
		                      <tr>
		                        <td class="text-center" width="15%">{{ $detail->code }}</td>
		                        <td>{{ $detail->account_name }}</td>
		                        <td class="text-right" width="20%">Rp{{ number_format($detail->debit,2) }}</td>
		                        <td class="text-right" width="20%">Rp{{ number_format($detail->credit,2) }}</td>
		                      </tr>
		                      @endif
		                    @endforeach
		                  </tbody>
		                </table>
		              </td>
		            </tr>
		            <tr>              
		              <td><b>Petugas</b>: {{ $journal->employee }}</td>
		              <td><b>Sumber</b>: {{ $journal->source_name }}</td>
		            </tr>
		          @endif
		          @php $x++; @endphp
		        @endforeach
			</tbody>
		</table>
		@endforeach
    </body>
</html>
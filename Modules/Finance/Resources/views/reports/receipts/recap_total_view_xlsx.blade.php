@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@inject('receiptOtherEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent')
@php
  $arr_sub = [];
  $grand_total = 0;
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="4" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="4" align="center" class="title"><b>LAPORAN PENERIMAAN {{ strtoupper($payloads->type) }}</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table class="no-border">
      <tbody>
      	<tr>
          <td colspan="2">Departemen</td>
          <td>: <b>{{ $payloads->department }}</b></td>
        </tr>
      	<tr>
          <td colspan="2">Periode</td>
          <td>: <b>{{ $payloads->start_date }} s.d {{ $payloads->end_date }}</b></td>
        </tr>
        <tr>
          <td colspan="2">Jenis</td>
          <td>: <b>{{ $payloads->receipt_category }}</b></td>
        </tr>
        <tr>
          <td colspan="2">Petugas</td>
          <td>: <b>{{ $payloads->employee }}</b></td>
        </tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
      <tbody>
        {{-- department --}}
		@foreach ($departments as $department)

		<tr height="35">
		  <td colspan="4" bgcolor="#CCCFFF">&nbsp;<b>Departemen: {{ $department->name }}</b></td>
		</tr>

		@php
			if ($payloads->receipt_category_id == 'JTT' || $payloads->receipt_category_id == 'CSWJB')
			{
				$dataRecapTotal = $receiptMajorEloquent->dataRecapTotal($payloads->bookyear_id, $department->id, $payloads->receipt_category_id, $payloads->start_date, $payloads->end_date, $payloads->employee_id);
			} elseif ($payloads->receipt_category_id == 'SKR' || $payloads->receipt_category_id == 'CSSKR') {
        $dataRecapTotal = $receiptVoluntaryEloquent->dataRecapTotal($payloads->bookyear_id, $department->id, $payloads->receipt_category_id, $payloads->start_date, $payloads->end_date, $payloads->employee_id);
			} else {
        $dataRecapTotal = $receiptOtherEloquent->dataRecapTotal($payloads->bookyear_id, $department->id, $payloads->receipt_category_id, $payloads->start_date, $payloads->end_date, $payloads->employee_id);
			}
		  $x = 1;
		@endphp

		<tr height="25">
		  <td class="text-center" width="5%"><b>No.</b></td>
		  <td colspan="2" class="text-left">&nbsp;<b>Penerimaan</b></td>
		  <td class="text-center" width="20%"><b>Total</b></td>
		</tr>

		@foreach ($dataRecapTotal['data'] as $data)
		@php 
		  $grand_total += $data->total_grand; 
		  if ($department->id == $data->department_id)
		  {
	      $arr_sub[] = array($department->id, $dataRecapTotal['subtotal']);
		  }
		@endphp

		<tr height="25">
		  <td class="text-center">{{ $x++ }}</td>
		  <td colspan="2">&nbsp;{{ $data->receipt_type }}</td>
		  <td class="text-right">Rp{{ number_format($data->total_grand,2) }}&nbsp;</td>
		</tr>

		@endforeach

		<tr height="25">
		  <td colspan="3" class="text-right"><b>Subtotal</b>&nbsp;</td>
		  <td class="text-right">
		      @for ($i = 0; $i < count($arr_sub); $i++)
		          @if ($department->id == $arr_sub[$i][0])
		          <b>Rp{{ number_format($arr_sub[$i][1],2) }}</b>&nbsp;
		          @endif
		      @endfor
		  </td>
		</tr>

		@endforeach

		<tr height="25" bgcolor="#CCFFFF">
		  <td colspan="3" class="text-center"><b>TOTAL PENERIMAAN</b></td>
		  <td class="text-right"><b>Rp{{ number_format($grand_total,2) }}</b>&nbsp;</td>
		</tr>
      </tbody>
    </table>
  </body>
</html>
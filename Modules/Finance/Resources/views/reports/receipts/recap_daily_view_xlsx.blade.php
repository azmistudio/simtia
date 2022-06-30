@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptTypeEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptTypeEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@inject('receiptOtherEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent')
@php
  $subtotal = 0;
  $total_type = 0;
  $count_name = 0;
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
          $x = 1;
          if ($payloads->receipt_category_id == 'JTT' || $payloads->receipt_category_id == 'CSWJB')
          {
            $dataRecapDaily = $receiptMajorEloquent->dataRecapDaily($payloads->bookyear_id, $department->id, $payloads->receipt_category_id, $payloads->start_date, $payloads->end_date, $payloads->employee_id);
          } elseif ($payloads->receipt_category_id == 'SKR' || $payloads->receipt_category_id == 'CSSKR') {
            $dataRecapDaily = $receiptVoluntaryEloquent->dataRecapDaily($payloads->bookyear_id, $department->id, $payloads->receipt_category_id, $payloads->start_date, $payloads->end_date, $payloads->employee_id);
          } else {
            $dataRecapDaily = $receiptOtherEloquent->dataRecapDaily($payloads->bookyear_id, $department->id, $payloads->receipt_category_id, $payloads->start_date, $payloads->end_date, $payloads->employee_id);
          }
          $receiptTypeNames = $receiptTypeEloquent->search($payloads->receipt_category_id, $department->id);
          $count_name = count($receiptTypeNames);
        @endphp

        <tr height="25">
          <td class="text-center" width="5%"><b>No.</b></td>
          <td class="text-center" width="10%"><b>Tanggal</b></td>
          @foreach ($receiptTypeNames as $type)
          <td class="text-center"><b>{{ $type->name }}</b></td>
          @endforeach
          <td class="text-center" width="15%"><b>Sub Total</b></td>
        </tr>

        @foreach ($dataRecapDaily as $data)

        <tr height="25">
          <td class="text-center">{{ $x++ }}</td>
          <td class="text-center">&nbsp;{{ $data->trans_date }}</td>
          @foreach ($receiptTypeNames as $type)
          @php
            if ($payloads->receipt_category_id == 'JTT' || $payloads->receipt_category_id == 'CSWJB')
            {
              $recapTransaction = $receiptMajorEloquent->dataRecapDailyTrans($payloads->bookyear_id, $department->id, $type->id, $data->trans_date, $payloads->employee_id);
            } elseif ($payloads->receipt_category_id == 'SKR' || $payloads->receipt_category_id == 'CSSKR') {
              $recapTransaction = $receiptVoluntaryEloquent->dataRecapDailyTrans($payloads->bookyear_id, $department->id, $type->id, $data->trans_date, $payloads->employee_id);
            } else {
              $recapTransaction = $receiptOtherEloquent->dataRecapDailyTrans($payloads->bookyear_id, $department->id, $type->id, $data->trans_date, $payloads->employee_id);
            }
            $total_type += $recapTransaction['transaction']->total;
            $subtotal += $recapTransaction['subtotal']->total;
          @endphp
          <td class="text-right">Rp{{ number_format($recapTransaction['transaction']->total,2) }}&nbsp;</td>
          <td class="text-right">Rp{{ number_format($recapTransaction['subtotal']->total,2) }}&nbsp;</td>
          @endforeach
        </tr>

        @endforeach

        @endforeach
        <tr height="25" bgcolor="#CCFFFF">
          <td colspan="2" class="text-center"><b>TOTAL PENERIMAAN</b></td>
          @for ($i = 0; $i < $count_name; $i++)
          <td class="text-right">Rp{{ number_format($total_type,2) }}</td>
          @endfor
          <td class="text-right">Rp{{ number_format($subtotal,2) }}</td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
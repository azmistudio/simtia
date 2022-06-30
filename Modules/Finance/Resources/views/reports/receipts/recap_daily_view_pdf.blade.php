@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptTypeEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptTypeEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@inject('receiptOtherEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $subtotal = 0;
  $total_type = 0;
  $count_name = 0;
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN PENERIMAAN {{ strtoupper($requests->type) }}</title>
    <link href="file:///{{ public_path('css/report.css') }}" rel="stylesheet" />
    <style type="text/css">
      table { border-collapse: collapse; border: 1px solid #000; font-size:13px; page-break-inside: auto; }
      table.row > tbody > tr:nth-child(even) { background: #f5f5f5; }
      table.row > tbody > tr:nth-child(odd) { background: #fff; }
    </style>
  </head>
  <body>
    <div id="header">
        <table class="table no-border" style="width:100%;">
            <tbody>
                <tr>
                    <th rowspan="2" width="100px"><img src="file:///{{ $logo }}" height="80px" /></th>
                    <td><b>{{ strtoupper($profile['name']) }}</b></td>
                </tr>
                <tr>
                    <td style="font-size:11px;">
                        {{ $profile['address'] }}<br/>
                        Telpon: {{ $profile['phone'] }} - Faksimili: {{ $profile['fax'] }}<br/>
                        Website: {{ $profile['web'] }} - Email: {{ $profile['email'] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr/>
    <div id="body">
      <br/>
      <div class="text-center" style="font-size:16px;"><b>LAPORAN PENERIMAAN {{ strtoupper($requests->type) }}</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;">Departemen</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->department }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Jenis</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->receipt_category }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Periode</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->start_date }} s.d {{ $requests->end_date }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Petugas</td>
              <td style="width:1%;text-align:center;">:</td>
              <td><b>{{ $requests->employee }}</b></td>
            </tr>
          </tbody>
        </table>
        <br/>
        <div>
          <table border="1" style="width:100%;border-collapse:collapse">
            <tbody>
              {{-- department --}}
              @foreach ($departments as $department)
              
              <tr height="35">
                <td colspan="4" bgcolor="#CCCFFF">&nbsp;<b>Departemen: {{ $department->name }}</b></td>
              </tr>

              @php
                $x = 1;
                if ($requests->receipt_category_id == 'JTT' || $requests->receipt_category_id == 'CSWJB')
                {
                  $dataRecapDaily = $receiptMajorEloquent->dataRecapDaily($requests->bookyear_id, $department->id, $requests->receipt_category_id, $requests->start_date, $requests->end_date, $requests->employee_id);
                } elseif ($requests->receipt_category_id == 'SKR' || $requests->receipt_category_id == 'CSSKR') {
                  $dataRecapDaily = $receiptVoluntaryEloquent->dataRecapDaily($requests->bookyear_id, $department->id, $requests->receipt_category_id, $requests->start_date, $requests->end_date, $requests->employee_id);
                } else {
                  $dataRecapDaily = $receiptOtherEloquent->dataRecapDaily($requests->bookyear_id, $department->id, $requests->receipt_category_id, $requests->start_date, $requests->end_date, $requests->employee_id);
                }
                $receiptTypeNames = $receiptTypeEloquent->search($requests->receipt_category_id, $department->id);
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
                  if ($requests->receipt_category_id == 'JTT' || $requests->receipt_category_id == 'CSWJB')
                  {
                    $recapTransaction = $receiptMajorEloquent->dataRecapDailyTrans($requests->bookyear_id, $department->id, $type->id, $data->trans_date, $requests->employee_id);
                  } elseif ($requests->receipt_category_id == 'SKR' || $requests->receipt_category_id == 'CSSKR') {
                    $recapTransaction = $receiptVoluntaryEloquent->dataRecapDailyTrans($requests->bookyear_id, $department->id, $type->id, $data->trans_date, $requests->employee_id);
                  } else {
                    $recapTransaction = $receiptOtherEloquent->dataRecapDailyTrans($requests->bookyear_id, $department->id, $type->id, $data->trans_date, $requests->employee_id);
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
        </div>
      </div>
  </body>
</html>
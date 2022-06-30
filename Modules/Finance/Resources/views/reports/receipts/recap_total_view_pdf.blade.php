@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@inject('receiptOtherEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $arr_sub = [];
  $grand_total = 0;
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
                if ($requests->receipt_category_id == 'JTT' || $requests->receipt_category_id == 'CSWJB')
                {
                  $dataRecapTotal = $receiptMajorEloquent->dataRecapTotal($requests->bookyear_id, $department->id, $requests->receipt_category_id, $requests->start_date, $requests->end_date, $requests->employee_id);
                } elseif ($requests->receipt_category_id == 'SKR' || $requests->receipt_category_id == 'CSSKR') {
                  $dataRecapTotal = $receiptVoluntaryEloquent->dataRecapTotal($requests->bookyear_id, $department->id, $requests->receipt_category_id, $requests->start_date, $requests->end_date, $requests->employee_id);
                } else {
                  $dataRecapTotal = $receiptOtherEloquent->dataRecapTotal($requests->bookyear_id, $department->id, $requests->receipt_category_id, $requests->start_date, $requests->end_date, $requests->employee_id);
                }
                $x = 1;
              @endphp
              
              <tr height="25">
                  <td class="text-center" width="5%"><b>No.</b></td>
                  <td class="text-left">&nbsp;<b>Penerimaan</b></td>
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
                  <td>&nbsp;{{ $data->receipt_type }}</td>
                  <td class="text-right">Rp{{ number_format($data->total_grand,2) }}&nbsp;</td>
              </tr>
              
              @endforeach
              
              <tr height="25">
                  <td colspan="2" class="text-right"><b>Subtotal</b>&nbsp;</td>
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
                  <td colspan="2" class="text-center"><b>TOTAL PENERIMAAN</b></td>
                  <td class="text-right"><b>Rp{{ number_format($grand_total,2) }}</b>&nbsp;</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  </body>
</html>
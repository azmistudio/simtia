@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $colspan = 3 + $max_installment;
  $totalMajor = 0;
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN PEMBAYARAN PER KELAS SUKARELA</title>
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
      <div class="text-center" style="font-size:16px;"><b>LAPORAN PEMBAYARAN PER KELAS - {{ $requests->payment }}</b></div>
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
              <td style="width:15%;">Tahun Ajaran</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->schoolyear }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Tingkat/Semester</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->grade }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Kelas</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->class }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table" style="width:100%;">
          <thead>
            <tr>
              <th class="text-center">No.</th>
              <th class="text-center">NIS</th>
              <th class="text-center">Nama</th>
              @for ($i = 0;$i < $max_installment; $i++)
              <th class="text-center">Bayaran-{{ $i + 1 }}</th>
              @endfor
              <th class="text-center">Total<br/>Pembayaran</th>
            </tr>
          </thead>
          <tbody>
            @php $x = 1; @endphp
            @foreach ($payments as $pay) 
            <tr>
              <td class="text-center">{{ $x }}</td>
              <td class="text-center">{{ $pay->student_no }}</td>
              <td>{{ $pay->student }}</td>
              @php $receipts = $receiptVoluntaryEloquent->listPayment($requests->bookyear_id, $requests->department_id, $requests->class_id, $pay->student_id, 0); @endphp
              @if (count($receipts['queries']) > 0)
              @foreach ($receipts['queries'] as $receipt) 
              <td>
                <table style="width: 100%;">
                  <tbody>
                    <tr>
                      <td class="text-right">Rp{{ number_format($receipt->total,2) }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">{{ $receipt->tdate }}</td>
                    </tr>
                  </tbody>
                </table>
              </td>
              @endforeach
              @if (count($receipts['queries']) < $max_installment)
              @for ($i = 0;$i < $max_installment - count($receipts['queries']); $i++)
                <td></td>
              @endfor
              @endif
              @else
              @for ($i = 0;$i < $max_installment; $i++)
                <td></td>
              @endfor
              @endif
              <td class="text-right">Rp{{ number_format($receipts['major'],2) }}</td>
            </tr>
            @php 
              $x++; 
              $totalMajor += $receipts['major'];
            @endphp
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="{{ $colspan }}" class="text-center"><b>TOTAL</b></td>
              <td class="text-right"><b>Rp{{ number_format($totalMajor,2) }}</b></td>
            </tr>
          </tfoot>
        </table>
      </div>
  </body>
</html>
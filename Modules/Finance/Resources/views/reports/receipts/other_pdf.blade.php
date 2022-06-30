@inject('reference', 'Modules\Finance\Http\Controllers\FinanceController')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN PENERIMAAN LAIN</title>
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
      <div class="text-center" style="font-size:16px;"><b>LAPORAN PENERIMAAN LAIN</b></div>
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
              <td style="width:15%;">Jenis Penerimaan</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->receipt }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Periode</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->start }} s.d {{ $requests->end }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table" style="width:100%;">
          <thead>
            <tr>
              <th class="text-center" width="3%">No.</th>
              <th class="text-center" width="8%">Jurnal</th>
              <th class="">Sumber</th>
              <th class="text-center" width="10%">Jumlah</th>
              <th class="">Keterangan</th>
              <th class="" width="10%">Petugas</th>
            </tr>
          </thead>
          <tbody>
            @php $x = 1; @endphp
            @foreach ($requests->rows as $row)
            <tr>
              <td class="text-center">{{ $x++ }}</td>
              <td class="text-center"><b>{{ $row->cash_no }}</b><br/>{{ $reference->formatDate($row->journal_date,'iso') }}</td>
              <td class="">{{ $row->source }}</td>
              <td class="text-right">Rp{{ $row->total }}</td>
              <td class="text-center">{{ $row->remark }}</td>
              <td class="">{{ $row->employee }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="{{ 3 }}" class="text-center"><b>TOTAL</b></td>
              <td class="text-right"><b>Rp{!! $requests->footer[0]->total !!}</b></td>
              <td></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
  </body>
</html>
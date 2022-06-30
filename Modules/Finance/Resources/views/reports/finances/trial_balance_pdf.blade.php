@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - NERACA PERCOBAAN</title>
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
      <div class="text-center" style="font-size:16px;"><b>NERACA PERCOBAAN</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:12%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:20%;">-- SEMUA --</td>
              <td style="width:12%;">Periode Tanggal</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:25%;">{{ $requests->start }} s/d {{ $requests->end }}</td>
              <td style="width:12%;">Mata Uang</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:25%;">Rupiah (Rp)</td>
            </tr>
            <tr>
              <td style="width:12%;">Tahun Buku</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:20%;">{{ $requests->bookyear }}</td>
              <td style="width:12%;">Dicetak oleh</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:25%;">{{ auth()->user()->name }}</td>
              <td style="width:12%;">Tanggal Cetak</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:25%;">{{ date('d/m/Y H:i:s') }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table no-border row" style="width:100%;">
          <thead>
            <tr>
              <td class="border-bottom text-center"><b>Kode</b></td>
              <td class="border-bottom text-center"><b>Nama</b></td>
              <td class="border-bottom text-right"><b>Debit</b></td>
              <td class="border-bottom text-right"><b>Kredit</b></td>
            </tr>
          </thead>
          <tbody>
            @foreach ($requests->rows as $row) 
              <tr>
                <td class="text-center"><b>{{ $row->code }}</b></td>
                <td>{{ $row->name }}</td>
                <td class="text-right">{{ $row->debit }}</td>
                <td class="text-right">{{ $row->credit }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr style="background-color:#eee;">
              <td colspan="2" class="text-center"><b>TOTAL</b></td>
              <td class="text-right">{!! $requests->footer[0]->debit !!}</td>
              <td class="text-right">{!! $requests->footer[0]->credit !!}</td>
            </tr>
          </tfoot>
        </table>
      </div>
  </body>
</html>
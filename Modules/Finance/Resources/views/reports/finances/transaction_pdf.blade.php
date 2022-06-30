@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN TRANSAKSI KEUANGAN</title>
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
      <div class="text-center" style="font-size:16px;"><b>LAPORAN TRANSAKSI KEUANGAN</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:9%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:29%;">{{ $requests->department }}</td>
              <td style="width:10%;">Periode Tanggal</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ $requests->start }} s/d {{ $requests->end }}</td>
              <td style="width:9%;">Mata Uang</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:25%;">Rupiah (Rp)</td>
            </tr>
            <tr>
              <td style="width:9%;">Tahun Buku</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:29%;">{{ $requests->bookyear }}</td>
              <td style="width:10%;">Dicetak oleh</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ auth()->user()->name }}</td>
              <td style="width:9%;">Tanggal Cetak</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:25%;">{{ date('d/m/Y H:i:s') }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table no-border row" style="width:100%;">
          <thead>
            <tr>
              <td class="border-bottom text-center"><b>No.</b></td>
              @if ($requests->department == '-- SEMUA --')
              <td class="border-bottom text-center"><b>Departemen</b></td>
              @endif
              <td class="border-bottom text-center"><b>No. Jurnal/Tgl.</b></td>
              <td class="border-bottom text-center"><b>Petugas</b></td>
              <td class="border-bottom text-center"><b>Transaksi</b></td>
              <td class="border-bottom text-center"><b>Debit</b></td>
              <td class="border-bottom text-center"><b>Kredit</b></td>
            </tr>
          </thead>
          <tbody>
            @php $x = 1; @endphp
            @foreach ($requests->rows as $row) 
              <tr>
                <td class="text-center">{{ $x }}</td>
                @if ($requests->department == '-- SEMUA --')
                <td class="text-center"><b>{{ $row->department }}</b></td>
                @endif
                <td class="text-center"><b>{{ $row->journal }}</b></td>
                <td class="text-center">{{ $row->employee }}</td>
                <td>{{ $row->transaction }}</td>
                <td class="text-right">{{ $row->debit }}</td>
                <td class="text-right">{{ $row->credit }}</td>
              </tr>
            @php $x++; @endphp
            @endforeach
          </tbody>
          <tfoot>
            <tr style="background-color:#eee;">
              <td colspan="{{ $requests->department == '-- SEMUA --' ? 5 : 4 }}" class="text-center"><b>TOTAL</b></td>
              <td class="text-right">{!! $requests->footer[0]->debit !!}</td>
              <td class="text-right">{!! $requests->footer[0]->credit !!}</td>
            </tr>
          </tfoot>
        </table>
      </div>
  </body>
</html>
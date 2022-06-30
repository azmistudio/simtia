@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $total_deposit = 0;
  $total_withdraw = 0;
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN REKAPITULASI TABUNGAN PEGAWAI</title>
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
      <div class="text-center" style="font-size:16px;"><b>LAPORAN REKAPITULASI TABUNGAN PEGAWAI</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;">Petugas</td>
              <td style="width:1%;text-align:center;">:</td>
              <td><b>{{ $requests->employee  }}</b></td>
            </tr>
            <tr>
              <td style="width:15%;">Periode</td>
              <td style="width:1%;text-align:center;">:</td>
              <td><b>{{ $requests->start_date .' s.d '. $requests->end_date }}</b></td>
            </tr>
          </tbody>
        </table>
        <br/>
        <div>
          <table border="1" style="width:100%;border-collapse:collapse">
            <thead>
              <tr height="35" bgcolor="#CCCFFF">
                <th class="text-center" width="5%">No.</th>
                <th class="text-center">Tabungan</th>
                <th class="text-center" width="20%">Jumlah Setoran</th>
                <th class="text-center" width="20%">Jumlah Tarikan</th>
                <th class="text-center" width="20%">Jumlah Saldo</th>
              </tr>
            </thead>
            <tbody>
              @php $x = 1; @endphp
              @foreach ($savings as $saving)
              @php
                $total_deposit += $saving->total_credit;
                $total_withdraw += $saving->total_debit;
              @endphp
              <tr height="25">
                <td class="text-center">{{ $x++ }}</td>
                <td>&nbsp;{{ $saving->saving_type }}</td>
                <td class="text-right">Rp{{ number_format($saving->total_credit,2) }}&nbsp;</td>
                <td class="text-right">Rp{{ number_format($saving->total_debit,2) }}&nbsp;</td>
                <td class="text-right">Rp{{ number_format($saving->total_credit - $saving->total_debit,2) }}&nbsp;</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr height="30" bgcolor="#CCFFFF">
                <th colspan="2" class="text-center"><b>TOTAL</b></th>
                <th class="text-right"><b>Rp{{ number_format($total_deposit,2) }}&nbsp;</b></th>
                <th class="text-right"><b>Rp{{ number_format($total_withdraw,2) }}&nbsp;</b></th>
                <th class="text-right"><b>Rp{{ number_format($total_deposit - $total_withdraw,2) }}&nbsp;</b></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
  </body>
</html>
@inject('savingEloquent', 'Modules\Finance\Repositories\Saving\SavingEloquent')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN TABUNGAN PEGAWAI</title>
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
      <div class="text-center" style="font-size:16px;"><b>LAPORAN TABUNGAN PEGAWAI</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;">Pegawai</td>
              <td style="width:1%;text-align:center;">:</td>
              <td><b>{{ $requests->employee_no .' - '. $requests->employee }}</b></td>
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
            <tbody>
              @foreach ($savings as $saving)
              @php 
                $savingDetail = $savingEloquent->dataSavingDetailInfo(1, $requests->employee_id, $requests->bookyear_id, $requests->start_date, $requests->end_date, $saving->saving_id);
              @endphp
              <tr height="35">
                <td colspan="5" bgcolor="#CCCFFF">&nbsp;<b>{{ $saving->saving_type }}</b></td>
              </tr>
              <tr height="25">
                <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Jumlah Setoran</strong></td>
                    <td width="15%" bgcolor="#FFFFFF" align="right"><b>{{ $savingDetail['deposit'] }}</b>&nbsp;</td>
                    <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Total Setoran</strong></td>
                    <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Total Tarikan</strong></td>
                    <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Saldo Tabungan</strong></td>
              </tr>
              <tr height="25">
                <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Setoran Terakhir</strong></td>
                    <td width="15%" bgcolor="#FFFFFF" align="right">{!! $savingDetail['last_deposit'] !!}&nbsp;</td>
                    <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h3>{{ $savingDetail['total_deposit'] }}</h3></b>&nbsp;</td>
                    <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h3>{{ $savingDetail['total_withdraw'] }}</h3></b>&nbsp;</td>
                    <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h3>{{ $savingDetail['total_balance'] }}</h3></b>&nbsp;</td>
              </tr>
              <tr height="25">
                <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Jumlah Tarikan</strong></td>
                    <td width="15%" bgcolor="#FFFFFF" align="right"><b>{{ $savingDetail['withdraw'] }}</b>&nbsp;</td>
              </tr>
              <tr height="25">
                <td width="20%" bgcolor="#CCFFFF">&nbsp;<strong>Tarikan Terakhir</strong></td>
                    <td width="15%" bgcolor="#FFFFFF" align="right">{!! $savingDetail['last_withdraw'] !!}&nbsp;</td>
              </tr>
              @endforeach                
            </tbody>
          </table>
        </div>
      </div>
  </body>
</html>
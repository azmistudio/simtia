@inject('savingEloquent', 'Modules\Finance\Repositories\Saving\SavingEloquent')
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="5" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="5" align="center" class="title"><b>LAPORAN TABUNGAN PEGAWAI</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table class="no-border">
      <tbody>
        <tr>
          <td>Pegawai</td>
          <td>: <b>{{ $payloads->employee_no .' - '. $payloads->employee }}</b></td>
        </tr>
        <tr>
          <td>Tanggal</td>
          <td>: <b>{{ $payloads->start_date }} s.d {{ $payloads->end_date }}</b></td>
        </tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
      <tbody>
        @foreach ($savings as $saving)
        @php 
          $savingDetail = $savingEloquent->dataSavingDetailInfo(1, $payloads->employee_id, $payloads->bookyear_id, $payloads->start_date, $payloads->end_date, $saving->saving_id);
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
              <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h5>{{ $savingDetail['total_deposit'] }}</h5></b>&nbsp;</td>
              <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h5>{{ $savingDetail['total_withdraw'] }}</h5></b>&nbsp;</td>
              <td width="15%" bgcolor="#FFFFFF" align="right" rowspan="3" valign="top"><b><h5>{{ $savingDetail['total_balance'] }}</h5></b>&nbsp;</td>
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
  </body>
</html>
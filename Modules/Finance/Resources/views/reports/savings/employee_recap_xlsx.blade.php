@php
  $total_deposit = 0;
  $total_withdraw = 0;
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="5" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="5" align="center" class="title"><b>LAPORAN REKAPITULASI TABUNGAN PEGAWAI</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table class="no-border">
      <tbody>
          <td>Petugas</td>
          <td>: <b>{{ $payloads->employee }}</b></td>
        </tr>
        <tr>
          <td>Tanggal</td>
          <td>: <b>{{ $payloads->start_date }} s.d {{ $payloads->end_date }}</b></td>
        </tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
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
  </body>
</html>
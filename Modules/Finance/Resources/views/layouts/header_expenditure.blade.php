@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<!DOCTYPE html>
<html>
  <head>
    <style type="text/css">
      body {margin: 0;padding: 0;font-size: 11px;font-family: "Segoe UI", "Open Sans", serif !important;}
      .text-left {text-align: left;}
      .text-center {text-align: center;}
      .text-right {text-align: right;}
      #imgLogo {margin-bottom: 5px;}
      table.no-border, table.no-border th, table.no-border td {border: none;}
      table {border-collapse: collapse;border: 1px solid #000;font-size:14px;page-break-inside: auto;}
      tr {page-break-inside: avoid;page-break-after: auto;}
      th, td {border: 1px solid #000;padding: 3px;}
    </style>
  </head>
  <body>
    <div>
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
      <div class="text-center" style="font-size:16px;"><b>BUKTI PENGELUARAN KAS</b></div>
      <br/>
      <table class="table no-border" width="100%">
        <tbody>
          <tr>
            <td width="12%">Pemohon</td>
            <td width="1%">:</td>
            <td width="60%"><b>{{ $receipt_majors->requested_name }}</b></td>
            <td width="12%">No. Jurnal</td>
            <td>:</td>
            <td width=""><b>{{ $receipt_majors->cash_no }}</b></td>
          </tr>
          <tr>
            <td>Penerima</td>
            <td width="1%">:</td>
            <td><b>{{ $receipt_majors->received_name }}</b></td>
            <td>Tanggal Jurnal</td>
            <td width="1%">:</td>
            <td><b>{{ $receipt_majors->trans_date }}</b></td>
          </tr>
          <tr>
            <td>Keterangan/Ref.</td>
            <td>:</td>
            <td colspan="4">{{ $receipt_majors->remark }}</td>
          </tr>
        </tbody>
      </table>
      <br/>
  </body>
</html>
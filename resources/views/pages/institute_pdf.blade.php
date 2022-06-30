@php
  $logo = !empty($rows->logo) ? storage_path('app/public/uploads/'.$rows->logo) : public_path('img/logo-yayasan.png');
@endphp
<html>
    <head>
      <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Kop Surat</title>
      <style type="text/css">
        body { margin: 0; padding: 0; font-size: 13px; font-family: "Segoe UI", "Open Sans", serif !important; }
      </style>
    </head>
    <body style="">
      <div id="header">
        <table style="width:100%;">
          <tbody>
            <tr>
              <th rowspan="2" style="width:15%;"><img src="file:///{{ $logo }}" height="80px" /></th>
              <td style="font-size:16px;"><b>{{ $rows->name }}</b></td>
            </tr>
            <tr>
              <td style="font-size:12px;">
                {{ $rows->address }}<br/>
                Telp. {{ $rows->phone ?: '-' }} - Fax. {{ $rows->fax ?: '-' }}<br/>
                Website: {{ $rows->website ?: '-' }} - Email: {{ $rows->email ?: '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <hr/>
    </body>
</html>
<html>
  <head>
    <style type="text/css">
      body {margin: 0;padding: 0;font-size: 11px;font-family: "Segoe UI", "Open Sans", serif !important;}
      .text-left {text-align: left;}
      .text-center {text-align: center;}
      .text-right {text-align: right;}
      .break {page-break-before: avoid;}
      .must-break {page-break-before: always;}
      table.no-border, table.no-border th, table.no-border td {border: none;}
      table {border-collapse: collapse;border: 1px solid #000;font-size:14px;page-break-inside: auto;}
      tr {page-break-inside: avoid;page-break-after: auto;}
      th, td {border: 1px solid #000;padding: 3px;}
      thead { display: table-header-group; }
    </style>
  </head>
  <body>
    <div id="body">
      <table class="table" width="100%" style="font-size:12px;">
        <thead>
          <tr>
            <th>No.</th>
            <th>Kode Akun</th>
            <th>Nama Akun</th>
            <th>Deskripsi</th>
            <th>Jumlah</th>
          </tr>
        </thead>
        <tbody>
          @php $x = 1; @endphp
          @foreach ($expenditures as $expenditure)
          <tr>
            <td class="text-center" width="5%">{{ $x }}</td>
            <td class="text-center" width="10%">{{ $expenditure->code }}</td>
            <td width="25%">{{ $expenditure->name }}</td>
            <td>{{ $expenditure->remark }}</td>
            <td class="text-right" width="15%">Rp{{ number_format($expenditure->credit,2) }}</td>
          </tr>
          @php $x++; @endphp
          @endforeach
        </tbody>
      </table>
    </div>
  </body>
</html>
@php
    $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - DATA MUTASI SANTRI</title>
    <style type="text/css">
      body { margin: 0; padding: 0; font-size: 11px; font-family: "Segoe UI", "Open Sans", serif !important; }
      .text-left { text-align: left; }
      .text-center { text-align: center; }
      .text-right { text-align: right; }
      #imgLogo { margin-bottom: 5px; }
      .break { page-break-before: avoid; }
      .must-break { page-break-before: always; }
      table.no-border, table.no-border th, table.no-border td { border: none; }
      table { border-collapse: collapse; border: 1px solid #000; font-size:13px; page-break-inside: auto; }
      tr { page-break-inside: avoid; page-break-after: auto; }
      th, td { border: 1px solid #000; padding: 3px; }
      thead, tfoot { display: table-row-group; }
    </style>
  </head>
  <body>
    <div id="header">
        <table class="table no-border" style="width:100%;">
            <tbody>
                <tr>
                    <th rowspan="2" width="100px"><img src="file:///{{ $logo }}" height="80px" /></th>
                    <td><b>{{ $profile['name'] }}</b></td>
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
      <div class="text-center" style="font-size:16px;"><b>DATA MUTASI SANTRI</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:2%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ $requests['department'] }}</td>
            </tr>
            <tr>
              <td style="width:2%;">Tahun Mutasi</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $requests['year'] }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table" style="width:100%;">
            <thead>
                <tr>
                    <th class="text-center" width="5%">No.</th>
                    <th class="text-center" width="8%">NIS</th>
                    <th>Nama</th>
                    <th class="text-center" width="12%">Kelas Terakhir</th>
                    <th class="text-center" width="10%">Tanggal Mutasi</th>
                    <th>Jenis Mutasi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
              @php $num = 1; @endphp
              @foreach ($models as $model)
              <tr>
                  <td class="text-center">{{ $num }}</td>
                  <td class="text-center">{{ $model->getStudent->student_no }}</td>
                  <td>{{ $model->getStudent->name }}</td>
                  <td class="text-center">{{ $model->getAlumniByStudent->getClass->class }}</td>
                  <td class="text-center">{{ Carbon\Carbon::createFromFormat('Y-m-d', $model->mutation_date)->format('d/m/Y') }}</td>
                  <td>{{ $model->getMutation->name }}</td>
                  <td>{{ $model->remark }}</td>
              </tr>
              @php $num++; @endphp
              @endforeach
            </tbody>
        </table>
      </div>
  </body>
</html>
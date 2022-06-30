<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="9" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="9" align="center" class="title"><b>LAPORAN TABUNGAN KELAS</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table class="no-border">
      <tbody>
      	<tr>
          <td colspan="2">Departemen</td>
          <td>: <b>{{ $payloads->department }}</b></td>
        </tr>
        <tr>
          <td colspan="2">Tahun Ajaran</td>
          <td>: <b>{{ $payloads->schoolyear }}</b></td>
        </tr>
        <tr>
          <td colspan="2">Tingkat</td>
          <td>: <b>{{ $payloads->grade }}</b></td>
        </tr>
      	<tr>
          <td colspan="2">Jenis Tabungan</td>
          <td>: <b>{{ $payloads->saving }}</b></td>
        </tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
      <thead>
        <tr>
          <th class="text-center">No.</th>
          <th class="text-center">NIS</th>
          <th>Nama</th>
          <th class="text-center">Kelas</th>
          <th class="text-center">Saldo Tabungan</th>
          <th class="text-center">Total Setoran</th>
          <th class="text-center">Setoran Terakhir</th>
          <th class="text-center">Total Tarikan</th>
          <th class="text-center">Tarikan Terakhir</th>
        </tr>
      </thead>
      <tbody>
        @php $x = 1; @endphp
        @foreach ($payloads->rows as $data)
          <tr>
            <td class="text-center" width="3%">{{ $x++ }}</td>
            <td class="text-center" width="7%">{{ $data->student_no }}</td>
            <td width="">{{ $data->name }}</td>
            <td class="text-center" width="10%">{{ $data->class }}</td>
            <td class="text-right" width="10%">{{ $data->balance }}</td>
            <td class="text-right" width="10%">{{ $data->total_saving }}</td>
            <td class="text-right" width="10%">{!! $data->last_saving !!}</td>
            <td class="text-right" width="10%">{{ $data->total_withdraw }}</td>
            <td class="text-right" width="10%">{!! $data->last_withdraw !!}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th colspan="4" class="text-right"><b>TOTAL</b></th>
          <th class="text-right"><b>{!! $payloads->footers[0]->balance !!}</b></th>
          <th class="text-right"><b>{!! $payloads->footers[0]->total_saving !!}</b></th>
          <th></th>
          <th class="text-right"><b>{!! $payloads->footers[0]->total_withdraw !!}</b></th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </body>
</html>
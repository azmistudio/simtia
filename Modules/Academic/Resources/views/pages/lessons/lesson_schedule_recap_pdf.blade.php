<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Rekapitulasi Jadwal Guru</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Rekapitulasi Jadwal Guru</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <hr/>
        <table id="table-info" class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $dept }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $schoolyear }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Info Jadwal</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $schedule }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Periode</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $period }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
            <th class="text-center" rowspan="2" width="5%">NO.</th>
            <th class="text-center" rowspan="2">NIP</th>
            <th class="text-center" rowspan="2">NAMA</th>
            <th class="text-center" colspan="6">JUMLAH</th>
            
        </tr>
        <tr>
            <th class="text-center">MENGAJAR</th>
            <th class="text-center">ASISTENSI</th>
            <th class="text-center">TAMBAHAN</th>
            <th class="text-center">JAM</th>
            <th class="text-center">KELAS</th>
            <th class="text-center">HARI</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $model->employee_no }}</td>
            <td>{{ $model->employee }}</td>
            <td class="text-center">{{ $model->teaching }}</td>
            <td class="text-center">{{ $model->assist }}</td>
            <td class="text-center">{{ $model->addition }}</td>
            <td class="text-center">{{ $model->time }}</td>
            <td class="text-center">{{ $model->class_id }}</td>
            <td class="text-center">{{ $model->day }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
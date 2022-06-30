<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data PSB Kelompok Calon Santri</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data PSB Kelompok Calon Santri</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
          <th class="text-center">NO.</th>
          <th class="text-left">NAMA DEPARTEMEN</th>
          <th class="text-center">NAMA PROSES</th>
          <th class="text-center">KELOMPOK</th>
          <th class="text-center">KAPASITAS</th>
          <th class="text-center">TERISI</th>
          <th class="text-left">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td>{{ $model['department'] }}</td>
            <td class="text-center">{{ $model['admission_id'] }}</td>
            <td class="text-center">{{ $model['group'] }}</td>
            <td class="text-center">{{ $model['capacity'] }}</td>
            <td class="text-center">{{ $model['occupied'] }}</td>
            <td>{{ $model['remark'] }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
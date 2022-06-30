<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Kelas</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Kelas</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
          <th class="text-center">NO.</th>
          <th class="text-center">DEPARTEMEN</th>
          <th class="text-center">TINGKAT</th>
          <th class="text-center">TAHUN AJARAN</th>
          <th class="text-center">KELAS</th>
          <th class="text-center">WALI KELAS</th>
          <th class="text-center">KAPASITAS</th>
          <th class="text-center">TERISI</th>
          <th class="text-center">AKTIF</th>
          <th class="text-center">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $model['department'] }}</td>
            <td class="text-center">{{ $model['grade_id'] }}</td>
            <td class="text-center">{{ $model['schoolyear_id'] }}</td>
            <td class="text-center">{{ $model['class'] }}</td>
            <td class="text-center">{{ $model['employee_id'] }}</td>
            <td class="text-center">{{ $model['capacity'] }}</td>
            <td class="text-center">{{ $model['occupied'] }}</td>
            <td class="text-center">{{ $model['is_active'] }}</td>
            <td>{{ $model['remark'] }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Angkatan</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Angkatan</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <table>
      <thead>
        <tr>
            <th class="text-center">NO.</th>
            <th class="text-left">NAMA DEPARTEMEN</th>
            <th class="text-center">ANGKATAN</th>
            <th class="text-center">AKTIF</th>
            <th class="text-center">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($model as $val)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td>{{ $val->deptid }}</td>
            <td class="text-center">{{ $val->generation }}</td>
            <td class="text-center">{{ $val->is_active }}</td>
            <td>{{ $val->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
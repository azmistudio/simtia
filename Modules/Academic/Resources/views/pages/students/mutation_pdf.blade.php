<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Jenis Mutasi</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Jenis Mutasi</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
          <th class="text-center">NO.</th>
          <th class="text-left">JENIS MUTASI</th>
          <th class="text-left">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-left">{{ ucwords($model->name) }}</td>
            <td>{{ $model->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Departemen</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Departemen</span><br/>
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
          <th class="text-left">KEPALA SEKOLAH</th>
          <th class="text-center">AKTIF</th>
          <th class="text-left">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="">{{ $model->name }}</td>
            <td class="">{{ optional($model->getEmployee)->title_first .' '. optional($model->getEmployee)->name .' '. optional($model->getEmployee)->title_end }}</td>
            <td class="text-center">{{ $model->is_active }}</td>
            <td class="">{{ $model->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
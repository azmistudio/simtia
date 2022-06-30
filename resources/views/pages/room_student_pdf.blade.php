<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Kamar Santri</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Kamar Santri</span><br/>
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
          <th class="text-left">NAMA KAMAR</th>
          <th class="text-center">STATUS</th>
          <th class="text-center">KAPASITAS/TERISI</th>
          <th class="text-left">PENANGGUNG JAWAB</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num++ }}</td>
            <td class="">{{ $model->department }}</td>
            <td class="">{{ $model->name }}</td>
            <td class="text-center">{{ $model->gender }}</td>
            <td class="text-center">{{ $model->capacity }}</td>
            <td class="">{{ optional($model->getEmployee)->title_first .' '. optional($model->getEmployee)->name .' '. optional($model->getEmployee)->title_end }}</td>
          </tr> 
        @endforeach
      </tbody>
    </table>
  </body>
</html>
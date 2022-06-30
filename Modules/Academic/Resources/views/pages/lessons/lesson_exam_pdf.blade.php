<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Jenis Pengujian</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Jenis Pengujian</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
            <th class="text-center" width="5%">NO.</th>
            <th class="text-center">DEPARTEMEN</th>
            <th class="text-left">PELAJARAN</th>
            <th class="text-center">SINGKATAN</th>
            <th class="text-center">JENIS PENGUJIAN</th>
            <th class="text-center">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $model->getLesson->getDepartment->name }}</td>
            <td class="text-left">{{ $model->getLesson->name }}</td>
            <td class="text-center">{{ $model->code }}</td>
            <td class="text-center">{{ $model->subject }}</td>
            <td>{{ ucwords($model->remark) }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
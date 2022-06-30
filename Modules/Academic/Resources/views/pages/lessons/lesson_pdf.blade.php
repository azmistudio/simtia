<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Pelajaran</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Pelajaran</span><br/>
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
            <th class="text-center">NAMA</th>
            <th class="text-center">SINGKATAN</th>
            <th class="text-center">KELOMPOK</th>
            <th class="text-center">SIFAT</th>
            <th class="text-center">KETERANGAN</th>
            <th class="text-center">STATUS AKTIF</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $model->getDepartment->name }}</td>
            <td>{{ $model->name }}</td>
            <td class="text-center">{{ $model->code }}</td>
            <td class="text-center">{{ $model->getLessonGroup->group }}</td>
            <td class="text-center">{{ $model->mandatory }}</td>
            <td class="text-center">{{ $model->remark }}</td>
            <td class="text-center">{{ $model->is_active }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
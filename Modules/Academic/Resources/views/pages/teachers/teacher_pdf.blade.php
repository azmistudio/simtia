<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Guru</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Guru Pelajaran</span><br/>
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
            <th class="text-center">PELAJARAN</th>
            <th class="text-center">NIP</th>
            <th class="text-left">GURU</th>
            <th class="text-center">STATUS GURU</th>
            <th class="text-center">STATUS</th>
            <th class="text-center">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $model->getLesson->getDepartment->name }}</td>
            <td class="text-center">{{ $model->getLesson->name }}</td>
            <td class="text-center">{{ $model->getEmployee->employee_id }}</td>
            <td>{{ $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end }}</td>
            <td class="text-center">{{ $model->getStatus->name }}</td>
            <td class="text-center">{{ $model->is_active }}</td>
            <td>{{ $model->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
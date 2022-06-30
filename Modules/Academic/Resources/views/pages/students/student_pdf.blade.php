<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Santri</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Santri</span><br/>
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
            <th class="text-center">TAHUN AJARAN</th>
            <th class="text-center">TINGKAT/KELAS</th>
            <th class="text-center">NIS</th>
            <th class="text-left">NAMA</th>
            <th class="text-center">JENIS KELAMIN</th>
            <th class="text-left">TEMPAT, TGL. LAHIR</th>
            <th class="text-center">STATUS</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $model->department }}</td>
            <td class="text-center">{{ $model->school_year }}</td>
            <td class="text-center">{{ $model->grade .' - '. $model->class }}</td>
            <td class="text-center">{{ $model->student_no }}</td>
            <td class="text-left">{{ $model->name }}</td>
            <td class="text-center">{{ $model->gender }}</td>
            <td class="text-left">{{ $model->pob }}, {{ $model->dob->format('d/m/Y') }}</td>
            <td class="text-center">{{ $model->is_active }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
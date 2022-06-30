<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data PSB Calon Santri</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data PSB Calon Santri</span><br/>
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
            <th class="text-center">PROSES</th>
            <th class="text-center">KELOMPOK</th>
            <th class="text-center">NO. PENDAFTARAN</th>
            <th class="text-center">NAMA</th>
            <th class="text-center">SUMBANGAN #1</th>
            <th class="text-center">SUMBANGAN #2</th>
            <th class="text-center">UJIAN<br/>#1</th>
            <th class="text-center">UJIAN<br/>#2</th>
            <th class="text-center">UJIAN<br/>#3</th>
            <th class="text-center">UJIAN<br/>#4</th>
            <th class="text-center">UJIAN<br/>#5</th>
            <th class="text-center">UJIAN<br/>#6</th>
            <th class="text-center">UJIAN<br/>#7</th>
            <th class="text-center">UJIAN<br/>#8</th>
            <th class="text-center">UJIAN<br/>#9</th>
            <th class="text-center">UJIAN<br/>#10</th>
            <th class="text-center">STATUS</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $model->department }}</td>
            <td class="text-center">{{ $model->admission_name }}</td>
            <td class="text-center">{{ $model->getProspectGroup->group }}</td>
            <td class="text-center">{{ $model->registration_no }}</td>
            <td class="text-left">{{ $model->name }}</td>
            <td class="text-right">{{ number_format($model->donation_1, 2) }}</td>
            <td class="text-right">{{ number_format($model->donation_2, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_01, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_02, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_03, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_04, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_05, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_06, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_07, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_08, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_09, 2) }}</td>
            <td class="text-right">{{ number_format($model->exam_10, 2) }}</td>
            <td class="text-center">{{ $model->is_active }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data PSB Proses</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data PSB Proses Penerimaan</span><br/>
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
          <th class="text-center">NAMA PROSES</th>
          <th class="text-center">AWALAN</th>
          <th class="text-center">JUMLAH</th>
          <th class="text-center">AKTIF</th>
          <th class="text-left">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
        @php
          $total = 0;
          foreach ($model->getProspectiveGroup as $row) 
          {
              $total += count($row->getAdmissionProspect);
          }
        @endphp
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td>{{ $model->getDepartment->name }}</td>
            <td class="text-center">{{ $model->name }}</td>
            <td class="text-center">{{ $model->prefix }}</td>
            <td class="text-center">{{ $total }}</td>
            <td class="text-center">{{ $model->is_active }}</td>
            <td>{{ $model->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
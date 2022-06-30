<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data SDM</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Sumber Daya Manusia</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
            <th class="text-center">NO.</th>
            <th class="text-center">BAGIAN</th>
            <th class="text-center">NIP</th>
            <th class="text-center">NAMA</th>
            <th class="text-center">TEMPAT & TANGGAL LAHIR</th>
            <th class="text-center">JENIS KELAMIN</th>
            <th class="text-center">MENIKAH</th>
            <th class="text-center">SUKU</th>
            <th class="text-center">NO. IDENTITAS</th>
            <th class="text-center">NO. TELPON</th>
            <th class="text-center">NO. HANDPHONE</th>
            <th class="text-center">EMAIL</th>
            <th class="text-center">TANGGAL BEKERJA</th>
            <th class="text-center">ALAMAT</th>
            <th class="text-center">AKTIF</th>
            <th class="text-center">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($models as $model)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $model->getSection->name }}</td>
            <td class="text-center">{{ $model->employee_id }}</td>
            <td>{{ $model->name }}</td>
            <td class="text-center">{{ $model->pob }}</td>
            <td class="text-center">{{ $model->gender }}</td>
            <td class="text-center">{{ $model->marital }}</td>
            <td class="text-center">{{ $model->getTribe->name }}</td>
            <td class="text-center">{{ $model->national_id }}</td>
            <td class="text-center">{{ $model->phone }}</td>
            <td class="text-center">{{ $model->mobile }}</td>
            <td>{{ $model->email }}</td>
            <td class="text-center">{{ $model->work_start->format('d/m/Y') }}</td>
            <td>{{ $model->address }}</td>
            <td class="text-center">{{ $model->is_active }}</td>
            <td>{{ $model->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
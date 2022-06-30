<html>
  <head>
    <title>SIMAK LTA {{ Session::get('institute') }} - Data Jenis Pengeluaran</title>
    <link href="{{ asset('css/print.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ Session::get('institute') }}</span><br/>
        <span>Data Jenis Pengeluaran </span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <table>
      <thead>
        <tr>
            <th class="text-center">NO.</th>
            <th class="text-center">NAMA DEPARTEMEN</th>
            <th class="text-center">NAMA</th>
            <th class="text-center">REKENING KAS</th>
            <th class="text-center">REKENING BEBAN</th>
            <th class="text-center">AKTIF</th>
            <th class="text-center">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $i = 1; @endphp
        @foreach ($expenditure_types as $val)
          <tr>
            <td class="text-center">{{ $i }}</td>
            <td>{{ $val->deptid }}</td>
            <td>{{ $val->name }}</td>
            <td>{{ $val->debit_account }}</td>
            <td>{{ $val->credit_account }}</td>
            <td class="text-center">{{ $val->is_active }}</td>
            <td>{{ $val->remark }}</td>
          </tr> 
          @php $i++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Laporan Pengeluaran</title>
    <link href="file:///{{ public_path('css/print.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Laporan Pengeluaran {{ $data->transaction }}</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span><br/>
        <span>Departemen: {{ $data->deptname }}</span><br/>
        <span>Periode: {{ $data->start . ' s.d ' . $data->end }}</span>
        <br/> 
      </div>
    </div>
    <table>
      <thead>
        <tr>
            <th class="text-center">NO.</th>
            <th class="text-center">TANGGAL</th>
            <th class="text-left">PEMOHON</th>
            <th class="text-left">PENERIMA</th>
            <th class="text-center">JUMLAH</th>
            <th class="text-left">KEPERLUAN</th>
            <th class="text-left">PETUGAS</th>
        </tr>
      </thead>
      <tbody>
        @php $i = 1; $total = 0; @endphp
        @foreach ($data->rows as $val)
          <tr>
            <td class="text-center">{{ $i }}</td>
            <td class="text-center">{{ $val->trans_date }}</td>
            <td>{{ $val->requested_person }}</td>
            <td>{{ $val->received_name }}</td>
            <td class="text-right">{{ $val->total }}</td>
            <td>{{ $val->purpose }}</td>
            <td>{{ $val->employee }}</td>
          </tr> 
          @php $total += str_replace(',','',str_replace('Rp', '', $val->total)); $i++; @endphp
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td class="text-right"><b>TOTAL</b></td>
          <td class="text-right"><b>Rp{{ number_format($total,2) }}</b></td>
          <td></td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </body>
</html>
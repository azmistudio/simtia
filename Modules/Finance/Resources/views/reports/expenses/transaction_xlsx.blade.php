<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="7" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="7" align="center" class="title"><b>LAPORAN TRANSAKSI PENGELUARAN</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table class="no-border">
      <tbody>
      	<tr>
          <td colspan="2">Departemen</td>
          <td>: <b>{{ $payloads->department }}</b></td>
        </tr>
        <tr>
          <td colspan="2">Tahun Buku</td>
          <td>: <b>{{ $payloads->bookyear }}</b></td>
        </tr>
      	<tr>
          <td colspan="2">Periode</td>
          <td>: <b>{{ $payloads->start }} s.d {{ $payloads->end }}</b></td>
        </tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
      <thead>
        <tr>
          <th class="text-center">No.</th>
          <th class="text-center">Tanggal</th>
          <th>Pemohon</th>
          <th>Penerima</th>
          <th class="text-center">Jumlah</th>
          <th>Keperluan</th>
          <th>Petugas</th>
        </tr>
      </thead>
      <tbody>
        @php $x = 1; @endphp
        @foreach ($payloads->rows as $data)
        @php
          $purposes = explode('<br/>', $data->purpose);
        @endphp
          <tr>
            <td class="text-center">{{ $x++ }}</td>
            <td class="text-center">{{ $data->trans_date }}</td>
            <td>{{ $data->requested_name }}</td>
            <td>{{ $data->received_name }}</td>
            <td class="text-right">Rp{{ number_format($data->total,2) }}</td>
            <td>
              {!! $purposes[0] !!}<br/>
              {!! $purposes[1] !!}
            </td>
            <td>{{ $data->employee }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <th colspan="4" class="text-center"><b>TOTAL</b></th>
          <th class="text-right"><b>{!! $payloads->footers[0]->total_val !!}</b></th>
          <th></th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </body>
</html>
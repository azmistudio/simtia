@inject('reference', 'Modules\Finance\Http\Controllers\BookYearController')
<html>
  <head>
    <title>{{ config('app.name') .' '. Session::get('institute') }} - Data Tahun Buku</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Tahun Buku - {{ config('app.name') }}</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
            <th class="text-center">NO.</th>
            <th class="text-center">TAHUN BUKU</th>
            <th class="text-center">TANGGAL MULAI</th>
            <th class="text-center">TANGGAL SELESAI</th>
            <th class="text-center">AWALAN</th>
            <th class="text-center">STATUS</th>
            <th class="text-left">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($bookyears as $bookyear)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $bookyear->book_year }}</td>
            <td class="text-center">{{ $reference->formatDate($bookyear->start_date,'local') }}</td>
            <td class="text-center">{{ $reference->formatDate($bookyear->end_date,'local') }}</td>
            <td class="text-center">{{ $bookyear->prefix }}</td>
            <td class="text-center">{{ $bookyear->is_active == 0 ? 'Tidak Aktif' : 'Aktif' }}</td>
            <td>{{ $bookyear->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>
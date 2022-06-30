@inject('reference', 'Modules\Finance\Http\Controllers\FinanceController')
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="6" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="6" align="center" class="title"><b>LAPORAN PENERIMAAN LAIN</b></td></tr>
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
          <td colspan="2">Jenis Penerimaan</td>
          <td>: <b>{{ $payloads->receipt }}</b></td>
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
          <th class="text-center" width="3%">No.</th>
          <th class="text-center" width="8%">Jurnal</th>
          <th class="">Sumber</th>
          <th class="text-center" width="10%">Jumlah</th>
          <th class="">Keterangan</th>
          <th class="" width="10%">Petugas</th>
        </tr>
      </thead>
      <tbody>
        @php $x = 1; @endphp
        @foreach ($payloads->rows as $row)
        <tr>
          <td class="text-center">{{ $x++ }}</td>
          <td class="text-center"><b>{{ $row->cash_no }}</b><br/>{{ $reference->formatDate($row->journal_date,'iso') }}</td>
          <td class="">{{ $row->source }}</td>
          <td class="text-right">Rp{{ $row->total }}</td>
          <td class="text-center">{{ $row->remark }}</td>
          <td class="">{{ $row->employee }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="{{ 3 }}" class="text-center"><b>TOTAL</b></td>
          <td class="text-right"><b>Rp{!! $payloads->footer[0]->total !!}</b></td>
          <td></td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </body>
</html>
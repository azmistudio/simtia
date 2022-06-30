@inject('journalEloquent', 'Modules\Finance\Repositories\Journal\journalEloquent')
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="7" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="7" align="center" class="title"><b>LAPORAN JURNAL PENGELUARAN</b></td></tr>
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
          <td colspan="2">Tanggal</td>
          <td>: <b>{{ $payloads->start }} s.d {{ $payloads->end }}</b></td>
        </tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
      <thead>
        <tr>
          <th class="text-center" width="3%">No.</th>
          <th class="text-center" width="8%">No. Jurnal/Tanggal</th>
          <th class="">Transaksi</th>
          <th class="">Detil Jurnal</th>
        </tr>
      </thead>
      <tbody>
        @php $x = 1; @endphp
        @foreach ($payloads->rows as $journal)
          <tr>
            <td class="text-center" rowspan="2">{{ $x++ }}</td>
            <td class="text-center" width="20%"><b>{!! $journal->cash_no !!}</b> / {{ $journal->trans_date }}</td>
            <td width="30%">{{ $journal->transaction }}</td>
            <td rowspan="2">
              <table width="100%">
                <tbody>
                  @php
                    $requestObj = new Illuminate\Http\Request();
                    $requestObj->merge(['journal_id' => $journal->journal_id]);
                    $details = $journalEloquent->dataDetail($requestObj);
                  @endphp
                  @foreach ($details['rows'] as $detail)
                    <tr>
                      <td class="text-center" width="15%">{{ $detail->code }}</td>
                      <td>{{ $detail->name }}</td>
                      <td class="text-right" width="20%">Rp{{ number_format($detail->debit,2) }}</td>
                      <td class="text-right" width="20%">Rp{{ number_format($detail->credit,2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </td>
          </tr>
          <tr>              
            <td><b>Petugas</b>: {{ $journal->name }}</td>
            <td><b>Sumber</b>: {{ $journal->source }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </body>
</html>
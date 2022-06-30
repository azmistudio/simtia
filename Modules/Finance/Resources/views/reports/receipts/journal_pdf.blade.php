@inject('journalEloquent', 'Modules\Finance\Repositories\Journal\journalEloquent')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN JURNAL PENERIMAAN</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
        <table class="table no-border" style="width:100%;">
            <tbody>
                <tr>
                    <th rowspan="2" width="100px"><img src="file:///{{ $logo }}" height="80px" /></th>
                    <td><b>{{ strtoupper($profile['name']) }}</b></td>
                </tr>
                <tr>
                    <td style="font-size:11px;">
                        {{ $profile['address'] }}<br/>
                        Telpon: {{ $profile['phone'] }} - Faksimili: {{ $profile['fax'] }}<br/>
                        Website: {{ $profile['web'] }} - Email: {{ $profile['email'] }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr/>
    <div id="body">
      <br/>
      <br/>
      <br/>
      <div class="text-center" style="font-size:16px;"><b>LAPORAN JURNAL PENERIMAAN</b></div>
      <br/>
      <br/>
      <table class="table no-border" width="100%">
        <tbody>
          <tr>
            <td style="width:3%;">Departemen</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td style="width:30%;"><b>{{ $requests->department }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Penerimaan</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->receipt_type }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Tahun Buku</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->bookyear }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Tanggal</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->start }}</b> s.d <b>{{ $requests->end }}</b></td>
          </tr>
        </tbody>
      </table>
      <br/>
      <br/>
      <table width="100%">
        <thead>
          <tr>
            <th>No.</th>
            <th>No. Jurnal/Tanggal</th>
            <th>Transaksi</th>
            <th>Detil Jurnal</th>
          </tr>
        </thead>
        <tbody>
        @php $x = 1; @endphp
        @foreach ($requests->rows as $journal)
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
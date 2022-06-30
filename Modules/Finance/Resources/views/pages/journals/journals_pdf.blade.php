@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - JURNAL UMUM</title>
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
      <div class="text-center" style="font-size:16px;"><b>TRANSAKSI JURNAL UMUM</b></div>
      <br/>
      <br/>
      @foreach ($departments as $department)
      <table class="table no-border" width="100%">
        <tbody>
          <tr>
            <td style="width:3%;">Departemen</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td style="width:30%;"><b>{{ $department->department }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Tahun Buku</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $department->book_year }}</b></td>
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
        @foreach ($journals as $journal)
          @if ($journal->deptid == $department->deptid)
            <tr>
              <td class="text-center" rowspan="2">{{ $x }}</td>
              <td class="text-center" width="20%"><b>{{ $journal->cash_no }}</b> / {{ $journal->date_journal }}</td>
              <td width="30%">{{ $journal->transaction }}</td>
              <td rowspan="2">
                <table width="100%">
                  <tbody>
                    @foreach ($journal_details as $detail)
                      @if ($detail->journal_id == $journal->id)
                      <tr>
                        <td class="text-center" width="15%">{{ $detail->code }}</td>
                        <td>{{ $detail->account_name }}</td>
                        <td class="text-right" width="20%">Rp{{ number_format($detail->debit,2) }}</td>
                        <td class="text-right" width="20%">Rp{{ number_format($detail->credit,2) }}</td>
                      </tr>
                      @endif
                    @endforeach
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>              
              <td><b>Petugas</b>: {{ $journal->employee }}</td>
              <td><b>Sumber</b>: {{ $journal->source_name }}</td>
            </tr>
          @endif
          @php $x++; @endphp
        @endforeach
        </tbody>
      </table>
      @endforeach
  </body>
</html>
@inject('reference', 'Modules\Finance\Http\Controllers\FinanceController')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - PERUBAHAN DATA IURAN SUKARELA CALON SANTRI</title>
    <link href="file:///{{ public_path('css/report-audit.css') }}" rel="stylesheet" />
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
      <div class="text-center" style="font-size:16px;"><b>PERUBAHAN DATA IURAN SUKARELA CALON SANTRI</b></div>
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
            <td style="width:3%;">Tahun Buku</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->bookyear }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Tanggal</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->start_date }}</b> s.d <b>{{ $requests->end_date }}</b></td>
          </tr>
        </tbody>
      </table>
      <br/>
      <br/>
      <table width="100%">
        <thead>
          <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Status Data</th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Jumlah</th>
            <th class="text-center">Keterangan</th>
            <th class="text-center">Petugas</th>
          </tr>
        </thead>
        <tbody>
          @php $no = 1; @endphp
          @foreach ($audits as $audit)
          <tr>
            <td rowspan="4" class="text-center">{{ $no }}</td>
            <td colspan="6">Perubahan dilakukan oleh {{ $audit->employee }} tanggal {{ $reference->formatDate($audit->audit_date,'isotime') }}</td>
          </tr>
          <tr>
            <td colspan="6">
              <b>No. Jurnal</b>: {{ $audit->cash_no }} &nbsp;&nbsp; <b>Alasan</b>: {{ $audit->remark }}<br/>
              <b>Transaksi</b>: {{ $audit->transaction }}
            </td>
          </tr>
          @foreach ($detail_audits as $detail)
          @if ($audit->id == $detail->audit_id)
          <tr>
            <td>{{ $detail->is_status == 0 ? 'Data Lama' : 'Data Perubahan' }}</td>
            <td class="text-center">{{ $reference->formatDate($audit->audit_date,'timeiso') }}</td>
            <td class="text-right">Rp{{ number_format($detail->total,2) }}</td>
            <td>{{ $detail->remark }}</td>
            <td class="text-center">{{ $detail->employee }}</td>
          </tr>
          @endif
          @endforeach
          @php $no++; @endphp
          @endforeach
        </tbody>
  </body>
</html>
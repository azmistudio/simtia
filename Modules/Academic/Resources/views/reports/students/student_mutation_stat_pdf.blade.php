@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Statistik Mutasi Santri</title>
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
      <div class="text-center" style="font-size:16px;"><b>Statistik Mutasi Santri Tahun {{ $payload->start }} s.d {{ $payload->end }}</b></div>
      <br/>
      <br/>
      <div>
        <div style="width:99.8%;height: 390px;text-align: center;border: solid 1px #d5d5d5;">
          {!! $graph !!}  
        </div>
        <br/>
        <table class="table table-sm table-bordered" style="width:100%;">
          <thead>
            <tr>
              <th class="text-center" width="5%">No.</th>
              <th class="text-center">Jenis Mutasi</th>
              <th class="text-center" width="10%">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            @php $num = 1; @endphp
            @foreach ($payload->rows as $row)
            <tr>
              <td class="text-center">{{ $num }}</td>
              <td>{{ $row->mutation }}</td>
              <td class="text-center">{{ $row->total }}</td>
            </tr>
            @foreach ($details as $detail)
            @if ($row->id == $detail->id_mutation)
            <tr>
              <td></td>
              <td>{{ $detail->department }} - NIS: {{ $detail->student_no }} - {{ $detail->student_id }} ({{ $detail->remark }} per {{ $detail->mutation_date }})</td>
              <td></td>
            </tr>
            @endif
            @endforeach
            @php $num++; @endphp
            @endforeach
          </tbody>
        </table>
        <br/>
        <br/>
      </div>
  </body>
</html>
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Rencana Program Pembelajaran</title>
    <style type="text/css">
      body { margin: 0; padding: 0; font-size: 11px; font-family: "Segoe UI", "Open Sans", serif !important; }
      .text-left { text-align: left; }
      .text-center { text-align: center; }
      .text-right { text-align: right; }
      #imgLogo { margin-bottom: 5px; }
      .break { page-break-before: avoid; }
      .must-break { page-break-before: always; }
      table.no-border, table.no-border th, table.no-border td { border: none; }
      table { border-collapse: collapse; border: 1px solid #000; font-size:13px; page-break-inside: auto; }
      tr { page-break-inside: avoid; page-break-after: auto; }
      th, td { border: 1px solid #000; padding: 3px; }
      thead, tfoot { display: table-row-group; }
    </style>
  </head>
  <body>
    <div id="header">
        <table class="table no-border" style="width:100%;">
            <tbody>
                <tr>
                    <th rowspan="2" width="10%"><img src="file:///{{ $logo }}" height="80px" /></th>
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
      <div class="text-center" style="font-size:16px;"><b>Data Rencana Program Pembelajaran</b></div>
      <br/>
      <br/>
      <div>
        <table style="width:100%;">
          <thead>
            <tr>
                <th class="text-center" width="4%">NO.</th>
                <th class="text-center" width="30%">MATERI</th>
                <th class="text-center">DESKRIPSI</th>
                <th class="text-center">STATUS</th>
            </tr>
          </thead>
          <tbody>
            @php $num = 1; @endphp
            @foreach ($models as $model)
              <tr>
                <td class="text-center" style="vertical-align: sub;">{{ $num }}</td>
                <td style="vertical-align: sub;">
                  <b>{{ $model->getDepartment->name }} / {{ $model->getGrade->grade }} / {{ $model->getSemester->semester }}</b>
                  <br/>
                  <ul style="padding-left: 20px;margin-top: 5px;">
                    <li>Pelajaran: {{ $model->getLesson->name }}</li>
                    <li>Kode: {{ $model->code }}</li>
                    <li>Materi: {{ $model->subject }}</li>
                  </ul>
                </td>
                <td >{!! $model->description !!}</td>
                <td class="text-center" style="vertical-align: sub;">{{ $model->is_active }}</td>
              </tr> 
              @php $num++; @endphp
            @endforeach
          </tbody>
        </table>
      </div>
  </body>
</html>
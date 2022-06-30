@php
    $present = array();
    $sick = array();
    $permit = array();
    $absent = array();
    $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN HASIL BELAJAR</title>
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
      <div class="text-center" style="font-size:16px;"><b>LAPORAN HASIL BELAJAR</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:3%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ $requests['department'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $requests['schoolyear'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $requests['semester'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $requests['grade'] .' - '. $requests['class'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">NIS</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $requests['student_no'] }}</td>
            </tr>
            <tr>
              <td style="width:3%;">Nama</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $requests['student_name'] }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        @foreach ($socials as $social)
        <div style="margin-bottom: 10px;">
            <fieldset>
                <legend style="font-size:12px;"><b>Komentar Sikap {{ ucfirst($social->aspect) }}</b></legend>
                <table class="table" style="width:100%;margin: 3px 0;">
                    <tbody>
                        <tr>
                            <td width="30%" style="vertical-align:top !important;">Predikat: <b>{{ $social->getType->name }}</b></td>
                            <td style="height: 100px;vertical-align:top !important;">{!! html_entity_decode($social->comment) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        @endforeach
        <div style="margin-bottom: 10px;">
            <fieldset>
                <legend style="font-size:12px;"><b>Nilai Pelajaran</b></legend>
                <table class="table" style="width:100%;margin: 3px 0;">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center" width="5%">No.</th>
                            <th rowspan="2" class="text-center">Pelajaran</th>
                            @php $i = 0; $columns = array(); @endphp
                            @foreach ($aspects as $aspect)
                                @php $columns[$i++] = array($aspect->id, $i); @endphp
                                <th colspan="2" class="text-center">{{ ucwords($aspect->remark) }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($columns as $column)
                            <th class="text-center" width="10%">Nilai</th>
                            <th class="text-center" width="10%">Predikat</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>{!! $tbody_lesson_score !!}</tbody>
                </table>
            </fieldset>
        </div>
        <div style="margin-bottom: 10px;">
            <fieldset>
                <legend style="font-size:12px;"><b>Deskripsi Nilai Pelajaran</b></legend>
                <table class="table" style="width:100%;margin: 3px 0;">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No.</th>
                            <th class="text-center">Pelajaran</th>
                            <th class="text-center" width="15%">Aspek</th>
                            <th class="text-center" width="30%">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>{!! $tbody_lesson_score_desc !!}</tbody>
                </table>
            </fieldset>
        </div>
        @if ($requests['daily'] == "true")
        <div style="margin-bottom:10px;">
            <fieldset>
                <legend style="font-size:12px;"><b>Presensi Harian</b></legend>
                <table class="table" style="width:100%;margin: 3px 0;">
                    <thead>
                        <tr>
                            <th colspan="2" class="text-center">Hadir</th>
                            <th colspan="2" class="text-center">Sakit</th>
                            <th colspan="2" class="text-center">Ijin</th>
                            <th colspan="2" class="text-center">Alpa</th>
                            <th colspan="2" class="text-center">Cuti</th>
                        </tr>
                        <tr>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{ $presences_daily->present }}</td>
                            <td class="text-center">@if ($presences_daily->present != 0 && $presences_daily->total != 0) {{ round(($presences_daily->present / $presences_daily->total) * 100,2) }} % @endif</td>
                            <td class="text-center">{{ $presences_daily->sick }}</td>
                            <td class="text-center">@if ($presences_daily->sick != 0 && $presences_daily->total != 0) {{ round(($presences_daily->sick / $presences_daily->total) * 100,2) }} % @endif</td>
                            <td class="text-center">{{ $presences_daily->permit }}</td>
                            <td class="text-center">@if ($presences_daily->permit != 0 && $presences_daily->total != 0) {{ round(($presences_daily->permit / $presences_daily->total) * 100,2) }} % @endif</td>
                            <td class="text-center">{{ $presences_daily->absent }}</td>
                            <td class="text-center">@if ($presences_daily->absent != 0 && $presences_daily->total != 0) {{ round(($presences_daily->absent / $presences_daily->total) * 100,2) }} % @endif</td>
                            <td class="text-center">{{ $presences_daily->leave }}</td>
                            <td class="text-center">@if ($presences_daily->leave != 0 && $presences_daily->total != 0) {{ round(($presences_daily->leave / $presences_daily->total) * 100,2) }} % @endif</td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        @endif
        @if ($requests['lesson'] == "true")
        <div style="margin-bottom:10px;">
            <fieldset>
                <legend style="font-size:12px;"><b>Presensi Pelajaran</b></legend>
                <table class="table" style="width:100%;margin: 3px 0;">
                    <thead>
                        <tr>
                            <th rowspan="2" class="text-center" width="40%">Pelajaran</th>
                            <th colspan="2" class="text-center">Hadir</th>
                            <th colspan="2" class="text-center">Sakit</th>
                            <th colspan="2" class="text-center">Ijin</th>
                            <th colspan="2" class="text-center">Alpa</th>
                        </tr>
                        <tr>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lessons as $lesson)
                        <tr>
                            <td>{{ strtoupper($lesson->lesson) }}</td>
                            @php $counter = 1; @endphp
                            @foreach ($presences_lesson as $presence)
                                @if ($presence->lesson_id == $lesson->lesson_id)
                                    @php $present[$counter] = $presence->present; @endphp
                                    @php $sick[$counter] = $presence->sick; @endphp
                                    @php $permit[$counter] = $presence->permit; @endphp
                                    @php $absent[$counter] = $presence->absent; @endphp
                                    <td class="text-center">{{ $presence->present }}</td>
                                    <td class="text-center">@if ($presence->present != 0 && $presence->total != 0) {{ round(($presence->present / $presence->total) * 100,2) }} % @endif</td>
                                    <td class="text-center">{{ $presence->sick }}</td>
                                    <td class="text-center">@if ($presence->sick != 0 && $presence->total != 0) {{ round(($presence->sick / $presence->total) * 100,2) }} % @endif</td>
                                    <td class="text-center">{{ $presence->permit }}</td>
                                    <td class="text-center">@if ($presence->permit != 0 && $presence->total != 0) {{ round(($presence->permit / $presence->total) * 100,2) }} % @endif</td>
                                    <td class="text-center">{{ $presence->absent }}</td>
                                    <td class="text-center">@if ($presence->absent != 0 && $presence->total != 0) {{ round(($presence->absent / $presence->total) * 100,2) }} % @endif</td>
                                @endif
                            @php $counter++; @endphp
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                    @php
                        $prs = 0;
                        for ($i = 1; $i <= count($present); $i++)
                        {
                            $prs += $present[$i];
                        }
                        $sck = 0;
                        for ($i = 1; $i <= count($sick); $i++)
                        {
                            $sck += $sick[$i];
                        }
                        $lve = 0;
                        for ($i = 1; $i <= count($permit); $i++)
                        {
                            $lve += $permit[$i];
                        }
                        $abs = 0;
                        for ($i = 1; $i <= count($absent); $i++)
                        {
                            $abs += $absent[$i];
                        }
                    @endphp
                    <tfoot>
                        <tr>
                            <td class="text-center"><b>Total</b></td>
                            <td class="text-center">{{ $prs }}</td>
                            <td class="text-center"></td>
                            <td class="text-center">{{ $sck }}</td>
                            <td class="text-center"></td>
                            <td class="text-center">{{ $lve }}</td>
                            <td class="text-center"></td>
                            <td class="text-center">{{ $abs }}</td>
                            <td class="text-center"></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
        @endif
      </div>
  </body>
</html>